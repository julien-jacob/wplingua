<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_url_current_is_translatable() {

	$is_translatable = true;

	if ( is_admin() ) {
		$is_translatable = false;
	}

	if ( wplng_get_language_website_id() === wplng_get_language_current_id() ) {
		$is_translatable = false;
	}

	$is_translatable = apply_filters(
		'wplng_url_current_is_translatable',
		$is_translatable
	);

	return $is_translatable;
}


function wplng_get_url_original( $url = '' ) {

	if ( empty( $url ) ) {
		$url = wplng_get_url_current();
	}

	$language_website_id = wplng_get_language_website_id();
	$language_current_id = wplng_get_language_current_id();

	if ( $language_website_id !== $language_current_id ) {
		$url = str_replace( '/' . $language_current_id . '/', '/', $url );
	}

	$url = apply_filters(
		'wplng_url_original',
		$url,
		$language_website_id,
		$language_current_id
	);

	return $url;
}


function wplng_get_url_current() {
	global $wplng_request_uri;
	return ( empty( $_SERVER['HTTPS'] ) ? 'http' : 'https' ) . "://$_SERVER[HTTP_HOST]$wplng_request_uri";
}


function wplng_get_url_current_for_language( $language_id ) {

	// TODO : Revoir cette fonction ;)

	$language_current_id = wplng_get_language_current_id();

	global $wplng_request_uri;
	$path = str_replace( '/' . $language_current_id . '/', '/', $wplng_request_uri );
	$path = '/' . $language_id . $path;

	$url = ( empty( $_SERVER['HTTPS'] ) ? 'http' : 'https' ) . "://$_SERVER[HTTP_HOST]$path";

	return $url;
}
