<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Add wpLingua admin menu when API Key is not registered
 *
 * @return void
 */
function wplng_create_menu_register() {

	add_menu_page(
		__( 'wplingua: Register', 'wplingua' ),
		__( 'wpLingua', 'wplingua' ),
		'administrator',
		'wplingua-settings',
		'wplng_option_page_register',
		'dashicons-translation',
		31
	);

}


/**
 * Add wpLingua admin menu when key is registered
 *
 * @return void
 */
function wplng_create_menu() {

	add_menu_page(
		__( 'wplingua: Settings', 'wplingua' ),
		__( 'wpLingua', 'wplingua' ),
		'administrator',
		'wplingua-settings',
		'wplng_option_page_settings',
		'dashicons-translation',
		31
	);

	add_submenu_page(
		'wplingua-settings',
		__( 'wplingua: Switcher', 'wplingua' ),
		__( 'Switcher', 'wplingua' ),
		'administrator',
		'wplingua-switcher',
		'wplng_option_page_switcher'
	);

	add_submenu_page(
		'wplingua-settings',
		__( 'wplingua: Exclusion', 'wplingua' ),
		__( 'Exclusion', 'wplingua' ),
		'administrator',
		'wplingua-exclusions',
		'wplng_option_page_exclusions'
	);

	add_submenu_page(
		'wplingua-settings',
		__( 'wplingua: Translations', 'wplingua' ),
		__( 'All translations', 'wplingua' ),
		'administrator',
		'edit.php?post_type=wplng_translation',
		false
	);

}




/**
 * Register wpLingua settings
 *
 * @return void
 */
function wplng_register_settings() {

	// Option page : Settings and register
	register_setting( 'wplng_settings', 'wplng_website_language' );
	register_setting( 'wplng_settings', 'wplng_website_flag' );
	register_setting( 'wplng_settings', 'wplng_target_languages' );
	register_setting( 'wplng_settings', 'wplng_translate_search' );
	register_setting( 'wplng_settings', 'wplng_translate_woocommerce' );
	register_setting( 'wplng_settings', 'wplng_api_key' );
	register_setting( 'wplng_settings', 'wplng_request_free_key' );

	// Option page : Exclusions
	register_setting( 'wplng_exclusions', 'wplng_excluded_selectors' );
	register_setting( 'wplng_exclusions', 'wplng_excluded_url' );

	// Option page : Switcher
	register_setting( 'wplng_switcher', 'wplng_insert' );
	register_setting( 'wplng_switcher', 'wplng_theme' );
	register_setting( 'wplng_switcher', 'wplng_style' );
	register_setting( 'wplng_switcher', 'wplng_name_format' );
	register_setting( 'wplng_switcher', 'wplng_flags_style' );
	register_setting( 'wplng_switcher', 'wplng_custom_css' );

}


/**
 * Add 'Settings' link on wpLingua in the plugin list
 *
 * @param array $settings
 * @return array
 */
function wplng_settings_link( $settings ) {

	$url = esc_url(
		add_query_arg(
			'page',
			'wplingua-settings',
			get_admin_url() . 'admin.php'
		)
	);

	$settings[] = '<a href="' . $url . '">' . __( 'Settings', 'wplingua' ) . '</a>';

	return $settings;
}
