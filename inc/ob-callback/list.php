<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_ob_callback_list( $html ) {

	if ( empty( $html ) ) {
		return $html;
	}

	$translations_modal = array();
	$excluded_elements  = array();

	$html = apply_filters( 'wplng_html_intercepted', $html );

	$html_saved = $html;

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

				// Replace original text in HTML by translation
				if ( preg_match( $regex, $html_head ) ) {
					// $html_head = preg_replace( $regex, $replace, $html_head );
					$translations_modal[] = $translation;
					// $translations_modal[] = array_merge( $translation, array( 'is_view' => false ) );
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

				if ( preg_match( $regex, $html_body ) ) {

					if ( ! $replace_by_link && ! in_array( $translation, $translations_modal ) ) {
						$translations_modal[] = $translation;
					}

					if ( ! in_array( $translation, $translations_modal ) ) {
						$translations_modal[] = $translation;
					}
				}
			}
		}
	}

	$html_saved = str_replace(
		'</body>',
		wplng_get_editor_modal_html( $translations_modal ) . '</body>',
		$html_saved
	);

	$html_saved = apply_filters( 'wplng_html_list', $html_saved );

	return $html_saved;
}


function wplng_get_editor_modal_html( $translations ) {

	if ( empty( $translations ) ) {
		return '';
	}

	$html = '<div id="wplng-modal-container">';
	$html .= '<div id="wplng-modal">';
	// $html .= '<div id="wplng-modal-header"></div>';
	$html .= '<div id="wplng-modal-items">';

	foreach ( $translations as $key => $translation ) {

		$edit_link = '';
		if ( empty( $translation['post_id'] ) ) {
			// TODO : Check source et translations
			continue;
		} else {
			$edit_link = get_edit_post_link( $translation['post_id'] );
		}

		$html .= '<div class="wplng-modal-item">';
		$html .= '<div class="wplng-item-text">';
		$html .= '<div class="wplng-item-source">';
		$html .= $translation['source'];
		$html .= '</div>'; // End .wplng-item-source
		$html .= '<div class="wplng-item-translation">';
		$html .= $translation['translation'];
		$html .= '</div>'; // End .wplng-item-translation
		$html .= '</div>'; // End .wplng-item-text
		$html     .= '<div class="wplng-item-edit">';
		$html .= '<a href="' . esc_url( $edit_link ) . '" ';
		$html .= 'title="' . __( 'Edit', 'wplingua' ) . '" ';
		$html .= 'class="wplng-button-icon" target="_blank">';
		$html .= '<span class="dashicons dashicons-edit"></span></a>';
		$html .= '</a>';
		$html .= '</div>'; // End .wplng-item-edit
		$html .= '</div>'; // ENd .wplng-modal-item
	}

	$html .= '</div>'; // End #wplng-modal-items
	$html .= '</div>'; // End #wplng-modal
	$html .= '</div>'; // End #wplng-modal-container

	return $html;
}
