<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_get_language_website() {
	return wplng_get_language_by_id( wplng_get_language_website_id() );
}

function wplng_get_language_website_id() {

	$language_api = wplng_get_api_language_website();

	if ('all' === $language_api) {
		$language_id = get_option( 'wplng_website_language' );
	} elseif (false === $language_api) {
		return 'en';
	} else {
		$language_id = wplng_get_api_language_website();
	}

	if ( ! wplng_is_valid_language_id( $language_id ) ) {
		return 'en';
	}

	return $language_id;
}


function wplng_get_language_website_flag() {

	$website_flag = get_option( 'wplng_website_flag' );

	if ( empty( $website_flag ) ) {
		$website_flag = wplng_get_language_by_id( 'en' );
		if ( ! empty( $website_flag['flags'][0]['flag'] ) ) {
			$website_flag = $website_flag['flags'][0]['flag'];
		} else {
			$website_flag = ''; // Return empty string if no valid flag
		}
	}

	$website_flag = apply_filters( 'wplng_language_website_flag', $website_flag );

	// error_log(var_export($website_flag, true));
	// return 'en';

	return $website_flag;
}


function wplng_get_language_emoji( $language ) {

	// if $language is a language array, return emoji
	if ( ! empty( $language['emoji'] ) ) {
		return esc_html( $language['emoji'] );
	}

	// $language is a language ID
	// convert language ID to language array
	$language = wplng_get_language_by_id( $language );

	// If $language is not a valid, return empty string
	if ( false === $language ) {
		return '';
	}

	// If emoji is valid, return the emoji
	if ( ! empty( $language['emoji'] ) ) {
		return esc_html( $language['emoji'] );
	}

	// If no emoji returned here, return empty string
	return '';
}


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


function wplng_get_languages_target_simplified() {

	$json = get_option( 'wplng_target_languages' );

	if ( empty( $json ) || ! is_string($json) ) {
		$json = '[]';
	}

	$languages_target = json_decode( $json, true );
	$all_languages    = wplng_get_languages_all();
	$ordered          = array();

	foreach ( $all_languages as $key => $language ) {
		foreach ( $languages_target as $key => $language_target ) {
			if (
				! empty( $language['id'] )
				&& ! empty( $language_target['id'] )
				&& $language['id'] === $language_target['id']
			) {
				$ordered[] = $language_target;
			}
		}
	}

	return $ordered;
}


function wplng_get_languages_target() {

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

				// $languages_target_clear[] = array(
				// 	'id'   => $language_target['id'],
				// 	'flag' => $language_target['flag'],
				// );
			}
		}
	}

	return $languages_target_clear;
}

function wplng_get_languages_target_ids() {

	$languages_target     = wplng_get_languages_target();
	$languages_target_ids = array();

	foreach ( $languages_target as $key => $language_target ) {
		$languages_target_ids[] = $language_target['id'];
	}
	return $languages_target_ids;
}



function wplng_get_language_current_id() {

	global $wplng_request_uri;
	$current_path     = $wplng_request_uri;
	$languages_target = wplng_get_languages_target_ids();

	foreach ( $languages_target as $language ) {
		if ( str_starts_with( $current_path, '/' . $language . '/' ) ) {
			return $language;
			break;
		}
	}

	return wplng_get_language_website_id();
}



function wplng_get_language_by_ids( $language_ids ) {

	$all_languages = wplng_get_languages_all();
	$languages     = array();

	foreach ( $language_ids as $key => $language_id ) {
		foreach ( $all_languages as $key => $language ) {
			if ( ! empty( $language['id'] ) && $language['id'] === $language_id ) {
				$languages[] = $language;
				break;
			}
		}
	}

	return $languages;
}


function wplng_get_language_by_id( $language_id ) {

	$all_languages = wplng_get_languages_all();

	foreach ( $all_languages as $key => $language ) {
		if ( ! empty( $language['id'] ) && $language['id'] === $language_id ) {
			return $language;
		}
	}

	return false;
}


function wplng_is_valid_language_id( $language_id ) {

	// If $language_id format is not valid, return default data
	if ( empty( $language_id ) || strlen( $language_id ) !== 2 ) {
		return false;
	}

	// Check if $language_id is in languages data
	$languages_data = wplng_get_languages_data();
	foreach ( $languages_data as $key => $language_data ) {
		if ( $language_data['id'] === $language_id ) {
			return true;
		}
	}

	return false;
}


function wplng_get_languages_all() {

	$languages       = wplng_get_languages_data();
	$source_language = get_option( 'wplng_website_language' );
	$source_flag     = get_option( 'wplng_website_flag' );
	$target_flags    = get_option( 'wplng_target_languages' );

	// TODO : Remplacer par une ternaire
	if ( empty( $target_flags ) || ! is_string($target_flags) ) {
		$target_flags = '[]';
	}

	$target_flags = json_decode( $target_flags, true );

	if ( empty( $source_language ) ) {
		$source_language = 'en';
	}

	foreach ( $languages as $key => $language ) {

		$flags_style = wplng_get_switcher_flags_style() . '/';

		// Set custom website flag if defined
		if ( ! empty( $source_language )
			&& $language['id'] == $source_language
			&& ! empty( $source_flag )
		) {
			$languages[ $key ]['flag'] = $source_flag;
		} else {
			$languages[ $key ]['flag'] = plugins_url() . '/wplingua/images/' . $flags_style . $language['id'] . '.png';
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
			$languages[ $key ]['flags'][ $key_flag ]['flag'] = plugins_url() . '/wplingua/images/' . $flags_style . $flag['flag'] . '.png';
		}
	}

	// TODO : Ajouter un filtre ici

	return $languages;
}


