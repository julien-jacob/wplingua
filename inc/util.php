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
	$text = preg_replace( '/\s+/u', ' ', $text );
	$text = trim( $text );

	return $text;
}


/**
 * Escape texte (used for editor)
 *
 * @param string $text
 * @return string
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
 * @param array  $parents
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
					( 'author' === $parents[ count( $parents ) - 2 ] )
					&& ( 'headline' === $parents[ count( $parents ) - 1 ] )
				)
				|| (
					( 'articleSection' === $parents[ count( $parents ) - 2 ] )
					&& ( is_int( $parents[ count( $parents ) - 1 ] ) )
				)
				|| ( 'caption' === $parents[ count( $parents ) - 1 ] )
				|| ( 'name' === $parents[ count( $parents ) - 1 ] )
				|| ( 'alternateName' === $parents[ count( $parents ) - 1 ] )
				|| ( 'description' === $parents[ count( $parents ) - 1 ] )
			)
		) {

			/**
			 * Is schema-graph
			 */

			$is_translatable = true;

		} elseif (
			count( $parents ) == 3
			&& ( 'elementorFrontendConfig' === $parents[0] )
			&& ( 'i18n' === $parents[1] )
		) {

			/**
			 * Plugin: Elementor - elementorFrontendConfig
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

		} elseif (
			! empty( $parents[0] )
			&& wplng_str_starts_with( $parents[0], 'CASE' )
			&& ! empty( $parents[1] )
			&& 'l10n' === $parents[1]
			&& ! empty( $parents[2] )
			&& (
				$parents[2] === 'selectOption'
				|| $parents[2] === 'errorLoading'
				|| $parents[2] === 'removeAllItems'
				|| $parents[2] === 'loadingMore'
				|| $parents[2] === 'noResults'
				|| $parents[2] === 'searching'
				|| $parents[2] === 'irreversible_action'
				|| $parents[2] === 'delete_listing_confirm'
				|| $parents[2] === 'copied_to_clipboard'
				|| $parents[2] === 'nearby_listings_location_required'
				|| $parents[2] === 'nearby_listings_retrieving_location'
				|| $parents[2] === 'nearby_listings_searching'
				|| $parents[2] === 'geolocation_failed'
				|| $parents[2] === 'something_went_wrong'
				|| $parents[2] === 'all_in_category'
				|| $parents[2] === 'invalid_file_type'
				|| $parents[2] === 'file_limit_exceeded'
				|| $parents[2] === 'file_size_limit'
				|| (
					$parents[2] === 'datepicker'
					&& ! empty( $parents[3] )
					&& (
						$parents[3] === 'applyLabel'
						|| $parents[3] === 'cancelLabel'
						|| $parents[3] === 'customRangeLabel'
						|| $parents[3] === 'daysOfWeek'
						|| $parents[3] === 'monthNames'
					)
				)
			)
		) {

			/**
			 * Is 'My listing' theme - JSON in HTML
			 */

			$is_translatable = true;

		} elseif (
			! empty( $parents[0] )
			&& ( 'children' === $parents[0] )
			&& ! empty( $parents[1] )
			&& ( wplng_str_starts_with( $parents[1], 'term_' ) )
			&& ! empty( $parents[2] )
			&& (
				( 'name' === $parents[2] )
				|| ( 'description' === $parents[2] )
			)
		) {

			/**
			 * Is 'My listing' theme - JSON in AJAX
			 */

			$is_translatable = true;

		} elseif ( 'label' === $parents[ count( $parents ) - 1 ] ) {
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
