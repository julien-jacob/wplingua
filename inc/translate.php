<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_get_translated_text_from_translations( $text, $translations ) {

	if ( empty( trim( $text ) ) ) {
		return $text;
	}

	/**
	 * Get spaces before and after text
	 */
	$temp          = array();
	$spaces_before = '';
	$spaces_after  = '';

	preg_match( '#^(\s*).*#', $text, $temp );
	if ( ! empty( $temp[1] ) ) {
		$spaces_before = $temp[1];
	}

	preg_match( '#.*(\s*)$#U', $text, $temp );
	if ( ! empty( $temp[1] ) ) {
		$spaces_after = $temp[1];
	}

	$text       = wplng_text_esc( $text );
	$translated = $text;

	if ( wplng_text_is_translatable( $text ) ) {
		foreach ( $translations as $key => $translation ) {

			if ( ! isset( $translation['source'] )
				|| ! isset( $translation['source'] )
			) {
				continue;
			}

			$source = wplng_text_esc( $translation['source'] );

			if ( $text === $source ) {
				$translated = $translation['translation'];
			}
		}
	}

	$translated = esc_attr( esc_html( $translated ) );

	return $spaces_before . $translated . $spaces_after;
}


function wplng_translate_json_array( $json_decoded, $translations, $parents = array() ) {

	$array_translated = $json_decoded;
	$json_excluded    = wplng_data_excluded_json();

	if ( in_array( $parents, $json_excluded ) ) {
		return $json_decoded;
	}

	foreach ( $json_decoded as $key => $value ) {

		if ( in_array( array_merge( $parents, array( $key ) ), $json_excluded ) ) {
			continue;
		}

		if ( is_array( $value ) ) {

			$array_translated[ $key ] = wplng_translate_json_array(
				$value,
				$translations,
				array_merge( $parents, array( $key ) )
			);

		} elseif ( is_string( $value ) ) {

			$locale  = get_locale();                    // Ex: fr_FR
			$locales = array(
				$locale,                                // Ex: fr_FR
				str_replace( '_', '-', $locale ),       // Ex: fr-FR
				substr( $locale, 0, 2 ),                // Ex: FR
				strtolower( substr( $locale, 0, 2 ) ),  // Ex: fr
			);

			if ( in_array( $value, $locales ) ) {

				$array_translated[ $key ] = wplng_get_language_current_id();

			} elseif ( wplng_str_is_html( $value ) ) {

				$array_translated[ $key ] = wplng_translate_html(
					$value,
					'',
					'',
					$translations
				);

			} elseif ( wplng_str_is_json( $value ) ) {

				$array_translated[ $key ] = wplng_translate_json(
					$value,
					$translations,
					array_merge( $parents, [ $key ] )
				);

			} elseif ( wplng_str_is_url( $value ) ) {

				$array_translated[ $key ] = wplng_url_translate( $value );

			} else {

				if ( wplng_text_is_translatable( $value )
					|| str_contains( $value, '_' )
				) {
					continue;
				}

				// Todo : Ajouter filtre bool pour exclure json

				$array_translated[ $key ] = wplng_get_translated_text_from_translations(
					$value,
					$translations
				);

				// error_log(
				// 	var_export(
				// 		array(
				// 			'parents'   => $parents,
				// 			'value'     => $value,
				// 			'translate' => $array_translated[ $key ],
				// 		),
				// 		true
				// 	)
				// );
			}
		}

		// if (
		//  $array_translated[ $key ] != $json_decoded[ $key ]
		// 	&& !is_array($json_decoded[ $key ])
		// ) {
		// 	error_log(
		// 		var_export(
		// 			array(
		// 				'parents'    => $parents,
		// 				'value'      => $json_decoded[ $key ],
		// 				'translated' => $array_translated[ $key ],
		// 			),
		// 			true
		// 		)
		// 	);
		// }
	}

	return $array_translated;
}


function wplng_translate_json( $json, $translations, $parents = array() ) {

	$json_translated = '';
	$json_decoded    = json_decode( $json, true );

	if ( empty( $json_decoded ) || ! is_array( $json_decoded ) ) {
		return $json;
	}

	$json_translated = wp_json_encode(
		wplng_translate_json_array(
			$json_decoded,
			$translations,
			$parents
		)
	);

	if ( empty( $json_translated ) ) {
		return $json;
	}

	return $json_translated;
}


