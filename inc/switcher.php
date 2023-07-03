<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function wplng_switcher_wp_footer() {

	if ( ! wplng_url_is_translatable() ) {
		return;
	}

	echo wplng_get_switcher_html();
}


function wplng_get_switcher_html() {

	$html = '<div class="wplng-switcher">';

	// Create link for website language
	$language_website = wplng_get_language_website();
	$html            .= '<a class="wplng-language" href="' . esc_url( wplng_get_url_original() ) . '">';
	if ( ! empty( $language_website['flag'] ) ) {
		$html .= '<img src="' . esc_url( $language_website['flag'] ) . '" alt="' . esc_attr( $language_website['name'] ) . '">';
	}
	$html .= esc_html( $language_website['name'] );
	$html .= '</a>';

	// Create link for each target languages
	$languages_target = wplng_get_languages_target();
	foreach ( $languages_target as $key => $language_target ) {
		$url   = wplng_get_url_current_for_language( $language_target['id'] );
		$html .= '<a class="wplng-language" href="' . esc_url( $url ) . '">';
		if ( ! empty( $language_website['flag'] ) ) {
			$html .= '<img src="' . esc_url( $language_target['flag'] ) . '" alt="' . esc_attr( $language_target['name'] ) . '">';
		}
		$html .= esc_html( $language_target['name'] );
		$html .= '</a>';
	}

	$html .= '</div>';

	$html = apply_filters(
		'wplng_switcher_html',
		$html,
		$language_website,
		$languages_target
	);

	return $html;
}
