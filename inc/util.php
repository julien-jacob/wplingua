<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Return true is $str is an URL
 *
 * @param string $str
 * @return bool
 */
function wplng_str_is_url( $str ) {

	$is_url = false;

	if ( '' !== parse_url( $str, PHP_URL_SCHEME ) ) {
		// URL has http/https/...
		$is_url = ! ( filter_var( $str, FILTER_VALIDATE_URL ) === false );
	} else {
		// PHP filter_var does not support relative urls, so we simulate a full URL
		$is_url = ! ( filter_var( 'https://website.com/' . ltrim( $str, '/' ), FILTER_VALIDATE_URL ) === false );
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

	// Check if it's a mail address
	if ( filter_var( $text, FILTER_VALIDATE_EMAIL ) ) {
		return false;
	}

	/**
	 * Get letters only
	 */
	$letters = $text;
	$letters = html_entity_decode( $letters );
	$letters = preg_replace( '#[^\p{L}\p{N}]#u', '', $letters );
	$letters = preg_replace( '#[\d\s]#u', '', $letters );

	return ! empty( $letters );
}


/**
 * Escape texte (used for comparison)
 *
 * @param string $text
 * @return string
 */
function wplng_text_esc( $text ) {

	$text = trim( $text );
	$text = html_entity_decode( $text );
	$text = preg_replace( '#\s+#', ' ', $text );

	return $text;
}


/**
 * Return true is $str is HTML
 *
 * @param string $str
 * @return string
 */
function wplng_str_is_html( $str ) {
	return $str !== strip_tags( $str );
}


/**
 * Return true is $str is a JSON
 *
 * @param string $str
 * @return string
 */
function wplng_str_is_json( $str ) {
	return ( json_decode( $str ) == null ) ? false : true;
}
