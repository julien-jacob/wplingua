<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function mcv_get_language_by_ids( $language_ids ) {
	$all_languages = mcv_get_all_languages();
	$languages = array();

	foreach ($language_ids as $key => $language_id) {
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
	return array(
		array(
			'name' => 'French',
			'id'   => 'fr',
			'flag' => array(
				'France',
				'Belgium',
			),
		),
		array(
			'name' => 'English',
			'id'   => 'en',
			'flag' => array(
				'United Kingdom',
				'United States',
			),
		),
		array(
			'name' => 'Spain',
			'id'   => 'es',
			'flag' => array(
				'Spain',
				'Mexico',
			),
		),
		array(
			'name' => 'Portuguese',
			'id'   => 'pt',
			'flag' => array(
				'Portugal',
				'Brazil',
			),
		),
	);
}


function mcv_all_language_json() {
	return json_encode(mcv_get_all_languages());
}