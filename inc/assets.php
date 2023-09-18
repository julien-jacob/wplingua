<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Register asset for wpLingua in front (CSS, JS)
 *
 * @return void
 */
function wplng_register_assets() {

	if ( is_admin() ) {
		return;
	}

	wp_enqueue_script( 'jquery' );

	wp_enqueue_script(
		'wplingua',
		plugins_url() . '/wplingua/assets/js/script.js',
		array( 'jquery' ),
		WPLNG_API_VERSION
	);

	wp_enqueue_style(
		'wplingua',
		plugins_url() . '/wplingua/assets/css/front.css',
		array(),
		WPLNG_API_VERSION
	);

	$custom_css = get_option( 'wplng_custom_css' );

	if ( ! empty( $custom_css ) ) {
		wp_add_inline_style( 'wplingua', $custom_css );
	}

	/**
	 * Load assets for visual editor
	 */
	if ( isset( $_GET['wplingua-editor'] ) ) {

		wp_enqueue_script(
			'wplingua-editor',
			plugins_url() . '/wplingua/assets/js/editor.js',
			array( 'jquery' ),
			WPLNG_API_VERSION
		);

		wp_enqueue_style(
			'wplingua-editor',
			plugins_url() . '/wplingua/assets/css/editor.css',
			array(),
			WPLNG_API_VERSION
		);

	} elseif ( isset( $_GET['wplingua-list'] ) ) {

		wp_enqueue_script(
			'wplingua-list',
			plugins_url() . '/wplingua/assets/js/list.js',
			array( 'jquery' ),
			WPLNG_API_VERSION
		);

		wp_enqueue_style(
			'wplingua-list',
			plugins_url() . '/wplingua/assets/css/list.css',
			array(),
			WPLNG_API_VERSION
		);

	}

}
