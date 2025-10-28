<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Translate JSON in attributes in dom for translated pages
 *
 * @param object $dom
 * @param array  $args
 * @return object
 */
function wplng_dom_translate_json_attr( $dom, $args ) {

	wplng_args_setup( $args );

	/**
	 * Translate JSON in attributes
	 */

	$attr_json_to_translate = wplng_data_attr_json_to_translate();

	foreach ( $attr_json_to_translate as $attr ) {
		foreach ( $dom->find( $attr['selector'] ) as $element ) {

			if ( empty( $element->attr[ $attr['attr'] ] ) ) {
				continue;
			}

			// Prepare arguments for translation
			wplng_args_setup( $args );
			$args['parents'] = array( $attr['attr'] );

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
