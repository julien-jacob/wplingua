<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * wpLingua Parser : Get texts in an array decoded from a JSON
 *
 * @param array $json_decoded
 * @param array $parents
 * @return array Texts
 */
function wplng_parse_json_array( $json_decoded, $parents = array() ) {

	$texts         = array();
	$json_excluded = wplng_data_excluded_json();

	if ( in_array( $parents, $json_excluded ) ) {
		return array();
	}

	foreach ( $json_decoded as $key => $value ) {

		if ( in_array( array_merge( $parents, array( $key ) ), $json_excluded ) ) {
			continue;
		}

		if ( is_array( $value ) ) {

			$texts = array_merge(
				$texts,
				wplng_parse_json_array( $value, array_merge( $parents, array( $key ) ) )
			);

		} elseif ( is_string( $value ) ) {

			if ( wplng_str_is_url( $value )
				|| wplng_str_is_locale_id( $value )
			) {
				continue;
			}

			if ( wplng_str_is_html( $value ) ) {

				$texts = array_merge(
					$texts,
					wplng_parse_html( $value, array_merge( $parents, array( $key ) ) )
				);

			} elseif ( wplng_str_is_json( $value ) ) {

				$texts = array_merge(
					$texts,
					wplng_parse_json( $value, array_merge( $parents, array( $key ) ) )
				);

			} else {

				$is_translatable = wplng_json_element_is_translatable(
					$value,
					array_merge( $parents, array( $key ) )
				);

				if ( ! $is_translatable ) {
					continue;
				}

				$texts[] = $value;
			}
		}
	}

	$texts = array_unique( $texts ); // Remove duplicate

	return $texts;
}


/**
 * wpLingua Parser : Get texts in an JSON
 *
 * @param string $json
 * @param array $parents
 * @return array Texts
 */
function wplng_parse_json( $json, $parents = array() ) {

	$json_decoded = json_decode( $json, true );

	if ( empty( $json_decoded ) || ! is_array( $json_decoded ) ) {
		return array();
	}

	$texts = wplng_parse_json_array( $json_decoded, $parents );

	$texts = array_unique( $texts ); // Remove duplicate

	return $texts;
}


/**
 * wpLingua Parser : Get texts in an JS
 *
 * @param string $js
 * @return array Texts
 */
function wplng_parse_js( $js ) {

	$texts = array();
	$json  = array();

	if ( empty( trim( $js ) ) ) {
		return array();
	}

	preg_match_all(
		'#var\s(.*)\s?=\s?(\{.*\});#Ui',
		$js,
		$json
	);

	if ( ! empty( $json[1][0] ) && ! empty( $json[2][0] ) ) {

		$var_name = $json[1][0];
		$var_json = $json[2][0];

		$texts = wplng_parse_json(
			$var_json,
			array( $var_name )
		);

	}

	$texts = array_unique( $texts ); // Remove duplicate

	return $texts;
}


/**
 * wpLingua Parser : Get texts in an HTML
 *
 * @param string $html
 * @return array Texts
 */
function wplng_parse_html( $html ) {

	$texts = array();
	$dom   = wplng_sdh_str_get_html( $html );

	if ( empty( $dom ) ) {
		return array();
	}

	/**
	 * Find and parse JSON
	 */
	foreach ( $dom->find( 'script[type="application/ld+json"]' ) as $element ) {
		$texts = array_merge(
			$texts,
			wplng_parse_json( $element->innertext )
		);
	}

	/**
	 * Find and translate JS
	 */
	foreach ( $dom->find( 'script' ) as $element ) {
		$texts = array_merge(
			$texts,
			wplng_parse_js( $element->innertext )
		);
	}

	/**
	 * Parse Node text
	 */

	$node_text_excluded = wplng_data_excluded_node_text();

	foreach ( $dom->find( 'text' ) as $element ) {

		$text = wplng_text_esc( $element->innertext );

		if ( in_array( $element->parent->tag, $node_text_excluded )
			|| ! wplng_text_is_translatable( $text )
		) {
			continue;
		}

		$texts[] = $text;
	}

	/**
	 * Parse attr
	 */

	$attr_to_translate = wplng_data_attr_text_to_translate();

	foreach ( $attr_to_translate as $attr ) {
		foreach ( $dom->find( $attr['selector'] ) as $element ) {

			if ( empty( $element->attr[ $attr['attr'] ] ) ) {
				continue;
			}

			$text = wplng_text_esc( $element->attr[ $attr['attr'] ] );

			if ( ! wplng_text_is_translatable( $text ) ) {
				continue;
			}

			$texts[] = $text;
		}
	}

	$texts = array_unique( $texts ); // Remove duplicate

	return $texts;
}
