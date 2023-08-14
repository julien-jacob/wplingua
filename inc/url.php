<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_url_translate( $url, $language_id_target ) {

	// Check if URL is an empty string
	if ( '' === $url ) {
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

	$domain           = $_SERVER['HTTP_HOST'];
	$languages_target = wplng_get_languages_target();

	if ( preg_match( '#^(http:\/\/|https:\/\/)?' . $domain . '(.*)$#', $url ) ) {

		// Check if URL is already translated
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

	} elseif ( preg_match( '#^[^\/]+\/[^\/].*$|^\/[^\/].*$#', $url ) ) {

		// Check if URL is already translated
		foreach ( $languages_target as $key => $language_target ) {
			if ( substr( $url, 0, 4 ) == '/' . $language_target['id'] . '/'
				|| substr( $url, 0, 3 ) == $language_target['id'] . '/'
			) {
				return $url;
			}
		}

		if ( substr( $url, 0, 1 ) == '/' ) {
			$url = '/' . $language_id_target . $url;
		} else {
			$url = $language_id_target . '/' . $url;
		}
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

	// Check if is in wp-uploads
	if (
		$is_translatable
		&& str_contains( $url, wp_make_link_relative( content_url() ) )
	) {
		$is_translatable = false;
	}

	// Check if URL is excluded in option page
	if ( $is_translatable ) {
		$url_exclude = wplng_get_url_exclude();

		foreach ( $url_exclude as $key => $url_exclude_element ) {
			if ( preg_match( '#' . $url_exclude_element . '#', $url ) ) {
				$is_translatable = false;
				break;
			}
		}
	}

	$is_translatable = apply_filters(
		'wplng_url_is_translatable',
		$is_translatable
	);

	return $is_translatable;
}


function wplng_get_url_exclude() {

	$url_exclude = explode(
		PHP_EOL,
		get_option( 'wplng_excluded_url' )
	);

	// Remove empty
	$url_exclude = array_values( array_filter( $url_exclude ) );

	// Remove duplicate
	$url_exclude = array_unique( $url_exclude );

	// Clear with esc_url
	foreach ( $url_exclude as $key => $url ) {
		$url_exclude[ $key ] = esc_url( $url );
	}

	$url_exclude = apply_filters(
		'wplng_url_exclude',
		$url_exclude
	);

	return $url_exclude;
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
	$url = ( empty( $_SERVER['HTTPS'] ) ? 'http' : 'https' ) . "://$_SERVER[HTTP_HOST]$wplng_request_uri";
	return $url;
}


function wplng_get_url_current_for_language( $language_id ) {

	global $wplng_request_uri;
	$path = wplng_get_url_original( $wplng_request_uri );

	if ( wplng_get_language_website_id() !== $language_id ) {
		$path = '/' . $language_id . $path;
	}

	$url = ( empty( $_SERVER['HTTPS'] ) ? 'http' : 'https' ) . "://$_SERVER[HTTP_HOST]$path";

	return $url;
}
