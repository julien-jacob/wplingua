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

		/**
		 * It's an URL starting y a domain name
		 */

		// Check if URL is already translated
		foreach ( $languages_target as $language_target ) {
			if ( wplng_str_contains( $url, '/' . $language_target['id'] . '/' ) ) {
				return $url;
			}
		}

		$parsed_url = wp_parse_url( $url );

		if ( ! empty( $parsed_url['path'] ) ) {
			$url = str_replace(
				$parsed_url['path'],
				wplng_slug_translate_path(
					$parsed_url['path'],
					$language_target_id
				),
				$url
			);
		}

		$url = preg_replace(
			'#^(http:\/\/|https:\/\/)?' . $preg_domain . '(.*)$#',
			'${1}' . $parsed_url_home['host'] . '/' . $language_target_id . '${2}',
			$url
		);

		if ( empty( $parsed_url['fragment'] )
			&& empty( $parsed_url['query'] )
		) {
			// Add slash at the end if is not an anchor link
			$url = trailingslashit( $url );
		}
	} elseif ( preg_match( '#^[^\/]+\/[^\/].*$|^\/[^\/].*$#', $url ) ) {

		/**
		 * It's a path
		 */

		// Check if URL is already translated
		foreach ( $languages_target as $language_target ) {
			if ( substr( $url, 0, 4 ) == '/' . $language_target['id'] . '/'
				|| substr( $url, 0, 3 ) == $language_target['id'] . '/'
			) {
				return $url;
			}
		}

		$url = wplng_slug_translate_path(
			$url,
			$language_target_id
		);

		if ( wplng_str_starts_with( $url, '/' ) ) {
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

	$url = trailingslashit( $url );
	$url = wp_make_link_relative( $url );

	// Check if is an admin page
	if ( wplng_str_contains( $url, wp_make_link_relative( get_admin_url() ) ) ) {
		$is_translatable = false;
	}

	// Check if URL is an anchor link for the current page
	if ( $is_translatable
		&& '#' === substr( $url, 0, 1 ) 
	) {
		return $url;
	}

	if ( $is_translatable
		&& (
			wplng_str_contains( $url, 'wp-login.php' )
			|| wplng_str_ends_with( $url, '/feed/' )
			|| wplng_str_contains( $url, 'wp-comments-post.php' )
			|| wplng_str_contains( $url, 'wp-register.php' )
			|| wplng_str_contains( $url, '/wp-json/' )
			|| wplng_str_contains( $url, '/wp-includes/' )
			|| wplng_str_contains( $url, '?wc-ajax=' ) 
		)
	) {
		$is_translatable = false;
	}



	// Check if is Divi editor
	if ( $is_translatable
		&& (
			wplng_str_contains( $url, '/?et_fb=1' )
			|| wplng_str_contains( $url, '&et_fb=1' )
		)
		&& current_user_can( 'edit_posts' )
	) {
		$is_translatable = false;
	}

	// Check if is in wp-uploads
	if ( $is_translatable
		&& wplng_str_contains( $url, wp_make_link_relative( content_url() ) )
	) {
		$is_translatable = false;
	}

	// Exclude files URL
	$regex_is_file = '#\.(avi|css|doc|exe|gif|html|jfif|jpg|jpeg|mid|midi|mp3|mpg|mpeg|mov|qt|pdf|png|ram|rar|tiff|txt|wav|zip|ico)$#Uis';
	if ( $is_translatable
		&& preg_match( $regex_is_file, $url )
	) {
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

	global $wplng_url_exclude_regex;

	if ( null !== $wplng_url_exclude_regex ) {
		return $wplng_url_exclude_regex;
	}

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

	$wplng_url_exclude_regex = $url_exclude;

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

		if ( ! wplng_str_contains( $url, '/' . $target_id . '/' ) ) {
			continue;
		}

		$url = str_replace(
			'/' . $target_id . '/',
			'/',
			$url
		);

		$parsed_url = wp_parse_url( $url );

		if ( isset( $parsed_url['path'] )
			&& $parsed_url['path'] !== ''
			&& $parsed_url['path'] !== '/'
		) {

			$url = str_replace(
				$parsed_url['path'],
				wplng_slug_original_path(
					$parsed_url['path'],
					$target_id
				),
				$url
			);
		}

		break;
	}

	$url = apply_filters(
		'wplng_url_original',
		$url
	);

	return $url;
}


/**
 * Get current path
 *
 * @return string
 */
function wplng_get_path_current() {
	global $wplng_request_uri;
	return $wplng_request_uri;
}


/**
 * Get current URL
 *
 * @return string
 */
function wplng_get_url_current() {
	return home_url( wplng_get_path_current() );
}


/**
 * Get the URL translated in specified language
 *
 * @param string $language_id
 * @return string
 */
function wplng_get_url_current_for_language( $language_id ) {
	return wplng_url_translate(
		wplng_get_url_original(),
		$language_id
	);
}
