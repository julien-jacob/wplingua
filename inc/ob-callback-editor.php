<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



function wplng_ob_callback_editor( $html ) {

	$html = apply_filters( 'wplng_html_intercepted', $html );

	/**
	 * Get saved translation
	 */
	$language_target_id = wplng_get_language_current_id();
	$translations       = wplng_get_translations_saved( $language_target_id );
	// return '<pre >' . var_export( $translations, true ) . '</pre>';

	/**
	 * Get new translation from API
	 */
	$start_time       = microtime( true );
	$translations_new = wplng_parser( $html, $translations );

	// Calculate script execution time
	$end_time       = microtime( true );
	$execution_time = ( $end_time - $start_time );
	// return var_export( $translations_new, true ) . ' Execution time of script = ' . $execution_time . ' sec';

	/**
	 * Save new translation as wplng_translation CPT
	 */
	wplng_save_translations( $translations_new, $language_target_id );

	/**
	 * Merge know and new translations
	 */
	$translations = array_merge( $translations, $translations_new );
	// return '<pre >' . var_export($translations, true) . '</pre>';

	/**
	 * Replace excluded HTML part by tab
	 */
	$excluded_elements = array();
	$html              = wplng_html_set_exclude_tag( $html, $excluded_elements );
	// return '<pre >' . var_export( $excluded_elements, true ) . '</pre>';

	/**
	 * Translate links
	 */
	// TODO : Faire la ligne suivante ?
	// $html = wplng_html_translate_links( $html, $language_target_id );

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
				// $html = preg_replace( $regex, $replace, $html_head );

				if ( preg_match( $regex, $html_head ) ) {
					$html_head              = preg_replace( $regex, $replace, $html_head );
					$translations_sidebar[] = $translation;
				}
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

				$replace_by_link = false;
				if (
					str_contains( $sr['replace'], '>' )
					|| str_contains( $sr['replace'], '<' )
				) {
					$replace_by_link = true;
				}

				if ( $replace_by_link ) {
					$edit_link = '';
					if ( ! empty( $translation['post_id'] ) ) {
						$edit_link = get_edit_post_link( $translation['post_id'] );
					}

					$replace = str_replace(
						'WPLNG',
						'<a href="' . esc_url( $edit_link ) . '" target="_blank">[' . str_replace( '$', '&#36;', $translation['translation'] ) . ' <span class="dashicons dashicons-translation"></span>] </a>',
						$sr['replace']
					);
				} else {
					$replace = str_replace(
						'WPLNG',
						str_replace( '$', '&#36;', $translation['translation'] ),
						$sr['replace']
					);
				}

				if ( preg_match( $regex, $html_body ) ) {

					$html_body = preg_replace( $regex, $replace, $html_body );

					if ( ! $replace_by_link ) {
						$translations_sidebar[] = $translation;
					}
				}
			}
		}
	}

	// return '<pre >' . var_export($translations_sidebar, true) . '</pre>';

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
	$html = wplng_html_replace_exclude_tag( $html, $excluded_elements );

	$html = apply_filters( 'wplng_html_editor', $html );

	return $html;
}
