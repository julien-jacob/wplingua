<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * wpLingua OB Callback function : On page translations list
 *
 * @param string $html
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

	/**
	 * Return button
	 */
	$url           = wplng_get_url_current();
	$url_original  = $url;
	$url_original  = remove_query_arg( 'wplingua-editor', $url_original );
	$url_original  = remove_query_arg( 'wplingua-list', $url_original );
	$return_button = '';
	if ( ! empty( $url_original ) ) {
		$return_button .= '<a href="' . esc_url( $url_original ) . '" title="' . esc_attr__( 'Return on page', 'wplingua' ) . '" class="wplng-button-icon wplng-button-return"><span class="dashicons dashicons-no"></span></a>';
	}

	/**
	 * Modal
	 */
	$html  = '';
	$html .= '<div id="wplng-modal-container">';
	$html .= '<div id="wplng-modal">';

	$html .= '<div id="wplng-modal-header">';
	$html .= '<span class="dashicons dashicons-translation wplng-modal-header-icon"></span> ';
	$html .= '<span id="wplng-modal-title">';
	$html .= esc_html__( 'All translations on page', 'wplingua' );
	$html .= '</span>';

	$html .= '<div id="wplng-modal-list-switcher">';
	$html .= wplng_get_modal_switcher_html();
	$html .= '</div>';

	$html .= $return_button;

	$html .= '</div>';

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
		$html .= 'title="' . esc_attr__( 'Edit this translation', 'wplingua' ) . '" ';
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


/**
 * Print HTML of switcher for translations list modal
 *
 * @return string
 */
function wplng_get_modal_switcher_html() {

	if ( ! wplng_url_is_translatable() && ! is_admin() ) {
		return '';
	}

	$language_website    = wplng_get_language_website();
	$language_current_id = wplng_get_language_current_id();
	$languages_target    = wplng_get_languages_target();

	if ( empty( $languages_target ) ) {
		return '';
	}

	$class = wplng_get_switcher_class(
		array(
			'theme' => 'grey-simple-smooth',
			'style' => 'dropdown',
			'flags' => 'rectangular',
			'title' => 'name',
		)
	);

	/**
	 * Create the switcher HTML
	 */

	$html  = '<div class="' . esc_attr( 'wplng-switcher ' . $class ) . '">';
	$html .= '<div class="switcher-content">';

	$html .= '<div class="wplng-languages">';

	// Create link for each target languages
	foreach ( $languages_target as $language_target ) {

		$url = 'javascript:void(0);';
		if ( $language_target['id'] === $language_current_id ) {
			continue;
		} elseif ( ! is_admin() && 0 <= strpos( $url, '/?et_fb=1' ) ) {
			$url = wplng_get_url_current_for_language( $language_target['id'] );
		}

		$html .= '<a class="wplng-language' . $class . '" href="' . $url . '">';

		if ( ! empty( $language_website['flags'][0]['flag'] ) ) {

			$alt = __( 'Flag for language: ', 'wplingua' ) . $language_target['name'];

			$html .= '<img ';
			$html .= 'src="' . esc_url( $language_target['flags'][0]['flag'] ) . '" ';
			$html .= 'alt="' . esc_attr( $alt ) . '">';
		}

		$html .= '<span class="language-name">' . esc_html( $language_target['name'] ) . '</span>';
		$html .= '</a>';
	}

	$html .= '</div>'; // End .wplng-languages

	// Create link for current language
	if ( $language_website['id'] === $language_current_id ) {

		$html .= '<a class="wplng-language wplng-language-current" href="javascript:void(0);">';
		if ( ! empty( $language_website['flags'][0]['flag'] ) ) {

			$alt = __( 'Flag for language: ', 'wplingua' ) . esc_attr( $language_website['name'] );

			$html .= '<img ';
			$html .= 'src="' . esc_url( $language_website['flags'][0]['flag'] ) . '" ';
			$html .= 'alt="' . esc_attr( $alt ) . '">';
		}
		$html .= '<span class="language-name">' . esc_html( $language_website['name'] ) . '</span>';
		$html .= '</a>';

	} else {

		foreach ( $languages_target as $language_target ) {

			if ( $language_target['id'] !== $language_current_id ) {
				continue;
			}

			$html .= '<a class="wplng-language wplng-language-current" href="javascript:void(0);">';
			if ( ! empty( $language_target['flags'][0]['flag'] ) ) {

				$alt = __( 'Flag for language: ', 'wplingua' ) . $language_target['name'];

				$html .= '<img ';
				$html .= 'src="' . esc_url( $language_target['flags'][0]['flag'] ) . '" ';
				$html .= 'alt="' . esc_attr( $alt ) . '">';
			}
			$html .= '<span class="language-name">' . esc_html( $language_target['name'] ) . '</span>';
			$html .= '</a>';
			break;
		}
	}

	$html .= '</div>'; // End .switcher-content
	$html .= '</div>'; // End .wplng-switcher

	$flags_style = wplng_get_switcher_flags_style();
	if ( 'none' !== $flags_style && 'rectangular' !== $flags_style ) {
		$html = str_replace(
			'/wplingua/assets/images/' . $flags_style . '/',
			'/wplingua/assets/images/rectangular/',
			$html
		);
	}

	return $html;
}
