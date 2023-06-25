<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_enqueue_callback( $hook ) {

	if ( ! is_admin() || $hook !== 'toplevel_page_wplingua/inc/option-page' ) {
		return;
	}

	wp_enqueue_script( 'jquery' );

	wp_enqueue_script(
		'wplingua-option',
		plugins_url() . '/wplingua/js/admin/option-page.js',
		array( 'jquery' )
	);

	wp_enqueue_style(
		'wplingua-option',
		plugins_url() . '/wplingua/css/admin/option-page.css'
	);
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
}


function wplng_dictionary_assets() {
	global $post_type;
	if ( 'wplng_dictionary' === $post_type ) {

		wp_enqueue_script(
			'wplingua-dictionary',
			plugins_url() . '/wplingua/js/admin/dictionary.js',
			array( 'jquery' )
		);

		wp_enqueue_style(
			'wplingua-dictionary',
			plugins_url() . '/wplingua/css/admin/dictionary.css'
		);

	}

}


function wplng_translation_assets() {
	global $post_type;
	if ( 'wplng_translation' === $post_type ) {

		wp_enqueue_script(
			'wplingua-translation',
			plugins_url() . '/wplingua/js/admin/translation.js',
			array( 'jquery' )
		);

		wp_enqueue_style(
			'wplingua-translation',
			plugins_url() . '/wplingua/css/admin/translation.css'
		);

	}

}


function wplng_inline_script_all_language() {
	?><script>var wplngAllLanguages = JSON.parse('<?php echo wplng_get_languages_all_json(); ?>');</script>
	<?php
}