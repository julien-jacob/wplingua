<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Translate nodes text in dom for translated pages
 *
 * @param object $dom
 * @param array  $args
 * @return object
 */
function wplng_dom_translate_nodes_texts( $dom, $args ) {

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
