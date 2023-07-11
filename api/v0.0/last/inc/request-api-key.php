<?php

if ( ! defined( 'WPLINGUA_API' ) ) {
	die;
}

function wplngapi_request_api_key() {

	if ( empty( $_POST['api_key'] ) ) {
		wplngapi_error_die( 13 );
	}

	// TODO : Check size
	// TODO : Check caractères contenus

	$file_name = '../../keys/' . $_POST['api_key'] . '.json';

	if ( ! file_exists( $file_name ) ) {
		wplngapi_error_die( 14 );
	}

	$api_key_data = json_decode( file_get_contents( $file_name ), true );

	if (
		! isset( $api_key_data['language_original'] )
		|| ! isset( $api_key_data['languages_target'] )
		|| ! isset( $api_key_data['features'] )
	) {
		wplngapi_error_die( 16 );
	}

	$api_key_data_clear = array(
		'language_original' => $api_key_data['language_original'],
		'languages_target'  => $api_key_data['languages_target'],
		'features'          => $api_key_data['features'],
	);

	return json_encode( $api_key_data_clear );
}
