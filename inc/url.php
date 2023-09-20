<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Get the translated URL
 *
 * @param string $url
 * @param string $language_target_id
 * @return string
 */
function wplng_url_translate( $url, $language_target_id = '' ) {

	// Check if URL is an empty string
	if ( '' === $url ) {
		return '';
	}

	// Check if URL is translatable (exclude /admin/...)
	if ( ! wplng_url_is_translatable( $url ) ) {
		return $url;
	}

	if ( str_contains( $url, '?wc-ajax=' ) ) {
		return $url;
	}

	// Check if URL is an anchor link for the current page
	if ( '#' === substr( $url, 0, 1 ) ) {
		return $url;
	}

	$domain           = preg_quote( $_SERVER['HTTP_HOST'] );
	$languages_target = wplng_get_languages_target();

	if ( '' === $language_target_id ) {
		$language_target_id = wplng_get_language_current_id();
	}

	if ( preg_match( '#^(http:\/\/|https:\/\/)?' . $domain . '(.*)$#', $url ) ) {

		// Check if URL is already translated
		foreach ( $languages_target as $language_target ) {
			if ( str_contains( $url, '/' . $language_target['id'] . '/' ) ) {
				return $url;
			}
		}

		$url = preg_replace(
			'#^(http:\/\/|https:\/\/)?' . $domain . '(.*)$#',
			'$1' . $domain . '/' . $language_target_id . '$2',
			$url
		);

		$url = esc_url( trailingslashit( $url ) );

	} elseif ( preg_match( '#^[^\/]+\/[^\/].*$|^\/[^\/].*$#', $url ) ) {

		// Check if URL is already translated
		foreach ( $languages_target as $language_target ) {
			if ( substr( $url, 0, 4 ) == '/' . $language_target['id'] . '/'
				|| substr( $url, 0, 3 ) == $language_target['id'] . '/'
			) {
				return $url;
			}
		}

		if ( '/' === substr( $url, 0, 1 ) ) {
			$url = '/' . $language_target_id . $url;
		} else {
			$url = $language_target_id . '/' . $url;
		}
	}

	$url = apply_filters(
		'wplng_url_translate',
		$url
	);

	return $url;
}


/**
 * Return true is $url is translatable
 *
 * @param string $url
 * @return bool
 */
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

	// Check if is wp-comments-post.php
	if ( $is_translatable
		&& str_contains( $url, 'wp-comments-post.php' )
	) {
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

		$url_exclude_regex = wplng_get_url_exclude_regex();

		foreach ( $url_exclude_regex as $regex ) {
			if ( preg_match( $regex, $url ) ) {
				$is_translatable = false;
				break;
			}
		}
	}

	// Exclude files URL
	$regex_is_file = '#\.(avi|css|doc|exe|gif|html|jpg|jpeg|mid|midi|mp3|mpg|mpeg|mov|qt|pdf|png|ram|rar|tiff|txt|wav|zip|ico)$#Uis';
	if ( $is_translatable && preg_match( $regex_is_file, $url ) ) {
		$is_translatable = false;
	}

	$is_translatable = apply_filters(
		'wplng_url_is_translatable',
		$is_translatable,
		$url
	);

	return $is_translatable;
}


/**
 * Get the REGEX list of excluded URLs
 *
 * @return array
 */
function wplng_get_url_exclude_regex() {

	// Get user excluded URLs
	$url_exclude = explode(
		PHP_EOL,
		get_option( 'wplng_excluded_url' )
	);

	// Add delimiter
	foreach ( $url_exclude as $key => $url ) {
		if ( ! empty( $url ) ) {
			$url_exclude[ $key ] = '#' . $url . '#';
		} else {
			unset( $url_exclude[ $key ] );
		}
	}

	// Remove empty
	$url_exclude = array_values( array_filter( $url_exclude ) );

	// Remove duplicate
	$url_exclude = array_unique( $url_exclude );

	$url_exclude = apply_filters(
		'wplng_url_exclude_regex',
		$url_exclude
	);

	return $url_exclude;
}


/**
 * Get the untranslated / original URL
 *
 * @param string $url
 * @return string
 */
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


/**
 * Get current URL
 *
 * @return string
 */
function wplng_get_url_current() {
	global $wplng_request_uri;
	$url = ( empty( $_SERVER['HTTPS'] ) ? 'http' : 'https' ) . "://$_SERVER[HTTP_HOST]$wplng_request_uri";
	return $url;
}


/**
 * Get the URL translated in specified language
 *
 * @param string $language_id
 * @return string
 */
function wplng_get_url_current_for_language( $language_id ) {

	global $wplng_request_uri;
	$path = wplng_get_url_original( $wplng_request_uri );

	if ( wplng_get_language_website_id() !== $language_id ) {
		$path = '/' . $language_id . $path;
	}

	$url = ( empty( $_SERVER['HTTPS'] ) ? 'http' : 'https' ) . "://$_SERVER[HTTP_HOST]$path";

	return $url;
}
