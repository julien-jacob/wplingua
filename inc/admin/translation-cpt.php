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


/**
 * Filter translations by status: Display option on CPT list
 *
 * @return void
 */
function wplng_restrict_manage_posts_translation_status() {

	if ( empty( $_GET['post_type'] )
		|| 'wplng_translation' !== $_GET['post_type']
	) {
		return;
	}

	$languages_target   = wplng_get_languages_target_ids();
	$options            = array();
	$translation_status = '';

	if ( ! empty( $_GET['translation_status'] ) ) {
		$translation_status = sanitize_title( $_GET['translation_status'] );
	}

	if ( count( $languages_target ) === 1 ) {
		$options = array(
			''           => __( 'All translation status', 'wplingua' ),
			'reviewed'   => __( 'Reviewed', 'wplingua' ),
			'unreviewed' => __( 'Unreviewed', 'wplingua' ),
		);
	} else {
		$options = array(
			''                   => __( 'All translation status', 'wplingua' ),
			'full-reviewed'      => __( 'Full reviewed', 'wplingua' ),
			'partially-reviewed' => __( 'Partially reviewed', 'wplingua' ),
			'reviewed'           => __( 'Reviewed', 'wplingua' ),
			'unreviewed'         => __( 'Full unreviewed', 'wplingua' ),
		);
	}

	$html = '<select name="translation_status">';

	foreach ( $options as $value => $label ) {

		$html .= '<option ';
		$html .= 'value="' . esc_attr( $value ) . '" ';

		if ( $value === $translation_status ) {
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
 * Filter translations by status: Apply custom query on CPT for translation_status
 *
 * @param object $query
 * @return void
 */
function wplng_posts_filter_translation_status( $query ) {

	global $pagenow;

	if ( empty( $_GET['post_type'] )
		|| 'wplng_translation' !== $_GET['post_type']
		|| empty( $_GET['translation_status'] )
		|| ! is_admin()
		|| $pagenow !== 'edit.php'
	) {
		return;
	}

	switch ( $_GET['translation_status'] ) {
		case 'full-reviewed':
			$query->set(
				'meta_query',
				array(
					'relation' => 'AND',
					array(
						'key'     => 'wplng_translation_translations',
						'value'   => '"status":\d',
						'compare' => 'REGEXP',
					),
					array(
						'key'     => 'wplng_translation_translations',
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
						'key'     => 'wplng_translation_translations',
						'value'   => '"status":\d',
						'compare' => 'REGEXP',
					),
					array(
						'key'     => 'wplng_translation_translations',
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
						'key'     => 'wplng_translation_translations',
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
						'key'     => 'wplng_translation_translations',
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
 * @param array String array
 * @return array
 */
function wplng_translation_status_columns( $columns ) {

	$cb = array();

	if ( isset( $columns['cb'] ) ) {
		$cb = array(
			'cb' => $columns['cb'],
		);
		unset( $columns['cb'] );
	}

	$columns = array_merge(
		$cb,
		array(
			'wplng_status' => __( 'Translation status', 'wplingua' ),
		),
		$columns
	);

	return $columns;
}


/**
 * Add status items in custom column on translations
 *
 * @param array $column
 * @param int $post_id
 * @return void
 */
function wplng_translation_status_item( $column, $post_id ) {

	if ( 'wplng_status' !== $column ) {
		return;
	}

	$translations = get_post_meta(
		$post_id,
		'wplng_translation_translations',
		true
	);

	$translations = json_decode(
		$translations,
		true
	);

	if ( empty( $translations ) ) {
		return;
	}

	$has_a_reviewed       = false;
	$is_not_full_reviewed = false;

	foreach ( $translations as $key => $translation ) {
		if ( empty( $translation['status'] ) ) {
			continue;
		}

		if ( 'generated' === $translation['status']
			|| 'ungenerated' === $translation['status']
		) {
			$is_not_full_reviewed = true;
		} elseif ( is_int( $translation['status'] ) ) {
			$has_a_reviewed = true;
		}
	}

	if ( ! $is_not_full_reviewed && $has_a_reviewed ) {
		echo '<span';
		echo ' class="dashicons dashicons-yes-alt wplng-status-full-review"';
		echo ' title="' . __( 'Full reviewed', 'wplingua' ) . '"';
		echo '></span>';
	} elseif ( $is_not_full_reviewed && ! $has_a_reviewed ) {
		echo '<span';
		echo ' class="dashicons dashicons-yes-alt wplng-status-has-review"';
		echo ' title="' . __( 'Partially reviewed', 'wplingua' ) . '"';
		echo '></span>';
	} else {
		echo '<span';
		echo ' class="dashicons dashicons-yes-alt wplng-status-unreview"';
		echo ' title="' . __( 'Unreviewed', 'wplingua' ) . '"';
		echo '></span>';
	}

}


/**
 * Add inline CSS for status on translations
 *
 * @return void
 */
function wplng_translation_status_style() {

	global $typenow;

	if ( 'wplng_translation' !== $typenow ) {
		return;
	}

	?>
	<style>

		/**
		* wpLingua: Translation status design
		*/

		.manage-column.column-wplng_status {
			width: 20px;
			padding: 8px 0 0 3px;
			font-size: 0;
			vertical-align: middle;			
		}

		.manage-column.column-wplng_status::before {
			content: "\f326";
			font-family: dashicons;
			font-size: 16px;
		}

		body.post-type-wplng_translation .column-wplng_status {
			padding: 8px 0 0 3px;
		}

		body.post-type-wplng_translation .wplng-status-full-review {
			color: #00a32a;
		}

		body.post-type-wplng_translation .wplng-status-has-review {
			color: #c3c4c7;
		}

		body.post-type-wplng_translation .wplng-status-unreview {
			color: #72aee6;
		}
	</style>
	<?php
}
