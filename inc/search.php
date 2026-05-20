<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Replace the search query value by a placeholder tag.
 *
 * Used before page translation to prevent the search query
 * from being translated as part of the page content.
 *
 * @param string $search The current search query string.
 * @return string The placeholder tag, or the original search string if no translation is pending.
 */
function wplng_search_put_tag( $search ) {

	global $wplng_translate_search_query;

	if ( $wplng_translate_search_query === null ) {
		return $search;
	}

	return '%wplng-search-query%';
}



/**
 * Restore the translated search query in the page HTML.
 *
 * Replaces the placeholder tag previously inserted by wplng_search_put_tag()
 * with the actual (translated) search query string.
 *
 * @param string $html The page HTML content containing the placeholder tag.
 * @return string The HTML with the placeholder replaced by the translated search query.
 */
function wplng_search_replace_tag( $html ) {

	global $wplng_translate_search_query;

	if ( $wplng_translate_search_query === null ) {
		return $html;
	}

	if ( current_user_can( 'edit_posts' )
		&& ! empty( $_GET['wplng-mode'] )
		&& ( $_GET['wplng-mode'] === 'editor'
			|| $_GET['wplng-mode'] === 'list'
		)
	) {
		return $html;
	}

	$html = str_replace(
		'%wplng-search-query%',
		wplng_text_esc(
			$wplng_translate_search_query
		),
		$html
	);

	return $html;
}


/**
 * Translate search query if page is translated and e
 * enable visitors to search on your website in their own language.
 *
 * Example: if your website is translated from English to French and
 * you have a post named "Hello". When a French visitor searches for
 * the term "Bonjour" on the website, this feature will translate it
 * on the fly to "Hello" before launching the search. This will allow
 * WordPress to find the post named "Hello" when your visitor has
 * searched for "Bonjour".
 *
 * @param object $query
 * @return void
 */
function wplng_translate_search_query( $query ) {

	global $wplng_translate_search_query;

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
	 * Sanitize search query and check if is translatable
	 */

	$search_string = sanitize_text_field( $query->query['s'] );
	$search_string = wplng_text_esc( $search_string );

	if ( ! wplng_text_is_translatable( $search_string ) ) {
		return;
	}

	if ( wplng_str_is_malicious( $search_string ) ) {
		$query->set( 's', '' );
		return;
	}

	$wplng_translate_search_query = $search_string;

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

	// Remove leading/trailing punctuation added by translation API
	$translated_search = trim( $translated_search, " \t\n\r\0\x0B.,;:!?\"'()[]{}…" );

	// Clear translation
	$translated_search = wplng_text_esc( $translated_search );

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
