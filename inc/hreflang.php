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
	$url_x_default    = '';

	if ( empty( $language_website ) || empty( $languages_target ) ) {
		return;
	}

	// Get and clear original URL
	$url_original = wplng_get_url_original();
	$url_original = remove_query_arg(
		array( 'wplng-mode', 'wplng-load', 'nocache' ),
		$url_original
	);

	// Create the starting comment
	$html .= '<!-- This website is made multilingual with the wpLingua plugin -->';
	$html .= PHP_EOL;

	// Create meta generator
	$html .= '<meta';
	$html .= ' name="generator"';
	$html .= ' content="wpLingua ' . esc_attr( WPLNG_PLUGIN_VERSION ) . '"';
	$html .= '/>' . PHP_EOL;

	// Create alternate link for website language

	if ( 'en' === $language_website['id'] ) {
		$url_x_default = $url_original;
	}

	$html .= '<link';
	$html .= ' rel="alternate"';
	$html .= ' href="' . esc_url( $url_original ) . '"';
	$html .= ' hreflang="' . esc_attr( $language_website['id'] ) . '"';
	$html .= '/>' . PHP_EOL;

	// Create alternate link for each target languages
	foreach ( $languages_target as $language_target ) {

		$url = wplng_url_translate(
			$url_original,
			$language_target['id']
		);

		if ( 'en' === $language_target['id'] ) {
			$url_x_default = $url;
		}

		$html .= '<link';
		$html .= ' rel="alternate"';
		$html .= ' href="' . esc_url( $url ) . '"';
		$html .= ' hreflang="' . esc_attr( $language_target['id'] ) . '"';
		$html .= '/>' . PHP_EOL;
	}

	// Create alternate link for x-default

	if ( '' === $url_x_default ) {
		$url_x_default = $url_original;
	}

	$url_x_default = apply_filters( 'wplng_hreflang_x_default', $url_x_default );

	if ( ! empty( $url_x_default ) ) {
		$html .= '<link';
		$html .= ' rel="alternate"';
		$html .= ' href="' . esc_url( $url_x_default ) . '"';
		$html .= ' hreflang="x-default"';
		$html .= '/>' . PHP_EOL;
	}

	// Create the ending comment
	$html .= '<!-- / wpLingua plugin. -->' . PHP_EOL . PHP_EOL;

	echo $html;
}


/**
 * Disable web browser automatic translation by adding attribute to HTML tag
 * <html lang="??_??" translate="no">
 *
 * @param string $attr
 * @return string $attr
 */
function wplng_disable_web_browser_auto_translate( $attr ) {
	return $attr . ' translate="no"';
}
