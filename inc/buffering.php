<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * wpLingua output buffering starting function
 *
 * @return void
 */
function wplng_ob_start() {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		ob_start( 'wplng_ob_callback_ajax' );
		return;
	}

	if ( wplng_get_language_website_id() === wplng_get_language_current_id() ) {
		return;
	}

	global $wplng_request_uri;
	$current_path = $wplng_request_uri;

	if ( ! is_string( $current_path ) ) {
		return;
	}

	$origin_path = wplng_get_url_original($current_path);

	if ( ! wplng_url_is_translatable( $origin_path ) ) {
		wp_safe_redirect( $origin_path );
		exit;
	}

	$_SERVER['REQUEST_URI'] = $origin_path;

	ob_start( 'wplng_ob_callback_page' );

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

			if ( apply_filters( 'wplng_enable_in_progress_feature', true ) ) {

				$args['load'] = 'enabled';

				if ( ! empty( $_GET['wplng-load'] ) ) {

					switch ( $_GET['wplng-load'] ) {

						case 'loading':
							$args['load'] = 'loading';
							break;

						case 'progress':
							$args['load'] = 'progress';
							break;
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

	if ( empty( $_SERVER['HTTP_REFERER'] ) ) {
		return $output;
	}

	$referer = sanitize_url( $_SERVER['HTTP_REFERER'] );

	// Check if the referer is clean
	if ( strtolower( esc_url_raw( $referer ) ) !== strtolower( $referer ) ) {
		return $output;
	}

	global $wplng_request_uri;
	$wplng_request_uri = wp_make_link_relative( $referer );

	if ( ! wplng_url_is_translatable( $wplng_request_uri )
		|| wplng_get_language_website_id() === wplng_get_language_current_id()
	) {
		return $output;
	}

	if ( wplng_str_is_json( $output ) ) {

		$output_translated = wplng_translate_json( $output );

	} elseif ( wplng_str_is_html( $output ) ) {

		$output_translated = wplng_translate_html( $output );

	} else {
		$output_translated = $output;
	}

	// Print debug data in debug.log file
	if ( true === WPLNG_LOG_AJAX_DEBUG ) {

		$debug = array(
			'title'       => 'wpLingua AJAX debug',
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
