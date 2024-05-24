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

	$parsed = wp_parse_url( $str );
	$is_url = false;

	if ( is_string( $str )
		&& ( '' !== trim( $str ) )
		&& ( false !== strpos( $str, '/' ) )
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

	if ( '' === $text ) {
		return false;
	}

	// Check if it's a mail address
	if ( filter_var( $text, FILTER_VALIDATE_EMAIL ) ) {
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
 * @param string $text
 * @return string
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
	$text = preg_replace( '#\s+#', ' ', $text );
	$text = trim( $text );

	return $text;
}


/**
 * Return true if $str is HTML
 *
 * @param string $str
 * @return string
 */
function wplng_str_is_html( $str ) {
	return $str !== wp_strip_all_tags( $str );
}


/**
 * Return true is $str is a JSON
 *
 * @param string $str
 * @return string
 */
function wplng_str_is_json( $str ) {
	$decoded = json_decode( $str, true );
	return ( json_last_error() === JSON_ERROR_NONE ) && is_array( $decoded );
}


/**
 * Return true if $str is a local ID
 * Ex: fr_FR, fr, FR, ...
 *
 * @param string $str
 * @return bool
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
 * Return true if a JSON string element is translatable
 *
 * @param string $element
 * @param array $parents
 * @return bool
 */
function wplng_json_element_is_translatable( $element, $parents ) {

	$is_translatable   = false;
	$json_excluded     = wplng_data_excluded_json();
	$json_to_translate = wplng_data_json_to_translate();

	if ( in_array( $parents, $json_excluded ) ) {

		/**
		 * Is an excluded JSON
		 */

		$is_translatable = false;

	} elseif ( in_array( $parents, $json_to_translate ) ) {

		/**
		 * Is an included JSON
		 */

		$is_translatable = true;

	} else {

		if (
			! empty( $parents[0] )
			&& ( '@graph' === $parents[0] )
			&& ( count( $parents ) > 2 )
			&& (
				(
					( 'logo' === $parents[ count( $parents ) - 2 ] )
					&& ( 'caption' === $parents[ count( $parents ) - 1 ] )
				)
				|| (
					( 'image' === $parents[ count( $parents ) - 2 ] )
					&& ( 'caption' === $parents[ count( $parents ) - 1 ] )
				)
				|| (
					( 'logo' === $parents[ count( $parents ) - 2 ] )
					&& ( 'caption' === $parents[ count( $parents ) - 1 ] )
				)
				|| (
					( 'author' === $parents[ count( $parents ) - 2 ] )
					&& ( 'headline' === $parents[ count( $parents ) - 1 ] )
				)
				|| (
					( 'articleSection' === $parents[ count( $parents ) - 2 ] )
					&& ( is_int( $parents[ count( $parents ) - 1 ] ) )
				)
				|| ( 'name' === $parents[ count( $parents ) - 1 ] )
				|| ( 'description' === $parents[ count( $parents ) - 1 ] )
			)
		) {

			/**
			 * Is schema-graph
			 */

			$is_translatable = true;

		} elseif (
			! empty( $parents[0] )
			&& ( 'wc_address_i18n_params' === $parents[0] )
			&& ( count( $parents ) > 1 )
			&& (
				( 'placeholder' === $parents[ count( $parents ) - 1 ] )
				|| ( 'label' === $parents[ count( $parents ) - 1 ] )
			)
		) {

			/**
			 * Is WooCommerce address params
			 */

			$is_translatable = true;
		}

		$element = wplng_text_esc( $element );

		if ( ! wplng_text_is_translatable( $element ) ) {
			$is_translatable = false;
		}
	}

	return apply_filters(
		'wplng_json_element_is_translatable',
		$is_translatable,
		$element,
		$parents
	);
}
