<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_url_translate( $url, $language_id_target ) {

	// Check if URL is an empty string
	if ( $url == '' ) {
		return '';
	}

	// Check if URL is translatable (exclude /admin/...)
	if ( ! wplng_url_is_translatable( $url ) ) {
		return $url;
	}

	// Check if URL is an anchor link for the current page
	if ( substr( $url, 0, 1 ) == '#' ) {
		return $url;
	}

	$domain = $_SERVER['HTTP_HOST'];

	if ( preg_match( '#^(http:\/\/|https:\/\/)?' . $domain . '(.*)$#', $url ) ) {

		// Check if URL is already translated
		$languages_target = wplng_get_languages_target();
		foreach ( $languages_target as $key => $language_target ) {
			if ( str_contains( $url, '/' . $language_target['id'] . '/' ) ) {
				return $url;
			}
		}

		$url = preg_replace(
			'#^(http:\/\/|https:\/\/)?' . $domain . '(.*)$#',
			'$1' . $domain . '/' . $language_id_target . '$2',
			$url
		);
		$url = esc_url( trailingslashit( $url ) );
	}

	$url = apply_filters(
		'wplng_url_translate',
		$url
	);

	return $url;
}


function wplng_url_is_translatable( $url = '' ) {

	global $wplng_request_uri;
	$is_translatable = true;

	// Get current URL if $url is empty
	if ( empty( $url ) ) {
		$url = $wplng_request_uri;
	}

	$url = wp_make_link_relative( $url );

	// Check if is an admin page
	if ( str_contains( $url, wp_make_link_relative( get_admin_url() ) ) ) {
		$is_translatable = false;
	}

	$is_translatable = apply_filters(
		'wplng_url_is_translatable',
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
