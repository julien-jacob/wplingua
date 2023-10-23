<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Get data from wpLingua API : Translated texts
 *
 * @param array $texts
 * @param string $language_source_id
 * @param string $language_target_id
 * @return array
 */
function wplng_api_call_translate(
	$texts,
	$language_source_id = '',
	$language_target_id = ''
) {

	if ( empty( $texts ) ) {
		return $texts;
	}

	$api_key = wplng_get_api_key();

	if ( empty( $api_key ) ) {
		return $texts;
	}

	if ( empty( $language_target_id ) ) {
		$language_target_id = wplng_get_language_current_id();
	}

	if ( empty( $language_source_id ) ) {
		$language_source_id = wplng_get_language_website_id();
	}

	$json_texts = wp_json_encode( $texts );
	if ( empty( $json_texts ) ) {
		return $texts;
	}

	$body = array(
		'api_key' => $api_key,
		'request' => 'translate',
		'version' => WPLNG_API_VERSION,
		'source'  => $language_source_id,
		'target'  => $language_target_id,
		'texts'   => $json_texts,
	);

	$args = array(
		'method'    => 'POST',
		'timeout'   => 99, // 1 min 29 s
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
		return $texts;
	}

	$response = json_decode( wp_remote_retrieve_body( $request ), true );

	if ( empty( $response['translations'] )
		|| ! empty( $response['error'] )
	) {
		return $texts;
	}

	return $response['translations'];
}
