<?php

if ( ! defined( 'MACHIAVEL_API' ) ) {
	die;
}

function mcvapi_machiavel_api() {
	$json = '';

	$grade = mcvapi_get_grade();

	if ( empty( $_POST['r'] ) ) {
		mcvapi_error_die( 4 );
	}

	switch ( $_POST['r'] ) {
		case 'translate':
			require_once 'inc/request-translate.php';
			mcvapi_request_translate();
			break;

		default:
			mcvapi_error_die( 5 );
			break;
	}

	return $json;
}
