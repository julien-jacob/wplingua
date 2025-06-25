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

	foreach ( $dom->find( 'body' ) as $element ) {
		$element->{'dir'} = esc_attr( $args['language_target_dir'] );
	}

	return $dom;
}
