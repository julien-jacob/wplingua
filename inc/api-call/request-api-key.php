<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Request an API key for wpLingua API
 *
 * @param array $data
 * @return array API validation or error after API key registration
 */
function wplng_api_call_request_api_key( $data ) {

	if ( empty( $data['request'] )
		|| $data['request'] !== 'register'
		|| empty( $data['mail_address'] )
		|| empty( $data['website'] )
		|| empty( $data['language_original'] )
		|| ! wplng_is_valid_language_id( $data['language_original'] )
		|| empty( $data['languages_target'] )
		|| ! wplng_is_valid_language_id( $data['languages_target'] )
		|| empty( $data['accept_eula'] )
		|| $data['accept_eula'] !== true
		|| $data['language_original'] === $data['languages_target']
	) {
		return array(
			'error'   => true,
			'message' => __( 'Error - Invalid data.', 'wplingua' ),
		);
	}

	$body = array(
		'request'           => 'register',
		'version'           => WPLNG_API_VERSION,
		'website'           => $data['website'],
		'mail_address'      => $data['mail_address'],
		'language_original' => $data['language_original'],
		'languages_target'  => array( $data['languages_target'] ),
		'accept_eula'       => true,
	);

	$args = array(
		'method'    => 'POST',
		'timeout'   => 5,
		'sslverify' => false,
		'body'      => $body,
	);

	$request = wp_remote_post(
		WPLNG_API_URL . '/account/',
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

	return $response;
}
