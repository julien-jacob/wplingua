<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_get_api_key() {

	$api_key = get_option( 'wplng_api_key' );

	return $api_key;
}


function wplng_get_api_key_data() {

	// TODO : Revoir cette fonction

	$api_key_data = get_transient( 'wplng_api_key_data' );

	if ( empty( $api_key_data ) ) {
		$api_key_data = wplng_validate_api_key();
	}

	// var_dump($api_key_data); die;

	return $api_key_data;
}

