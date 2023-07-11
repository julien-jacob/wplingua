<?php

if ( ! defined( 'WPLINGUA_API' ) ) {
	die;
}


function wplngapi_get_all_languages_data() {
	return array(
		array(
			'name' => 'French',
			'id'   => 'fr'
		),
		array(
			'name' => 'English',
			'id'   => 'fr'
		),
		array(
			'name' => 'Spain',
			'id'   => 'es'
		),
		array(
			'name' => 'Portuguese',
			'id'   => 'pt'
		),
	);
}


function wplngapi_get_all_languages_ids() {

	$languages_data = wplngapi_get_all_languages_data();
	$languages_ids  = array();

	foreach ( $languages_data as $key => $data ) {
		if ( ! empty( $data['id'] ) ) {
			$languages_ids[] = $data['id'];
		}
	}

	return $languages_ids;

}


function wplngapi_is_valide_language_id( $id = '' ) {

	if ( empty( $id ) ) {
		return false;
	}

	$languages_data = wplngapi_get_all_languages_data();

	foreach ( $languages_data as $key => $data ) {
		if ( ! empty( $data['id'] ) && $id === $data['id'] ) {
			return true;
		}
	}

	return false;
}
