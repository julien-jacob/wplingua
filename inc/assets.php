<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



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


function mcv_inline_script_all_language() {
	?><script>var mcvAllLanguages = JSON.parse('<?php echo mcv_get_languages_all_json(); ?>');</script>
	<?php
}



function mcv_register_assets() {

	if ( is_admin() ) {
		return;
	}

	wp_enqueue_script( 'jquery' );

	wp_enqueue_script(
		'machiavel',
		plugins_url() . '/machiavel/js/script.js',
		array( 'jquery' )
	);

	wp_enqueue_style(
		'machiavel',
		plugins_url() . '/machiavel/css/front.css'
	);
}
