<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_ob_callback_ajax( $output ) {

	global $wplng_request_uri;
	$wplng_request_uri = wp_make_link_relative( $_SERVER['HTTP_REFERER'] );

	if ( 
		! wplng_url_is_translatable($wplng_request_uri)
		|| wplng_get_language_website_id() === wplng_get_language_current_id() 
	) {
		return $output;
	}

	// error_log( $output );

	$output = wplng_ob_callback_translate( $output );
	// error_log($wplng_request_uri);
	// error_log(var_export($_SERVER['HTTP_REFERER'], true));
	// error_log(wplng_get_language_current_id());

	error_log( $output );

	// error_log(var_export($_SERVER, true));

	return $output;
}