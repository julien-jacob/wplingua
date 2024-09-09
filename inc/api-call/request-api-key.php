<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Request an API key for wpLingua API
 *
 * Return an error message with error code
 * or a confirmation for the API key creation
 *
 * API terms : https://wplingua.com/terms/
 *
 * ---------------------------------------------------
 * Data sent :
 * ---------------------------------------------------
 * - request           : 'register'
 * - mail_address      : A email address
 * - language_original : A language ID
 * - languages_target  : An array of languages ID
 * - accept_eula       : Boolean
 *
 * ---------------------------------------------------
 * Data received if successful :
 * ---------------------------------------------------
 * - register : true
 *
 * ---------------------------------------------------
 * Data received in case of failure
 * ---------------------------------------------------
 * - error   : true
 * - code    : An integer
 * - message : Error description
 *
 * @param array $data
 * @return array API validation or error after API key registration
 */
function wplng_api_call_request_api_key( $data ) {

	/**
	 * Sanitize and check data
	 */

	// Check if needed data is present

	if ( empty( $data['request'] )
		|| $data['request'] !== 'register'
		|| empty( $data['mail_address'] )
		|| empty( $data['website'] )
		|| empty( $data['language_original'] )
		|| empty( $data['languages_target'] )
		|| empty( $data['accept_eula'] )
		|| $data['accept_eula'] !== true
	) {
		return array(
			'error'   => true,
			'message' => __( 'Error - Invalid data.', 'wplingua' ),
		);
	}

	// Sanitize and check mail address

	$mail_address = sanitize_email( $data['mail_address'] );

	if ( ! is_email( $mail_address ) ) {
		return array(
			'error'   => true,
			'message' => __( 'Error - Invalid data (email address).', 'wplingua' ),
		);
	}

	// Sanitize and check website URL

	$website = sanitize_url( $data['website'] );

	if ( $website !== $data['website'] ) {
		return array(
			'error'   => true,
			'message' => __( 'Error - Invalid data (website URL).', 'wplingua' ),
		);
	}

	// Sanitize and check original and target language

	$language_original = sanitize_key( $data['language_original'] );
	$languages_target  = sanitize_key( $data['languages_target'] );

	if ( ! wplng_is_valid_language_id( $language_original )
		|| ! wplng_is_valid_language_id( $languages_target )
		|| ( $language_original === $languages_target )
	) {
		return array(
			'error'   => true,
			'message' => __( 'Error - Invalid data (languages).', 'wplingua' ),
		);
	}

	/**
	 * Get the API call
	 */

	$body = array(
		'request'           => 'register',
		'version'           => WPLNG_API_VERSION,
		'website'           => $website,
		'mail_address'      => $mail_address,
		'language_original' => $language_original,
		'languages_target'  => array( $languages_target ),
		'accept_eula'       => true,
	);

	$args = array(
		'method'    => 'POST',
		'timeout'   => 5,
		'sslverify' => WPLNG_API_SSLVERIFY,
		'body'      => $body,
	);

	$request = wp_remote_post(
		WPLNG_API_URL . '/account/',
		$args
	);

	/**
	 * Check if the API call worked
	 */

	if ( is_wp_error( $request )
		|| wp_remote_retrieve_response_code( $request ) != 200
	) {
		return array(
			'error'   => true,
			'message' => __( 'Error - The API could not be called.', 'wplingua' ),
		);
	}

	/**
	 * Check and sanitize the API response
	 */

	$response_checked = array();
	$response         = json_decode(
		wp_remote_retrieve_body( $request ),
		true
	);

	if ( isset( $response['register'] )
		&& ( true === $response['register'] )
	) {

		// API returning confirmation of key creation

		$response_checked = array(
			'register' => true,
		);

	} elseif ( ! empty( $response['error'] )
		&& ( true === $response['error'] )
		&& isset( $response['code'] )
		&& is_int( $response['code'] )
		&& ! empty( $response['message'] )
		&& is_string( $response['message'] )
	) {

		// API returning a valid error

		$response_checked = array(
			'error'   => true,
			'message' => sanitize_text_field( $response['message'] ),
		);

	} else {

		// API returned an unexpected response

		$response_checked = array(
			'error'   => true,
			'message' => __( 'Error - API response not valid.', 'wplingua' ),
		);

	}

	return $response_checked;
}
