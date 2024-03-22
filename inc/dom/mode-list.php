<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Modify dom for the list mode
 *
 * @param array $translations
 * @return object $dom
 */
function wplng_dom_mode_list( $dom, $args ) {

	wplng_args_setup( $args );

	if ( 'list' !== $args['mode']
		|| 'disabled' !== $args['load']
		|| empty( $args['translations'] )
	) {
		return $dom;
	}

	/**
	 * Add body class : wplingua-list
	 */

	foreach ( $dom->find( 'body[class]' ) as $element ) {
		$element->class = $element->class . ' wplingua-list';
	}

	/**
	 * Add list assets
	 */

	$asset_url = add_query_arg(
		'ver',
		WPLNG_PLUGIN_VERSION,
		plugins_url() . '/wplingua/assets/css/list.css'
	);

	$asset  = '<link ';
	$asset .= 'rel="stylesheet" ';
	$asset .= 'id="wplingua-list-css" ';
	$asset .= 'href="' . esc_url( $asset_url ) . '" ';
	$asset .= 'type="text/css"/>';

	foreach ( $dom->find( 'head' ) as $element ) {
		$element->innertext = $element->innertext . $asset;
	}

	/**
	 * Switcher
	 */

	$language_website    = wplng_get_language_by_id( $args['language_source'] );
	$language_current_id = $args['language_target'];
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

	// Create the switcher HTML

	$html_switcher  = '<div class="' . esc_attr( 'wplng-switcher ' . $class ) . '">';
	$html_switcher .= '<div class="switcher-content">';

	$html_switcher .= '<div class="wplng-languages">';

	// Create link for each target languages
	foreach ( $languages_target as $language_target ) {

		$url = wplng_get_url_current_for_language( $language_target['id'] );

		if ( $language_target['id'] === $language_current_id ) {
			continue;
		}

		$html_switcher .= '<a ';
		$html_switcher .= 'class="wplng-language" ';
		$html_switcher .= 'href="' . esc_url( $url ) . '">';

		if ( ! empty( $language_target['flag'] ) ) {
			$html_switcher .= '<img ';
			$html_switcher .= 'src="' . esc_url( $language_target['flag'] ) . '" ';
			$html_switcher .= 'alt="' . esc_attr( $language_target['name'] ) . '">';
		}

		$html_switcher .= '<span class="language-name">';
		$html_switcher .= esc_html( $language_target['name'] );
		$html_switcher .= '</span>';

		$html_switcher .= '</a>';
	}

	$html_switcher .= '</div>'; // End .wplng-languages

	// Create link for current language
	if ( $language_website['id'] !== $language_current_id ) {

		foreach ( $languages_target as $language_target ) {

			if ( $language_target['id'] !== $language_current_id ) {
				continue;
			}

			$url = wplng_get_url_current_for_language( $language_target['id'] );

			$html_switcher .= '<a ';
			$html_switcher .= 'class="wplng-language wplng-language-current" ';
			$html_switcher .= 'href="' . esc_url( $url ) . '" ';
			$html_switcher .= 'onclick="event.preventDefault();">';

			if ( ! empty( $language_target['flag'] ) ) {
				$html_switcher .= '<img ';
				$html_switcher .= 'src="' . esc_url( $language_target['flag'] ) . '" ';
				$html_switcher .= 'alt="' . esc_attr( $language_target['name'] ) . '">';
			}

			$html_switcher .= '<span class="language-name">';
			$html_switcher .= esc_html( $language_target['name'] );
			$html_switcher .= '</span>';

			$html_switcher .= '</a>';

			break;
		}
	}

	$html_switcher .= '</div>'; // End .switcher-content
	$html_switcher .= '</div>'; // End .wplng-switcher

	/**
	 * Return button
	 */

	$return_button  = '<a ';
	$return_button .= 'href="' . esc_url( $args['url_current'] ) . '" ';
	$return_button .= 'title="' . esc_attr__( 'Return on page', 'wplingua' ) . '" ';
	$return_button .= 'class="wplng-button-icon wplng-button-return">';
	$return_button .= '<span class="dashicons dashicons-no"></span>';
	$return_button .= '</a>';

	/**
	 * Modal
	 */

	$html  = '';
	$html .= '<div id="wplng-modal-container">';
	$html .= '<div id="wplng-modal">';

	$html .= '<div id="wplng-modal-header">';
	$html .= '<span id="wplng-modal-title">';
	$html .= '<span class="dashicons dashicons-translation wplng-modal-header-icon"></span> ';
	$html .= esc_html__( 'All translations on page', 'wplingua' );
	$html .= '</span>';

	$html .= '<div id="wplng-modal-list-switcher">';
	$html .= $html_switcher;
	$html .= '</div>';

	$html .= $return_button;

	$html .= '</div>';

	$html .= '<div id="wplng-modal-items">';

	foreach ( $args['translations'] as $translation ) {

		if ( empty( $translation['post_id'] )
			|| empty( $translation['source'] )
			|| empty( $translation['translation'] )
		) {
			continue;
		}

		$edit_link = get_edit_post_link( $translation['post_id'] );

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

	// return $html;

	foreach ( $dom->find( 'body' ) as $body ) {
		$body->innertext = $body->innertext . $html;
	}

	return $dom;
}
