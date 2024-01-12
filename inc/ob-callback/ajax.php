<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * wpLingua OB Callback function : AJAX
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

	$output = wplng_ob_callback_translate( $output );

	return $output;
}
