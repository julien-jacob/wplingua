<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_parse_json_array( $json_decoded, $parents = array() ) {

	$texts = array();

	foreach ( $json_decoded as $key => $value ) {

		if ( is_array( $value ) ) {

			$texts = array_merge(
				$texts,
				wplng_parse_json_array( $value, array_merge( $parents, [ $key ] ) )
			);

		} elseif ( is_string( $value ) ) {

			// TODO : Continuer ici !!!

			if ( wplng_str_is_html( $value ) ) {

				$texts = array_merge(
					$texts,
					wplng_parse_html( $value, array_merge( $parents, [ $key ] ) )
				);

			} elseif ( wplng_str_is_json( $value ) ) {

				$texts = array_merge(
					$texts,
					wplng_parse_json( $value, array_merge( $parents, [ $key ] ) )
				);

			} else {

				$parents = array_merge( $parents, [ $key ] );

				// error_log(
				// 	var_export(
				// 		array(
				// 			'parents' => $parents,
				// 			'value'   => $value,
				// 		),
				// 		true
				// 	)
				// );

				$json_signatures = wplng_data_excluded_json();

				// Todo : Ajouter filtre bool pour exclure json

				if (
					in_array( $parents, $json_signatures, true )
					&& wplng_text_is_translatable( $value )
				) {
					$texts[] = $value;
				}
			}
		}
	}

	// Remove duplicate
	$texts = array_unique( $texts );

	return $texts;
}


function wplng_parse_json( $json, $parents = array() ) {

	$json_decoded = json_decode( $json, true );

	if ( empty( $json_decoded ) || ! is_array( $json_decoded ) ) {
		return array();
	}

	$texts = wplng_parse_json_array( $json_decoded, $parents );

	// Remove duplicate
	$texts = array_unique( $texts );

	return $texts;
}


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

		$texts[] = array(
			'var_name' => $var_name,
			'var_json' => $var_json,
		);

		$texts = wplng_parse_json(
			$var_json,
			[ $var_name ]
		);

	}

	// Remove duplicate
	$texts = array_unique( $texts );

	return $texts;
}


function wplng_parse_html( $html ) {

	$texts = array();
	$dom   = str_get_html( $html );

	if ( empty( $dom ) ) {
		return $html;
	}

	
	foreach ( $dom->find( 'script[type="application/ld+json"]' ) as $element ) {
		$texts = array_merge(
			$texts,
			wplng_parse_json( $element->innertext )
		);
	}
	
	foreach ( $dom->find( 'script' ) as $element ) {
		$texts = array_merge(
			$texts,
			wplng_parse_js( $element->innertext )
		);
	}
	// return var_export( $texts, true );

	/**
	 * Parse Node text
	 */
	$excluded_node_text = wplng_data_excluded_node_text();
	foreach ( $dom->find( 'text' ) as $element ) {

		if ( in_array( $element->parent->tag, $excluded_node_text ) ) {
			continue;
		}

		$text = wplng_text_esc( $element->innertext );

		if ( ! wplng_text_is_translatable( $text ) ) {
			continue;
		}

		$texts[] = $text;
	}

	/**
	 * Parse attr
	 */
	$attr_to_translate = wplng_data_attr_to_translate();
	foreach ( $dom->find( '*' ) as $element ) {

		if ( empty( $element->attr ) ) {
			continue;
		}

		foreach ( $element->attr as $attr => $value ) {
			if ( ! in_array( $attr, $attr_to_translate )
				|| empty( $value )
			) {
				continue;
			}

			$text = wplng_text_esc( $value );

			if ( ! wplng_text_is_translatable( $text ) ) {
				continue;
			}

			$texts[] = $text;
		}
	}

	// Remove duplicate
	$texts = array_unique( $texts );

	// return var_export( $texts, true );
	return $texts;
}
