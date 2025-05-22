<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Add wpLingua menu in Admin Bar for wpLingua
 *
 * @return void
 */
function wplng_admin_bar_menu() {

	if ( is_admin() || ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	global $wp_admin_bar;

	$wp_admin_bar->add_menu(
		array(
			'id'    => 'wplingua-menu',
			'title' => __( 'wpLingua', 'wplingua' ),
			'href'  => false,
		)
	);

	if ( ! wplng_url_is_translatable() ) {
		$wp_admin_bar->add_menu(
			array(
				'id'     => 'wplingua-url-exclude',
				'parent' => 'wplingua-menu',
				'title'  => __( 'This URL is excluded from translation', 'wplingua' ),
				'href'   => esc_url(
					add_query_arg(
						'page',
						'wplingua-exclusions',
						get_admin_url() . 'admin.php'
					)
				),
			)
		);
		return;
	}

	$url       = wplng_get_url_current();
	$url_clean = remove_query_arg(
		array( 'wplng-mode', 'wplng-load', 'nocache' ),
		$url
	);

	if ( ! empty( $_GET['wplng-mode'] )
		&& (
			'list' === $_GET['wplng-mode']
			|| 'editor' === $_GET['wplng-mode']
		)
	) {

		$wp_admin_bar->add_menu(
			array(
				'id'     => 'wplingua-return',
				'parent' => 'wplingua-menu',
				'title'  => __( 'Return on page', 'wplingua' ),
				'href'   => $url_clean,
			)
		);
	}

	$wp_admin_bar->add_menu(
		array(
			'id'     => 'wplingua-editor',
			'parent' => 'wplingua-menu',
			'title'  => __( 'Visual editor', 'wplingua' ),
			'href'   => false,
		)
	);

	$wp_admin_bar->add_menu(
		array(
			'id'     => 'wplingua-list',
			'parent' => 'wplingua-menu',
			'title'  => __( 'All translations on page', 'wplingua' ),
			'href'   => false,
		)
	);

	$languages_target = wplng_get_languages_target();

	if ( ! empty( $languages_target ) ) {

		foreach ( $languages_target as $language ) {

			if ( empty( $language['name'] )
				|| empty( $language['id'] )
			) {
				continue;
			}

			$name      = sanitize_text_field( $language['name'] );
			$id_editor = sanitize_title( 'wplingua-editor-' . $language['id'] );
			$id_list   = sanitize_title( 'wplingua-list-' . $language['id'] );

			$wp_admin_bar->add_menu(
				array(
					'id'     => $id_editor,
					'parent' => 'wplingua-editor',
					'title'  => $name,
					'href'   => add_query_arg(
						'wplng-mode',
						'editor',
						wplng_url_translate(
							wplng_get_url_original( $url_clean ),
							$language['id']
						)
					),
				)
			);

			$wp_admin_bar->add_menu(
				array(
					'id'     => $id_list,
					'parent' => 'wplingua-list',
					'title'  => $name,
					'href'   => add_query_arg(
						'wplng-mode',
						'list',
						wplng_url_translate(
							wplng_get_url_original( $url_clean ),
							$language['id']
						)
					),
				)
			);

		} // End foreach ( $languages_target as $language )
	} // End if ( ! empty( $languages_target ) )

	/**
	 * Edit slug translation link
	 */

	if ( is_404() ) {
		return;
	}

	$url_original        = wplng_get_url_original( $url_clean );
	$url_original_parsed = parse_url( $url_original );

	if ( ! isset( $url_original_parsed['path'] ) ) {
		return;
	}

	$path = $url_original_parsed['path'];

	// Return path if no contains slug

	if ( '/' === $path || '' === $path ) {
		return;
	}

	// Transform the multi part slug to an slug array

	$slugs = explode( '/', $path );
	$slugs = array_filter( $slugs );

	$last_slug = '';

	foreach ( $slugs as $slug ) {
		if ( '/' === $slug || '' === $slug ) {
			continue;
		}
		$last_slug = $slug;
	}

	if ( '/' === $last_slug
		|| '' === $last_slug
		|| ! wplng_text_is_translatable( $last_slug )
	) {
		return;
	}

	$slug_id = wplng_get_slug_saved_from_original( $last_slug );

	if ( false === $slug_id ) {
		$slug_id = wplng_create_slug( $slug );
	}

	$edit_slug_link = get_edit_post_link( $slug_id );

	if ( ! is_string( $edit_slug_link ) ) {
		return;
	}

	$wp_admin_bar->add_menu(
		array(
			'id'     => 'wplingua-slug',
			'parent' => 'wplingua-menu',
			'title'  => __( 'Edit the page slug', 'wplingua' ),
			'href'   => $edit_slug_link,
			'meta'   => array(
				'target' => '_blank',
			),
		)
	);
}


/**
 * Add an edit translation link in admin bar for post and page edit
 *
 * @return void
 */
function wplng_admin_bar_edit() {

	/**
	 * Check the current page
	 */

	if ( ! function_exists( 'get_current_screen' )
		|| ! is_admin()
		|| ! current_user_can( 'edit_posts' )
	) {
		return;
	}

	$current_screen = get_current_screen();

	if ( empty( $current_screen->base )
		|| empty( $current_screen->id )
		|| 'post' !== $current_screen->base
		|| (
			'page' !== $current_screen->id
			&& 'post' !== $current_screen->id
		)
	) {
		return;
	}

	/**
	 * Check if a target language is defined
	 */

	$languages_target_ids = wplng_get_languages_target_ids();

	if ( empty( $languages_target_ids[0] ) ) {
		return;
	}

	/**
	 * Get the link
	 */

	$url = get_permalink();

	if ( empty( $url ) || ! wplng_url_is_translatable( $url ) ) {
		return;
	}

	$url = add_query_arg(
		'wplng-mode',
		'list',
		wplng_url_translate(
			$url,
			$languages_target_ids[0]
		)
	);

	/**
	 * Add the admin bar link
	 */

	global $wp_admin_bar;

	$wp_admin_bar->add_menu(
		array(
			'id'    => 'wplingua-edit-translations',
			'title' => __( 'Translations', 'wplingua' ),
			'href'  => $url,
		)
	);
}
