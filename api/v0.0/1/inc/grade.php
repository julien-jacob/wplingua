<?php

if ( ! defined( 'MACHIAVEL_API' ) ) {
	die;
}

function mcvapi_get_grade() {

	// $x = var_export($_POST);
	// echo $x;// $_POST['api-key'];
	// die;

	if ( empty( $_POST['api-key'] ) ) {
		mcvapi_error_die( 1 );
	}

	$apikey = $_POST['api-key'];

	if ( strlen( $apikey ) !== API_KEY_LENGTH ) {
		mcvapi_error_die( 2 );
	}

	if ( '1111111111111111' !== $apikey ) {
		mcvapi_error_die( 3 );
	} 
	
	return 'premium';
}
