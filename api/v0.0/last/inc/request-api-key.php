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

	$api_key_data = file_get_contents( $file_name );
	return $api_key_data;

	// $response = array(
	// 	'register' => true
	// );

	// return json_encode( $response );

}


