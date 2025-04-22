<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Get data from wpLingua API : API key validation
 *
 * Return an error message with error code
 * or return the basic information of the API key
 *
 * API terms : https://wplingua.com/terms/
 *
 * ---------------------------------------------------
 * Data sent :
 * ---------------------------------------------------
 * - request : 'api_key'
 * - version : API compatile version
 * - context : Sends page calling URL
 * - api_key : the API key of website
 *
 * ---------------------------------------------------
 * Data received if successful :
 * ---------------------------------------------------
 * - language_original : A language ID
 * - languages_target  : An array of languages ID
 * - features          : Array of allowed API features
 * - status            : FREE | PREMIUM | VIP
 * - expiration        : A date dd/mm/yyyy
 *
 * ---------------------------------------------------
 * Data received in case of failure
 * ---------------------------------------------------
 * - error   : true
 * - code    : An integer
 * - message : Error description
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
		'context' => wplng_get_context(),
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
		&& is_array( $response['languages_target'] )
		&& isset( $response['features'] )
		&& is_array( $response['features'] )
	) {

		/**
		 * API returned valid key informations
		 */

		// Sanitize languages target

		$languages_target = array();

		foreach ( $response['languages_target'] as $id ) {

			if ( ! wplng_is_valid_language_id( $id ) ) {
				continue;
			}

			$languages_target[] = sanitize_key( $id );
		}

		// Sanitize features list

		$features = array();

		foreach ( $response['features'] as $key => $allow ) {

			if ( ! is_string( $key ) || ! is_bool( $allow ) ) {
				continue;
			}

			$key   = sanitize_key( $key );
			$allow = ( true === $allow );

			$features[ $key ] = $allow;
		}

		// Sanitize status

		$status = 'FREE';

		if ( ! empty( $response['status'] )
			&& (
				'PREMIUM' === $response['status']
				|| 'VIP' === $response['status']
			)
		) {
			$status = $response['status'];
		}

		// Make the checked response

		$response_checked = array(
			'language_original' => sanitize_key( $response['language_original'] ),
			'languages_target'  => $languages_target,
			'features'          => $features,
			'status'            => $status,
		);

		// Add expiration

		if ( ! empty( $response['expiration'] )
			&& is_string( $response['expiration'] )
		) {
			$response_checked['expiration'] = $response['expiration'];
		}
	} elseif ( isset( $response['error'] )
		&& ( true === $response['error'] )
		&& isset( $response['code'] )
		&& is_int( $response['code'] )
		&& isset( $response['message'] )
		&& is_string( $response['message'] )
	) {

		/**
		 * API returning a valid error
		 */

		$error_message  = __( 'Code', 'wplingua' ) . ' ';
		$error_message .= $response['code'] . ' - ';
		$error_message .= $response['message'];

		set_transient(
			'wplng_api_key_error',
			$error_message,
			MINUTE_IN_SECONDS * 5
		);

		if ( isset( $response['disconnect'] )
			&& true === $response['disconnect']
		) {
			delete_option( 'wplng_api_key_data' );
			delete_option( 'wplng_api_key' );
			wplng_clear_translations_cache();
			wplng_clear_slugs_cache();
		}
	} else {

		/**
		 * API returned an unexpected response
		 */

		set_transient(
			'wplng_api_key_error',
			__( 'API returned an unexpected response.', 'wplingua' ),
			MINUTE_IN_SECONDS * 5
		);

	}

	return $response_checked;
}
