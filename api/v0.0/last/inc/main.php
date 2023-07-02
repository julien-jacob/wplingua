<?php

if ( ! defined( 'WPLINGUA_API' ) ) {
	die;
}

function wplngapi_wplingua_api() {
	$json = '';

	// $grade = wplngapi_get_grade();

	if ( empty( $_POST['r'] ) ) {
		wplngapi_error_die( 4 );
	}

	switch ( $_POST['r'] ) {

		case 'api_key':
			require_once 'inc/request-api-key.php';
			$json = wplngapi_request_api_key();
			break;

		case 'register':
			require_once 'inc/request-register.php';
			$json = wplngapi_request_register();
			break;

		case 'translate':
			require_once 'inc/request-translate.php';
			$json = wplngapi_request_translate();
			break;

		case 'parser':
			require_once 'inc/request-parser.php';
			$json = wplngapi_request_parser();
			break;

		default:
			wplngapi_error_die( 5 );
			break;
	}

	return $json;
}
