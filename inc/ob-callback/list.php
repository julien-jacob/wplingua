<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * wpLingua OB Callback function : On page translations list
 *
 * @param [type] $html
 * @return void
 */
function wplng_ob_callback_list( $html ) {

	if ( empty( $html ) ) {
		return $html;
	}

	$html       = apply_filters( 'wplng_html_intercepted', $html );
	$html_saved = $html;

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
		$translations = wplng_get_translations_target( $language_target_id );
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
	 * Separate page translations
	 */

	$translations_in_page = array();

	foreach ( $translations as $translation ) {
		foreach ( $texts as $text ) {
			if ( ! empty( $translation['source'] ) 
				&& $translation['source'] === $text
			) {
				$translations_in_page[] = $translation;
			}
		}
	}

	/**
	 * Merge know and new translations
	 */
	
	$translations = array_merge( $translations_in_page, $translations_new );

	/**
	 * Place the modal HTML before body ending
	 */
	
	$html_saved = str_replace(
		'</body>',
		wplng_get_editor_modal_html( $translations ) . '</body>',
		$html_saved
	);

	/**
	 * Apply a filter before return $html
	 */

	$html = apply_filters( 'wplng_html_translated', $html );

	return $html_saved;
}


function wplng_get_editor_modal_html( $translations ) {

	if ( empty( $translations ) ) {
		return '';
	}

	$html  = '<div id="wplng-modal-container">';
	$html .= '<div id="wplng-modal">';
	// $html .= '<div id="wplng-modal-header"></div>';
	$html .= '<div id="wplng-modal-items">';

	foreach ( $translations as $translation ) {

		$edit_link = '';
		if ( empty( $translation['post_id'] )
			|| empty( $translation['source'] )
			|| empty( $translation['translation'] )
		) {
			continue;
		} else {
			$edit_link = get_edit_post_link( $translation['post_id'] );
		}

		$html .= '<div class="wplng-modal-item">';
		$html .= '<div class="wplng-item-text">';
		$html .= '<div class="wplng-item-source">';
		$html .= esc_attr( $translation['source'] );
		$html .= '</div>'; // End .wplng-item-source
		$html .= '<div class="wplng-item-translation">';
		$html .= esc_attr( $translation['translation'] );
		$html .= '</div>'; // End .wplng-item-translation
		$html .= '</div>'; // End .wplng-item-text
		$html .= '<div class="wplng-item-edit">';
		$html .= '<a href="' . esc_url( $edit_link ) . '" ';
		$html .= 'title="' . __( 'Edit', 'wplingua' ) . '" ';
		$html .= 'class="wplng-button-icon" target="_blank">';
		$html .= '<span class="dashicons dashicons-edit" title="';
		$html .= __( 'Edit this translation', 'wplingua' );
		$html .= '"></span></a>';
		$html .= '</a>';
		$html .= '</div>'; // End .wplng-item-edit
		$html .= '</div>'; // ENd .wplng-modal-item
	}

	$html .= '</div>'; // End #wplng-modal-items
	$html .= '</div>'; // End #wplng-modal
	$html .= '</div>'; // End #wplng-modal-container

	return $html;
}
