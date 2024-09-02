<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Modify links in dom for translated pages
 *
 * @param object $dom
 * @param array $args
 * @return object
 */
function wplng_dom_replace_links( $dom, $args ) {

	wplng_args_setup( $args );

	if ( 'loading' === $args['load'] ) {
		return $dom;
	}

	$attr_url_to_translate = wplng_data_attr_url_to_translate();

	foreach ( $attr_url_to_translate as $attr ) {
		foreach ( $dom->find( $attr['selector'] ) as $element ) {

			if ( empty( $element->attr[ $attr['attr'] ] ) ) {
				continue;
			}

			$link = sanitize_url( $element->attr[ $attr['attr'] ] );

			$translated_url = wplng_url_translate(
				$link,
				$args['language_target']
			);

			$element->attr[ $attr['attr'] ] = esc_url( $translated_url );
		}
	}

	return $dom;
}
