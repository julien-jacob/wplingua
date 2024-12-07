<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_register_block() {
	register_block_type(
		'wplingua/languages-switcher',
		array(
			'title'           => __( 'wpLingua: languages switcher', 'wplingua' ),
			'description'     => __( 'Add a language switcher to your page.', 'wplingua' ),
			'icon'            => 'translation',
			'render_callback' => 'wplng_render_switcher_block',
		)
	);
}


function wplng_render_switcher_block( $atts ) {

	$output = wplng_get_switcher_html(
		array( 'class' => 'switcher-preview' )
	);

	return $output;
}


function wplng_register_block_scripts() {

	wp_enqueue_script(
		'wplingua-render-block',
		plugins_url() . '/wplingua/assets/js/block.js',
		array(
			'wp-blocks',
			'wp-server-side-render',
		),
		WPLNG_PLUGIN_VERSION
	);

	wp_localize_script(
		'wplingua-render-block',
		'wplngLocalize',
		array(
			'message' => array(
				'title' => esc_html__( 'Languages switcher', 'wplingua' ),
			),
		)
	);

	/**
	 * Enqueue jQuery
	 */

	 wp_enqueue_script( 'jquery' );

	 /**
	  * Enqueue wpLingua JS script
	  */

	wp_enqueue_script(
		'wplingua-script',
		plugins_url() . '/wplingua/assets/js/script.js',
		array( 'jquery' ),
		WPLNG_PLUGIN_VERSION
	);

	 /**
	  * Enqueue wpLingua CSS style
	  */

	wp_enqueue_style(
		'wplingua',
		plugins_url() . '/wplingua/assets/css/front.css',
		array(),
		WPLNG_PLUGIN_VERSION
	);

}
