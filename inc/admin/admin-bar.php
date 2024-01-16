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

	if ( is_admin() ) {
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

	$url          = wplng_get_url_current();
	$url_original = $url;
	$url_original = remove_query_arg( 'wplingua-editor', $url_original );
	$url_original = remove_query_arg( 'wplingua-list', $url_original );

	if ( isset( $_GET['wplingua-editor'] )
		|| isset( $_GET['wplingua-list'] )
	) {

		$wp_admin_bar->add_menu(
			array(
				'id'     => 'wplingua-return',
				'parent' => 'wplingua-menu',
				'title'  => __( 'Return on page', 'wplingua' ),
				'href'   => $url_original,
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
						'wplingua-editor',
						'1',
						wplng_url_translate(
							wplng_get_url_original( $url_original ),
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
						'wplingua-list',
						'1',
						wplng_url_translate(
							wplng_get_url_original( $url_original ),
							$language['id']
						)
					),
				)
			);

		} // End foreach ( $languages_target as $language )
	} // End if ( ! empty( $languages_target ) )

}
