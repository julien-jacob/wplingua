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

	$origin_path = '/' . substr( $current_path, 4, strlen( $current_path ) - 1 );
	$origin_path = sanitize_url( $origin_path );

	if ( ! wplng_url_is_translatable( $origin_path ) ) {
		wp_redirect( $origin_path );
		exit;
	}

	$_SERVER['REQUEST_URI'] = $origin_path;

	if ( current_user_can( 'edit_posts' ) ) {
		if ( isset( $_GET['wplingua-editor'] ) ) {

			ob_start( 'wplng_ob_callback_editor' );

		} elseif ( isset( $_GET['wplingua-list'] ) ) {

			add_filter(
				'body_class', function( $classes ) {
					return array_merge( $classes, array( 'wplingua-list' ) );
				}
			);

			ob_start( 'wplng_ob_callback_list' );

		} else {

			add_filter(
				'body_class', function( $classes ) {
					return array_merge( $classes, array( 'wplingua-editor' ) );
				}
			);

			ob_start( 'wplng_ob_callback_translate' );

		}
	} else {
		ob_start( 'wplng_ob_callback_translate' );
	}

}
