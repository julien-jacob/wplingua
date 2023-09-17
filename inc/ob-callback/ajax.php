<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_ob_callback_ajax( $output ) {

	global $wplng_request_uri;
	$wplng_request_uri = wp_make_link_relative( $_SERVER['HTTP_REFERER'] );

	if ( ! wplng_url_is_translatable( $wplng_request_uri )
		|| wplng_get_language_website_id() === wplng_get_language_current_id()
	) {
		return $output;
	}

	$output = wplng_ob_callback_translate( $output );

	return $output;
}
