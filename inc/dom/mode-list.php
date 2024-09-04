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

	$asset  = '<link';
	$asset .= ' rel="stylesheet"';
	$asset .= ' id="wplingua-list-css"';
	$asset .= ' href="' . esc_url( $asset_url ) . '"';
	$asset .= ' type="text/css"';
	$asset .= '/>';

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

	$html_switcher  = '<div id="wplng-modal-list-switcher">';
	$html_switcher .= '<div class="' . esc_attr( 'wplng-switcher ' . $class ) . '">';
	$html_switcher .= '<div class="switcher-content">';

	$html_switcher .= '<div class="wplng-languages">';

	// Create link for each target languages
	foreach ( $languages_target as $language_target ) {

		$url = wplng_get_url_current_for_language( $language_target['id'] );

		if ( $language_target['id'] === $language_current_id ) {
			continue;
		}

		$html_switcher .= '<a';
		$html_switcher .= ' class="wplng-language"';
		$html_switcher .= ' href="' . esc_url( $url ) . '"';
		$html_switcher .= '>';

		if ( ! empty( $language_target['flag'] ) ) {
			$html_switcher .= '<img';
			$html_switcher .= ' src="' . esc_url( $language_target['flag'] ) . '"';
			$html_switcher .= ' alt="' . esc_attr( $language_target['name'] ) . '"';
			$html_switcher .= '>';
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

			$html_switcher .= '<a';
			$html_switcher .= ' class="wplng-language wplng-language-current"';
			$html_switcher .= ' href="' . esc_url( $url ) . '"';
			$html_switcher .= ' onclick="event.preventDefault();"';
			$html_switcher .= '>';

			if ( ! empty( $language_target['flag'] ) ) {
				$html_switcher .= '<img';
				$html_switcher .= ' src="' . esc_url( $language_target['flag'] ) . '"';
				$html_switcher .= ' alt="' . esc_attr( $language_target['name'] ) . '"';
				$html_switcher .= '>';
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
	$html_switcher .= '</div>'; // End #wplng-modal-list-switcher

	/**
	 * Return button
	 */

	$return_button  = '<a';
	$return_button .= ' href="' . esc_url( $args['url_current'] ) . '"';
	$return_button .= ' title="' . esc_attr__( 'Return on page', 'wplingua' ) . '"';
	$return_button .= ' class="wplng-button-icon wplng-button-return"';
	$return_button .= '>';
	$return_button .= '<span class="dashicons dashicons-no"></span>';
	$return_button .= '</a>';

	/**
	 * Filter : Search
	 */

	$filter_search  = '<div class="wplng-filter">';
	$filter_search .= '<label for="wplng-filter-search">';
	$filter_search .= '<span class="dashicons dashicons-search"></span> ';
	$filter_search .= esc_html__( 'Search', 'wplingua' );
	$filter_search .= '</label>';
	$filter_search .= '<input type="text" id="wplng-filter-search">';
	$filter_search .= '</div>'; // End .wplng-filter

	/**
	 * Filter : Status
	 */

	$filter_status  = '<div class="wplng-filter">';
	$filter_status .= '<label for="wplng-filter-status">';
	$filter_status .= '<span class="dashicons dashicons-yes"></span> ';
	$filter_status .= esc_html__( 'Status', 'wplingua' );
	$filter_status .= '</label>';
	$filter_status .= '<div class="wplng-filter-select">';
	$filter_status .= '<select id="wplng-filter-status">';

	$filter_status .= '<option value="all">';
	$filter_status .= esc_html__( 'All', 'wplingua' );
	$filter_status .= '</option>';

	$filter_status .= '<option value="reviewed">';
	$filter_status .= esc_html__( 'Reviewed', 'wplingua' );
	$filter_status .= '</option>';

	$filter_status .= '<option value="unreviewed">';
	$filter_status .= esc_html__( 'Unreviewed', 'wplingua' );
	$filter_status .= '</option>';

	$filter_status .= '</select>';
	$filter_status .= '</div>'; // End .wplng-filter-select
	$filter_status .= '</div>'; // End .wplng-filter

	/**
	 * Filter : Order
	 */

	$filter_order  = '<div class="wplng-filter">';
	$filter_order .= '<label for="wplng-filter-order">';
	$filter_order .= '<span class="dashicons dashicons-randomize"></span> ';
	$filter_order .= esc_html__( 'Order', 'wplingua' );
	$filter_order .= '</label>';
	$filter_order .= '<div class="wplng-filter-select">';
	$filter_order .= '<select id="wplng-filter-order">';

	$filter_order .= '<option value="occurrence">';
	$filter_order .= esc_html__( 'Occurrence order', 'wplingua' );
	$filter_order .= '</option>';

	$filter_order .= '<option value="alphabetical-sources">';
	$filter_order .= esc_html__( 'Alphabetical - sources', 'wplingua' );
	$filter_order .= '</option>';

	$filter_order .= '<option value="alphabetical-translations">';
	$filter_order .= esc_html__( 'Alphabetical - translations', 'wplingua' );
	$filter_order .= '</option>';

	$filter_order .= '</select>';
	$filter_order .= '</div>'; // End .wplng-filter-select
	$filter_order .= '</div>'; // End .wplng-filter

	/**
	 * Modal
	 */

	$html  = '<div id="wplng-modal-container">';
	$html .= '<div id="wplng-modal">';

	/**
	 * Modal header
	 */

	$html .= '<div id="wplng-modal-header">';

	$html .= '<div id="wplng-modal-header-main">';

	$html .= '<div id="wplng-modal-header-title">';
	$html .= '<span class="dashicons dashicons-translation wplng-modal-header-icon"></span> ';
	$html .= '<span id="wplng-modal-title-text">';
	$html .= esc_html__( 'All translations on page', 'wplingua' );
	$html .= '</span>'; // End #wplng-modal-title-text
	$html .= '</div>'; // End #wplng-modal-title

	$html .= '<div id="wplng-modal-header-control">';
	$html .= $html_switcher;
	$html .= $return_button;
	$html .= '</div>'; // End #wplng-modal-header-control

	$html .= '</div>'; // End #wplng-modal-header-main

	$html .= '<div id="wplng-modal-filter">';
	$html .= $filter_search;
	$html .= $filter_status;
	$html .= $filter_order;
	$html .= '</div>'; // End #wplng-modal-filter

	$html .= '</div>'; // End #wplng-modal-header

	/**
	 * Modal items
	 */

	$html .= '<div id="wplng-modal-items">';

	foreach ( $args['translations'] as $key => $translation ) {

		if ( empty( $translation['post_id'] )
			|| empty( $translation['source'] )
			|| empty( $translation['translation'] )
		) {
			continue;
		}

		$class = 'wplng-modal-item';

		if ( ! empty( $translation['review'] ) ) {
			$class .= ' wplng-is-review';
		}

		$html .= '<div class="' . esc_attr( $class ) . '"';
		$html .= ' data-wplng-post="' . esc_attr( $translation['post_id'] ) . '"';
		$html .= ' data-wplng-order="' . esc_attr( $key ) . '"';
		$html .= '>';

		$html .= '<div class="wplng-item-text">';
		$html .= '<div class="wplng-item-source">';
		$html .= esc_html( $translation['source'] );
		$html .= '</div>'; // End .wplng-item-source
		$html .= '<div class="wplng-item-translation">';
		$html .= esc_html( $translation['translation'] );
		$html .= '</div>'; // End .wplng-item-translation
		$html .= '</div>'; // End .wplng-item-text
		$html .= '<div class="wplng-item-edit">';

		$html .= '<a';
		$html .= ' title="' . esc_attr__( 'Edit this translation', 'wplingua' ) . '"';
		$html .= ' data-wplng-post="' . esc_attr( $translation['post_id'] ) . '"';
		$html .= ' class="wplng-button-icon wplng-edit-link"';
		$html .= '>';
		$html .= '<span class="dashicons dashicons-edit"></span></a>';
		$html .= '</a>';

		$html .= '</div>'; // End .wplng-item-edit
		$html .= '</div>'; // ENd .wplng-modal-item
	}

	$html .= '</div>'; // End #wplng-modal-items
	$html .= '</div>'; // End #wplng-modal

	$html .= '<button';
	$html .= ' id="wplng-scroll-to-top"';
	$html .= ' title="' . esc_attr__( 'Go to top', 'wplingua' ) . '"';
	$html .= ' style="display: none;"';
	$html .= '>';
	$html .= '<span class="dashicons dashicons-arrow-up-alt2"></span>';
	$html .= '</button>';

	$html .= '</div>'; // End #wplng-modal-container

	/**
	 * Place the translation edit modale
	 */

	$html .= wplng_translation_edit_modal_get_html();

	foreach ( $dom->find( 'body' ) as $body ) {
		$body->innertext = $body->innertext . $html;
	}

	return $dom;
}
