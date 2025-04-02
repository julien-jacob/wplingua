<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Replace exclusion tag with corresponding HTML part.
 *
 * This function scans the DOM for elements with the 'wplng-tag-exclude' attribute
 * and replaces them with HTML parts provided in the '$excluded_elements' array.
 *
 * @param object $dom The DOM object to process.
 * @param array  $excluded_elements An array of HTML parts to replace the exclusion tags.
 * @return object The modified DOM object.
 */
function wplng_dom_exclusions_replace_tags( $dom, $excluded_elements ) {

	$contain_excluded = true;
	$counter          = 0;

	// Continue processing the DOM until no excluded elements are found
	while ( $contain_excluded && $counter <= 6 ) {

		$contain_excluded = false;
		$dom              = wplng_sdh_str_get_html( $dom );

		// Iterate over all elements with the 'wplng-tag-exclude' attribute
		foreach ( $dom->find( '[wplng-tag-exclude]' ) as $element ) {

			// Check if the exclusion tag attribute is set
			if ( ! isset( $element->attr['wplng-tag-exclude'] ) ) {
				continue;
			}

			// Get the index of the excluded element
			$exclude_index = (int) $element->attr['wplng-tag-exclude'];

			// Replace the element's outer text with the corresponding HTML part
			if ( isset( $excluded_elements[ $exclude_index ] ) ) {
				$element->outertext = $excluded_elements[ $exclude_index ];
			}

			// Indicate that further processing may be needed
			$contain_excluded = true;
		}

		++$counter;
	}

	// Return the processed DOM
	return $dom;
}
