<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Replace excluded elements in dom by tag
 *
 * @param object $dom
 * @param array  $excluded_elements
 * @return object
 */
function wplng_dom_exclusions_put_tags( $dom, &$excluded_elements ) {

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

	/**
	 * Replace excluded element by tags
	 * and fill $excluded_elements array
	 */

	$excluded_elements = array();

	foreach ( $selector_exclude as $selector ) {
		foreach ( $dom->find( $selector ) as $element ) {
			$excluded_elements[] = $element->outertext;
			$attr                = count( $excluded_elements ) - 1;
			$element->outertext  = '<div wplng-tag-exclude="' . esc_attr( $attr ) . '"></div>';
		}
	}

	return $dom;
}
