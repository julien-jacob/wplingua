<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function mcv_get_language_by_ids( $language_ids ) {
	$all_languages = mcv_get_all_languages();
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
	$all_languages = mcv_get_all_languages();

	foreach ( $all_languages as $key => $language ) {
		if ( ! empty( $language['id'] ) && $language['id'] === $language_id ) {
			return $language;
		}
	}

	return false;
}

function mcv_get_all_languages() {
	$languages = array(
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
					'id'   => 'pt',
					'flag' => 'br',
				),
			),
		),
	);

	foreach ( $languages as $key => $language ) {
		$languages[ $key ]['flag'] = plugins_url() . '/machiavel/images/rounded/' . $language['id'] . '.png';
		foreach ( $languages[ $key ]['flags'] as $key_flag => $flag ) {
			$languages[ $key ]['flags'][ $key_flag ]['flag'] = plugins_url() . '/machiavel/images/rounded/' . $flag['flag'] . '.png';
		}
	}

	return $languages;
}


function mcv_all_language_json() {
	return json_encode( mcv_get_all_languages() );
}
