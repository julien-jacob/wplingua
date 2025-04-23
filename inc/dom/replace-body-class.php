<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Modify the ody class in dom for translated pages
 *
 * @param object $dom
 * @param array  $args
 * @return object
 */
function wplng_dom_replace_body_class( $dom, $args ) {

	wplng_args_setup( $args );

	if ( 'loading' === $args['load'] ) {
		return $dom;
	}

	// Replace languages IDs in <body> class
	foreach ( $dom->find( 'body[class]' ) as $element ) {

		$class_array = explode( ' ', $element->class );

		foreach ( $class_array as $key => $class ) {
			if ( wplng_str_is_locale_id( $class ) ) {
				$class_array[ $key ] = $args['language_target'];
			} elseif ( 'ltr' === $class || 'rtl' === $class ) {
				$class_array[ $key ] = $args['language_target_dir'];
			}
		}

		$class_array[] = 'wplingua-translated';

		global $wplng_class_reload;

		if ( true === $wplng_class_reload ) {
			$class_array[] = 'wplingua-reload';
		}

		$class_array = array_unique( $class_array ); // Remove duplicate
		$class_str   = '';

		foreach ( $class_array as $class ) {
			$class_str .= $class . ' ';
		}

		$class_str      = trim( $class_str );
		$element->class = esc_attr( $class_str );
	}

	return $dom;
}
