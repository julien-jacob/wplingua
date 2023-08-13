<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_admin_bar_menu() {

	if ( ! wplng_url_is_translatable()
		|| wplng_get_language_website_id() === wplng_get_language_current_id()
	) {
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

	$url = wplng_get_url_current();

	$url_original = $url;
	$url_original = remove_query_arg( 'wplingua-editor', $url_original );
	$url_original = remove_query_arg( 'wplingua-list', $url_original );

	if ( isset( $_GET['wplingua-editor'] )
		|| isset( $_GET['wplingua-list'] )
	) {
		

		$wp_admin_bar->add_menu(
			array(
				'id'     => 'wplangua-return',
				'parent' => 'wplingua-menu',
				'title'  => __( 'Return on page', 'wplingua' ),
				'href'   => $url_original,
			)
		);
	}

	if ( ! isset( $_GET['wplingua-editor'] ) ) {
		$wp_admin_bar->add_menu(
			array(
				'id'     => 'wplangua-editor',
				'parent' => 'wplingua-menu',
				'title'  => __( 'Visual editor', 'wplingua' ),
				'href'   => add_query_arg( 'wplingua-editor', '1', $url_original ),
			)
		);
	}

	if ( ! isset( $_GET['wplingua-list'] ) ) {
		$wp_admin_bar->add_menu(
			array(
				'id'     => 'wplangua-list',
				'parent' => 'wplingua-menu',
				'title'  => __( 'All translations on page', 'wplingua' ),
				'href'   => add_query_arg( 'wplingua-list', '1', $url_original ),
			)
		);
	}

}
