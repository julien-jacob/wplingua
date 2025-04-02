<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Modify the dir attribute in dom for translated pages
 *
 * @param object $dom
 * @param array  $args
 * @return object
 */
function wplng_dom_replace_attr_dir( $dom, $args ) {

	wplng_args_setup( $args );

	if ( 'loading' === $args['load'] ) {
		return $dom;
	}

	$language_target = wplng_get_language_by_id( $args['language_target'] );

	if ( ! empty( $language_target['dir'] ) ) {
		foreach ( $dom->find( 'body' ) as $element ) {
			$element->{'dir'} = esc_attr( $language_target['dir'] );
		}
	}

	return $dom;
}
