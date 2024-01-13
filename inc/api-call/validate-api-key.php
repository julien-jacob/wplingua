<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Get data from wpLingua API : API key validation
 *
 * @param string $api_key
 * @return array
 */
function wplng_api_call_validate_api_key( $api_key = '' ) {

	/**
	 * Get and check the API key
	 */

	if ( empty( $api_key ) ) {

		$api_key = wplng_get_api_key();

		if ( empty( $api_key ) ) {
			return array();
		}
	}

	if ( ! wplng_is_valid_api_key_format( $api_key ) ) {
		return array();
	}

	/**
	 * Get the API call
	 */

	$body = array(
		'request' => 'api_key',
		'version' => WPLNG_API_VERSION,
		'api_key' => $api_key,
	);

	$args = array(
		'method'    => 'POST',
		'timeout'   => 5,
		'sslverify' => WPLNG_API_SSLVERIFY,
		'body'      => $body,
	);

	$request = wp_remote_post(
		WPLNG_API_URL . '/app/',
		$args
	);

	/**
	 * Check if the API call worked
	 */

	if ( is_wp_error( $request )
		|| wp_remote_retrieve_response_code( $request ) != 200
	) {
		return array();
	}

	/**
	 * Check and sanitize the API response
	 */

	$response_checked = array();
	$response         = json_decode(
		wp_remote_retrieve_body( $request ),
		true
	);

	if ( ! empty( $response['language_original'] )
		&& (
			wplng_is_valid_language_id( $response['language_original'] )
			|| 'all' === $response['language_original']
		)
		&& ! empty( $response['languages_target'] )
		&& wplng_is_valid_language_ids( $response['languages_target'] )
		&& $response['language_original'] !== $response['languages_target']
		&& isset( $response['features']['search'] )
		&& is_bool( $response['features']['search'] )
		&& isset( $response['features']['woocommerce'] )
		&& is_bool( $response['features']['woocommerce'] )
	) {

		// API returned valid key informations

		$languages_target = array();

		foreach ( $response['languages_target'] as $id ) {
			$languages_target[] = sanitize_key( $id );
		}

		$response_checked = array(
			'language_original' => sanitize_key( $response['language_original'] ),
			'languages_target'  => $languages_target,
			'features'          => array(
				'search'      => ( true === $response['features']['search'] ),
				'woocommerce' => ( true === $response['features']['woocommerce'] ),
			),
		);

	} elseif ( isset( $response['error'] )
		&& ( true === $response['error'] )
		&& isset( $response['code'] )
		&& is_int( $response['code'] )
		&& isset( $response['message'] )
		&& is_string( $response['message'] )
	) {

		// API returning a valid error

		$error_message  = __( 'Code', 'wplingua' ) . ' ';
		$error_message  = $response['code'] . ' - ';
		$error_message .= $response['message'];

		set_transient(
			'wplng_api_key_error',
			$error_message,
			60 * 5
		);

	} else {

		// API returned an unexpected response

		set_transient(
			'wplng_api_key_error',
			__( 'API returned an unexpected response.', 'wplingua' ),
			60 * 5
		);

	}

	return $response_checked;
}
