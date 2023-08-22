<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_api_informations() {

	$cached_info = json_decode(
		get_transient( 'wplng_api_informations' ),
		true
	);

	if ( ! empty( $cached_info ) ) {
		return $cached_info;
	}

	$args = array(
		'method'    => 'POST',
		'timeout'   => 5,
		'sslverify' => false,
		'body'      => array(),
	);

	$request = wp_remote_post(
		WPLNG_API_URL,
		$args
	);

	if ( is_wp_error( $request )
		|| wp_remote_retrieve_response_code( $request ) != 200
	) {
		return array(
			'error'   => true,
			'message' => __( 'Error - API response not valid.', 'wplingua' ),
		);
	}

	$response = json_decode( wp_remote_retrieve_body( $request ), true );

	if ( empty( $response['error'] ) ) {
		set_transient(
			'wplng_api_informations',
			wp_json_encode( $response ),
			60 * 60
		);
	}

	return $response;
}
