<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Get website language data array
 *
 * @return array Language data
 */
function wplng_get_language_website() {

	global $wplng_language_website;

	if ( null != $wplng_language_website ) {
		return $wplng_language_website;
	}

	$language = wplng_get_language_by_id(
		wplng_get_language_website_id()
	);

	$wplng_language_website = $language;

	return $language;
}


/**
 * Get the website language ID
 *
 * @return string Language ID
 */
function wplng_get_language_website_id() {

	$language_api = wplng_get_api_language_website();

	if ( 'all' === $language_api ) {
		$language_id = get_option( 'wplng_website_language' );
	} elseif ( false === $language_api ) {
		return 'en';
	} else {
		$language_id = wplng_get_api_language_website();
	}

	if ( ! wplng_is_valid_language_id( $language_id ) ) {
		return 'en';
	}

	return $language_id;
}


/**
 * Get the URL of website language flag image
 *
 * @return string URL flag image
 */
function wplng_get_language_website_flag() {

	$website_flag = get_option( 'wplng_website_flag' );

	if ( empty( $website_flag ) ) {

		$website_flag = wplng_get_language_by_id(
			wplng_get_language_website_id()
		);

		if ( ! empty( $website_flag['flags'][0]['flag'] ) ) {
			$website_flag = $website_flag['flags'][0]['flag'];
		} else {
			$website_flag = ''; // Return empty string if no valid flag
		}
	}

	$website_flag = apply_filters( 'wplng_language_website_flag', $website_flag );

	return esc_url( $website_flag );
}


/**
 * Get language name from language ID or data
 *
 * @param mixed $language
 * @return string
 */
function wplng_get_language_name( $language ) {

	// if $language is a language array, return name
	if ( ! empty( $language['name'] ) ) {
		return esc_html( $language['name'] );
	}

	// $language is a language ID
	// convert language ID to language array
	$language = wplng_get_language_by_id( $language );

	// If $language is not a valid, return empty string
	if ( false === $language ) {
		return '';
	}

	// If name is valid, return the name
	if ( ! empty( $language['name'] ) ) {
		return esc_html( $language['name'] );
	}

	// If no name returned here, return empty string
	return '';
}


/**
 * Get language ID from language ID or data
 *
 * @param mixed $language
 * @return string Language ID
 */
function wplng_get_language_id( $language ) {

	// If $language is a language array
	if ( ! empty( $language['id'] )
		&& wplng_is_valid_language_id( $language['id'] )
	) {
		return $language['id'];
	}

	// If $language is a language ID, get language array
	$language = wplng_get_language_by_id( $language );

	// If language ID is invalid, return a default value
	if ( false === $language || empty( $language['id'] ) ) {
		return 'en';
	}

	return $language['id'];
}


/**
 * Get language name translated from language ID or data
 *
 * @param mixed  $language Language ID or data
 * @param string $language_target Language ID
 * @return string Language name translated
 */
function wplng_get_language_name_translated( $language, $language_target = '' ) {

	// Get target language ID
	if ( empty( $language_target ) ) {
		$language_target = wplng_get_language_current_id();
	} else {
		$language_target_id = wplng_get_language_id( $language_target );
	}

	// Get language array
	if ( empty( $language['id'] ) ) {
		$language = wplng_get_language_by_id( $language );
	}

	$translated_language_name = '';

	if ( ! empty( $language['name_translation'][ $language_target_id ] ) ) {
		$translated_language_name = esc_html( $language['name_translation'][ $language_target_id ] );
	} else {
		$translated_language_name = wplng_get_language_name( $language );
	}

	return esc_html( $translated_language_name );
}


function wplng_get_language_name_untranslated( $language ) {
	return wplng_get_language_name_translated( $language, $language );
}


/**
 * Return a JSON with languages data simplified
 *
 * @return array Languages data simplified
 */
