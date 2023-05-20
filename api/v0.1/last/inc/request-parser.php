<?php

if ( ! defined( 'MACHIAVEL_API' ) ) {
	die;
}


function mcvapi_request_parser() {

	if ( empty( $_POST['source'] ) ) {
		mcvapi_error_die( 6 );
	}

	if ( empty( $_POST['target'] ) ) {
		mcvapi_error_die( 7 );
	}

	if ( empty( $_POST['text'] ) ) {
		mcvapi_error_die( 8 );
	}

}
