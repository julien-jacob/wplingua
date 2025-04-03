<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Translate HTML in attributes in dom for translated pages
 *
 * @param object $dom
 * @param array  $args
 * @return object
 */
function wplng_dom_translate_html_attr( $dom, $args ) {

	wplng_args_setup( $args );

	if ( 'loading' === $args['load']
		|| empty( $args['translations'] )
	) {
		return $dom;
	}

	$attr_html_to_translate = wplng_data_attr_html_to_translate();

	foreach ( $attr_html_to_translate as $attr ) {
		foreach ( $dom->find( $attr['selector'] ) as $element ) {

			if ( empty( $element->attr[ $attr['attr'] ] ) ) {
				continue;
			}

			$html = wplng_text_esc( $element->attr[ $attr['attr'] ] );

			if ( empty( $html ) ) {
				continue;
			}

			$html = wp_specialchars_decode(
				$html,
				ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401
			);

			$html = wplng_translate_html(
				$html,
				$args
			);

			$element->attr[ $attr['attr'] ] = esc_attr( $html );
		}
	}

	return $dom;
}
