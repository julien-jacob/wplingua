<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * wpLingua translate : Get translated HTML
 *
 * @param string $html
 * @param array $args
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

	wplng_args_setup_with_html( $args, $dom );
	// return var_export($args, true);

	/**
	 * Update the dom
	 */

	$dom = wplng_dom_replace_attr_dir( $dom, $args );
	$dom = wplng_dom_replace_attr_lang( $dom, $args );
	$dom = wplng_dom_replace_body_class( $dom, $args );
	$dom = wplng_dom_replace_links( $dom, $args );
	$dom = wplng_dom_translate_json( $dom, $args );
	$dom = wplng_dom_translate_js( $dom, $args );
	$dom = wplng_dom_translate_texts_attr( $dom, $args );
	$dom = wplng_dom_translate_texts_nodes( $dom, $args );
	// $dom = wplng_dom_load_progress( $dom, $args );
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

	if ( empty( $dom ) ) {
		return $html;
	}

	return $dom;
}
