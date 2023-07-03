<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function wplng_switcher_wp_footer() {

	if ( ! wplng_url_is_translatable() ) {
		return;
	}

	// Return if automatic insert is false

	// $switcher_style -> list, block, dropdown
	// $show_flag -> true, false
	// $languages_name_style -> no, id, name

	echo wplng_get_switcher_html();
}


function wplng_get_switcher_html() {

	$language_website = wplng_get_language_website();
	$languages_target = wplng_get_languages_target();

	// TODO : Check if language original and target is OK, else return

	$html = '<div class="wplng-switcher">';
	$html .= '<div class="wplng-language-current">';

	// Create link for website language
	$language_website = wplng_get_language_website();
	$html            .= '<a class="wplng-language" href="' . esc_url( wplng_get_url_original() ) . '">';
	if ( ! empty( $language_website['flag'] ) ) {
		$html .= '<img src="' . esc_url( $language_website['flag'] ) . '" alt="' . esc_attr( $language_website['name'] ) . '">';
	}
	$html .= esc_html( $language_website['name'] );
	$html .= '</a>';
	$html .= '</div>';
	$html .= '<div class="wplng-languages-target">';

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
	$html .= '</div>';

	$html = apply_filters(
		'wplng_switcher_html',
		$html,
		$language_website,
		$languages_target
	);

	return $html;
}
