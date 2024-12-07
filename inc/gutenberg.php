<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_block_category( $block_categories ) {

	$block_categories[] = array(
		'slug'  => 'wplingua',
		'title' => 'wpLingua',
	);

	return $block_categories;
}


function wplng_register_block() {
	register_block_type(
		'wplingua/languages-switcher',
		array(
			'title'           => __( 'wpLingua: languages switcher', 'wplingua' ),
			'description'     => __( 'Add a language switcher to your page.', 'wplingua' ),
			'icon'            => 'translation',
			'category'        => 'wplingua',
			'render_callback' => 'wplng_render_switcher_block',
			'attributes'      => array(
				'style' => array(
					'type'    => 'string',
					'default' => '',
					'enum'    => array_merge(
						array( '' ),
						array_keys( wplng_data_switcher_valid_style() )
					),
				),
				'title' => array(
					'type'    => 'string',
					'default' => '',
					'enum'    => array_merge(
						array( '' ),
						array_keys( wplng_data_switcher_valid_name_format() )
					),
				),
				'flags' => array(
					'type'    => 'string',
					'default' => '',
					'enum'    => array_merge(
						array( '' ),
						array_keys( wplng_data_switcher_valid_flags_style() )
					),
				),
				'theme' => array(
					'type'    => 'string',
					'default' => '',
					'enum'    => array_merge(
						array( '' ),
						array_keys( wplng_data_switcher_valid_theme() )
					),
				),
			),
			'editor_script'   => 'wplingua-render-block',
		)
	);
}



function wplng_render_switcher_block( $attributes ) {

	if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return wplng_get_switcher_html(
			array_merge(
				$attributes,
				array( 'class' => 'switcher-preview' )
			)
		);
	}

	return wplng_get_switcher_html( $attributes );
}

function wplng_register_block_scripts() {

	wp_enqueue_script(
		'wplingua-render-block',
		plugins_url() . '/wplingua/assets/js/block-switcher.js',
		array(
			'wp-blocks',
			'wp-editor',
			'wp-server-side-render',
		),
		WPLNG_PLUGIN_VERSION
	);

	wp_localize_script(
		'wplingua-render-block',
		'wplngLocalize',
		array(
			'label' => array(
				'title'       => esc_html__( 'Languages switcher', 'wplingua' ),
				'description' => esc_html__( 'Display the wpLingua languages switcher.', 'wplingua' ),
				'default'     => esc_html__( 'Default', 'wplingua' ),
			),
			'input' => array(
				'style' => esc_html__( 'Layout: ', 'wplingua' ),
				'title' => esc_html__( 'Displayed names: ', 'wplingua' ),
				'flags' => esc_html__( 'Flags style:', 'wplingua' ),
				'theme' => esc_html__( 'Color theme: ', 'wplingua' ),
			),
			'style' => wplng_data_switcher_valid_style(),
			'title' => wplng_data_switcher_valid_name_format(),
			'flags' => wplng_data_switcher_valid_flags_style(),
			'theme' => wplng_data_switcher_valid_theme(),
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
