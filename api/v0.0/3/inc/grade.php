<?php

if ( ! defined( 'WPLINGUA_API' ) ) {
	die;
}

function wplngapi_get_grade() {

	if ( empty( $_POST['api-key'] ) ) {
		wplngapi_error_die( 1 );
	}

	$apikey = $_POST['api-key'];

	if ( strlen( $apikey ) !== API_KEY_LENGTH ) {
		wplngapi_error_die( 2 );
	}

	if ( '1111111111111111' !== $apikey ) {
		wplngapi_error_die( 3 );
	}

	return 'premium';
}
