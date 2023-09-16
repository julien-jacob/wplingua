<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_ob_callback_translate( $html ) {

	$html = apply_filters( 'wplng_html_intercepted', $html );

	if ( wplng_str_is_json( $html ) ) {
		$html = wplng_ob_callback_translate_json( $html );
	} elseif ( wplng_str_is_html( $html ) ) {
		$html = wplng_ob_callback_translate_html( $html );
	}

	$html = apply_filters( 'wplng_html_translated', $html );

	return $html;
}


function wplng_ob_callback_translate_json( $json ) {

	if ( empty( $json ) ) {
		return $json;
	}

	/**
	 * Get all texts in JSON
	 */

	$texts = wplng_parse_json( $json );

	/**
	 * Get saved translation
	 */

	$language_target_id = wplng_get_language_current_id();
	$translations       = array();

	if ( ! empty( $texts ) ) {
		$translations = wplng_get_translations_saved( $language_target_id );
	}

	/**
	 * Get unknow texts
	 */

	$texts_unknow = array();

	foreach ( $texts as $text ) {
		$is_in = false;
		foreach ( $translations as $translation ) {
			if ( $text === $translation['source'] ) {
				$is_in = true;
				break;
			}
		}
		if ( ! $is_in ) {
			$texts_unknow[] = $text;
		}
	}

	$texts_unknow = array_splice(
		$texts_unknow,
		0,
		WPLNG_MAX_TRANSLATIONS + 1
	);

	/**
	 * Get new translated text
	 */

	$texts_unknow = array();

	$texts_unknow_translated = wplng_api_call_translate(
		$texts_unknow,
		false,
		$language_target_id
	);

	/**
	 * Save new translation as wplng_translation CPT
	 */

	$translations_new = array();

	foreach ( $texts_unknow as $key => $text_source ) {
		if ( isset( $texts_unknow_translated[ $key ] ) ) {
			$translations_new[] = array(
				'source'      => $text_source,
				'translation' => $texts_unknow_translated[ $key ],
			);
		}
	}

	$translations_new = wplng_save_translations(
		$translations_new,
		$language_target_id
	);

	/**
	 * Merge know and new translations
	 */

	$translations = array_merge( $translations_new, $translations );

	/**
	 * Replace original texts by translations
	 * Translate links
	 * Replace locale ID in data
	 */

	$json = wplng_translate_json(
		$json,
		$translations
	);

	return $json;
}


function wplng_ob_callback_translate_html( $html ) {

	if ( empty( $html ) ) {
		return $html;
	}

	/**
	 * Replace excluded HTML part by tag
	 */

	$excluded = array();

	$html = wplng_html_set_exclude_tag(
		$html,
		$excluded
	);

	/**
	 * Get all texts in HTML
	 */

	$texts = wplng_parse_html( $html );

	/**
	 * Get saved translation
	 */

	$language_target_id = wplng_get_language_current_id();
	$translations       = array();

	if ( ! empty( $texts ) ) {
		$translations = wplng_get_translations_saved( $language_target_id );
	}

	/**
	 * Get unknow texts
	 */
	$texts_unknow = array();

	foreach ( $texts as $text ) {
		$is_in = false;
		foreach ( $translations as $translation ) {
			if ( $text === $translation['source'] ) {
				$is_in = true;
				break;
			}
		}
		if ( ! $is_in ) {
			$texts_unknow[] = $text;
		}
	}

	$texts_unknow = array_splice(
		$texts_unknow,
		0,
		WPLNG_MAX_TRANSLATIONS + 1
	);

	/**
	 * Get new translated text
	 */

	$texts_unknow_translated = wplng_api_call_translate(
		$texts_unknow,
		false,
		$language_target_id
	);

	/**
	 * Save new translation as wplng_translation CPT
	 */

	$translations_new = array();

	foreach ( $texts_unknow as $key => $text_source ) {
		if ( isset( $texts_unknow_translated[ $key ] ) ) {
			$translations_new[] = array(
				'source'      => $text_source,
				'translation' => $texts_unknow_translated[ $key ],
			);
		}
	}

	$translations_new = wplng_save_translations(
		$translations_new,
		$language_target_id
	);

	/**
	 * Merge know and new translations
	 */

	$translations = array_merge( $translations_new, $translations );

	/**
	 * Replace original texts by translations
	 */

	$html = wplng_translate_html(
		$html,
		false,
		$language_target_id,
		$translations
	);

	/**
	 * Replace tag by saved excluded HTML part
	 */

	$html = wplng_html_replace_exclude_tag(
		$html,
		$excluded
	);

	return $html;
}
