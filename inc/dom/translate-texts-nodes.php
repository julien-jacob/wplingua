<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_dom_translate_texts_nodes( $dom, $args ) {

	if ( 'editor' === $args['mode']
		|| 'disabled' !== $args['load']

	) {
		return $dom;
	}

	// Si mode == editor ou mode = list ou load == progress :
	// 		Ne traduire que les nodes du head

	// Si load == loading
	// Retourner dom directement

	$node_text_excluded = wplng_data_excluded_node_text();

	foreach ( $dom->find( 'text' ) as $element ) {

		if ( in_array( $element->parent->tag, $node_text_excluded ) ) {
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
