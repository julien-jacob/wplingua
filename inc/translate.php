<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Get the translated text from translations array
 *
 * @param string $text
 * @param array $translations
 * @return string
 */
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
		foreach ( $translations as $translation ) {

			if ( ! isset( $translation['source'] ) ) {
				continue;
			}

			$source = wplng_text_esc( $translation['source'] );

			if ( $text === $source ) {
				$translated = $translation['translation'];
			}
		}
	}

	$translated = esc_html( $translated );

	return $spaces_before . $translated . $spaces_after;
}


/**
 * wpLingua translate : Get translated array from decoded JSON
 *
 * @param array $json_decoded
 * @param array $translations
 * @param array $parents
 * @return array
 */
function wplng_translate_json_array( $json_decoded, $translations, $parents = array() ) {

	$array_translated = $json_decoded;
	$json_excluded    = wplng_data_excluded_json();

	/**
	 * Don't parse JSON if it's exclude
	 */
	if ( in_array( $parents, $json_excluded ) ) {

		if ( true === WPLNG_LOG_JSON_DEBUG ) {

			$debug = array(
				'title'   => 'wpLingua JSON debug - Excluded parent',
				'parents' => $parents,
				'value'   => $json_decoded,
			);

			error_log(
				var_export(
					$debug,
					true
				)
			);
		}

		return $json_decoded;
	}

	/**
	 * Parse each JSON elements
	 */
	foreach ( $json_decoded as $key => $value ) {

		/**
		 * Don't parse element if it's exclude
		 */
		if ( in_array( array_merge( $parents, array( $key ) ), $json_excluded ) ) {

			if ( true === WPLNG_LOG_JSON_DEBUG ) {

				$debug = array(
					'title'   => 'wpLingua JSON debug - Excluded element',
					'parents' => array_merge( $parents, array( $key ) ),
					'value'   => $value,
				);

				error_log(
					var_export(
						$debug,
						true
					)
				);
			}

			continue;
		}

		if ( is_array( $value ) ) {

			/**
			 * If element is an array, parse it
			 */

			$array_translated[ $key ] = wplng_translate_json_array(
				$value,
				$translations,
				array_merge( $parents, array( $key ) )
			);

		} elseif ( is_string( $value ) ) {

			$debug_type = '';

			/**
			 * If element is a string
			 */

			if ( wplng_str_is_locale_id( $value ) ) {

				/**
				 * If is a local ID (fr_FR, fr, FR, ...), replace by current
				 */

				$debug_type               = 'String - Local ID';
				$array_translated[ $key ] = wplng_get_language_current_id();

			} elseif ( wplng_str_is_html( $value ) ) {

				/**
				 * If element is a HTML, parse and translate it
				 */

				$debug_type               = 'String - HTML';
				$array_translated[ $key ] = wplng_translate_html(
					$value,
					'',
					'',
					$translations
				);

			} elseif ( wplng_str_is_json( $value ) ) {

				/**
				 * If element is a JSON, parse and translate it
				 */

				$debug_type               = 'String - JSON';
				$array_translated[ $key ] = wplng_translate_json(
					$value,
					$translations,
					array_merge( $parents, [ $key ] )
				);

			} elseif ( wplng_str_is_url( $value ) ) {

				/**
				 * If element is an URL, replace by translated URL
				 */

				$debug_type               = 'String - URL';
				$array_translated[ $key ] = wplng_url_translate( $value );

			} else {

				/**
				 * Element is a unknow string, check if it's translatable
				 * - Check if is an excluded element
				 * - Check if is an included element
				 * - Check if is a translatable string
				 */

				$is_translatable = wplng_json_element_is_translatable(
					$value,
					array_merge( $parents, array( $key ) )
				);

				if ( $is_translatable ) {
					$debug_type               = 'String - Translatale';
					$array_translated[ $key ] = wplng_get_translated_text_from_translations(
						$value,
						$translations
					);
				} else {
					$debug_type = 'String - Untranslatale';
				}
			}

			/**
			 * Print debug data in debug.log file
			 */

			if ( true === WPLNG_LOG_JSON_DEBUG
				&& isset( $json_decoded[ $key ] )
				&& isset( $array_translated[ $key ] )
			) {

				$debug = array(
					'title'   => 'wpLingua JSON debug',
					'parents' => array_merge( $parents, array( $key ) ),
					'type'    => $debug_type,
					'value'   => $json_decoded[ $key ],
				);

				if ( $json_decoded[ $key ] !== $array_translated[ $key ] ) {
					$debug['translated'] = $array_translated[ $key ];
				}

				error_log(
					var_export(
						$debug,
						true
					)
				);
			}
		}
	}

	return $array_translated;
}


/**
 * wpLingua translate : Get translated JSON
 *
 * @param string $json
 * @param array $translations
 * @param array $parents
 * @return string
 */
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


/**
 * wpLingua translate : Get translated JS
 *
 * @param string $js
 * @param array $translations
 * @return string
 */
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
			$js = str_replace(
				$var_json,
				$json_translated,
				$js
			);
		}
	}

	return $js;
}