function wplng_get_languages_target_simplified() {

	global $wplng_languages_target_simplified;

	if ( null !== $wplng_languages_target_simplified ) {
		return $wplng_languages_target_simplified;
	}

	$json = get_option( 'wplng_target_languages' );

	if ( empty( $json ) || ! is_string( $json ) ) {
		$json = '[]';
	}

	$languages_target = json_decode( $json, true );
	$all_languages    = wplng_get_languages_allow();
	$ordered          = array();

	foreach ( $all_languages as $language ) {
		foreach ( $languages_target as $language_target ) {
			if ( empty( $language['id'] )
				|| empty( $language_target['id'] )
				|| $language['id'] !== $language_target['id']
				|| ( ! empty( $language_target['private'] )
					&& ! current_user_can( 'edit_posts' )
				)
			) {
				continue;
			}

			$ordered[] = $language_target;
		}
	}

	$wplng_languages_target_simplified = $ordered;

	return $ordered;
}


/**
 * Get target languages data
 *
 * @return array
 */
function wplng_get_languages_target() {

	global $wplng_languages_target;

	if ( null !== $wplng_languages_target ) {
		return $wplng_languages_target;
	}

	$languages_target       = wplng_get_languages_target_simplified();
	$languages_target_clear = array();

	// Check each $languages_target
	foreach ( $languages_target as $key => $language_target ) {
		// Check languages target format
		if ( ! empty( $language_target['id'] ) && isset( $language_target['flag'] ) ) {
			// Check if language is valid
			if ( wplng_is_valid_language_id( $language_target['id'] ) ) {
				$language = wplng_get_language_by_id( $language_target['id'] );
				if ( false !== $language ) {
					$languages_target_clear[] = $language;
				}
			}
		}
	}

	$wplng_languages_target = $languages_target_clear;

	return $languages_target_clear;
}


/**
 * Get target languages IDs
 *
 * @return array
 */
function wplng_get_languages_target_ids() {

	if ( ! empty( $languages ) ) {
		return $languages;
	}

	$languages_target     = wplng_get_languages_target();
	$languages_target_ids = array();

	foreach ( $languages_target as $language_target ) {
		$languages_target_ids[] = $language_target['id'];
	}

	return $languages_target_ids;
}


/**
 * Get current language ID
 *
 * @return string
 */
function wplng_get_language_current_id() {

	global $wplng_language_current_id;

	if ( null !== $wplng_language_current_id ) {
		return $wplng_language_current_id;
	}

	global $wplng_request_uri;
	$current_path     = $wplng_request_uri;
	$languages_target = wplng_get_languages_target_ids();

	if ( empty( $current_path )
		|| ! is_string( $current_path )
	) {
		return wplng_get_language_website_id();
	}

	foreach ( $languages_target as $language ) {
		if ( substr( $current_path, 0, 4 ) === '/' . $language . '/'
			|| substr( $current_path, 0, 3 ) === $language . '/'
		) {

			$wplng_language_current_id = $language;

			return $language;
		}
	}

	return wplng_get_language_website_id();
}


/**
 * Get languages data from languages IDs list
 *
 * @param array $language_ids
 * @return array
 */
function wplng_get_languages_by_ids( $language_ids ) {

	$all_languages = wplng_get_languages_all();
	$languages     = array();

	foreach ( $language_ids as $language_id ) {
		foreach ( $all_languages as $language ) {
			if ( ! empty( $language['id'] ) && $language['id'] === $language_id ) {
				$languages[] = $language;
				break;
			}
		}
	}

	return $languages;
}


/**
 * Get language data from ID
 *
 * @param string $language_id
 * @return array
 */
function wplng_get_language_by_id( $language_id ) {

	$all_languages = wplng_get_languages_all();

	foreach ( $all_languages as $language ) {
		if ( ! empty( $language['id'] ) && $language['id'] === $language_id ) {
			return $language;
		}
	}

	// Return a default value if $language_id not exist
	return wplng_get_language_by_id( 'en' );
}


/**
 * Check if a language ID is valid
 *
 * @param string $language_id
 * @return bool
 */
