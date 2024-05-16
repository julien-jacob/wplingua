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

	// Check if URL is translatable (exclude /admin/, ...)
	if ( ! wplng_url_is_translatable( $url ) ) {
		return $url;
	}

	// Check if it's an WooCommece AJAX URL
	if ( str_contains( $url, '?wc-ajax=' ) ) {
		return $url;
	}

	// Check if URL is an anchor link for the current page
	if ( '#' === substr( $url, 0, 1 ) ) {
		return $url;
	}

	$languages_target = wplng_get_languages_target();

	if ( '' === $language_target_id ) {
		$language_target_id = wplng_get_language_current_id();
	}

	$preg_domain     = '';
	$parsed_url_home = wp_parse_url( home_url() );

	if ( isset( $parsed_url_home['host'] )
		&& is_string( $parsed_url_home['host'] )
	) {
		$preg_domain = preg_quote( $parsed_url_home['host'] );
	}

	if ( ! empty( $preg_domain )
		&& preg_match( '#^(http:\/\/|https:\/\/)?' . $preg_domain . '(.*)$#', $url )
	) {

		// Check if URL is already translated
		foreach ( $languages_target as $language_target ) {
			if ( str_contains( $url, '/' . $language_target['id'] . '/' ) ) {
				return $url;
			}
		}

		$url = preg_replace(
			'#^(http:\/\/|https:\/\/)?' . $preg_domain . '(.*)$#',
			'${1}' . $parsed_url_home['host'] . '/' . $language_target_id . '${2}',
			$url
		);

		$parsed_url = wp_parse_url( $url );

		if ( empty( $parsed_url['fragment'] ) ) {
			// Add slash at the end if is not an anchor link
			$url = trailingslashit( $url );
		}
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
	if ( '' === $url ) {
		$url = sanitize_url( $wplng_request_uri );
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

	// Exclude files URL
	$regex_is_file = '#\.(avi|css|doc|exe|gif|html|jfif|jpg|jpeg|mid|midi|mp3|mpg|mpeg|mov|qt|pdf|png|ram|rar|tiff|txt|wav|zip|ico)$#Uis';
	if ( $is_translatable && preg_match( $regex_is_file, $url ) ) {
		$is_translatable = false;
	}

	// Check if URL is excluded in option page
	if ( $is_translatable ) {

		$url_original      = wplng_get_url_original( $url );
		$url_exclude_regex = wplng_get_url_exclude_regex();

		foreach ( $url_exclude_regex as $regex ) {
			if ( preg_match( $regex, $url_original ) ) {
				$is_translatable = false;
				break;
			}
		}
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

	$url_exclude = array();

	// Get, check and sanitize excluded URL REGEX as string
	$option = get_option( 'wplng_excluded_url' );
	if ( empty( $option ) || ! is_string( $option ) ) {
		$option = '';
	} else {
		$option = sanitize_textarea_field( $option );
	}

	// Get exclude URL REGEX as array
	$option = explode( PHP_EOL, $option );

	// Check each URL REGEX
	foreach ( $option as $regex ) {
		$regex = trim( $regex );
		if ( '' !== $regex ) {
			$url_exclude[] = '#' . $regex . '#';
		}
	}

	// Remove duplicate
	$url_exclude = array_unique( $url_exclude );

	// Apply wplng_url_exclude_regex filter
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

	if ( '' === $url ) {
		$url = wplng_get_url_current();
	}

	$target = wplng_get_languages_target_ids();

	foreach ( $target as $target_id ) {
		$url = str_replace( '/' . $target_id . '/', '/', $url );
	}

	$url = esc_url_raw( $url );

	$url = apply_filters(
		'wplng_url_original',
		$url
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
	return home_url( $wplng_request_uri );
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

	if ( wplng_url_is_translatable( $path )
		&& ( wplng_get_language_website_id() !== $language_id )
	) {
		$path = '/' . $language_id . $path;
	}

	return home_url( $path );
}