/**
 * wpLingua translate : Get translated HTML
 *
 * @param string $html
 * @param string $language_source_id
 * @param string $language_target_id
 * @param array $translations
 * @return string
 */
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

	$dom = wplng_sdh_str_get_html( $html );

	if ( empty( $dom ) ) {
		return $html;
	}

	/**
	 * Set dir attr on <body> (ltr or rtl)
	 */

	$language_current = wplng_get_language_by_id( $language_target_id );

	if ( ! empty( $language_current['dir'] ) ) {
		foreach ( $dom->find( 'body' ) as $element ) {
			$element->{'dir'} = esc_attr( $language_current['dir'] );
		}
	}

	/**
	 * Replace languages IDs in attributes
	 */

	// Replace languages IDs in attributes
	$attr_lang_id_to_replace = wplng_data_attr_lang_id_to_replace();
	foreach ( $attr_lang_id_to_replace as $attr ) {
		foreach ( $dom->find( $attr['selector'] ) as $element ) {

			if ( empty( $element->attr[ $attr['attr'] ] ) ) {
				continue;
			}

			$lang_id = $element->attr[ $attr['attr'] ];

			if ( ! wplng_str_is_locale_id( $lang_id ) ) {
				continue;
			}

			$element->attr[ $attr['attr'] ] = esc_attr( $language_target_id );
		}
	}

	// Replace languages IDs in <body> class
	foreach ( $dom->find( 'body[class]' ) as $element ) {

		$class_array = explode( ' ', $element->class );

		foreach ( $class_array as $key => $class ) {
			if ( wplng_str_is_locale_id( $class ) ) {
				$class_array[ $key ] = $language_target_id;
			} elseif ( 'ltr' === $class || 'rtl' === $class ) {
				if ( ! empty( $language_current['dir'] ) ) {
					$class_array[ $key ] = $language_current['dir'];
				} else {
					$class_array[ $key ] = 'ltr';
				}
			}
		}

		$class_array[] = 'wplingua-translated';
		$class_array   = array_unique( $class_array ); // Remove duplicate
		$class_str     = '';

		foreach ( $class_array as $key => $class ) {
			$class_str .= $class . ' ';
		}

		$class_str      = trim( $class_str );
		$element->class = esc_attr( $class_str );
	}

	/**
	 * Translate links in attributes
	 */

	$attr_url_to_translate = wplng_data_attr_url_to_translate();

	foreach ( $attr_url_to_translate as $attr ) {
		foreach ( $dom->find( $attr['selector'] ) as $element ) {

			if ( empty( $element->attr[ $attr['attr'] ] ) ) {
				continue;
			}

			$link = sanitize_url( $element->attr[ $attr['attr'] ] );

			$translated_url = wplng_url_translate(
				$link,
				$language_target_id
			);

			$element->attr[ $attr['attr'] ] = esc_url( $translated_url );
		}
	}

	/**
	 * If empty translations, return HTML with translated link
	 */

	if ( empty( $translations ) ) {
		$dom->save();
		return (string) wplng_sdh_str_get_html( $dom );
	}

	/**
	 * Find and parse JSON
	 */

	foreach ( $dom->find( 'script[type="application/ld+json"]' ) as $element ) {

		$translated_json = wplng_translate_json(
			$element->innertext,
			$translations
		);

		$element->innertext = $translated_json;
	}

	/**
	 * Find and translate JS
	 */

	foreach ( $dom->find( 'script' ) as $element ) {

		$translated_js = wplng_translate_js(
			$element->innertext,
			$translations
		);

		$element->innertext = $translated_js;
	}

	/**
	 * Parse Node text
	 */

	$node_text_excluded = wplng_data_excluded_node_text();

	foreach ( $dom->find( 'text' ) as $element ) {

		if ( in_array( $element->parent->tag, $node_text_excluded ) ) {
			continue;
		}

		$translated_text = wplng_get_translated_text_from_translations(
			$element->innertext,
			$translations
		);

		$element->innertext = esc_html( $translated_text );
	}

	/**
	 * Parse attr
	 */

	$attr_text_to_translate = wplng_data_attr_text_to_translate();

	foreach ( $attr_text_to_translate as $attr ) {
		foreach ( $dom->find( $attr['selector'] ) as $element ) {

			if ( empty( $element->attr[ $attr['attr'] ] ) ) {
				continue;
			}

			$text = wplng_text_esc( $element->attr[ $attr['attr'] ] );

			if ( ! wplng_text_is_translatable( $text ) ) {
				continue;
			}

			$translated_attr = wplng_get_translated_text_from_translations(
				$text,
				$translations
			);

			$element->attr[ $attr['attr'] ] = esc_attr( $translated_attr );
		}
	}

	$dom->save();
	$dom = (string) wplng_sdh_str_get_html( $dom );

	if ( empty( $dom ) ) {
		return $html;
	}

	return $dom;
}
