<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
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
