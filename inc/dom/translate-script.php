<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Modify scripts in dom for translated pages
 *
 * @param object $dom
 * @param array  $args
 * @return object
 */
function wplng_dom_translate_script( $dom, $args ) {

	wplng_args_setup( $args );

	foreach ( $dom->find( 'script' ) as $element ) {

		if ( ! empty( $element->attr['type'] )
			&& (
				$element->attr['type'] === 'application/ld+json'
				|| $element->attr['type'] === 'application/json'
			)
		) {

			/**
			 * Translate JSON in scripts
			 */

			 $element->innertext = wplng_translate_json(
				$element->innertext,
				$args
			);

		} else {

			/**
			 * Translate JS in scripts
			 */

			 $element->innertext = wplng_translate_js(
				$element->innertext,
				$args
			);
		}
	}

	return $dom;
}
