<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Retrieves the list of excluded CSS selectors.
 * Get from: WordPress options, default values, and applied filters.
 *
 * @return array An array of excluded CSS selectors.
 */
function wplng_get_excluded_selectors() {

	/**
	 * Get wpLingua excluded selectors
	 */

	$selector_exclude = array();

	// Get, check and sanitize excluded selector as string
	$option = get_option( 'wplng_excluded_selectors' );
	if ( empty( $option ) || ! is_string( $option ) ) {
		$option = '';
	}
	$option = sanitize_textarea_field( $option );

	// Get exclude selector as array
	$option = explode( PHP_EOL, $option );

	// Sanitize each selectors
	foreach ( $option as $selector ) {
		$selector = esc_attr( trim( $selector ) );
		if ( ! empty( $selector ) ) {
			$selector_exclude[] = $selector;
		}
	}

	// Add default selectors
	$selector_exclude = array_merge(
		wplng_data_excluded_selector_default(), // Make HTML parsed smaller
		$selector_exclude
	);

	// Remove duplicate
	$selector_exclude = array_unique( $selector_exclude );

	// Apply wplng_selector_exclude filters
	$selector_exclude = apply_filters(
		'wplng_selector_exclude',
		$selector_exclude
	);

	return $selector_exclude;
}
