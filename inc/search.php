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

	/**
	 * Check if it's a search query
	 */

	if ( ! $query->is_search()
		|| ! isset( $query->query['s'] )
		|| ! is_string( $query->query['s'] )
	) {
		return;
	}

	/**
	 * Get current and website languages
	 * Ckeck if search need to be translate
	 */

	$language_current = wplng_get_language_current_id();
	$language_website = wplng_get_language_website_id();

	if ( $language_website === $language_current ) {
		return;
	}

	/**
	 * Sanitize search query and check if is translatale
	 */

	$search_string = sanitize_text_field( $query->query['s'] );
	$search_string = wplng_text_esc( $search_string );

	if ( ! wplng_text_is_translatable( $search_string ) ) {
		return;
	}

	/**
	 * Call API to get the translation
	 */

	$translated_search = wplng_api_call_translate(
		array( $search_string ),
		$language_current,
		$language_website
	);

	if ( ! isset( $translated_search[0] ) ) {
		return;
	}

	$translated_search = $translated_search[0];

	/**
	 * Check and clear the translation
	 */

	// Remove added ponctuation
	$translated_search = preg_replace(
		'#[^A-Za-z0-9]#',
		'',
		$translated_search
	);

	// Clear translation
	$translated_search = trim( esc_attr( $translated_search ) );

	// Check if translation is not empty after cleaning
	if ( '' === $translated_search ) {
		return;
	}

	/**
	 * Replace the search text by the translation
	 */

	$query->set( 's', $translated_search );
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
