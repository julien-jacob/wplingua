<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


// Effectuer la redirection
add_action( 'template_redirect', 'wplng_browser_language_redirect' );
function wplng_browser_language_redirect() {

	// TODO : Check si le visiteur est connecté

	if ( ! is_front_page()
		|| wp_doing_ajax()
		|| ( defined( 'DOING_AJAX' ) && DOING_AJAX )
		|| wplng_is_bot()
	) {
		return;
	}

	$language_website_id = wplng_get_language_website_id();
	$language_current_id = wplng_get_language_current_id();

	if ( $language_website_id !== $language_current_id ) {
		return;
	}

	/**
	 * Get browser language ID
	 */

	$language_browser_id = false;

	if ( ! empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
		$langs = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
		if ( ! empty( $langs ) ) {
			$language_browser_id = strtolower( substr( trim( $langs[0] ), 0, 2 ) );
		}
	}

	if ( empty( $language_browser_id ) ) {
		return;
	}

	/**
	 * Check is the browser language is in target languages
	 */

	$language_target_ids = wplng_get_languages_target_ids();

	if ( ! in_array( $language_browser_id, $language_target_ids, true ) ) {
		return;
	}

	/**
	 * Check if the front page is exclude
	 */

	if ( ! wplng_url_is_translatable( wplng_get_url_original() ) ) {
		return;
	}

	wp_safe_redirect(
		wplng_get_url_current_for_language(
			$language_browser_id
		),
		302
	);

	exit;
}


// Vérifie si User-Agent est un bot
function wplng_is_bot() {

	$bots = array( 'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider', 'yandex' );
	$ua   = strtolower( $_SERVER['HTTP_USER_AGENT'] ?? '' );

	foreach ( $bots as $bot ) {
		if ( strpos( $ua, $bot ) !== false ) {
			return true;
		}
	}

	return false;
}