function wplng_translate_js( $js, $translations ) {

	if ( empty( trim( $js ) ) ) {
		return $js;
	}

	$json = array();

	preg_match_all(
		'#var\s(.*)\s?=\s?(\{.*\});#Ui',
		$js,
		$json
	);

	if ( ! empty( $json[1][0] ) && ! empty( $json[2][0] ) ) {

		$var_name = $json[1][0];
		$var_json = $json[2][0];

		$json_translated = wplng_translate_json(
			$var_json,
			$translations,
			array( $var_name )
		);

		if ( $var_json != $json_translated ) {

			// error_log(
			// 	var_export(
			// 		array(
			// 			'var_json'    => $var_json,
			// 			'json_translated'      => $json_translated,
			// 		),
			// 		true
			// 	)
			// );

			$js = str_replace(
				$var_json,
				$json_translated,
				$js
			);

		}
	}

	return $js;
}



function wplng_translate_html(
	$html,
	$language_source_id = '',
	$language_target_id = '',
	$translations = array()
) {

	if ( empty( $html ) ) {
		return $html;
	}

	if ( empty( $language_target_id ) ) {
		$language_target_id = wplng_get_language_current_id();
	}

	if ( empty( $language_source_id ) ) {
		$language_source_id = wplng_get_language_website_id();
	}

	$dom = str_get_html( $html );

	if ( empty( $dom ) ) {
		return $html;
	}

	/**
	 * Set dir attr on <html> (ltr or rtl)
	 */

	$language_current = wplng_get_language_by_id( $language_target_id );

	if ( ! empty( $language_current['dir'] ) ) {
		foreach ( $dom->find( 'html' ) as $element ) {
			$element->{'dir'} = esc_attr( $language_current['dir'] );
		}
	}

	/**
	 * Replace languages IDs in attributes
	 */

	$attr_lang_id_to_replace = wplng_data_attr_lang_id_to_replace();

	$locale  = get_locale();                    // Ex: fr_FR
	$locales = array(
		$locale,                                // Ex: fr_FR
		str_replace( '_', '-', $locale ),       // Ex: fr-FR
		substr( $locale, 0, 2 ),                // Ex: FR
		strtolower( substr( $locale, 0, 2 ) ),  // Ex: fr
	);

	foreach ( $attr_lang_id_to_replace as $key => $attr ) {
		foreach ( $dom->find( $attr['selector'] ) as $element ) {

			if ( empty( $element->attr[ $attr['attr'] ] ) ) {
				continue;
			}

			$lang_id = $element->attr[ $attr['attr'] ];

			if ( ! in_array( $lang_id, $locales ) ) {
				continue;
			}

			$element->attr[ $attr['attr'] ] = esc_attr( $language_target_id );
		}
	}

	/**
	 * Translate links in attributes
	 */

	$attr_url_to_translate = wplng_data_attr_url_to_translate();

	foreach ( $attr_url_to_translate as $key => $attr ) {
		foreach ( $dom->find( $attr['selector'] ) as $element ) {

			if ( empty( $element->attr[ $attr['attr'] ] ) ) {
				continue;
			}

			$link = $element->attr[ $attr['attr'] ];

			$element->attr[ $attr['attr'] ] = wplng_url_translate(
				$link,
				$language_target_id
			);
		}
	}

	/**
	 * If empty translations, return HTML with translated link
	 */

	if ( empty( $translations ) ) {
		$dom->save();
		return (string) str_get_html( $dom );
	}

	/**
	 * Find and parse JS
	 */

	foreach ( $dom->find( 'script[type="application/ld+json"]' ) as $element ) {
		$element->innertext = wplng_translate_json( $element->innertext, $translations );
	}

	/**
	 * Find and translate JS
	 */

	foreach ( $dom->find( 'script' ) as $element ) {
		$element->innertext = wplng_translate_js( $element->innertext, $translations );
	}

	/**
	 * Parse Node text
	 */

	$node_text_excluded = wplng_data_excluded_node_text();

	foreach ( $dom->find( 'text' ) as $element ) {

		if ( in_array( $element->parent->tag, $node_text_excluded ) ) {
			continue;
		}

		$element->innertext = wplng_get_translated_text_from_translations(
			$element->innertext,
			$translations
		);
	}

	/**
	 * Parse attr
	 */

	$attr_text_to_translate = wplng_data_attr_text_to_translate();

	foreach ( $attr_text_to_translate as $key => $attr ) {
		foreach ( $dom->find( $attr['selector'] ) as $element ) {

			if ( empty( $element->attr[ $attr['attr'] ] ) ) {
				continue;
			}

			$text = wplng_text_esc( $element->attr[ $attr['attr'] ] );

			if ( ! wplng_text_is_translatable( $text ) ) {
				continue;
			}

			$element->attr[ $attr['attr'] ] = wplng_get_translated_text_from_translations(
				$text,
				$translations
			);
		}
	}

	$dom->save();

	return (string) str_get_html( $dom );
}
