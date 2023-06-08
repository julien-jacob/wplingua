<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function wplng_replace_og_local( $html ) {

	if ( ! wplng_url_current_is_translatable() ) {
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



function wplng_init() {

	if ( ! wplng_url_current_is_translatable() ) {
		return;
	}

	global $wplng_request_uri;

	$current_path           = $wplng_request_uri;
	$origin_path            = '/' . substr( $current_path, 4, strlen( $current_path ) - 1 );
	$_SERVER['REQUEST_URI'] = $origin_path;


	if ( isset( $_GET['wplingua-visual-editor'] ) ) {
		// TODO : wp_nonce ?
		// TODO : Meilleur argument GET ?

		ob_start( 'wplng_ob_callback_editor' );
	} else {
		ob_start( 'wplng_ob_callback_translate' );
	}

}

