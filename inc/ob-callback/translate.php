<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_ob_callback_translate( $html ) {

	$html = apply_filters( 'wplng_html_intercepted', $html );

	/**
	 * Get saved translation
	 */
	$language_target_id = wplng_get_language_current_id();
	$translations       = wplng_get_translations_saved( $language_target_id );

	/**
	 * Get new translation from API
	 */
	$translations_new = wplng_parser( $html, false, false, $translations );

	/**
	 * Save new translation as wplng_translation CPT
	 */
	wplng_save_translations( $translations_new, $language_target_id );

	/**
	 * Merge know and new translations
	 */
	$translations = array_merge( $translations, $translations_new );

	/**
	 * Replace excluded HTML part by tab
	 */
	$excluded_elements = array();
	$html              = wplng_html_set_exclude_tag( $html, $excluded_elements );

	/**
	 * Translate links
	 */
	$html = wplng_html_translate_links( $html, $language_target_id );

	/**
	 * Replace original texts by translations
	 */
	foreach ( $translations as $translation ) {

		// Check if translaton data is valid
		if (
			! isset( $translation['source'] ) // Original text
			|| ! isset( $translation['translation'] ) // Translater text
			|| ! isset( $translation['sr'] ) // Search Replace
		) {
			continue;
		}

		if ( ! empty( $translation['source'] ) ) {

			foreach ( $translation['sr'] as $key => $sr ) {
				$regex = str_replace(
					'WPLNG',
					preg_quote( $translation['source'] ),
					$sr['search']
				);

				$replace = str_replace(
					'WPLNG',
					str_replace( '$', '&#36;', $translation['translation'] ),
					$sr['replace']
				);

				// Replace original text in HTML by translation
				$html = preg_replace( $regex, $replace, $html );
			}
		}
	}

	/**
	 * Replace tag by saved excluded HTML part
	 */
	$html = wplng_html_replace_exclude_tag( $html, $excluded_elements );

	$html = apply_filters( 'wplng_html_translated', $html );

	return $html;
}
