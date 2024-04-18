<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Print HTML of alternate link with hreflang for each available languages
 *
 * @return void
 */
function wplng_link_alternate_hreflang() {

	if ( ! wplng_url_is_translatable() ) {
		return;
	}

	$html             = PHP_EOL . PHP_EOL;
	$language_website = wplng_get_language_website();
	$languages_target = wplng_get_languages_target();
	$url_original     = wplng_get_url_original();
	$url_x_default    = '';

	if ( empty( $language_website ) || empty( $languages_target ) ) {
		return;
	}

	// Create the starting comment
	$html .= '<!-- This site is made multilingual with the wpLingua plugin -->';
	$html .= PHP_EOL;

	// Create meta generator
	$html .= '<meta ';
	$html .= 'name="generator" ';
	$html .= 'content="wpLingua ' . esc_attr( WPLNG_PLUGIN_VERSION ) . '"/>';
	$html .= PHP_EOL;

	// Create alternate link for website language

	if ( 'en' === $language_website['id'] ) {
		$url_x_default = $url_original;
	}

	$html .= '<link ';
	$html .= 'rel="alternate" ';
	$html .= 'href="' . esc_url( $url_original ) . '" ';
	$html .= 'hreflang="' . esc_attr( $language_website['id'] ) . '"/>';
	$html .= PHP_EOL;

	// Create alternate link for each target languages
	foreach ( $languages_target as $language_target ) {

		$url = wplng_get_url_current_for_language( $language_target['id'] );

		if ( 'en' === $language_target['id'] ) {
			$url_x_default = $url;
		}

		$html .= '<link ';
		$html .= 'rel="alternate" ';
		$html .= 'href="' . esc_url( $url ) . '" ';
		$html .= 'hreflang="' . esc_attr( $language_target['id'] ) . '"/>';
		$html .= PHP_EOL;
	}

	// Create alternate link for x-default

	if ( '' === $url_x_default ) {
		$url_x_default = $url_original;
	}

	$html .= '<link ';
	$html .= 'rel="alternate" ';
	$html .= 'href="' . esc_url( $url_x_default ) . '" ';
	$html .= 'hreflang="x-default"/>';
	$html .= PHP_EOL;

	// Create the ending comment
	$html .= '<!-- / wpLingua plugin. -->' . PHP_EOL . PHP_EOL;

	echo $html;
}
