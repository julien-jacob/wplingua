<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


add_action( 'admin_enqueue_scripts', 'mcv_enqueue_callback' );
function mcv_enqueue_callback( $hook ) {

	if ( ! is_admin() || $hook !== 'toplevel_page_machiavel/inc/option-page' ) {
		return;
	}

	wp_enqueue_script( 'jquery' );
	
	wp_enqueue_script(
		'machiavel-option',
		plugins_url() . '/machiavel/js/option-page.js',
		array( 'jquery' )
	);

	wp_enqueue_style(
		'machiavel-option',
		plugins_url() . '/machiavel/css/option-page.css'
	);
}

add_action( 'toplevel_page_machiavel/inc/option-page', 'mcv_inline_script_all_language' );
function mcv_inline_script_all_language() {
	?><script>var mcvAllLanguages = JSON.parse('<?php echo mcv_all_language_json(); ?>'); console.log(mcvAllLanguages);</script><?php
}


add_action( 'wp_enqueue_scripts', 'mcv_register_assets' );
function mcv_register_assets() {

	if ( is_admin() ) {
		return;
	}

	wp_enqueue_script( 'jquery' );

	wp_enqueue_script(
		'machiavel',
		plugins_url( 'js/script.js', __FILE__ ),
		array( 'jquery' )
	);

	wp_enqueue_style(
		'machiavel',
		plugins_url( 'css/front.css', __FILE__ )
	);
}
