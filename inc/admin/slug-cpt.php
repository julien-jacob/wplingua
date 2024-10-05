<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Register wpLingua Slug CPT
 *
 * @return void
 */
function wplng_register_post_type_slug() {
	register_post_type(
		'wplng_slug',
		array(
			'labels'              => array(
				'name'          => __( 'Website slugs', 'wplingua' ),
				'singular_name' => __( 'Website slug', 'wplingua' ),
				'all_items'     => __( 'All slugs', 'wplingua' ),
				'edit_item'     => __( 'Edit slug', 'wplingua' ),
				'menu_name'     => __( 'Website slugs', 'wplingua' ),
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
 * Remove quick edit on wpLingua slugs list
 *
 * @param array $actions
 * @param object $post
 * @return array
 */
function wplng_slug_remove_quick_edit( $actions, $post ) {

	if ( $post->post_type != 'wplng_slug' ) {
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
function wplng_slug_per_page( $result ) {
	if ( false === $result ) {
		$result = '100';
	}

	return $result;
}
