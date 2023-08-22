<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



function wplng_translate( $text, $language_source_id = '', $language_target_id = '' ) {

	$api_key = wplng_get_api_key();

	if ( empty( $api_key ) ) {
		return $text;
	}

	if ( empty( $language_target_id ) ) {
		$language_target_id = wplng_get_language_current_id();
	}

	if ( empty( $language_source_id ) ) {
		$language_source_id = wplng_get_language_website_id();
	}

	$body = array(
		'api_key' => $api_key,
		'request'       => 'translate',
		'version'       => WPLNG_API_VERSION,
		'source'  => $language_source_id,
		'target'  => $language_target_id,
		'text'    => $text,
	);

	$args = array(
		'method'    => 'POST',
		'timeout'   => 5,
		'sslverify' => false,
		'body'      => $body,
	);

	$request = wp_remote_post(
		WPLNG_API_URL . '/app/',
		$args
	);

	if ( is_wp_error( $request )
		|| wp_remote_retrieve_response_code( $request ) != 200
	) {
		return $text;
	}

	$response = json_decode( wp_remote_retrieve_body( $request ), true );

	if ( ! isset( $response['translation'] ) ) {
		return $text;
	}

	return (string) $response['translation'];
}