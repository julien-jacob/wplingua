<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_api_call_validate_api_key( $api_key = '' ) {

	if ( empty( $api_key ) ) {
		$api_key = wplng_get_api_key();
		if ( empty( $api_key ) ) {
			return array();
		}
	}

	if ( ! wplng_is_valid_api_key_format( $api_key ) ) {
		return array();
	}

	$body = array(
		'request' => 'api_key',
		'version' => WPLNG_API_VERSION,
		'api_key' => $api_key,
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
		return array();
	}
	$response = json_decode( wp_remote_retrieve_body( $request ), true );

	if ( ! empty( $response['error'] )
		&& ! empty( $response['message'] )
		&& isset( $response['code'] )
	) {
		$error_message  = __( 'Code', 'wplingua' ) . ' ' . esc_html( $response['code'] );
		$error_message .= ' - ' . esc_html( $response['message'] );
		set_transient(
			'wplng_api_key_error',
			$error_message,
			60 * 5
		);
		return array();
	}

	return $response;
}
