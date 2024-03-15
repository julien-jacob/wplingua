<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * wpLingua Parser : Get texts in an HTML
 *
 * @param string|object $html string or $dom object
 * @return array Texts
 */
function wplng_parse_html( $html ) {

	$texts = array();
	$dom   = wplng_sdh_str_get_html( $html );

	if ( empty( $dom ) ) {
		return array();
	}

	/**
	 * Find and parse JSON
	 */

	foreach ( $dom->find( 'script[type="application/ld+json"]' ) as $element ) {
		$texts = array_merge(
			$texts,
			wplng_parse_json( $element->innertext )
		);
	}

	/**
	 * Find and translate JS
	 */

	foreach ( $dom->find( 'script' ) as $element ) {
		$texts = array_merge(
			$texts,
			wplng_parse_js( $element->innertext )
		);
	}

	/**
	 * Parse Node text
	 */

	$node_text_excluded = wplng_data_excluded_node_text();

	foreach ( $dom->find( 'text' ) as $element ) {

		$text = wplng_text_esc( $element->innertext );

		if ( in_array( $element->parent->tag, $node_text_excluded )
			|| ! wplng_text_is_translatable( $text )
		) {
			continue;
		}

		$texts[] = $text;
	}

	/**
	 * Parse attr
	 */

	$attr_to_translate = wplng_data_attr_text_to_translate();

	foreach ( $attr_to_translate as $attr ) {
		foreach ( $dom->find( $attr['selector'] ) as $element ) {

			if ( empty( $element->attr[ $attr['attr'] ] ) ) {
				continue;
			}

			$text = wplng_text_esc( $element->attr[ $attr['attr'] ] );

			if ( ! wplng_text_is_translatable( $text ) ) {
				continue;
			}

			$texts[] = $text;
		}
	}

	$texts = array_unique( $texts ); // Remove duplicate

	return $texts;
}
