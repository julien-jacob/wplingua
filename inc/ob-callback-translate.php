<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



function wplng_ob_callback_translate( $html ) {

	$selector_clear = array(
		'style',
		'script',
		'svg',
	);

	$selector_exclude = array(
		'#wpadminbar',
		'.wplng-switcher',
	);

	/**
	 * Clear HTML for API call
	 */

	// Remove comments from HTML Clear
	$html_clear = preg_replace( '#<!--.*-->#Uis', '', $html );

	// Remove useless and excluded elements from HTML clear
	$dom = str_get_html( $html_clear );

	if ( $dom === false ) {
		return $html;
	}

	$selector_to_remove = array_merge( $selector_exclude, $selector_clear );

	foreach ( $selector_to_remove as $key => $selector ) {
		foreach ( $dom->find( $selector ) as $element ) {
			$element->outertext = '';
		}
	}

	$dom->save();
	$html_clear = (string) str_get_html( $dom );

	// Clear HTML from multiple space and tab
	$html_clear = preg_replace('#\s+#', ' ', $html_clear);

	// Clear HTML from useless attributes
	$html_clear = preg_replace('# (src|srcset|rel|class|href)=(\"|\').*(\"|\')#Uis', '', $html_clear);

	// return $html_clear;

	/**
	 * Get saved translation
	 */
	$wplng_language_target = wplng_get_language_current_id();
	$translations          = wplng_get_translations_saved( $wplng_language_target );
	// return '<pre >' . var_export($translations, true) . '</pre>';

	/**
	 * Remove saved translation from HTML clear
	 */
	foreach ( $translations as $translation ) {

		// Check if translaton data is valid
		if (
			! isset( $translation['source'] ) // Original text
			|| ! isset( $translation['translation'] ) // Translater text
			|| ! isset( $translation['search'] ) // Search
			|| ! isset( $translation['replace'] ) // Replace
		) {
			continue;
		}

		// TODO : Mettre preg_quote() plutôt sur $regex ?
		$regex = str_replace(
			'WPLNG',
			preg_quote( $translation['source'] ),
			// '#>(\s*?)WPLNG(\s*?)<#Uis'
			$translation['search']
		);

		$replace = str_replace(
			'WPLNG',
			'',
			$translation['replace']
		);

		// Replace knowing translation by empty string
		$html_clear = preg_replace(
			$regex,
			$replace,
			$html_clear
		);
	}
	// return $html_clear;

	
	// return strlen($html_clear) . ' -- ' . strlen($html);
	/**
	 * Get new translation from API
	 */
	$start_time = microtime(true);
	$translations_new = wplng_parser( $html_clear );
	// End clock time in seconds
	$end_time = microtime(true);
	
	// Calculate script execution time
	$execution_time = ($end_time - $start_time);

	// return $html_clear;
	
	// return var_export($translations_new, true) . " Execution time of script = ".$execution_time." sec";
	// $translations_new = array();
	// return '<pre >' . var_export($translations_new, true) . '</pre>';

	/**
	 * Save new translation as wplng_translation CPT
	 */
	if ( ! empty( $translations_new ) ) {

		foreach ( $translations_new as $key => $translation ) {

			if (
				! isset( $translation['source'] ) // Original text
				|| ! isset( $translation['translation'] ) // Translater text
				|| ! isset( $translation['search'] ) // Search
				|| ! isset( $translation['replace'] ) // Replace
			) {
				continue;
			}

			wplng_save_translation(
				$wplng_language_target,
				$translation['source'],
				$translation['translation'],
				$translation['search'],
				$translation['replace']
			);
		}
	}

	/**
	 * Merge know and new translations
	 */
	$translations = array_merge( $translations, $translations_new );

	/**
	 * Replace excluded HTML part by tab
	 */
	$excluded_elements = array();
	$dom               = str_get_html( $html );

	if ( $dom === false ) {
		return $html;
	}

	foreach ( $selector_exclude as $key => $selector ) {
		foreach ( $dom->find( $selector ) as $element ) {
			$excluded_elements[] = $element->outertext;
			$attr                = count( $excluded_elements ) - 1;
			$element->outertext  = '<div wplng-tag-exclude="' . esc_attr( $attr ) . '"></div>';
		}
	}

	$dom->load( $dom->save() );
	// $x = array();

	/**
	 * Translate links
	 */
	foreach ( $dom->find( 'a' ) as $element ) {
		$link          = $element->href;
		$element->href = wplng_url_translate( $link, $wplng_language_target );
		// $x[] = wplng_url_translate( $link, wplng_get_language_current_id(), $wplng_language_target );
	}

	// return '<pre>' . var_export( $x, true ) . '</pre>';

	$dom->save();
	$html = (string) str_get_html( $dom );

	/**
	 * Replace original texts by translations
	 */
	foreach ( $translations as $translation ) {

		// Check if translaton data is valid
		if (
			! isset( $translation['source'] ) // Original text
			|| ! isset( $translation['translation'] ) // Translater text
			|| ! isset( $translation['search'] ) // Search
			|| ! isset( $translation['replace'] ) // Replace
		) {
			continue;
		}

		if ( ! empty( $translation['source'] ) ) {

			$regex = str_replace(
				'WPLNG',
				preg_quote( $translation['source'] ),
				$translation['search']
			);

			$replace = str_replace(
				'WPLNG',
				$translation['translation'],
				$translation['replace']
			);

			// Replace original text in HTML by translation
			$html = preg_replace( $regex, $replace, $html );
		}
	}

	/**
	 * Replace tag by saved excluded HTML part
	 */
	foreach ( $excluded_elements as $key => $element ) {
		$s    = '<div wplng-tag-exclude="' . esc_attr( $key ) . '"></div>';
		$html = str_replace( $s, $element, $html );
	}

	$html = apply_filters( 'wplng_html_translated', $html );

	return $html;
}