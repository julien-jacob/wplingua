<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



/**
 * Create a menu link for the plugin settings in the admin area
 *
 * @return void
 */
function wplng_create_menu() {

	if ( empty( wplng_get_api_key_data() ) ) {

		add_menu_page(
			__( 'wpLingua : Register', 'wplingua' ),
			__( 'wpLingua', 'wplingua' ),
			'administrator',
			'wplng-settings',
			'wplng_option_page_register',
			'dashicons-admin-site'
			// plugins_url( '/images/icon.png', __FILE__ )
		);

	} else {

		add_menu_page(
			__( 'wpLingua : Settings', 'wplingua' ),
			__( 'wpLingua', 'wplingua' ),
			'administrator',
			'wplng-settings',
			'wplng_option_page_settings',
			'dashicons-admin-site'
			// plugins_url( '/images/icon.png', __FILE__ )
		);

		add_submenu_page(
			'wplng-settings',
			__( 'wplingua : Exclusion', 'wplingua' ),
			__( 'Exclusion', 'wplingua' ),
			'administrator',
			'wplng-exclusions',
			'wplng_option_page_exclusions'
		);

	}

}




/**
 * register settings
 *
 * @return void
 */
function wplng_register_settings() {

	register_setting( 'wplng_settings', 'wplng_api_key' );

	register_setting( 'wplng_settings', 'wplng_website_language' );
	register_setting( 'wplng_settings', 'wplng_website_flag' );
	register_setting( 'wplng_settings', 'wplng_target_languages' );
	register_setting( 'wplng_settings', 'wplng_translate_mail' );
	register_setting( 'wplng_settings', 'wplng_translate_search' );

	register_setting( 'wplng_exclusions', 'wplng_excluded_selectors' );
	register_setting( 'wplng_exclusions', 'wplng_excluded_url' );

}


/**
 * Add 'Settings' link on the plugin list
 *
 * @param array $settings
 * @return array
 */
function wplng_settings_link( $settings ) {

	$url = esc_url(
		add_query_arg(
			'page',
			'wplingua/inc/admin/option-page.php',
			get_admin_url() . 'admin.php'
		)
	);

	$settings[] = '<a href="' . $url . '">' . __( 'Settings', 'wplingua' ) . '</a>';

	return $settings;
}
