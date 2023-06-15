<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


// wp_set_object_terms( $post_id, array( $cat_slug ), 'wprock_htmlentity_cat' );

/**
 * Add custom taxonomies
 *
 * Additional custom taxonomies can be defined here
 * https://codex.wordpress.org/Function_Reference/register_taxonomy
 */
function add_custom_taxonomies() {
	// Add new "Locations" taxonomy to Posts
	register_taxonomy(
		'wplng_url', 'wplng_translation', array(
			// Hierarchical taxonomy (like categories)
			'hierarchical' => false,
			// This array of options controls the labels displayed in the WordPress Admin UI
			'labels'       => array(
				'name'              => __( 'URL', 'wplingua' ),
				'singular_name'     => __( 'URL', 'wplingua' ),
				'search_items'      => __( 'Search URL', 'wplingua' ),
				'all_items'         => __( 'All URL', 'wplingua' ),
				'parent_item'       => __( 'Parent URL', 'wplingua' ),
				'parent_item_colon' => __( 'Parent URL:', 'wplingua' ),
				'edit_item'         => __( 'Edit URL', 'wplingua' ),
				'update_item'       => __( 'Update URL', 'wplingua' ),
				'add_new_item'      => __( 'Add New URL', 'wplingua' ),
				'new_item_name'     => __( 'New URL Name', 'wplingua' ),
				'menu_name'         => __( 'URL', 'wplingua' ),
			),
			// Control the slugs used for this taxonomy
			'rewrite'      => array(
				'slug'         => 'wplingua-url', // This controls the base slug that will display before each term
				'with_front'   => false, // Don't display the category base before "/locations/"
				'hierarchical' => false, // This will allow URL's like "/locations/boston/cambridge/"
			),
		)
	);
}
add_action( 'init', 'add_custom_taxonomies', 0 );

/**
 * Display a custom taxonomy dropdown in admin
 * @author Mike Hemberger
 * @link http://thestizmedia.com/custom-post-type-filter-admin-custom-taxonomy/
 */
add_action('restrict_manage_posts', 'tsm_filter_post_type_by_taxonomy');
function tsm_filter_post_type_by_taxonomy() {
	global $typenow;
	$post_type = 'wplng_translation'; // change to your post type
	$taxonomy  = 'wplng_url'; // change to your taxonomy
	if ($typenow == $post_type) {
		$selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
		$info_taxonomy = get_taxonomy($taxonomy);
		wp_dropdown_categories(array(
			'show_option_all' => sprintf( __( 'Show all %s', 'wplingua' ), $info_taxonomy->label ),
			'taxonomy'        => $taxonomy,
			'name'            => $taxonomy,
			'orderby'         => 'name',
			'selected'        => $selected,
			'show_count'      => true,
			'hide_empty'      => true,
		));
	};
}
/**
 * Filter posts by taxonomy in admin
 * @author  Mike Hemberger
 * @link http://thestizmedia.com/custom-post-type-filter-admin-custom-taxonomy/
 */
add_filter('parse_query', 'tsm_convert_id_to_term_in_query');
function tsm_convert_id_to_term_in_query($query) {
	global $pagenow;
	$post_type = 'wplng_translation'; // change to your post type
	$taxonomy  = 'wplng_url'; // change to your taxonomy
	$q_vars    = &$query->query_vars;
	if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
		$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
		$q_vars[$taxonomy] = $term->slug;
	}
}


add_filter('manage_edit-post_tag_columns', function ( $columns ) 
{
    if( isset( $columns['description'] ) )
        unset( $columns['description'] );   

    return $columns;
} );