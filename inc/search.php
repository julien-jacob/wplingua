<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_translate_search_query( $query_object ) {
	if ( $query_object->is_search() ) {

		$language_website = wplng_get_language_website_id();
		$language_current = wplng_get_language_current_id();

		if ( $language_website == $language_current ) {
			return;
		}

		$translated_search = wplng_translate(
			$query_object->query['s'],
			$language_current,
			$language_website
		);

		// TODO : Gérer les signes de ponctuations ajoutés dans l'API ?
		$translated_search = preg_replace( '#[^A-Za-z0-9 ]#', '', $translated_search );

		if ( ! empty( $translated_search ) ) {
			$query_object->set( 's', $translated_search );
		}
	}
}


function wplng_exclude_search( $is_translatable ) {
	return $is_translatable && ! ( is_search() || isset( $_GET['s'] ) );
}