function wplng_is_valid_language_id( $language_id ) {

	// If $language_id format is not valid, return default data
	if ( empty( $language_id )
		|| ! is_string( $language_id )
		|| strlen( $language_id ) !== 2
	) {
		return false;
	}

	// wplng_data_languages_ids
	$languages_data_id = wplng_data_languages_id();
	foreach ( $languages_data_id as $language_data_id ) {
		if ( $language_data_id === $language_id ) {
			return true;
		}
	}

	return false;
}


/**
 * Check if a list of languages ID are valid
 *
 * @param array $language_id_list
 * @return bool Languages ID are valid
 */
function wplng_is_valid_language_ids( $language_id_list ) {

	if ( ! is_array( $language_id_list ) ) {
		return false;
	}

	$languages_data_id = wplng_data_languages_id();

	foreach ( $language_id_list as $language_id ) {

		// If $language_id format is not valid, return default data
		if ( empty( $language_id )
			|| ! is_string( $language_id )
			|| strlen( $language_id ) !== 2
		) {
			return false;
		}

		// If language ID not exist
		$is_valid_language = false;
		foreach ( $languages_data_id as $language_data_id ) {
			if ( $language_data_id === $language_id ) {
				$is_valid_language = true;
				break;
			}
		}

		if ( false === $is_valid_language ) {
			return false;
		}
	}

	return true;
}


/**
 * Get data of all languages
 *
 * @return array
 */
function wplng_get_languages_all() {

	global $wplng_languages_all;

	if ( null !== $wplng_languages_all ) {
		return $wplng_languages_all;
	}

	$languages       = wplng_data_languages();
	$source_language = get_option( 'wplng_website_language' );
	$source_flag     = get_option( 'wplng_website_flag' );
	$target_flags    = get_option( 'wplng_target_languages' );

	if ( empty( $target_flags ) || ! is_string( $target_flags ) ) {
		$target_flags = '[]';
	}

	$target_flags = json_decode( $target_flags, true );

	if ( empty( $source_language ) ) {
		$source_language = 'en';
	}

	foreach ( $languages as $key => $language ) {

		$flags_style = wplng_get_switcher_flags_style() . '/';

		if ( 'none/' === $flags_style ) {
			$flags_style = 'rectangular/';
		}

		// Set custom website flag if defined
		if ( ! empty( $source_language )
			&& $language['id'] == $source_language
			&& ! empty( $source_flag )
		) {
			$languages[ $key ]['flag'] = $source_flag;
		} else {
			$languages[ $key ]['flag'] = plugins_url() . '/wplingua/assets/images/' . $flags_style . $language['flag'] . '.png';
		}

		// Set custom target flag if defined
		foreach ( $target_flags as $target_key => $target_flag ) {
			if ( ! empty( $target_flag['id'] ) && $target_flag['id'] == $language['id'] ) {
				$languages[ $key ]['flag'] = $target_flag['flag'];
				break;
			}
		}

		// Transform flags to URL
		foreach ( $languages[ $key ]['flags'] as $key_flag => $flag ) {
			$languages[ $key ]['flags'][ $key_flag ]['flag'] = plugins_url() . '/wplingua/assets/images/' . $flags_style . $flag['flag'] . '.png';
		}

		// Add default DIR as LTR if not specified or invalid
		if ( empty( $language['dir'] )
			|| 'rtl' !== $language['dir']
		) {
			$languages[ $key ]['dir'] = 'ltr';
		}
	}

	$wplng_languages_all = $languages;

	return $languages;
}


/**
 * Get data of all languages allowed
 *
 * @return array
 */
function wplng_get_languages_allow() {

	global $wplng_languages_allow;

	if ( null !== $wplng_languages_allow ) {
		return $wplng_languages_allow;
	}

	$languages_alow = wplng_get_api_languages_target();
	$languages      = array();

	if ( 'all' === $languages_alow ) {
		return wplng_get_languages_all();
	} elseif ( empty( $languages_alow ) || ! is_array( $languages_alow ) ) {
		return array();
	} else {
		foreach ( $languages_alow as $language_id_alow ) {
			$languages[] = wplng_get_language_by_id( $language_id_alow );
		}

		$wplng_languages_allow = $languages;
	}

	return $languages;
}
