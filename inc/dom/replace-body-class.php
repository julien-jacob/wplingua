<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_dom_replace_body_class( $dom, $args ) {

	// Replace languages IDs in <body> class
	foreach ( $dom->find( 'body[class]' ) as $element ) {

		$class_array = explode( ' ', $element->class );

		foreach ( $class_array as $key => $class ) {
			if ( wplng_str_is_locale_id( $class ) ) {
				$class_array[ $key ] = $args['language_target'];
			} elseif ( 'ltr' === $class || 'rtl' === $class ) {
				if ( ! empty( $language_target['dir'] ) ) {
					$class_array[ $key ] = $language_target['dir'];
				} else {
					$class_array[ $key ] = 'ltr';
				}
			}
		}

		$class_array[] = 'wplingua-translated';

		$class_array = array_unique( $class_array ); // Remove duplicate
		$class_str   = '';

		foreach ( $class_array as $key => $class ) {
			$class_str .= $class . ' ';
		}

		$class_str      = trim( $class_str );
		$element->class = esc_attr( $class_str );
	}

	return $dom;
}
