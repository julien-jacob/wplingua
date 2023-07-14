<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


// TODO : Shortcode no translate

// TODO : Shortcode switcher
function wplng_shortcode_switcher( $atts ) {
	
	$attributes = shortcode_atts(
		array(
			'style' => false,
			'theme' => false,
			'title' => false,
		), $atts
	);

	$class = 'insert-shortcode';

	if ( empty( $attributes['style'] ) ) {
		$class .= ' style-' . wplng_get_switcher_style();
	}

	if ( empty( $attributes['theme'] ) ) {
		$class .= ' theme-' . wplng_get_switcher_theme();
	}

	if ( empty( $attributes['title'] ) ) {
		$class .= ' title-' . wplng_get_switcher_name_format();
	}

	return wplng_get_switcher_html(
		$class,
		wplng_get_switcher_flags_style() !== 'none'
	);

}
add_shortcode( 'wplingua-switcher', 'wplng_shortcode_switcher' );
