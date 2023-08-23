<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_ob_callback_editor( $html ) {

	if ( empty( $html ) ) {
		return $html;
	}

	$html = apply_filters( 'wplng_html_intercepted', $html );

	/**
	 * Remove tabulation in $html
	 */
	$html = preg_replace( '#\t#', '', $html );

	/**
	 * Replace excluded HTML part by tag
	 */
	$excluded_elements = array();
	$html              = wplng_html_set_exclude_tag(
		$html,
		$excluded_elements
	);

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
	$translations_new = wplng_save_translations( $translations_new, $language_target_id );

	/**
	 * Merge know and new translations
	 */
	$translations = array_merge( $translations_new, $translations );

	/**
	 * Get <head>
	 */
	preg_match( '#<head>(.*)</head>#Uis', $html, $html_head );
	if ( empty( $html_head[0] ) ) {
		return $html;
	}
	$html_head = $html_head[0];

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
					str_replace( '$', '&#36;', esc_attr( $translation['translation'] ) ),
					$sr['replace']
				);

			}
		}
	}

	/**
	 * Get <body>
	 */
	preg_match( '#<body .*>(.*)</body>#Uis', $html, $html_body );
	if ( empty( $html_body[0] ) ) {
		return $html;
	}
	$html_body = $html_body[0];

	/**
	 * Transform links
	 */
	$html_body = preg_replace(
		'#<a (.*)<\/a>#Uis',
		'<span wplingua-editor-link $1</span>',
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
					&& str_contains( $sr['replace'], '<' )
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
						'<a href="' . esc_url( $edit_link ) . '" class="wplng-edit-link" target="_blank">' . str_replace( '$', '&#36;', esc_html( $translation['translation'] ) ) . ' </a>',
						$sr['replace']
					);
				} else {
					$replace = str_replace(
						'WPLNG',
						str_replace( '$', '&#36;', esc_html( $translation['translation'] ) ),
						$sr['replace']
					);
				}

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
	$html = wplng_html_replace_exclude_tag( $html, $excluded_elements );

	$html = apply_filters( 'wplng_html_editor', $html );

	return $html;
}
