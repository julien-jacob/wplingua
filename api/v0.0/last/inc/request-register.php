<?php

if ( ! defined( 'WPLINGUA_API' ) ) {
	die;
}


function wplngapi_request_register() {

	if ( empty( $_POST['website'] ) ) {
		// TODO : Check URL format
		// TODO : Blacklist ?
		wplngapi_error_die( 9 );
	}

	if ( empty( $_POST['mail_address'] ) ) {
		// TODO : Check if Mail address is valid
		wplngapi_error_die( 10 );
	}

	if ( empty( $_POST['language_original'] ) ) {
		// TODO : Check if valid
		wplngapi_error_die( 11 );
	}

	if ( empty( $_POST['languages_target'] ) ) {
		// TODO : Check if valid
		wplngapi_error_die( 12 );
	}

	$api_key_data = array(
		'website'           => $_POST['website'],
		'mail_address'      => $_POST['mail_address'],
		'language_original' => $_POST['language_original'],
		'languages_target'  => $_POST['languages_target'],
		'features'          => array(
			'search'      => false,
			'mail'        => false,
			'woocommerce' => false,
		),
	);

	$api_key = wplngapi_get_new_api_key();

	file_put_contents(
		'../../keys/' . $api_key . '.json',
		json_encode( $api_key_data )
	);

	$response = array(
		'website'           => $_POST['website'],
		'mail_address'      => $_POST['mail_address'],
		'language_original' => $_POST['language_original'],
		'languages_target'  => $_POST['languages_target'],
		'api_key'           => $api_key,
		'features'          => array(
			'search'      => false,
			'mail'        => false,
			'woocommerce' => false,
		),
	);

	return json_encode( $response );

}


