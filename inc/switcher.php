<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function mcv_switcher_wp_footer() {

	if ( is_admin() ) {
		return;
	}

	echo mcv_get_switcher_html();
}


function mcv_get_switcher_html() {

	$html = '<div class="mcv-switcher">';

	// Create link for website language
	$language_website = mcv_get_language_website();
	$html            .= '<a class="mcv-language" href="' . esc_url( mcv_get_url_original() ) . '">';
	if ( ! empty( $language_website['flag'] ) ) {
		$html .= '<img src="' . esc_url( $language_website['flag'] ) . '" alt="' . esc_attr( $language_website['name'] ) . '">';
	}
	$html .= esc_html( $language_website['name'] );
	$html .= '</a>';

	// Create link for each target languages
	$languages_target = mcv_get_languages_target();
	foreach ( $languages_target as $key => $language_target ) {
		$url   = mcv_get_url_current_for_language( $language_target['id'] );
		$html .= '<a class="mcv-language" href="' . esc_url( $url ) . '">';
		if ( ! empty( $language_website['flag'] ) ) {
			$html .= '<img src="' . esc_url( $language_target['flag'] ) . '" alt="' . esc_attr( $language_target['name'] ) . '">';
		}
		$html .= esc_html( $language_target['name'] );
		$html .= '</a>';
	}

	$html .= '</div>';

	$html = apply_filters(
		'mcv_switcher_html',
		$html,
		$language_website,
		$languages_target
	);

	return $html;
}
