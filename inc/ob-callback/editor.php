<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_ob_callback_editor( $html ) {

	// $translations_sidebar = array();
	$excluded_elements = array();

	$html = apply_filters( 'wplng_html_intercepted', $html );

	/**
	 * Replace excluded HTML part by tag
	 */
	$html = wplng_html_set_exclude_tag( $html, $excluded_elements );

	/**
	 * Get saved translation
	 */
	$language_target_id = wplng_get_language_current_id();
	$translations       = wplng_get_translations_saved( $language_target_id );

	/**
	 * Get new translation from API
	 */
	$translations_new = wplng_parser( $html, '', '', $translations );

	/**
	 * Save new translation as wplng_translation CPT
	 */
	wplng_save_translations( $translations_new, $language_target_id );

	/**
	 * Merge know and new translations
	 */
	$translations = array_merge( $translations, $translations_new );

	/**
	 * Get <head>
	 */
	// TODO : Revoir regex
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
					str_replace( '$', '&#36;', $translation['translation'] ),
					$sr['replace']
				);

				// Replace original text in HTML by translation
				if ( preg_match( $regex, $html_head ) ) {
					$html_head = preg_replace( $regex, $replace, $html_head );
					// $translations_sidebar[] = $translation;
				}
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
						'<a href="' . esc_url( $edit_link ) . '" class="wplng-edit-link" target="_blank">' . str_replace( '$', '&#36;', $translation['translation'] ) . ' </a>',
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

					// if ( ! $replace_by_link && ! in_array( $translation, $translations_sidebar ) ) {
					// 	$translations_sidebar[] = $translation;
					// }
				}
			}
		}
	}

	$html_popup = '<div id="wplng-popup-container">';

	$html_popup .= '<div id="wplng-popup">';

	$html_popup .= '<div id="wplng-popup-header">';
	$html_popup .= '<h2>Hello Header</h2>';

	$html_popup .= '</div>';

	$html_popup .= '<div id="wplng-popup-items">';

	foreach ( $translations as $key => $translation ) {

		$html_popup .= '<div class="wplng-popup-item">';
		$html_popup .= __( 'Original:', 'wplingua' ) . $translation['source'];
		$html_popup .= '<br>';
		$html_popup .= __( 'Translation:', 'wplingua' ) . $translation['translation'];
		$html_popup .= '<br>';

		$edit_link = '';
		if ( ! empty( $translation['post_id'] ) ) {
			$edit_link = get_edit_post_link( $translation['post_id'] );
		}

		$html_popup .= '<a href="' . esc_url( $edit_link ) . '" class="wplng-edit-link" target="_blank">Edit </a>';

		$html_popup .= '</div>';
	}

	$html_popup .= '</div>';
	$html_popup .= '</div>';
	$html_popup .= '</div>';
	// $html_popup .= '</div>';

	$html_body = str_replace(
		'</body>',
		$html_popup . '</body>',
		$html_body
	);

	// $html_body = str_replace(
	// 	'</body>',
	// 	'<pre>' . var_export($translations_sidebar, true) . '</pre></body>',
	// 	$html_body
	// );

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
