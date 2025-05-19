<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * wpLingua translate : Get translated HTML
 *
 * @param string $html
 * @param array  $args
 * @return string
 */
function wplng_translate_html( $html, $args = array() ) {

	if ( empty( $html ) ) {
		return $html;
	}

	/**
	 * Create the dom element
	 */

	$dom = wplng_sdh_str_get_html( $html );

	if ( empty( $dom ) ) {
		return $html;
	}

	/**
	 * Replace excluded HTML part by tag
	 */

	$excluded_elements = array();

	$dom = wplng_dom_exclusions_put_tags( $dom, $excluded_elements );

	/**
	 * Update args and get all texts in HTML if needed
	 */

	wplng_args_setup( $args );

	if ( empty( $args['translations'] ) ) {
		$texts = wplng_parse_html( $dom );
		wplng_args_update_from_texts( $args, $texts );
	}

	/**
	 * Update the dom
	 */

	$dom = wplng_dom_load_progress( $dom, $args );
	$dom = wplng_dom_load_overload( $dom, $args );

	$dom = wplng_dom_replace_attr_dir( $dom, $args );
	$dom = wplng_dom_replace_attr_lang( $dom, $args );
	$dom = wplng_dom_replace_body_class( $dom, $args );
	$dom = wplng_dom_replace_links( $dom, $args );

	$dom = wplng_dom_translate_nodes_texts( $dom, $args );
	$dom = wplng_dom_translate_attr_texts( $dom, $args );
	$dom = wplng_dom_translate_html_attr( $dom, $args );
	$dom = wplng_dom_translate_json( $dom, $args );
	$dom = wplng_dom_translate_js( $dom, $args );

	$dom = wplng_dom_mode_editor( $dom, $args );
	$dom = wplng_dom_mode_list( $dom, $args );

	/**
	 * Replace exclude tags by HTML
	 */

	$dom = wplng_dom_exclusions_replace_tags( $dom, $excluded_elements );

	/**
	 * Dom is ready: check, apply filter and return
	 */

	$dom->save();
	$dom = (string) wplng_sdh_str_get_html( $dom );
	$dom = str_replace( '_wplingua_no_translate_', '', $dom );

	if ( empty( $dom ) ) {
		return $html;
	}

	return $dom;
}