function wplng_get_languages_all_json() {
	return json_encode( wplng_get_languages_all() );
}


function wplng_get_languages_allow() {
	$languages_alow = wplng_get_api_languages_target();
	$languages      = array();

	if ( 'all' === $languages_alow ) {
		return wplng_get_languages_all();
	} elseif ( false === $languages_alow || ! is_array( $languages_alow ) ) {
		return array();
	}

	foreach ( $languages_alow as $key => $language_id_alow ) {
		$languages[] = wplng_get_language_by_id( $language_id_alow );
	}

	return $languages;
}


function wplng_get_languages_data() {
	return array(
		array(
			'name'  => __( 'English', 'wplingua' ),
			'id'    => 'en',
			'flag'  => 'en',
			'emoji' => '🇬🇧',
			'flags' => array(
				array(
					'name'  => __( 'United Kingdom', 'wplingua' ),
					'id'    => 'en',
					'flag'  => 'en',
					'emoji' => '🇬🇧',
				),
				array(
					'name'  => __( 'United States', 'wplingua' ),
					'id'    => 'us',
					'flag'  => 'us',
					'emoji' => '🇺🇸',
				),
			),
		),
		array(
			'name'  => __( 'French', 'wplingua' ),
			'id'    => 'fr',
			'flag'  => 'fr',
			'emoji' => '🇫🇷',
			'flags' => array(
				array(
					'name'  => __( 'France', 'wplingua' ),
					'id'    => 'fr',
					'flag'  => 'fr',
					'emoji' => '🇫🇷',
				),
				array(
					'name'  => __( 'Belgium', 'wplingua' ),
					'id'    => 'be',
					'flag'  => 'be',
					'emoji' => '🇧🇪',
				),
			),
		),
		array(
			'name'  => __( 'German', 'wplingua' ),
			'id'    => 'de',
			'flag'  => 'de',
			'emoji' => '🇩🇪',
			'flags' => array(
				array(
					'name'  => __( 'Germany', 'wplingua' ),
					'id'    => 'de',
					'flag'  => 'de',
					'emoji' => '🇩🇪',
				),
			),
		),
		array(
			'name'  => __( 'Italian', 'wplingua' ),
			'id'    => 'it',
			'flag'  => 'it',
			'emoji' => '🇮🇹',
			'flags' => array(
				array(
					'name'  => __( 'Italy', 'wplingua' ),
					'id'    => 'it',
					'flag'  => 'it',
					'emoji' => '🇮🇹',
				),
			),
		),
		array(
			'name'  => __( 'Portuguese', 'wplingua' ),
			'id'    => 'pt',
			'flag'  => 'pt',
			'emoji' => '🇵🇹',
			'flags' => array(
				array(
					'name'  => __( 'Portugal', 'wplingua' ),
					'id'    => 'pt',
					'flag'  => 'pt',
					'emoji' => '🇵🇹',
				),
				array(
					'name'  => __( 'Brazil', 'wplingua' ),
					'id'    => 'br',
					'flag'  => 'br',
					'emoji' => '🇧🇷',
				),
			),
		),
		array(
			'name'  => __( 'Spanish', 'wplingua' ),
			'id'    => 'es',
			'flag'  => 'es',
			'emoji' => '🇪🇸',
			'flags' => array(
				array(
					'name'  => __( 'Spain', 'wplingua' ),
					'id'    => 'es',
					'flag'  => 'es',
					'emoji' => '🇪🇸',
				),
				array(
					'name'  => __( 'Mexico', 'wplingua' ),
					'id'    => 'mx',
					'flag'  => 'mx',
					'emoji' => '🇲🇽',
				),
			),
		),
		array(
			'name'  => __( 'Japanese', 'wplingua' ),
			'id'    => 'ja',
			'flag'  => 'ja',
			'emoji' => '🇯🇵',
			'flags' => array(
				array(
					'name'  => __( 'Japan', 'wplingua' ),
					'id'    => 'ja',
					'flag'  => 'ja',
					'emoji' => '🇯🇵',
				),
			),
		),
		array(
			'name'  => __( 'Russian', 'wplingua' ),
			'id'    => 'ru',
			'flag'  => 'ru',
			'emoji' => '🇷🇺',
			'flags' => array(
				array(
					'name'  => __( 'Russia', 'wplingua' ),
					'id'    => 'ru',
					'flag'  => 'ru',
					'emoji' => '🇷🇺',
				),
			),
		),
		array(
			'name'  => __( 'Chinese', 'wplingua' ),
			'id'    => 'zh',
			'flag'  => 'zh',
			'emoji' => '🇨🇳',
			'flags' => array(
				array(
					'name'  => __( 'China', 'wplingua' ),
					'id'    => 'zh',
					'flag'  => 'zh',
					'emoji' => '🇨🇳',
				),
				array(
					'name'  => __( 'Hong Kong', 'wplingua' ),
					'id'    => 'hk',
					'flag'  => 'hk',
					'emoji' => '🇭🇰',
				),
			),
		),
	);
}
