<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function mcv_get_url_original( $url = '' ) {

	$language_website_id = mcv_get_language_website_id();
	$language_current_id = mcv_get_language_current_id();

	if ( empty( $url ) ) {
		$url = mcv_get_url_current();
	}

	if ( $language_website_id !== $language_current_id ) {
		$url = str_replace( '/' . $language_current_id . '/', '/', $url );
	}

	return $url;
}


function mcv_get_url_current() {
	global $mcv_request_uri;
	return ( empty( $_SERVER['HTTPS'] ) ? 'http' : 'https' ) . "://$_SERVER[HTTP_HOST]$mcv_request_uri";
}


function mcv_get_url_current_for_language( $language_id ) {

	$language_current_id = mcv_get_language_current_id();

	global $mcv_request_uri;
	$path = $mcv_request_uri;
	$path = str_replace( '/' . $language_current_id . '/', '/', $path );
	$path = '/' . $language_id . $path;

	$url = ( empty( $_SERVER['HTTPS'] ) ? 'http' : 'https' ) . "://$_SERVER[HTTP_HOST]$path";

	return $url;
}


// function mcv_get_url_for_language( $url, $language_id ) {

// }
