<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Translate texts attributes and texts nodes in dom
 *
 * @param object $dom
 * @param array $args
 * @return object
 */
function wplng_dom_translate_texts( $dom, $args ) {

	$dom = wplng_dom_translate_texts_attr( $dom, $args );
	$dom = wplng_dom_translate_texts_nodes( $dom, $args );

	return $dom;
}


/**
 * Translate attributes in dom for translated pages
 *
 * @param object $dom
 * @param array $args
 * @return object
 */
function wplng_dom_translate_texts_attr( $dom, $args ) {

	wplng_args_setup( $args );

	if ( 'loading' === $args['load']
		|| empty( $args['translations'] )
	) {
		return $dom;
	}

	$attr_text_to_translate = wplng_data_attr_text_to_translate();

	foreach ( $attr_text_to_translate as $attr ) {
		foreach ( $dom->find( $attr['selector'] ) as $element ) {

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



/**
 * Translate nodes text in dom for translated pages
 *
 * @param object $dom
 * @param array $args
 * @return object
 */
function wplng_dom_translate_texts_nodes( $dom, $args ) {

	wplng_args_setup( $args );

	if ( 'disabled' !== $args['load']
		|| empty( $args['translations'] )
	) {
		return $dom;
	}

	$selector = 'text';

	if ( 'editor' === $args['mode']
		|| 'list' === $args['mode']
	) {
		$selector = 'head text';
	}

	$node_text_excluded = wplng_data_excluded_node_text();

	foreach ( $dom->find( $selector ) as $element ) {

		if ( in_array( $element->parent->tag, $node_text_excluded ) ) {
			continue;
		}

		$text = wplng_text_esc( $element->innertext );

		if ( ! wplng_text_is_translatable( $text ) ) {
			continue;
		}

		$translated_text = wplng_get_translated_text_from_translations(
			$element->innertext,
			$args['translations']
		);

		$element->innertext = esc_html( $translated_text );
	}

	return $dom;
}
