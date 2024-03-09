<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Get wpLingua excluded selectors
 *
 * @return array
 */
function wplng_get_selector_exclude() {

	$selector_exclude = array();

	// Get, check and sanitize excluded selector as string
	$option = get_option( 'wplng_excluded_selectors' );
	if ( empty( $option ) || ! is_string( $option ) ) {
		wplng_data_excluded_selector_default();
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


/**
 * Replace all excluded elements by empty tags with wplng-tag-exclude attribute
 *
 * @param string $html
 * @param array $excluded_elements
 * @return string HTML
 */
function wplng_html_set_exclude_tag( $html, &$excluded_elements ) {

	$selector_exclude = wplng_get_selector_exclude();

	if ( empty( $selector_exclude ) ) {
		return $html;
	}

	$dom = wplng_sdh_str_get_html( $html );

	if ( false === $dom ) {
		return $html;
	}

	foreach ( $selector_exclude as $selector ) {
		foreach ( $dom->find( $selector ) as $element ) {
			$excluded_elements[] = $element->outertext;
			$attr                = count( $excluded_elements ) - 1;
			$element->outertext  = '<div wplng-tag-exclude="' . esc_attr( $attr ) . '"></div>';
		}
	}

	$dom->save();

	return (string) wplng_sdh_str_get_html( $dom );
}


/**
 * Replace all tags with wplng-tag-exclude attribute by value in $excluded_elements
 *
 * @param string $html
 * @param array $excluded_elements
 * @return string
 */
function wplng_html_replace_exclude_tag( $html, $excluded_elements ) {

	if ( empty( $excluded_elements ) ) {
		return $html;
	}

	$dom = wplng_sdh_str_get_html( $html );

	if ( false === $dom ) {
		return $html;
	}

	foreach ( $dom->find( '[wplng-tag-exclude]' ) as $element ) {

		if ( isset( $element->attr['wplng-tag-exclude'] ) ) {

			$exclude_index = (int) $element->attr['wplng-tag-exclude'];

			if ( isset( $excluded_elements[ $exclude_index ] ) ) {
				$element->outertext = $excluded_elements[ $exclude_index ];
			}
		}
	}

	$dom->save();

	return (string) wplng_sdh_str_get_html( $dom );
}

