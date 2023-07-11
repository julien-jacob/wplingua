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
		'languages_target'  => array(
			$_POST['languages_target'],
		),
		'features'          => array(
			'search'      => false,
			'mail'        => false,
			'woocommerce' => false,
		),
		'created'           => date( 'd/m/Y H:i:s' ),
		'validated'         => false,
		'used'              => false,
		'ban'               => false,
	);

	$api_key = wplngapi_get_new_api_key();

	file_put_contents(
		'../../keys/' . $api_key . '.json',
		json_encode( $api_key_data )
	);

	// Get Website name
	$url_parsed   = parse_url( $_POST['website'] );
	$website_name = $_POST['website'];
	if ( ! empty( $url_parsed['host'] ) ) {
		$website_name = $url_parsed['host'];
	}

	$mail_subject = 'wpLingua : API key for ' . $website_name;
	$mail_message = 'Your wpLingua API key : ' . $api_key;
	$mail_headers = 'From: no-reply@wplingua.com';

	$mail_sending = mail(
		$_POST['mail_address'],
		$mail_subject,
		$mail_message,
		$mail_headers
	);

	if ( ! $mail_sending ) {
		wplngapi_error_die( 15 );
	}

	$response = array(
		'register' => true,
	);

	return json_encode( $response );
}


