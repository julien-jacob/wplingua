<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function wplng_replace_og_local( $html ) {

	if ( ! wplng_url_is_translatable()
		|| wplng_get_language_website_id() === wplng_get_language_current_id()
	) {
		return $html;
	}

	$html = preg_replace(
		'#<meta (.*?)?property=(\"|\')og:locale(\"|\') (.*?)?>#',
		'<meta property=$2og:locale$2 content=$2' . wplng_get_language_current_id() . '$2>',
		$html
	);

	return $html;
}


function wplng_language_attributes( $attr ) {

	$language_current_id = wplng_get_language_current_id();

	if ( is_admin() || empty( $language_current_id ) ) {
		return $attr;
	}

	$attr = preg_replace(
		'#lang=(\"|\')(..)-(..)(\"|\')#i',
		'lang=$1' . esc_attr( $language_current_id ) . '$4',
		$attr
	);

	return $attr;
}



function wplng_link_alternate_hreflang() {

	$html = '';

	// Create alternate link for website language
	$language_website = wplng_get_language_website();
	$html            .= '<link rel="alternate" hreflang="' . esc_attr( $language_website['id'] ) . '" href="' . esc_url( wplng_get_url_original() ) . '">';

	// Create alternate link for each target languages
	$languages_target = wplng_get_languages_target();
	foreach ( $languages_target as $key => $language_target ) {
		$url   = wplng_get_url_current_for_language( $language_target['id'] );
		$html .= '<link rel="alternate" hreflang="' . esc_attr( $language_target['id'] ) . '" href="' . esc_url( $url ) . '">';
	}

	echo $html;
}



function wplng_html_translate_links( $html, $language_target ) {
	$dom = str_get_html( $html );
	foreach ( $dom->find( 'a' ) as $element ) {
		$link          = $element->href;
		$element->href = wplng_url_translate( $link, $language_target );
	}
	foreach ( $dom->find( 'form' ) as $element ) {
		$link            = $element->action;
		$element->action = wplng_url_translate( $link, $language_target );
	}

	$dom->save();
	return (string) str_get_html( $dom );
}


function wplng_get_selector_exclude() {

	$selector_exclude = explode(
		PHP_EOL,
		get_option( 'wplng_excluded_selectors' )
	);

	// Remove empty
	$selector_exclude = array_values( array_filter( $selector_exclude ) );

	// Add default selectors
	$selector_exclude = array_merge(
		$selector_exclude,
		array(
			'#wpadminbar',
			'.no-translate',
			'.notranslate',
			'.wplng-switcher',
		)
	);

	// Remove duplicate
	$selector_exclude = array_unique( $selector_exclude );

	// TODO : Faire le code ci-dessous ?
	// foreach ($selector_exclude as $key => $selector) {
	// 	$selector_exclude[$key] = esc_attr($selector_exclude);
	// }

	$selector_exclude = apply_filters(
		'wplng_selector_exclude',
		$selector_exclude
	);

	return $selector_exclude;
}


function wplng_get_selector_clear() {

	$selector_clear = array(
		'style',
		'script',
		'svg',
	);

	$selector_clear = apply_filters(
		'wplng_selector_clear',
		$selector_clear
	);

	return $selector_clear;
}


function wplng_html_set_exclude_tag( $html, &$excluded_elements ) {

	$selector_exclude = wplng_get_selector_exclude();
	$dom              = str_get_html( $html );

	if ( $dom === false ) {
		return $html;
	}

	foreach ( $selector_exclude as $key => $selector ) {
		foreach ( $dom->find( $selector ) as $element ) {
			$excluded_elements[] = $element->outertext;
			$attr                = count( $excluded_elements ) - 1;
			$element->outertext  = '<div wplng-tag-exclude="' . esc_attr( $attr ) . '"></div>';
		}
	}

	$dom->load( $dom->save() );

	return (string) str_get_html( $dom );
}

function wplng_html_replace_exclude_tag( $html, $excluded_elements ) {

	foreach ( $excluded_elements as $key => $element ) {
		$s    = '<div wplng-tag-exclude="' . esc_attr( $key ) . '"></div>';
		$html = str_replace( $s, $element, $html );
	}

	return $html;
}


function wplng_init() {

	if ( wplng_get_language_website_id() === wplng_get_language_current_id() ) {
		return;
	}

	global $wplng_request_uri;

	$current_path           = $wplng_request_uri;
	$origin_path            = '/' . substr( $current_path, 4, strlen( $current_path ) - 1 );

	if (! wplng_url_is_translatable()) {
		wp_redirect($origin_path);
		exit;
	}

	$_SERVER['REQUEST_URI'] = $origin_path;

	if ( isset( $_GET['wplingua-visual-editor'] ) ) {
		// TODO : wp_nonce ?
		// TODO : Meilleur argument GET ?
		// TODO : Check user can edit post

		ob_start( 'wplng_ob_callback_editor' );
	} else {
		ob_start( 'wplng_ob_callback_translate' );
	}

}

