<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function mcv_get_language_website() {
	return mcv_get_language_by_id(mcv_get_language_website_id());
}

function mcv_get_language_website_id() {

	$language_id = get_option( 'mcv_website_language' );

	if ( ! mcv_is_valid_language_id( $language_id ) ) {
		return 'en';
	}

	// TODO : Ajouter un filtre ici

	return $language_id;
}


function mcv_get_language_website_flag() {

	$website_flag = get_option( 'mcv_website_flag' );

	// TODO : Ajouter un filtre ici
	// TODO : Mettre valeur par défaut si vide ?

	return $website_flag;
}


function mcv_get_languages_target_simplified() {

	$json = get_option( 'mcv_target_languages' );

	if ( empty( $json ) ) {
		$json = '[]';
	}

	$languages_target = json_decode( $json, true );

	return $languages_target;
}


function mcv_get_languages_target() {

	$languages_target       = mcv_get_languages_target_simplified();
	$languages_target_clear = array();

	// Check each $languages_target
	foreach ( $languages_target as $key => $language_target ) {
		// Check languages target format
		if ( ! empty( $language_target['id'] ) && isset( $language_target['flag'] ) ) {
			// Check if language is valid
			if ( mcv_is_valid_language_id( $language_target['id'] ) ) {
				$language = mcv_get_language_by_id($language_target['id']);
				if (false !== $language) {
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

function mcv_get_languages_target_ids() {

	$languages_target     = mcv_get_languages_target();
	$languages_target_ids = array();

	foreach ( $languages_target as $key => $language_target ) {
		$languages_target_ids[] = $language_target['id'];
	}
	return $languages_target_ids;
}



function mcv_get_language_current_id() {

	global $mcv_request_uri;
	$current_path         = $mcv_request_uri;
	
	$mcv_language_target  = false;
	$mcv_languages_target = mcv_get_languages_target_ids();

	foreach ( $mcv_languages_target as $language ) {
		if ( str_starts_with( $current_path, '/' . $language . '/' ) ) {
			return $language;
			break;
		}
	}

	return $mcv_language_target;
}



function mcv_get_language_by_ids( $language_ids ) {

	$all_languages = mcv_get_languages_all();
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


function mcv_get_language_by_id( $language_id ) {

	$all_languages = mcv_get_languages_all();

	foreach ( $all_languages as $key => $language ) {
		if ( ! empty( $language['id'] ) && $language['id'] === $language_id ) {
			return $language;
		}
	}

	return false;
}


function mcv_is_valid_language_id( $language_id ) {

	// If $language_id format is not valid, return default data
	if ( empty( $language_id ) || strlen( $language_id ) !== 2 ) {
		return false;
	}

	// Check if $language_id is in languages data
	$languages_data = mcv_get_languages_data();
	foreach ( $languages_data as $key => $language_data ) {
		if ( $language_data['id'] === $language_id ) {
			return true;
		}
	}

	return false;
}


function mcv_get_languages_all() {

	


	$languages       = mcv_get_languages_data();
	// $source_language = mcv_get_language_website_id();
	$source_flag     = get_option( 'mcv_website_flag' );
	// $target_flags    = mcv_get_languages_target();

	$target_flags = get_option( 'mcv_website_language' );

	$target_flags = get_option( 'mcv_target_languages' );

	if ( empty( $target_flags ) ) {
		$target_flags = '[]';
	}

	$target_flags = json_decode( $target_flags, true );



	foreach ( $languages as $key => $language ) {

		// Set custom website flag if defined
		if ( !empty($source_language) 
			&& $language['id'] == $source_language 
			&& ! empty( $source_flag ) 
		) {
			$languages[ $key ]['flag'] = $source_flag;
		} else {
			$languages[ $key ]['flag'] = plugins_url() . '/machiavel/images/rounded/' . $language['id'] . '.png';
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
			$languages[ $key ]['flags'][ $key_flag ]['flag'] = plugins_url() . '/machiavel/images/rounded/' . $flag['flag'] . '.png';
		}
	}

	// TODO : Ajouter un filtre ici

	return $languages;
}


function mcv_get_languages_all_json() {
	return json_encode( mcv_get_languages_all() );
}


function mcv_get_languages_data() {
	return array(
		array(
			'name'  => 'French',
			'id'    => 'fr',
			'flag'  => 'fr',
			'flags' => array(
				array(
					'name' => 'France',
					'id'   => 'fr',
					'flag' => 'fr',
				),
				array(
					'name' => 'Belgium',
					'id'   => 'be',
					'flag' => 'be',
				),
			),
		),
		array(
			'name'  => 'English',
			'id'    => 'en',
			'flag'  => 'en',
			'flags' => array(
				array(
					'name' => 'United Kingdom',
					'id'   => 'en',
					'flag' => 'en',
				),
				array(
					'name' => 'United States',
					'id'   => 'us',
					'flag' => 'us',
				),
			),
		),
		array(
			'name'  => 'Spain',
			'id'    => 'es',
			'flag'  => 'es',
			'flags' => array(
				array(
					'name' => 'Spain',
					'id'   => 'es',
					'flag' => 'es',
				),
				array(
					'name' => 'Mexico',
					'id'   => 'mx',
					'flag' => 'mx',
				),
			),
		),
		array(
			'name'  => 'Portuguese',
			'id'    => 'pt',
			'flag'  => 'pt',
			'flags' => array(
				array(
					'name' => 'Portugal',
					'id'   => 'pt',
					'flag' => 'pt',
				),
				array(
					'name' => 'Brazil',
					'id'   => 'br',
					'flag' => 'br',
				),
			),
		),
	);
}