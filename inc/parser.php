<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_get_json_signature() {

	$json_signatures = array(
		array( '@context', '@graph', 0, '@type', '@id', 'url', 'name' ),
		array( '@context', '@graph', 0, '@type', '@id', 'url', 'name', 'thumbnailUrl', 'datePublished', 'dateModified', 'description' ),
		array( '@context', '@graph', 2, '@type', '@id', 'itemListElement', 0, '@type', 'name' ),
		array( '@context', '@graph', 2, '@type', '@id', 'itemListElement', 1, '@type', 'name' ),
		array( '@context', '@graph', 3, '@type', '@id', 'url', 'name' ),
		array( '@context', '@graph', 3, '@type', '@id', 'url', 'name', 'description' ),
		array( '@context', '@graph', 4, '@type', '@id', 'name' ),
		array( '@context', '@graph', 4, '@type', '@id', 'name', 'url', 'logo', '@type', 'inLanguage', '@id', 'url', 'contentUrl', 'caption' ),
	);

	$json_signatures = apply_filters(
		'wplng_json_signatures',
		$json_signatures
	);

	return $json_signatures;
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

				$json_signatures = wplng_get_json_signature();

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
	foreach ( $dom->find( 'text' ) as $element ) {

		if ( in_array( $element->parent->tag, array( 'style', 'svg', 'script', 'canvas', 'link' ) ) ) {
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
	foreach ( $dom->find( '*' ) as $element ) {

		if ( empty( $element->attr ) ) {
			continue;
		}

		foreach ( $element->attr as $attr => $value ) {
			if ( ! in_array( $attr, array( 'alt', 'title', 'placeholder', 'aria-label' ) )
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
