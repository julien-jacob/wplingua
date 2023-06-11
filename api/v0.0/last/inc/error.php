<?php

if ( ! defined( 'WPLINGUA_API' ) ) {
	die;
}

/**
 * Echo an JSON for the error
 *
 * @param integer $code
 * @return void
 */
function wplngapi_error_die( $code = 0 ) {

	$prefix  = 'Error : ';
	$message = '';

	switch ( $code ) {
		case 1:
			$message = 'Empty API KEY';
			break;

		case 2:
			// Bad length for the API Key
			$message = 'Invalid API KEY format';
			break;

		case 3:
			// API Key not in DB
			$message = 'Unknown API KEY';
			break;

		case 4:
			// No "r" parametter in request
			$message = 'Empty request';
			break;

		case 5:
			// The "r" parametter is not in possibility list
			$message = 'Unknown request';
			break;

		case 6:
			$message = 'Translate API - Empty source language';
			break;

		case 7:
			$message = 'Translate API - Empty target language';
			break;

		case 8:
			$message = 'Translate API - Empty text';
			break;

		default:
			$message = 'Unknown error';
			break;
	}

	$response = array(
		'error'   => true,
		'code'    => $code,
		'message' => $prefix . ' ' . $message,
	);

	echo json_encode( $response );
	die;
}
