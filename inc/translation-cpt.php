<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_register_post_type_translation() {
	register_post_type(
		'wplng_translation',
		array(
			'labels'              => array(
				'name'          => __( 'Translations', 'wplingua' ),
				'singular_name' => __( 'Translation', 'wplingua' ),
			),
			'public'              => false,  // it's not public, it shouldn't have it's own permalink, and so on
			'publicly_queryable'  => true,  // you should be able to query it
			'show_ui'             => true,  // you should be able to edit it in wp-admin
			'exclude_from_search' => true,  // you should exclude it from search results
			'show_in_nav_menus'   => false,  // you shouldn't be able to add it to menus
			'has_archive'         => false,  // it shouldn't have archive page
			'rewrite'             => false,  // it shouldn't have rewrite rules
			'supports'            => array(
				'title',
				'revisions',
			),
			'capability_type'     => 'post',
			'capabilities'        => array(
				'create_posts' => false, // Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups )
			),
			'map_meta_cap'        => true, // Set to `false`, if users are not allowed to edit/delete existing posts
		)
	);
}
