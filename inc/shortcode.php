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
			'insert' => false,
			'style'  => false,
			'title'  => false,
			'theme'  => false,
			'flags'  => false,
			'class'  => false,
		), $atts
	);

	$class = 'insert-shortcode';


	return wplng_get_switcher_html( $attributes );

}
add_shortcode( 'wplingua-switcher', 'wplng_shortcode_switcher' );
