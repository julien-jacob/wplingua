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
		'',
		'dashicons-translation',
		31
	);

	add_submenu_page(
		'wplingua-settings',
		__( 'wplingua: Settings', 'wplingua' ),
		__( 'General settings', 'wplingua' ),
		'administrator',
		'wplingua-settings',
		'wplng_option_page_settings'
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
		__( 'wplingua: Dictionary', 'wplingua' ),
		__( 'Dictionary', 'wplingua' ),
		'administrator',
		'wplingua-dictionary',
		'wplng_option_page_dictionary'
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

	// Option page : Dictionary
	register_setting( 'wplng_dictionary', 'wplng_dictionary_entries' );
}


/**
 * Set custom admin_footer_text on wpLingua options pages
 *
 * @param string $text
 * @return string
 */
function wplng_admin_footer_text( $text ) {

	global $pagenow;

	if ( 'admin.php' === $pagenow
		&& isset( $_GET['page'] )
		&& (
			$_GET['page'] === 'wplingua-settings'
			|| $_GET['page'] === 'wplingua-switcher'
			|| $_GET['page'] === 'wplingua-dictionary'
			|| $_GET['page'] === 'wplingua-exclusions'
		)
	) {
		$text = sprintf(
			esc_html__( '%1$s If you like wpLingua please leave us a %2$s rating. A huge thanks!', 'woocommerce' ),
			'<span class="dashicons dashicons-heart"></span>',
			'<a href="https://wordpress.org/support/plugin/wplingua/reviews/?filter=5" target="_blank" class="wc-rating-link" aria-label="' . esc_attr__( 'five star', 'wplingua' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
		);
	}

	return $text;
}


/**
 * Set custom update_footer text on wpLingua options pages
 *
 * @param string $text
 * @return string
 */
function wplng_update_footer( $text ) {

	global $pagenow;

	if ( 'admin.php' === $pagenow
		&& isset( $_GET['page'] )
		&& (
			$_GET['page'] === 'wplingua-settings'
			|| $_GET['page'] === 'wplingua-switcher'
			|| $_GET['page'] === 'wplingua-dictionary'
			|| $_GET['page'] === 'wplingua-exclusions'
		)
	) {
		$text  = '<a href="https://wplingua.com/" target="_blank">';
		$text .= 'wplingua.com';
		$text .= '</a> | ';
		$text .= '<a href="https://github.com/julien-jacob/wplingua" target="_blank">';
		$text .= 'GitHub';
		$text .= '</a> | ';
		$text .= esc_html__( 'Version', 'wplingua' );
		$text .= ' ' . esc_html( WPLNG_PLUGIN_VERSION );
	}

	return $text;
}


/**
 * Add 'Settings' link on wpLingua in the plugin list
 *
 * @param array $settings
 * @return array
 */
function wplng_settings_link( $settings ) {

	$url = add_query_arg(
		'page',
		'wplingua-settings',
		get_admin_url() . 'admin.php'
	);

	$link  = '<a href="' . esc_url( $url ) . '">';
	$link .= esc_html__( 'Settings', 'wplingua' );
	$link .= '</a>';

	$settings[] = $link;

	return $settings;
}


/**
 * Display a notice if the plugin is activate but not configured
 *
 * @return string
 */
function wplng_admin_notice_no_key_set() {

	$url = add_query_arg(
		'page',
		'wplingua-settings',
		get_admin_url() . 'admin.php'
	);

	$html  = '<div class="notice notice-info is-dismissible">';
	$html .= '<p style="font-weight: 600;">';
	$html .= '<span class="dashicons dashicons-translation"></span> ';
	$html .= esc_html__( 'wpLingua - Translation solution for multilingual website', 'wplingua' );
	$html .= '</p>';
	$html .= '<p>';
	$html .= esc_html__( 'wpLingua is installed, but not yet configured. You are just a few clicks away from making your site multilingual!', 'wplingua' );
	$html .= '<br> ';
	$html .= '<a href="' . esc_url( $url ) . '">';
	$html .= esc_html__( 'Go to the configuration page', 'wplingua' );
	$html .= '</a>';
	$html .= '</p>';
	$html .= '</div>';

	echo $html;
}
