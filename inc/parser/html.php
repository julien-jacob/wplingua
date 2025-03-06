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
	 * Find and parse JSON in scripts
	 */

	foreach ( $dom->find( 'script[type="application/ld+json"]' ) as $element ) {
		$texts = array_merge(
			$texts,
			wplng_parse_json( $element->innertext )
		);
	}

	/**
	 * Parse JSON in attriutes
	 */

	$attr_json_to_translate = wplng_data_attr_json_to_translate();

	foreach ( $attr_json_to_translate as $attr ) {
		foreach ( $dom->find( $attr['selector'] ) as $element ) {

			if ( empty( $element->attr[ $attr['attr'] ] ) ) {
				continue;
			}

			$json = wp_specialchars_decode(
				$element->attr[ $attr['attr'] ],
				ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401
			);

			$texts = array_merge(
				$texts,
				wplng_parse_json( $json )
			);
		}
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

		if ( in_array( $element->parent->tag, $node_text_excluded ) ) {
			continue;
		}

		$text = wplng_text_esc( $element->innertext );

		if ( ! wplng_text_is_translatable( $text ) ) {
			continue;
		}

		$texts[] = $text;
	}

	/**
	 * Parse texts in attriutes
	 */

	$attr_texts_to_translate = wplng_data_attr_text_to_translate();

	foreach ( $attr_texts_to_translate as $attr ) {
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

	/**
	 * Parse HTML in attriutes
	 */

	$attr_html_to_translate = wplng_data_attr_html_to_translate();

	foreach ( $attr_html_to_translate as $attr ) {
		foreach ( $dom->find( $attr['selector'] ) as $element ) {

			if ( empty( $element->attr[ $attr['attr'] ] ) ) {
				continue;
			}

			$html = wp_specialchars_decode(
				$element->attr[ $attr['attr'] ],
				ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401
			);

			$texts = array_merge(
				$texts,
				wplng_parse_html( $html )
			);

		}
	}

	return $texts;
}
