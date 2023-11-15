<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Register wpLingua Translation CPT
 *
 * @return void
 */
function wplng_register_post_type_translation() {
	register_post_type(
		'wplng_translation',
		array(
			'labels'              => array(
				'name'          => __( 'Translations', 'wplingua' ),
				'singular_name' => __( 'Translation', 'wplingua' ),
				'all_items'     => __( 'All translations', 'wplingua' ),
				'edit_item'     => __( 'Edit translation', 'wplingua' ),
				'menu_name'     => __( 'Translations', 'wplingua' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'exclude_from_search' => true,
			'show_in_nav_menus'   => false,
			'show_in_menu'        => false,
			'has_archive'         => false,
			'rewrite'             => false,
			'menu_icon'           => 'dashicons-translation',
			'supports'            => array(
				'title',
			),
			'capability_type'     => 'post',
			'capabilities'        => array(
				'create_posts' => false,
			),
			'map_meta_cap'        => true,
		)
	);
}


/**
 * Remove quick edit on wpLingua translations list
 *
 * @param array $actions
 * @param object $post
 * @return array
 */
function wplng_translation_remove_quick_edit( $actions, $post ) {

	if ( $post->post_type != 'wplng_translation' ) {
		return $actions;
	}

	unset( $actions['view'] );
	unset( $actions['inline hide-if-no-js'] );

	return $actions;
}


/**
 * Display 100 translations by default in admin area
 *
 * @param mixed $result
 * @return mixed
 */
function wplng_translation_per_page( $result ) {
	if ( false === $result ) {
		$result = '100';
	}

	return $result;
}
