<?php

if ( ! defined( 'WPLINGUA_API' ) ) {
	die;
}

function wplngapi_get_new_api_key() {

	$characters        = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$characters_length = strlen( $characters );
	$already_exist     = true;
	$api_key           = '';

	while ( $already_exist ) {
		$api_key = '';
		for ( $i = 0; $i < API_KEY_LENGTH; $i++ ) {
			$selected = random_int( 0, $characters_length - 1 );
			$api_key .= $characters[ $selected ];
		}
		if ( ! file_exists( '../../keys/' . $api_key . '.json' ) ) {
			$already_exist = false;
		}
	}

	return $api_key;
}
