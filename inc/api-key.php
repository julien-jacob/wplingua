<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_is_valid_api_key_format( $api_key ) {

	if (
		empty( $api_key )
		|| ! is_string( $api_key )
		|| 32 !== strlen( $api_key )
	) {
		return false;
	}

	return true;
}


function wplng_get_api_key() {

	$api_key = trim( get_option( 'wplng_api_key' ) );

	if ( ! wplng_is_valid_api_key_format( $api_key ) ) {
		$api_key = '';
	}

	return $api_key;
}


function wplng_get_api_data() {

	if ( empty( wplng_get_api_key() ) ) {
		return array();
	}

	$api_key_data = get_transient( 'wplng_api_key_data' );
	$api_key_data = json_decode( $api_key_data, true );

	if ( empty( $api_key_data ) ) {

		$api_key_data = wplng_api_call_validate_api_key();

		if ( empty( $api_key_data ) ) {
			return array();
		}

		set_transient(
			'wplng_api_key_data',
			wp_json_encode( $api_key_data ),
			60 * 60 * 24
		);
	}

	return $api_key_data;
}


function wplng_get_api_language_website() {

	$data = wplng_get_api_data();

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
			foreach ( $data['languages_target'] as $language_id ) {
				if ( ! wplng_is_valid_language_id( $language_id ) ) {
					return false;
				}
			}
		}
		return $data['languages_target'];
	}

	return false;
}


function wplng_get_api_feature() {

	$data     = wplng_get_api_data();
	$all      = array( 'search', 'mail', 'woocommerce' );
	$features = array();

	if ( ! empty( $data['features'] ) && is_array( $data['features'] ) ) {
		foreach ( $data['features'] as $feature_name => $feature_allow ) {
			if ( $feature_allow && in_array( $feature_name, $all ) ) {
				$features[] = $feature_name;
			}
		}
	}

	return $features;
}


function wplng_api_feature_is_allow( $feature_name ) {
	return in_array( $feature_name, wplng_get_api_feature() );
}

function wplng_on_update_option_wplng_api_key( $old_value, $new_value ) {
	delete_transient( 'wplng_api_key_data' );
}
