<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Check if substring is contained in string
 *
 * @param string $haystack String to check
 * @param string $needle Sub-string
 *
 * @return bool
 */
function wplng_str_contains( $haystack, $needle ) {
	return ( strpos( $haystack, $needle ) !== false );
}


/**
 * Check if string starts by sub_string
 *
 * @param string $haystack String to check
 * @param string $needle Sub-string
 *
 * @return bool
 */
function wplng_str_starts_with( $haystack, $needle ) {

	if ( ! is_string( $haystack ) || ! is_string( $needle ) ) {
		return false;
	}

	return substr_compare( $haystack, $needle, 0, strlen( $needle ) ) === 0;
}


/**
 * Check if string ends by sub_string
 *
 * @param string $haystack String to check
 * @param string $needle Sub-string
 *
 * @return bool
 */
function wplng_str_ends_with( $haystack, $needle ) {

	if ( ! is_string( $haystack ) || ! is_string( $needle ) ) {
		return false;
	}

	return substr_compare( $haystack, $needle, -strlen( $needle ) ) === 0;
}

/**
 * Return true is $str is an URL
 *
 * @param string $str
 * @return bool
 */
function wplng_str_is_url( $str ) {

	$parsed = wp_parse_url( $str );
	$is_url = false;

	if ( is_string( $str )
		&& ( '' !== trim( $str ) )
		&& wplng_str_contains( $str, '/' )
		&& ! wplng_str_starts_with( $str, 'wpgb-content-block/' ) // Plugin: WP Grid Builder
		&& ! wplng_str_starts_with( $str, '/wc/store/v1' ) // Plugin: WooCommerce
		&& ! wplng_str_starts_with( $str, 'GlotPress/' ) // Plugin: WooCommerce
	) {
		if ( isset( $parsed['scheme'] )
			&& (
				( 'https' === $parsed['scheme'] )
				|| ( 'http' === $parsed['scheme'] )
			)
		) {
			// URL has http/https/...
			$is_url = ! ( filter_var( $str, FILTER_VALIDATE_URL ) === false );
		} else {
			// PHP filter_var does not support relative urls, so we simulate a full URL
			$is_url = ( filter_var( 'https://website.com/' . ltrim( $str, '/' ), FILTER_VALIDATE_URL ) !== false );
		}
	}

	return $is_url;
}


/**
 * Return true is $str is a translatable text
 * Return false if $str is a number, mail addredd, symbol, ...
 *
 * @param string $text
 * @return bool
 */
function wplng_text_is_translatable( $text ) {

	$text = trim( $text );

	if ( '' === $text ) {
		return false;
	}

	// Check special no translate tag
	if ( wplng_str_contains( $text, '_wplingua_no_translate_' ) ) {
		return false;
	}

	// Check for better plugin compatibility
	if ( wplng_str_contains( $text, 'presto_player' )
		|| wplng_str_contains( $text, 'presto-player' )
	) {
		return false;
	}

	// Check if it's a email address
	if ( filter_var( $text, FILTER_VALIDATE_EMAIL ) ) {
		return false;
	}

	// Check bad HTML tags and templating tags
	if ( wplng_str_starts_with( $text, '<' )
		&& wplng_str_ends_with( $text, '>' )
	) {
		return false;
	}

	// Check JS tags
	if ( wplng_str_starts_with( $text, '{{' )
		&& wplng_str_ends_with( $text, '}}' )
	) {
		return false;
	}

	// Get letters only
	$letters = $text;
	$letters = html_entity_decode( $letters );
	$letters = preg_replace( '#[^\p{L}\p{N}]#u', '', $letters );
	$letters = preg_replace( '#[\d\s]#u', '', $letters );

	return ! empty( $letters );
}


/**
 * Escape texte (used for comparison)
 *
 * @param string $text String to escape
 * @return string Escape texte for comparison
 */
function wplng_text_esc( $text ) {

	$text = html_entity_decode( $text );
	$text = esc_html( $text );
	$text = esc_attr( $text );

	$text = wp_specialchars_decode(
		$text,
		ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401
	);

	$text = str_replace( '\\', '', $text );
	$text = preg_replace( '/\s+/u', ' ', $text );
	$text = trim( $text );

	return $text;
}


/**
 * Escape texte (used for editor)
 *
 * @param string $text String to escape
 * @return string Escape texte for editor
 */
function wplng_text_esc_displayed( $text ) {

	$search  = array( '<', '&lt;', '>', '&gt;' );
	$replace = array( '[', '[', ']', ']' );

	$text = str_replace(
		$search,
		$replace,
		$text
	);

	return $text;
}


/**
 * Return true if $str is HTML
 *
 * @param string $str String to check
 * @return bool true if $str is HTML
 */
