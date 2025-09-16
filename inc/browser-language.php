<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Outputs a JavaScript snippet to handle browser language-based redirection.
 *
 * This function is designed to inject a JavaScript script into the front-end
 * for redirecting users based on their browser's language settings. It ensures
 * that the script is only output under specific conditions:
 * - The user is not logged in with editing permissions.
 * - The user is not a bot.
 * - The current page is the front page.
 * - The current language matches the website's default language.
 *
 * If the JavaScript file is missing or empty, the function exits early.
 *
 * @return void
 */
function wplng_browser_language_redirect_js_only() {

	if ( current_user_can( 'edit_posts' )
		|| wplng_is_bot()
		|| ! is_front_page()
		|| ( wplng_get_language_website_id() !== wplng_get_language_current_id() )
	) {
		return;
	}

	$script = file_get_contents( WPLNG_PLUGIN_PATH . '/assets/js/browser-redirect.js' );

	if ( empty( $script ) ) {
		return;
	}

	$script = str_replace(
		'//# sourceMappingURL=browser-redirect.js.map',
		'',
		$script
	);

	echo '<script id="wplingue-browser-redirect-js">' . rtrim( $script ) . '</script>';
}


/**
 * Redirects the user to the translated version of the page based on browser language or a user-defined cookie.
 *
 * This function is designed to run on the 'template_redirect' hook. It handles the logic for
 * redirecting users to the appropriate language version of a page. The redirection is based on
 * a stored cookie (if available) or the browser's 'Accept-Language' header.
 *
 * It prevents redirection for specific user types (e.g., logged-in users with 'edit_posts' capability)
 * and for known web crawlers and bots.
 *
 * @return void
 */
function wplng_browser_language_redirect_php_js() {

	// Exit early for users with editing permissions or for bots to prevent redirection loops.
	if ( current_user_can( 'edit_posts' )
		|| wplng_is_bot()
	) {
		return;
	}

	$language_website_id = wplng_get_language_website_id();
	$language_current_id = wplng_get_language_current_id();
	$is_translated_page  = $language_website_id !== $language_current_id;
	$cookie              = wplng_browser_language_cookie_get();

	// If the user is on a translated page.
	if ( $is_translated_page ) {
		// Update the cookie with the selected language code. This is a voluntary choice by the user.
		wplng_browser_language_cookie_set( $language_current_id );
		return;
	}

	// The user is on the original language page (no language prefix).

	// Determine the language to redirect to.
	$redirect_language_id = false;

	if ( $cookie !== false ) {
		// If the cookie contains a valid language code, use it for redirection.
		// This honors the user's last choice.
		if ( wplng_is_valid_language_id( $cookie ) ) {

			if ( $cookie === $language_website_id ) {
				return;
			} else {
				$redirect_language_id = $cookie;
			}
		}
	} else {
		// This is the very first visit (no cookie). Use the browser's language.
		if ( ! empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
			$langs = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
			if ( ! empty( $langs ) ) {
				$language_browser_id  = strtolower( substr( trim( $langs[0] ), 0, 2 ) );
				$redirect_language_id = $language_browser_id;
			}
		}
	}

	// Check if the determined language is a valid target language.
	if ( empty( $redirect_language_id )
		|| ( $redirect_language_id === $language_website_id )
		|| ! in_array( $redirect_language_id, wplng_get_languages_target_ids(), true )
	) {
		// If not, set the cookie to 'original' to prevent future redirects on the original page.
		wplng_browser_language_cookie_set( $language_website_id );
		return;
	}

	// Check if the URL to redirect is translatable.
	$url_to_redirect = wplng_get_url_current_for_language( $redirect_language_id );

	if ( $url_to_redirect === wplng_get_url_original() ) {
		wplng_browser_language_cookie_set( $language_website_id );
		return;
	}

	// Perform the redirection.
	wplng_browser_language_cookie_set( $redirect_language_id );
	wp_safe_redirect(
		$url_to_redirect,
		302
	);
	exit;
}


/**
 * Retrieves the language code from the 'wplingua-lang' cookie.
 *
 * This function safely retrieves the language code stored in the user's cookie,
 * sanitizing the value to prevent security issues. It returns the language code
 * if it's valid ('original' or a known language ID), otherwise it returns false.
 *
 * @return string|false The language code from the cookie, or false if the cookie is not set or invalid.
 */
function wplng_browser_language_cookie_get() {
	$cookie = false;
	if ( ! empty( $_COOKIE['wplingua-lang'] ) ) {
		// Sanitize the cookie value to ensure it's safe.
		$cookie = sanitize_text_field( $_COOKIE['wplingua-lang'] );
		// Validate the language code against known languages or the 'original' status.
		if ( ! wplng_is_valid_language_id( $cookie ) ) {
			$cookie = false;
		}
	}

	return $cookie;
}


/**
 * Sets the 'wplingua-lang' cookie with a specified language ID.
 *
 * This function creates or updates a cookie to store the user's preferred language choice.
 * The cookie is set to expire in 30 days and is accessible across the site's domain.
 *
 * @param string $language_id The language code to be stored (e.g., 'en', 'fr', 'es', or 'original').
 * @return void
 */
function wplng_browser_language_cookie_set( $language_id ) {
	setcookie(
		'wplingua-lang',
		$language_id,
		time() + 30 * DAY_IN_SECONDS,
		COOKIEPATH,
		COOKIE_DOMAIN
	);
}


/**
 * Determines if the current user agent is a bot or a web crawler.
 *
 * This function checks the user agent string against a predefined list of known bots
 * using a regular expression. The result is cached for the duration of the request
 * to improve performance on multiple calls.
 *
 * @return bool True if the user agent is a bot, false otherwise.
 */
function wplng_is_bot() {
	return (bool) preg_match(
		'/googlebot|bingbot|slurp|duckduckbot|baiduspider|yandex|semrushbot|ahrefsbot|mj12bot|dotbot|exabot|facebookexternalhit/i',
		strtolower( $_SERVER['HTTP_USER_AGENT'] ?? '' )
	);
}
