<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_get_api_key() {

	$api_key = get_option( 'wplng_api_key' );

	return $api_key;
}


function wplng_get_api_data() {

	// TODO : Revoir cette fonction

	$api_key_data = get_transient( 'wplng_api_key_data' );

	if ( empty( $api_key_data ) ) {
		$api_key_data = wplng_validate_api_key();
	}

	// var_dump($api_key_data); die;

	return $api_key_data;
}


function wplng_get_api_language_website() {

	$data = json_decode(wplng_get_api_data(), true);

	if (
		! empty( $data['language_original'] )
		&& (
			wplng_is_valid_language_id( $data['language_original'] )
			|| 'all' === $data['language_original']
		)
	) {
		return $data['language_original'];
	}

	return false;
}


function wplng_get_api_languages_target() {

	$data = wplng_get_api_data();

	if ( ! empty( $data['languages_target'] ) ) {
		if ( 'all' === $data['languages_target'] ) {
			return 'all';
		} elseif ( is_array( $data['languages_target'] ) ) {
			foreach ( $data['languages_target'] as $key => $language_id ) {
				if ( ! wplng_is_valid_language_id( $language_id ) ) {
					return false;
				}
			}
		}
		return $data['languages_target'];
	}

	return false;
}
