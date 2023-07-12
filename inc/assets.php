<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_register_assets() {

	if ( is_admin() ) {
		return;
	}

	wp_enqueue_script( 'jquery' );

	wp_enqueue_script(
		'wplingua',
		plugins_url() . '/wplingua/js/script.js',
		array( 'jquery' )
	);

	wp_enqueue_style(
		'wplingua',
		plugins_url() . '/wplingua/css/front.css'
	);

	$custom_css = get_option( 'wplng_custom_css' );

	if ( ! empty( $custom_css ) ) {
		wp_add_inline_style( 'wplingua', $custom_css );
	}

}
