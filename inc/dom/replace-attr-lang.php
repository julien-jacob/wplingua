<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Modify the lang attribute in dom for translated pages
 *
 * @param object $dom
 * @param array  $args
 * @return object
 */
function wplng_dom_replace_attr_lang( $dom, $args ) {

	wplng_args_setup( $args );

	if ( 'loading' === $args['load'] ) {
		return $dom;
	}

	// Replace languages IDs in attributes

	$attr_lang_id_to_replace = wplng_data_attr_lang_id_to_replace();

	foreach ( $attr_lang_id_to_replace as $attr ) {
		foreach ( $dom->find( $attr['selector'] ) as $element ) {

			if ( empty( $element->attr[ $attr['attr'] ] ) ) {
				continue;
			}

			$lang_id = $element->attr[ $attr['attr'] ];

			if ( ! wplng_str_is_locale_id( $lang_id ) ) {
				continue;
			}

			$element->attr[ $attr['attr'] ] = esc_attr( $args['language_target'] );
		}
	}

	return $dom;
}
