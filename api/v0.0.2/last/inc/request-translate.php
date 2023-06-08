<?php

if ( ! defined( 'WPLINGUA_API' ) ) {
	die;
}


function wplngapi_request_translate() {

	if ( empty( $_POST['source'] ) ) {
		wplngapi_error_die( 6 );
	}

	if ( empty( $_POST['target'] ) ) {
		wplngapi_error_die( 7 );
	}

	if ( empty( $_POST['text'] ) ) {
		wplngapi_error_die( 8 );
	}

	$translation = wplngapi_translate( $_POST['source'], $_POST['target'], $_POST['text'] );

	$response = array(
		'translation' => $translation,
	);

	return json_encode( $response );

}


