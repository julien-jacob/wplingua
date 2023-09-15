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

			if ( ! isset( $translation['source'] ) || ! isset( $translation['source'] ) ) {
				continue;
			}

			if ( $text === $translation['source'] ) {
				$translated = esc_attr( esc_html( $translation['translation'] ) );
			}
		}
	}

	return $spaces_before . $translated . $spaces_after;
}


function wplng_translate_json_array( $json_decoded, $translations, $parents = array() ) {

	$array_translated = $json_decoded;

	foreach ( $json_decoded as $key => $value ) {

		if ( is_array( $value ) ) {

			$array_translated[ $key ] = wplng_translate_json_array(
				$value,
				$translations,
				array_merge( $parents, [ $key ] )
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
					$translations,
					array_merge( $parents, [ $key ] )
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

				$parents       = array_merge( $parents, [ $key ] );
				$json_excluded = wplng_data_excluded_json();

				// Todo : Ajouter filtre bool pour exclure json

				if (
					in_array( $parents, $json_excluded, true )
					&& wplng_text_is_translatable( $value )
				) {
					$texts[]                  = $value;
					$array_translated[ $key ] = wplng_get_translated_text_from_translations(
						$value,
						$translations
					);
				}
			}
		}
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

	$json = array();

	if ( empty( trim( $js ) ) ) {
		return $js;
	}

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
			[ $var_name ]
		);

		if ( $var_json != $json_translated ) {
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
	 * Translate links
	 */
	foreach ( $dom->find( 'a' ) as $element ) {
		$link          = $element->href;
		$element->href = wplng_url_translate( $link, $language_target_id );
	}

	foreach ( $dom->find( 'form' ) as $element ) {
		$link            = $element->action;
		$element->action = wplng_url_translate( $link, $language_target_id );
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
	foreach ( $dom->find( 'text' ) as $element ) {
		$element->innertext = wplng_get_translated_text_from_translations(
			$element->innertext,
			$translations
		);
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

			$element->attr[ $attr ] = wplng_get_translated_text_from_translations(
				$element->innertext,
				$translations
			);
		}
	}

	$dom->save();

	return (string) str_get_html( $dom );
}
