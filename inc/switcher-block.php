<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Adds the 'wpLingua' category to the Gutenberg block categories
 *
 * @param array $block_categories The block categories.
 * @return array The updated block categories.
 */
function wplng_block_category( $block_categories ) {

	// Create a new category for the wpLingua language switcher block
	$new_category = array(
		'slug'  => 'wplingua',
		'title' => 'wpLingua',
	);

	// Find the index of the 'design' category in the block categories array
	$design_index = array_search( 'design', array_column( $block_categories, 'slug' ) );

	if ( $design_index !== false ) {
		// If the 'design' category is found, insert the new category after it
		array_splice( $block_categories, $design_index + 1, 0, array( $new_category ) );
	} else {
		// If the 'design' category is not found, append the new category to the end of the array
		$block_categories[] = $new_category;
	}

	return $block_categories;
}


/**
 * Registers the block with the name 'wplingua/languages-switcher'
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-registration/
 */
function wplng_register_block() {
	register_block_type(
		'wplingua/languages-switcher',
		array(
			'title'           => __( 'wpLingua: languages switcher', 'wplingua' ),
			'description'     => __( 'Add a language switcher to your page.', 'wplingua' ),
			'icon'            => 'translation',
			'category'        => 'wplingua',
			'render_callback' => 'wplng_render_switcher_block',
			'keywords'        => array(
				__( 'language', 'wplingua' ),
				__( 'switcher', 'wplingua' ),
				__( 'multilanguage', 'wplingua' ),
				__( 'multilingual', 'wplingua' ),
				__( 'translate', 'wplingua' ),
				__( 'translation', 'wplingua' ),
				__( 'flag', 'wplingua' ),
				__( 'flags', 'wplingua' ),
				'wpLingua',
			),
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
			'editor_script'   => 'wplingua-block-switcher',
		)
	);
}


/**
 * Renders the block content.
 *
 * @param array $attributes The block attributes.
 * @return string The block content.
 */
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


/**
 * Registers the JavaScript and CSS files for the block editor.
 *
 * This function enqueues the necessary scripts and styles for the wpLingua
 * language switcher block in the Gutenberg editor. It also localizes script
 * data for use in JavaScript and includes necessary dependencies like jQuery.
 */
function wplng_register_block_assets() {

	// Enqueue the script for rendering the language switcher block in the editor
	wp_enqueue_script(
		'wplingua-block-switcher',
		plugins_url() . '/wplingua/assets/js/block-switcher.js',
		array(
			'wp-blocks',
			'wp-editor',
			'wp-server-side-render',
		),
		WPLNG_PLUGIN_VERSION
	);

	// Localize script data for use in JavaScript
	wp_localize_script(
		'wplingua-block-switcher',
		'wplngI18nGutenberg',
		array(
			'label' => array(
				'title'       => esc_html__( 'Languages switcher', 'wplingua' ),
				'description' => esc_html__( 'Display the wpLingua languages switcher.', 'wplingua' ),
				'default'     => esc_html__( 'Default', 'wplingua' ),
			),
			'input' => array(
				'style' => esc_html__( 'Layout: ', 'wplingua' ),
				'title' => esc_html__( 'Displayed names: ', 'wplingua' ),
				'flags' => esc_html__( 'Flags style: ', 'wplingua' ),
				'theme' => esc_html__( 'Color theme: ', 'wplingua' ),
			),
			'style' => wplng_data_switcher_valid_style(),
			'title' => wplng_data_switcher_valid_name_format(),
			'flags' => wplng_data_switcher_valid_flags_style(),
			'theme' => wplng_data_switcher_valid_theme(),
		)
	);

	// Enqueue jQuery
	wp_enqueue_script( 'jquery' );

	// Enqueue wpLingua main JavaScript file
	wp_enqueue_script(
		'wplingua-script',
		plugins_url() . '/wplingua/assets/js/front.js',
		array( 'jquery' ),
		WPLNG_PLUGIN_VERSION
	);

	// Enqueue wpLingua frontend CSS style
	wp_enqueue_style(
		'wplingua',
		plugins_url() . '/wplingua/assets/css/front.css',
		array(),
		WPLNG_PLUGIN_VERSION
	);
}
