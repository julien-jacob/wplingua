<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Modify JSON in dom for translated pages
 *
 * @param object $dom
 * @param array  $args
 * @return object
 */
function wplng_dom_translate_json( $dom, $args ) {

	wplng_args_setup( $args );

	if ( 'loading' === $args['load'] ) {
		return $dom;
	}

	/**
	 * Translate JSON in JSON script tag (not in JS)
	 */

	foreach ( $dom->find( 'script[type="application/ld+json"]' ) as $element ) {
		$element->innertext = wplng_translate_json(
			$element->innertext,
			$args
		);
	}

	/**
	 * Translate JSON in attributes
	 */

	$attr_json_to_translate = wplng_data_attr_json_to_translate();

	foreach ( $attr_json_to_translate as $attr ) {
		foreach ( $dom->find( $attr['selector'] ) as $element ) {

			if ( empty( $element->attr[ $attr['attr'] ] ) ) {
				continue;
			}

			$translated_json = wplng_translate_json(
				wp_specialchars_decode(
					$element->attr[ $attr['attr'] ],
					ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401
				),
				$args
			);

			$element->attr[ $attr['attr'] ] = esc_attr( $translated_json );
		}
	}

	return $dom;
}
