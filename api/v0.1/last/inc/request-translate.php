<?php

if ( ! defined( 'MACHIAVEL_API' ) ) {
	die;
}


function mcvapi_request_translate() {

	if ( empty( $_POST['source'] ) ) {
		mcvapi_error_die( 6 );
	}

	if ( empty( $_POST['target'] ) ) {
		mcvapi_error_die( 7 );
	}

	if ( empty( $_POST['text'] ) ) {
		mcvapi_error_die( 8 );
	}

	$translation = mcvapi_translate( $_POST['source'], $_POST['target'], $_POST['text'] );

	$response = array(
		'translation' => $translation,
	);

	return json_encode( $response );

}


