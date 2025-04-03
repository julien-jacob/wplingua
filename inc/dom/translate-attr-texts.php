<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Translate attributes in dom for translated pages
 *
 * @param object $dom
 * @param array  $args
 * @return object
 */
function wplng_dom_translate_attr_texts( $dom, $args ) {

	wplng_args_setup( $args );

	if ( 'loading' === $args['load']
		|| empty( $args['translations'] )
	) {
		return $dom;
	}

	$attr_text_to_translate = wplng_data_attr_text_to_translate();

	foreach ( $attr_text_to_translate as $attr ) {
		foreach ( $dom->find( $attr['selector'] ) as $element ) {

			if ( empty( $element->attr[ $attr['attr'] ] ) ) {
				continue;
			}

			$text = wplng_text_esc( $element->attr[ $attr['attr'] ] );

			if ( ! wplng_text_is_translatable( $text ) ) {
				continue;
			}

			$translated_attr = wplng_get_translated_text_from_translations(
				$text,
				$args['translations']
			);

			$element->attr[ $attr['attr'] ] = esc_attr( $translated_attr );
		}
	}

	return $dom;
}
