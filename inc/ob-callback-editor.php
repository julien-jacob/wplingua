<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



function wplng_ob_callback_editor( $html ) {

	$selector_clear = array(
		'style',
		'script',
		'svg',
	);

	$selector_exclude = array(
		'#wpadminbar',
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
			// '#>(\s*)wplng(\s*)<#Uis'
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

	/**
	 * Get new translation from API
	 */
	$translations_new = wplng_parser( $html_clear );
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

			$translations_new[ $key ]['post_id'] = wplng_save_translation(
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
	// return '<pre >' . var_export($translations, true) . '</pre>';

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

	$dom->save();
	$html = (string) str_get_html( $dom );

	/**
	 * Get <head>
	 */
	// TODO : Revoir regex
	preg_match( '#<head>(.*)</head>#Uis', $html, $html_head );
	if ( empty( $html_head[0] ) ) {
		return $html;
	}
	$html_head = $html_head[0];

	// TODO : Changer de place ?
	$translations_sidebar = array();

	/**
	 * Manage translation for <head>
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
			// $html = preg_replace( $regex, $replace, $html );

			if ( preg_match( $regex, $html_head ) ) {
				$html_head              = preg_replace( $regex, $replace, $html_head );
				$translations_sidebar[] = $translation;
			}
		}
	}

	// return var_export( $translations_sidebar, true );

	/**
	 * Get <body>
	 */
	// TODO : Revoir regex
	preg_match( '#<body .*>(.*)</body>#Uis', $html, $html_body );
	if ( empty( $html_body[0] ) ) {
		return $html;
	}
	$html_body = $html_body[0];

	// TODO : Remplacer les liens dans $html_body
	$html_body = preg_replace(
		'#<a (.*)<\/a>#Uis',
		'<span $1</span>',
		$html_body
	);

	/**
	 * Manage translation for <body>
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

			$edit_link = '';
			if ( ! empty( $translation['post_id'] ) ) {
				$edit_link = get_edit_post_link( $translation['post_id'] );
			}

			$replace = str_replace(
				'WPLNG',
				'<a href="' . esc_url( $edit_link ) . '" target="_blank">[' . $translation['translation'] . ' <span class="dashicons dashicons-translation"></span>] </a>',
				$translation['replace']
			);

			// Replace original text in HTML by translation
			// $html = preg_replace( $regex, $replace, $html );

			if ( preg_match( $regex, $html_body ) ) {

				$html_body = preg_replace( $regex, $replace, $html_body );

			}
		}
	}

	$html = preg_replace(
		'#<body .*>.*</body>#Uis',
		$html_body,
		$html
	);

	$html = preg_replace(
		'#<head>.*</head>#Uis',
		$html_head,
		$html
	);

	/**
	 * Replace tag by saved excluded HTML part
	 */
	foreach ( $excluded_elements as $key => $element ) {
		$s    = '<div wplng-tag-exclude="' . esc_attr( $key ) . '"></div>';
		$html = str_replace( $s, $element, $html );
	}

	$html = apply_filters( 'wplng_html_editor', $html );

	return $html;
}
