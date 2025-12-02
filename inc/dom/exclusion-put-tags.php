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

	$selector_exclude = wplng_get_excluded_selectors();

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
