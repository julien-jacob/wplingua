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



/**
 * Filter slugs by status: Display option on CPT list
 *
 * @return void
 */
function wplng_restrict_manage_posts_slug_status() {

	if ( empty( $_GET['post_type'] )
		|| 'wplng_slug' !== $_GET['post_type']
	) {
		return;
	}

	$languages_target   = wplng_get_languages_target_ids();
	$options            = array();
	$slug_status = '';

	if ( ! empty( $_GET['slug_status'] ) ) {
		$slug_status = sanitize_title( $_GET['slug_status'] );
	}

	if ( count( $languages_target ) === 1 ) {
		$options = array(
			''           => __( 'All slug status', 'wplingua' ),
			'reviewed'   => __( 'Reviewed', 'wplingua' ),
			'unreviewed' => __( 'Unreviewed', 'wplingua' ),
		);
	} else {
		$options = array(
			''                   => __( 'All slug status', 'wplingua' ),
			'full-reviewed'      => __( 'Full reviewed', 'wplingua' ),
			'partially-reviewed' => __( 'Partially reviewed', 'wplingua' ),
			'reviewed'           => __( 'Reviewed', 'wplingua' ),
			'unreviewed'         => __( 'Full unreviewed', 'wplingua' ),
		);
	}

	$html = '<select name="slug_status">';

	foreach ( $options as $value => $label ) {

		$html .= '<option ';
		$html .= 'value="' . esc_attr( $value ) . '" ';

		if ( $value === $slug_status ) {
			$html .= 'selected="selected" ';
		}

		$html .= '>';
		$html .= esc_html( $label );
		$html .= '</option>';
	}

	$html .= '</select>';

	echo $html;
}


/**
 * Filter slugs by status: Apply custom query on CPT for slug_status
 *
 * @param object $query
 * @return void
 */
function wplng_posts_filter_slug_status( $query ) {

	global $pagenow;

	if ( empty( $_GET['post_type'] )
		|| 'wplng_slug' !== $_GET['post_type']
		|| empty( $_GET['slug_status'] )
		|| ! is_admin()
		|| $pagenow !== 'edit.php'
	) {
		return;
	}

	switch ( $_GET['slug_status'] ) {
		case 'full-reviewed':
			$query->set(
				'meta_query',
				array(
					'relation' => 'AND',
					array(
						'key'     => 'wplng_slug_translations',
						'value'   => '"status":\d',
						'compare' => 'REGEXP',
					),
					array(
						'key'     => 'wplng_slug_translations',
						'value'   => '("status":"ungenerated"|"status":"generated")',
						'compare' => 'NOT REGEXP',
					),
				)
			);
			break;

		case 'partially-reviewed':
			$query->set(
				'meta_query',
				array(
					'relation' => 'AND',
					array(
						'key'     => 'wplng_slug_translations',
						'value'   => '"status":\d',
						'compare' => 'REGEXP',
					),
					array(
						'key'     => 'wplng_slug_translations',
						'value'   => '("status":"ungenerated"|"status":"generated")',
						'compare' => 'REGEXP',
					),
				)
			);
			break;

		case 'reviewed':
			$query->set(
				'meta_query',
				array(
					'relation' => 'AND',
					array(
						'key'     => 'wplng_slug_translations',
						'value'   => '"status":\d',
						'compare' => 'REGEXP',
					),
				)
			);
			break;

		case 'unreviewed':
			$query->set(
				'meta_query',
				array(
					'relation' => 'AND',
					array(
						'key'     => 'wplng_slug_translations',
						'value'   => '"status":\d',
						'compare' => 'NOT REGEXP',
					),
				)
			);
			break;

	}

}