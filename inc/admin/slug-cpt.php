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
 * Remove quick edit on wpLingua slugs list.
 *
 * This function is a filter on post_row_actions and is used to remove the
 * quick edit action from the list of actions on the slugs list page.
 *
 * @param array   $actions An array of row action links.
 * @param WP_Post $post    The post object.
 *
 * @return array
 */
function wplng_slug_remove_quick_edit( $actions, $post ) {

	// Check if the post type is 'wplng_slug'.
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
 * This function is a filter on the posts_per_page option and is used to
 * display 100 translations by default in the admin area.
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

	// Check if we are on the right post type.
	if ( empty( $_GET['post_type'] )
		|| 'wplng_slug' !== $_GET['post_type']
	) {
		return;
	}

	// Get languages target IDs.
	$languages_target = wplng_get_languages_target_ids();

	// Set default options for the select.
	$options = array(
		'' => __( 'All slug status', 'wplingua' ),
	);

	// Set slug status value from URL parameter if set.
	$slug_status = '';

	if ( ! empty( $_GET['slug_status'] ) ) {
		$slug_status = sanitize_title( $_GET['slug_status'] );
	}

	// If only one language target id, then use simple status options.
	if ( count( $languages_target ) === 1 ) {
		$options = array_merge(
			$options,
			array(
				'reviewed'   => __( 'Reviewed', 'wplingua' ),
				'unreviewed' => __( 'Unreviewed', 'wplingua' ),
			)
		);
	} else {
		// If more than one language target id,
		// then use full, partially and reviewed status options.
		$options = array_merge(
			$options,
			array(
				'full-reviewed'      => __( 'Full reviewed', 'wplingua' ),
				'partially-reviewed' => __( 'Partially reviewed', 'wplingua' ),
				'reviewed'           => __( 'Reviewed', 'wplingua' ),
				'unreviewed'         => __( 'Full unreviewed', 'wplingua' ),
			)
		);
	}

	// Display the select with options.
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
 * Filter slugs by status: Apply custom query on CPT for slug_status.
 *
 * This function modifies the query to filter slugs based on their review status.
 * It is hooked into the WordPress query system for 'wplng_slug' post type
 * on the admin edit screen.
 *
 * @param object $query The current WP_Query instance.
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

	// Determine the slug status and set the corresponding meta query.
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




/**
 * Add status custom column on translations
 *
 * @param array $columns String array
 * @return array
 */
function wplng_slug_status_columns( $columns ) {

	// Save the 'cb' column value if it exists
	$cb = array();
	if ( isset( $columns['cb'] ) ) {
		$cb = array(
			'cb' => $columns['cb'],
		);
		unset( $columns['cb'] );
	}

	// Add the 'wplng_status' column
	$columns = array_merge(
		$cb,
		array(
			'wplng_status' => __( 'Slug status', 'wplingua' ),
		),
		$columns
	);

	return $columns;
}


/**
 * Add slug status class on wplng_slug post list in admin area
 *
 * @param string[] $classes An array of post class names.
 * @param string[] $css_class An array of additional class names added to the post.
 * @param int      $post_id The post ID.
 * @return string[]
 */
function wplng_post_class_slug_status( $classes, $css_class, $post_id ) {

	global $typenow;

	if ( 'wplng_slug' !== $typenow ) {
		return $classes;
	}

	$translations = get_post_meta(
		$post_id,
		'wplng_slug_translations',
		true
	);

	$translations = json_decode(
		$translations,
		true
	);

	if ( empty( $translations ) ) {
		return $classes;
	}

	$languages_target_ids = wplng_get_languages_target_ids();
	$has_a_reviewed       = false;
	$is_not_full_reviewed = false;
	$checked_language     = array();
	$status_class         = 'wplng-status-unreview';

	foreach ( $languages_target_ids as $language_target_id ) {
		foreach ( $translations as $translation ) {
			if ( $language_target_id !== $translation['language_id']
				|| empty( $translation['status'] )
			) {
				continue;
			}

			$checked_language[] = $translation['language_id'];

			if ( 'generated' === $translation['status']
				|| 'ungenerated' === $translation['status']
			) {
				$is_not_full_reviewed = true;
			} elseif ( is_int( $translation['status'] ) ) {
				$has_a_reviewed = true;
			}
		}
	}

	if ( ! $is_not_full_reviewed && $has_a_reviewed ) {

		if ( $checked_language === $languages_target_ids ) {
			$status_class = 'wplng-status-full-review';
		} else {
			$status_class = 'wplng-status-has-review';
		}
	} elseif ( ! $is_not_full_reviewed || $has_a_reviewed ) {
		$status_class = 'wplng-status-has-review';
	}

	if ( 'wplng-status-full-review' === $status_class
		&& $checked_language !== $languages_target_ids
	) {
		$status_class = 'wplng-status-has-review';
	}

	$classes[] = $status_class;

	return $classes;
}


/**
 * Add status items in custom column on slugs
 *
 * @param string $column The name of the column to display.
 * @param int    $post_id The current post ID.
 * @return void
 */
function wplng_slug_status_item( $column, $post_id ) {

	if ( 'wplng_status' !== $column ) {
		return;
	}

	$html  = '<span';
	$html .= ' class="dashicons dashicons-yes-alt wplng-status wplng-status-full-review"';
	$html .= ' title="' . __( 'Full reviewed', 'wplingua' ) . '"';
	$html .= '></span>';

	$html .= '<span';
	$html .= ' class="dashicons dashicons-yes-alt wplng-status wplng-status-has-review"';
	$html .= ' title="' . __( 'Partially reviewed', 'wplingua' ) . '"';
	$html .= '></span>';

	$html .= '<span';
	$html .= ' class="dashicons dashicons-yes-alt wplng-status wplng-status-unreview"';
	$html .= ' title="' . __( 'Unreviewed', 'wplingua' ) . '"';
	$html .= '></span>';

	echo $html;
}


/**
 * Add status items text on slugs
 *
 * Defaults $actions are 'Edit', ‘Quick Edit’, 'Restore', 'Trash', ‘Delete Permanently’, 'Preview', and 'View'.
 *
 * @param string[] $actions An array of row action links.
 * @param WP_Post  $post The post object.
 * @return string[]
 */
function wplng_post_row_actions_slug_status( $actions, $post ) {

	if ( 'wplng_slug' !== $post->post_type ) {
		return $actions;
	}

	$html = __( 'Slug status: ', 'wplingua' );

	$html .= '<span';
	$html .= ' class="wplng-status wplng-status-full-review"';
	$html .= '>';
	$html .= __( 'Full reviewed', 'wplingua' );
	$html .= '</span>';

	$html .= '<span';
	$html .= ' class="wplng-status wplng-status-has-review"';
	$html .= '>';
	$html .= __( 'Partially reviewed', 'wplingua' );
	$html .= '</span>';

	$html .= '<span';
	$html .= ' class="wplng-status wplng-status-unreview"';
	$html .= '>';
	$html .= __( 'Unreviewed', 'wplingua' );
	$html .= '</span>';

	$actions['wplng-status-text'] = $html;

	return $actions;
}


/**
 * Add inline CSS for status on slugs
 *
 * @return void
 */
function wplng_slug_status_style() {

	global $typenow;

	if ( 'wplng_slug' !== $typenow ) {
		return;
	}

	?>
	<style>

		/**
		* wpLingua: slug status design
		*/

		.manage-column.column-wplng_status {
			width: 20px;
			padding: 8px 0 0 8px;
			font-size: 0;
			vertical-align: middle;
			box-sizing: content-box;
		}

		.wp-list-table tr td.wplng_status.column-wplng_status::before {
			content: "" !important;
			display: none;
		}

		#the-list .type-wplng_slug .wplng_status.column-wplng_status {
			padding: 8px 4px;
		}

		.manage-column.column-wplng_status::before {
			content: "\f326";
			font-family: dashicons;
			font-size: 16px;
		}

		#the-list .type-wplng_slug .wplng-status-text {
			color: #1d2327;
		}

		#the-list .type-wplng_slug .wplng-status-text .wplng-status {
			font-weight: 600;
		}

		/* ------------------------------- */

		#the-list .type-wplng_slug .wplng-status.wplng-status-full-review  {
			color: #00a32a;
		}

		#the-list .type-wplng_slug .wplng-status.wplng-status-has-review  {
			color: #72aee6;
		}

		#the-list .type-wplng_slug .wplng-status.wplng-status-unreview {
			color: #c3c4c7;
		}

		#the-list .type-wplng_slug .wplng-status-text .wplng-status.wplng-status-unreview {
			color: #1d2327;
		}
		
		/* ------------------------------- */

		#the-list .type-wplng_slug.wplng-status-full-review .wplng-status.wplng-status-has-review,
		#the-list .type-wplng_slug.wplng-status-full-review .wplng-status.wplng-status-unreview,
		#the-list .type-wplng_slug.wplng-status-has-review .wplng-status.wplng-status-full-review,
		#the-list .type-wplng_slug.wplng-status-has-review .wplng-status.wplng-status-unreview,
		#the-list .type-wplng_slug.wplng-status-unreview .wplng-status.wplng-status-full-review,
		#the-list .type-wplng_slug.wplng-status-unreview .wplng-status.wplng-status-has-review {
			display: none;
		}
	</style>
	<?php
}