function wplng_str_is_html( $str ) {
	return wplng_str_contains( $str, '<' )
		&& wplng_str_contains( $str, '>' )
		&& ( $str !== wp_strip_all_tags( $str ) );
}


/**
 * Checks whether a string is a valid XML.
 *
 * @param string $str The string to validate.
 * @return bool Returns true if the string is valid XML, false otherwise.
 */
function wplng_str_is_xml( $str ) {
	// Return false if the input is empty or not a string.
	if ( empty( $str ) || ! is_string( $str ) ) {
		return false;
	}

	// Suppress XML parsing errors to avoid warnings/notices.
	libxml_use_internal_errors( true );

	// Try to load the string as XML.
	$xml = simplexml_load_string( $str );

	// Determine if parsing was successful.
	$is_valid_xml = ( $xml !== false );

	// Clear any accumulated libxml errors.
	libxml_clear_errors();
	libxml_use_internal_errors( false );

	return $is_valid_xml;
}


/**
 * Return true if $str is a local ID
 * Ex: fr_FR, fr, FR, ...
 *
 * @param string $str String to check
 * @return bool true if $str is a local ID
 */
function wplng_str_is_locale_id( $str ) {

	$locale  = get_locale();
	$locales = array(
		$locale,                                        // Ex: fr_FR
		strtolower( $locale ),                          // Ex: fr_fr
		str_replace( '_', '-', $locale ),               // Ex: fr-FR
		strtolower( str_replace( '_', '-', $locale ) ), // Ex: fr-fr
		substr( $locale, 0, 2 ),                        // Ex: FR
		strtolower( substr( $locale, 0, 2 ) ),          // Ex: fr
	);

	return in_array( $str, $locales );
}


/**
 * Return true if $str contains sub-strings present in the i18n script
 *
 * @param string $str String to check
 * @return bool String is a i18n script
 */
function wplng_str_is_script_i18n( $str ) {

	$str = trim( $str );

	return wplng_str_contains( $str, 'wp.i18n.setLocaleData' )
		&& wplng_str_contains( $str, 'translations.locale_data.messages' )
		// Check if $str ends with ");"
		&& wplng_str_ends_with( $str, ');' )
		// Check if $str starts with "( function( domain, translations ) {"
		&& ( preg_match( '#^\(\s*function\s*\(\s*domain\s*,\s*translations\s*\)\s*\{#', $str ) === 1 );
}


/**
 * Return true is $str is a JSON
 *
 * @param string $str String to check
 * @return bool true is $str is a JSON
 */
function wplng_str_is_json( $str ) {
	$decoded = json_decode( $str, true );
	return ( json_last_error() === JSON_ERROR_NONE ) && is_array( $decoded );
}


/**
 * Checks if a JSON element should be excluded based on defined exclusion rules.
 *
 * @param mixed $element The JSON element to check.
 * @param array $parents The parent elements of the JSON element.
 *
 * @return bool True if the element matches any exclusion rule, false otherwise.
 */
function wplng_json_element_is_excluded( $element, $parents ) {

	$rules = wplng_data_json_rules_exclusion();

	foreach ( $rules as $rule ) {
		if ( $rule( $element, $parents ) === true ) {
			return true;
		}
	}

	return false;
}


/**
 * Checks if a JSON element should be included based on defined inclusion rules.
 *
 * @param mixed $element The JSON element to check.
 * @param array $parents The parent elements of the JSON element.
 *
 * @return bool True if the element matches any inclusion rule, false otherwise.
 */
function wplng_json_element_is_included( $element, $parents ) {

	$rules = wplng_data_json_rules_inclusion();

	foreach ( $rules as $rule ) {
		if ( $rule( $element, $parents ) === true ) {
			return true;
		}
	}

	return false;
}


/**
 * Get the context
 *
 * @return string Context
 */
function wplng_get_context() {

	$context = 'UNKNOW';

	if ( defined( 'DOING_AJAX' )
		&& DOING_AJAX
		&& ! empty( $_SERVER['HTTP_REFERER'] )
	) {
		$context = $_SERVER['HTTP_REFERER'];
		$context = sanitize_url( $context );
	} elseif ( isset( $_SERVER['HTTPS'] )
		&& isset( $_SERVER['HTTP_HOST'] )
		&& isset( $_SERVER['REQUEST_URI'] )
	) {
		$context  = ( empty( $_SERVER['HTTPS'] ) ? 'http' : 'https' );
		$context .= '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$context  = sanitize_url( $context );
	}

	return apply_filters(
		'wplng_api_call_translate_context',
		$context
	);
}


/**
 * Return true is website is in sub folder
 *
 * @return bool
 */
function wplng_website_in_sub_folder() {
	$parsed = wp_parse_url( get_home_url() );
	return ! empty( $parsed['path'] );
}
