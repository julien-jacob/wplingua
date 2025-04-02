<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Modify JS script in dom for translated pages
 *
 * @param object $dom
 * @param array  $args
 * @return object
 */
function wplng_dom_translate_js( $dom, $args ) {

	wplng_args_setup( $args );

	if ( 'loading' === $args['load'] ) {
		return $dom;
	}

	foreach ( $dom->find( 'script' ) as $element ) {
		$element->innertext = wplng_translate_js(
			$element->innertext,
			$args
		);
	}

	return $dom;
}
