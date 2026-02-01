<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Modify dom for the "in progress" load mode
 *
 * @param object $dom
 * @param array  $args
 * @return object
 */
function wplng_dom_load_progress( $dom, $args ) {

	wplng_args_setup( $args );

	if ( 'progress' !== $args['load']
		|| ! current_user_can( 'edit_posts' )
	) {
		return $dom;
	}

	/**
	 * Add effect on unknow texts and translate know texts
	 */

	$edit_link_excluded = wplng_data_excluded_editor_link();
	$node_text_excluded = wplng_data_excluded_node_text();

	foreach ( $dom->find( 'body text' ) as $element ) {

		if ( in_array( $element->parent->tag, $edit_link_excluded )
			|| in_array( $element->parent->tag, $node_text_excluded )
		) {
			continue;
		}

		$text = $element->innertext;

		if ( empty( trim( $text ) ) ) {
			continue;
		}

		// Manage non breaking space
		$text = str_replace(
			array( '&nbsp;', html_entity_decode( '&nbsp;' ) ),
			array( ' ', ' ' ),
			$text
		);

		/**
		 * Get spaces before and after text
		 */

		$temp          = array();
		$spaces_before = '';
		$spaces_after  = '';

		preg_match( '/^(\s*).*/', $text, $temp );
		if ( ! empty( $temp[1] ) ) {
			$spaces_before = $temp[1];
		}

		preg_match( '/.*(\s*)$/U', $text, $temp );
		if ( ! empty( $temp[1] ) ) {
			$spaces_after = $temp[1];
		}

		$text       = wplng_text_esc( $text );
		$translated = '';

		if ( wplng_text_is_translatable( $text ) ) {

			foreach ( $args['translations'] as $translation ) {
				if ( $text === $translation['source'] ) {
					$translated = $translation['translation'];
					break;
				}
			}

			if ( '' === $translated ) {

				$innertext  = '<span';
				$innertext .= ' class="wplng-in-progress-text"';
				$innertext .= ' title="' . esc_attr__( 'Translation in progress', 'wplingua' ) . '"';
				$innertext .= '>';
				$innertext .= esc_html( $spaces_before . $text . $spaces_after );
				$innertext .= '</span>';

				$element->innertext = $innertext;

			} else {
				$element->innertext = esc_html( $spaces_before . $translated . $spaces_after );
			}
		}
	}

	/**
	 * Create the html of message bar
	 */

	$number_of_texts           = (int) $args['count_texts'];
	$numer_of_translated_texts = count( $args['translations'] );

	// Calculate percentage

	$percentage = 0;
	if ( $number_of_texts > 0 ) {
		$percentage = (int) ( ( $numer_of_translated_texts / $number_of_texts ) * 100 );
	}

	if ( $percentage < 1 ) {
		$percentage = 1;
	}

	// Make the HTML

	$html = '<div id="wplng-in-progress-container">';

	$html .= '<div id="wplng-in-progress-message">';
	$html .= '<span class="dashicons dashicons-update wplng-spin"></span> ';

	$html .= '<span class="wplng-in-progress-text-mobile">';
	$html .= esc_html__( 'Translation in progress', 'wplingua' );
	$html .= '</span>';

	$html .= '<span class="wplng-in-progress-text-desktop">';
	$html .= esc_html__( 'In progress: Translation and saving of new texts', 'wplingua' );
	$html .= '</span>';

	$html .= ' - ';
	$html .= '<span id="wplng-in-progress-percent">';
	$html .= esc_html( $percentage );
	$html .= '</span>';
	$html .= ' %';
	$html .= '</div>'; // End #wplng-translation-in-progress

	$html .= '<div id="wplng-progress-bar">';
	$html .= '<div';
	$html .= ' id="wplng-progress-bar-value"';
	$html .= ' style="width: ' . esc_attr( $percentage ) . '%"';
	$html .= '>';
	$html .= '</div>'; // End #wplng-progress-bar-value
	$html .= '</div>'; // End #wplng-progress-bar

	$html .= '<div id="wplng-in-progress-error" style="display: none;">';
	$html .= '<span class="dashicons dashicons-info-outline"></span> ';

	$html .= '<span class="wplng-in-progress-text-mobile">';
	$html .= esc_html__( 'Error during translation.', 'wplingua' );
	$html .= '</span>';

	$html .= '<span class="wplng-in-progress-text-desktop">';
	$html .= esc_html__( 'Error during translation. Check the browser console for more information.', 'wplingua' );
	$html .= '</span>';

	$html .= '<span';
	$html .= ' id="wplng-in-progress-error-close"';
	$html .= ' class="dashicons dashicons-no-alt"';
	$html .= ' title="' . esc_attr__( 'Close', 'wpLingua' ) . '"';
	$html .= '></span>';

	$html .= '</div>'; // End wplng-in-progress-error

	$html .= '</div>'; // End #wplng-in-progress-containe

	/**
	 * Prepare the texts chunks
	 */

	$texts_unknow_by_chunk = array();
	$current_chunk         = array();
	$current_chars         = 0;
	$max_items_per_chunk   = (int) ( WPLNG_MAX_TRANSLATIONS_STR / 4 );
	$max_chars_per_chunk   = (int) ( WPLNG_MAX_TRANSLATIONS_CHAR / 2 );

	foreach ( $args['texts_unknow'] as $text_unknow ) {

		$text = (string) $text_unknow;
		$len  = function_exists( 'mb_strlen' ) ? mb_strlen( $text, 'UTF-8' ) : strlen( $text );

		// If a single item is longer than allowed, truncate it to fit the limit.
		if ( $len > $max_chars_per_chunk ) {
			$text = function_exists( 'mb_substr' ) ? mb_substr( $text, 0, $max_chars_per_chunk, 'UTF-8' ) : substr( $text, 0, $max_chars_per_chunk );
			$len  = $max_chars_per_chunk;
		}

		// If adding this text would exceed either limit, flush current chunk.
		if ( count( $current_chunk ) >= $max_items_per_chunk || ( $current_chars + $len ) > $max_chars_per_chunk ) {
			if ( ! empty( $current_chunk ) ) {
				$texts_unknow_by_chunk[] = array(
					'percentage' => (int) ( ( $numer_of_translated_texts / $number_of_texts ) * 100 ),
					'texts'      => $current_chunk,
				);
				$current_chunk           = array();
				$current_chars           = 0;
			}
		}

		$current_chunk[] = wplng_encryption_encrypt( $text );
		$current_chars  += $len;
		++$numer_of_translated_texts;
	}

	// Push last chunk if any
	if ( ! empty( $current_chunk ) ) {
		$texts_unknow_by_chunk[] = array(
			'percentage' => 99,
			'texts'      => $current_chunk,
		);
	}

	/**
	 * Make the reload URL
	 */

	$url_reload_query_arg = array(
		'wplng-load' => 'translated',
		'nocache'    => (string) time() . (string) rand( 100, 999 ),
	);

	if ( $args['mode'] !== 'vanilla' ) {
		$url_reload_query_arg['wplng-mode'] = $args['mode'];
	}

	$url_reload = add_query_arg(
		$url_reload_query_arg,
		$args['url_current']
	);

	// Ensure the reload URL is safe for JS: decode entities then use esc_url_raw (programmatic URL)
	$url_reload_for_js = html_entity_decode( $url_reload, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	$url_reload_for_js = esc_url_raw( $url_reload_for_js );

	/**
	 * JS Script - Load in progress - Data
	 */

	$load_in_progress_data_js = array(
		'urlAjax'   => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
		'urlReload' => $url_reload_for_js,
		'language'  => esc_attr( $args['language_target'] ),
		'chunks'    => $texts_unknow_by_chunk,
		'nonce'     => wp_create_nonce( 'wplng_load_in_progress' ),
	);

	$html .= '<script id="wplingua-js-load-in-progress-data">';
	$html .= 'let wplngLoadInProgressData = ' . wp_json_encode( $load_in_progress_data_js ) . ';';
	$html .= '</script>';

	/**
	 * JS Script - Load in progress - File
	 */

	$script_url  = plugins_url() . '/wplingua/assets/js/load-in-progress.js';
	$script_url .= '?ver=' . WPLNG_PLUGIN_VERSION;

	$html .= '<script';
	$html .= ' type="text/javascript"';
	$html .= ' src="' . esc_url( $script_url ) . '"';
	$html .= ' id="wplingua-js-load-in-progress-script"';
	$html .= '>';
	$html .= '</script>';

	/**
	 * Place the HTML in the end of body
	 */

	foreach ( $dom->find( 'body' ) as $body ) {
		$body->innertext = $body->innertext . $html;
	}

	return $dom;
}


/**
 * AJAX handler for "load in progress" translations.
 *
 * Validates the current user capability and incoming POST data, requests
 * translations for the provided texts and returns a JSON response. On error
 * a JSON error response is sent and execution ends.
 *
 * Expected POST parameters:
 *  - wplng_language (string) : target language id
 *  - wplng_texts   (array)  : list of texts to translate
 *
 * Permissions: current user must have 'edit_posts'.
 *
 * @return void Sends JSON success or error and exits.
 */
function wplng_ajax_load_in_progress() {

	/**
	 * Check authorizations
	 */

	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error(
			array(
				'error_load_in_progress' => true,
				'error_message'          => 'Unauthorized user',
			)
		);
		return;
	}

	// Verify nonce sent from client
	if ( empty( $_POST['wplng_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['wplng_nonce'] ), 'wplng_load_in_progress' ) ) {
		wp_send_json_error(
			array(
				'error_load_in_progress' => true,
				'error_message'          => 'Invalid nonce',
			)
		);
		return;
	}

	if ( ! wplng_api_feature_is_allow( 'detection' ) ) {
		wp_send_json_error(
			array(
				'error_load_in_progress' => true,
				'error_message'          => 'Error detecting text',
			)
		);
		return;
	}

	/**
	 * Check target language
	 */

	if ( empty( $_POST['wplng_language'] )
		|| ! wplng_is_valid_language_id( $_POST['wplng_language'] )
	) {
		wp_send_json_error(
			array(
				'error_load_in_progress' => true,
				'error_message'          => 'Invalid language',
			)
		);
		return;
	}

	$language_target  = $_POST['wplng_language'];
	$language_website = wplng_get_api_language_website();

	if ( $language_website !== 'all' && $language_website !== $language_target ) {
		wp_send_json_error(
			array(
				'error_load_in_progress' => true,
				'error_message'          => 'Unauthorized website language',
			)
		);
		return;
	}

	/**
	 * Check texts to translate
	 */

	if ( empty( $_POST['wplng_texts'] )
		|| ! is_array( $_POST['wplng_texts'] )
	) {
		wp_send_json_error(
			array(
				'error_load_in_progress' => true,
				'error_message'          => 'Invalid texts data',
			)
		);
		return;
	}

	$texts_original_encrypted = $_POST['wplng_texts'];
	$texts_original           = array();

	foreach ( $texts_original_encrypted as $text_encrypted ) {
		$text_decrypted = wplng_encryption_decrypt( $text_encrypted );

		if ( $text_decrypted === '' ) {
			wp_send_json_error(
				array(
					'error_load_in_progress' => true,
					'error_message'          => 'Texts decryption failed',
				)
			);
			return;
		}

		$texts_original[] = $text_decrypted;
	}

	/**
	 * Setup $args and get translations
	 */

	$args = array( 'language_target' => $language_target );
	wplng_args_setup( $args );
	wplng_args_update_from_texts( $args, $texts_original );

	/**
	 * Basic translation check
	 */

	if ( empty( $args['translations'] ) ) {
		wp_send_json_error(
			array(
				'error_load_in_progress' => true,
				'error_message'          => 'No translation returned',
			)
		);
		return;
	}

	/**
	 * Setup translation array for AJAX response
	 */

	$translation_response = array();

	foreach ( $args['translations'] as $translation ) {
		if ( ! isset( $translation['source'] ) || ! isset( $translation['translation'] ) ) {
			continue;
		}

		$translation_response[] = array(
			'source'      => $translation['source'],
			'translation' => $translation['translation'],
		);
	}

	wp_send_json_success( $translation_response );
}
