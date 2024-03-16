<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_dom_exclusions_replace_tags( $dom, $excluded_elements ) {

	$dom = wplng_sdh_str_get_html( $dom );

	foreach ( $dom->find( '[wplng-tag-exclude]' ) as $element ) {

		if ( isset( $element->attr['wplng-tag-exclude'] ) ) {

			$exclude_index = (int) $element->attr['wplng-tag-exclude'];

			if ( isset( $excluded_elements[ $exclude_index ] ) ) {
				$element->outertext = $excluded_elements[ $exclude_index ];
			}
		}
	}

	return $dom;
}
