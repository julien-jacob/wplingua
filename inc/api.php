<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_translate( $language_source, $language_target, $text ) {

	$body = array(
		'api-key' => '1111111111111111',
		'r'       => 'translate',
		'source'  => $language_source,
		'target'  => $language_target,
		'text'    => $text,
	);
	$args = array(
		'method'    => 'POST',
		'timeout'   => 5,
		'sslverify' => false,
		'body'      => $body,
	);

	error_log( var_export( $body, true ) );

	$request = wp_remote_post( wplng_API, $args );

	if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		error_log( print_r( $request, true ) );
		return '';
	}

	$response = json_decode( wp_remote_retrieve_body( $request ), true );

	if ( ! isset( $response['translation'] ) ) {
		// TODO : Check for remove or update
		return 'Erreur :: [' . $text . ']';
	}

	return (string) $response['translation'];
}


function wplng_parser( $html ) {

	$body = array(
		'api-key' => '1111111111111111',
		'r'       => 'parser',
		'source'  => wplng_get_language_website_id(),
		'target'  => wplng_get_language_current_id(),
		'text'    => $html,
	);
	$args = array(
		'method'    => 'POST',
		'timeout'   => 120,
		'sslverify' => false,
		'body'      => $body,
	);

	// error_log( var_export( $body, true ) );

	$request = wp_remote_post( WPLNG_API, $args );

	if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		error_log( print_r( $request, true ) );
		return array();
	}

	$response = json_decode( wp_remote_retrieve_body( $request ), true );
// return wp_remote_retrieve_body( $request );
	return $response;
}
