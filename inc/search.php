<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Translate search query if page is translated
 *
 * @param object $query
 * @return void
 */
function wplng_translate_search_query( $query ) {

	if ( $query->is_search() ) {

		if ( ! wplng_text_is_translatable( $query->query['s'] ) ) {
			return;
		}

		$language_website = wplng_get_language_website_id();
		$language_current = wplng_get_language_current_id();

		if ( $language_website == $language_current ) {
			return;
		}

		$translated_search = wplng_api_call_translate(
			array( $query->query['s'] ),
			$language_current,
			$language_website
		);

		if ( empty( $translated_search[0] ) ) {
			return;
		}

		$translated_search = esc_attr( $translated_search[0] );

		// Remove added ponctuation
		$translated_search = preg_replace( '#[^A-Za-z0-9 ]#', '', $translated_search );

		if ( ! empty( $translated_search ) ) {
			$query->set( 's', $translated_search );
		}
	}
}


/**
 * Make search untranslated
 *
 * @param bool $is_translatable
 * @return void
 */
function wplng_exclude_search( $is_translatable ) {
	return $is_translatable && ! ( is_search() || isset( $_GET['s'] ) );
}
