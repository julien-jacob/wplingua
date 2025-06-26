<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Redirect page if is called wiht an untranslate slug to the translated URL
 *
 * @return void
 */
function wplng_redirect_translated_slug() {

	if ( is_404() ) {
		return;
	}

	$language_website_id = wplng_get_language_website_id();
	$language_current_id = wplng_get_language_current_id();

	if ( $language_website_id === $language_current_id ) {
		return;
	}

	if ( empty( $_COOKIE['wplingua'] )
		&& apply_filters( 'wplng_cookie_check', true )
	) {
		// Set HTTP no-cache header
		nocache_headers();

		// Disable cache for plugins
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
	}

	$url_current = wplng_get_url_current();

	if ( ! is_string( $url_current )
		|| '' === $url_current
		|| '/' === $url_current
	) {
		return;
	}

	$url_translated = wplng_get_url_current_for_language(
		$language_current_id
	);

	if ( $url_current === $url_translated ) {
		return;
	}

	wp_safe_redirect(
		wp_make_link_relative(
			$url_translated
		)
	);

	exit;
}


/**
 * wpLingua output buffering starting function
 *
 * @return void
 */
function wplng_ob_start() {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

		/**
		 * Is an AJAX call
		 */

		if ( empty( $_SERVER['HTTP_REFERER'] )
			|| ! is_string( $_SERVER['HTTP_REFERER'] )
		) {
			return;
		}

		$referer = sanitize_url( $_SERVER['HTTP_REFERER'] );

		// Check if the referer is clean
		if ( strtolower( esc_url_raw( $referer ) ) !== strtolower( $referer ) ) {
			return;
		}

		// Check AJAX action
		if ( ! empty( $_POST['action'] )
			&& in_array( $_POST['action'], wplng_data_excluded_ajax_action() )
		) {
			return;
		}

		global $wplng_request_uri;
		$wplng_request_uri = wp_make_link_relative( $referer );

		if ( ! wplng_url_is_translatable( $wplng_request_uri )
			|| wplng_get_language_website_id() === wplng_get_language_current_id()
		) {
			return;
		}

		ob_start( 'wplng_ob_callback_ajax' );

	} else {

		/**
		 * Is a front call
		 */

		if ( wplng_get_language_website_id() === wplng_get_language_current_id() ) {
			return;
		}

		global $wplng_request_uri;
		$current_path = $wplng_request_uri;

		if ( ! is_string( $current_path ) ) {
			return;
		}

		$origin_path = wplng_get_url_original( $current_path );

		if ( ! wplng_url_is_translatable( $origin_path ) ) {
			wp_safe_redirect( $origin_path );
			exit;
		}

		$_SERVER['REQUEST_URI'] = $origin_path;

		ob_start( 'wplng_ob_callback_page' );
	}
}


/**
 * wpLingua OB Callback function : Translate pages
 *
 * @param string $html
 * @return string
 */
function wplng_ob_callback_page( $content ) {

	if ( wplng_str_is_json( $content ) ) {

		$content = apply_filters( 'wplng_intercepted_json', $content );
		$content = wplng_translate_json( $content );
		$content = apply_filters( 'wplng_translated_json', $content );

	} elseif ( wplng_str_is_html( $content ) ) {

		$args = array();

		if ( current_user_can( 'edit_posts' ) ) {

			if ( ! empty( $_GET['wplng-mode'] ) ) {

				switch ( $_GET['wplng-mode'] ) {

					case 'editor':
						$args['mode'] = 'editor';
						break;

					case 'list':
						$args['mode'] = 'list';
						break;
				}
			}

			$load_in_progress_enabled = apply_filters(
				'wplng_enable_in_progress_feature',
				get_option( 'wplng_load_in_progress', false )
			);

			if ( $load_in_progress_enabled ) {

				$args['load'] = 'enabled';

				if ( ! empty( $_GET['wplng-load'] )
					&& (
						$_GET['wplng-load'] === 'loading'
						|| $_GET['wplng-load'] === 'progress'
						|| $_GET['wplng-load'] === 'disabled'
					)
				) {

					$args['load'] = $_GET['wplng-load'];

					wp_cache_flush();

					if ( $args['load'] === 'loading' ) {
						return wplng_translate_html_loading( $content, $args );
					}
				}
			}
		}

		$content = apply_filters( 'wplng_intercepted_html', $content );
		$content = wplng_translate_html( $content, $args );
		$content = apply_filters( 'wplng_translated_html', $content );

	}

	return $content;
}


/**
 * wpLingua OB Callback function : AJAX call
 *
 * @param string $output
 * @return string
 */
function wplng_ob_callback_ajax( $output ) {

	global $wplng_request_uri;

	if ( wplng_str_is_json( $output ) ) {

		$output_translated = wplng_translate_json( $output );

	} elseif ( wplng_str_is_html( $output ) ) {

		$output_translated = wplng_translate_html( $output );

	} else {
		$output_translated = $output;
	}

	// Print debug data in debug.log file
	if ( true === WPLNG_DEBUG_AJAX ) {

		$action = 'UNKNOW';
		if ( ! empty( $_POST['action'] ) && is_string( $_POST['action'] ) ) {
			$action = $_POST['action'];
		}

		$debug = array(
			'title'       => 'wpLingua AJAX debug',
			'action'      => $action,
			'request_uri' => $wplng_request_uri,
			'value'       => $output,
			'translated'  => $output_translated,
		);

		error_log(
			var_export(
				$debug,
				true
			)
		);
	}

	return $output_translated;
}
