<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Get data from wpLingua API : API informations
 * - Last plugin version
 * - Global message
 *
 * @return array
 */
function wplng_api_call_api_informations() {

	$args = array(
		'method'    => 'POST',
		'timeout'   => 5,
		'sslverify' => WPLNG_API_SSLVERIFY,
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

	if ( empty( $response ) ) {
		return array(
			'error'   => true,
			'message' => __( 'Error - API response format not valid.', 'wplingua' ),
		);
	}

	return $response;
}
