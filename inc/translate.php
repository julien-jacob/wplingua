<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}




function wplng_get_translated_text_from_translations( $text, $translations ) {

	if ( empty( trim( $text ) ) ) {
		return $text;
	}

	// $text = esc_attr(esc_html( $text ));

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
				$translated = esc_attr(esc_html( $translation['translation'] ));
			}
		}
	}

	return $spaces_before . $translated . $spaces_after;
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
	 * Parse Node text
	 */
	foreach ( $dom->find( 'text' ) as $element ) {

		if ( in_array( $element->parent->tag, array( 'style', 'svg', 'script', 'canvas', 'link' ) ) ) {
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
	foreach ( $dom->find( '*' ) as $element ) {

		if ( empty( $element->attr ) ) {
			continue;
		}

		foreach ( $element->attr as $attr => $value ) {

			$attr_to_translate = array( 
				'alt', 
				'title', 
				'placeholder', 
				'aria-label' 
			);

			if ( ! in_array( $attr, $attr_to_translate )
				|| empty( $value )
			) {
				continue;
			}

			$element->innertext = wplng_get_translated_text_from_translations(
				$element->innertext,
				$translations
			);
		}
	}

	$dom->save();
	$html = (string) str_get_html( $dom );

	return $html;
}


// function wplng_translate_js(
// 	$js,
// 	$language_source_id = '',
// 	$language_target_id = '',
// 	$translations = array()
// ) {

// }
