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
		__( 'wpLingua : Register', 'wplingua' ),
		__( 'wpLingua', 'wplingua' ),
		'administrator',
		'wplingua-settings',
		'wplng_option_page_register',
		'dashicons-admin-site'
	);

}


/**
 * Add wpLingua admin menu when key is registered
 *
 * @return void
 */
function wplng_create_menu() {

	add_menu_page(
		__( 'wpLingua : Settings', 'wplingua' ),
		__( 'wpLingua', 'wplingua' ),
		'administrator',
		'wplingua-settings',
		'wplng_option_page_settings',
		'dashicons-admin-site'
	);

	add_submenu_page(
		'wplingua-settings',
		__( 'wplingua : Switcher', 'wplingua' ),
		__( 'Switcher', 'wplingua' ),
		'administrator',
		'wplingua-switcher',
		'wplng_option_page_switcher'
	);

	add_submenu_page(
		'wplingua-settings',
		__( 'wplingua : Exclusion', 'wplingua' ),
		__( 'Exclusion', 'wplingua' ),
		'administrator',
		'wplingua-exclusions',
		'wplng_option_page_exclusions'
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
 * Show message from wpLingua API (plugin Update or global message)
 *
 * @return void
 */
function wplng_show_api_message() {

	$api_info = wplng_api_call_api_informations();

	if ( ! empty( $api_info['global_message'] )
		&& is_string( $api_info['global_message'] )
	) {
		?>
		<div class="wplng-notice notice notice-info is-dismissible">
			<p><?php echo esc_html( $api_info['global_message'] ); ?></p>
		</div>
		<?php
	}

	if ( empty( $api_info['wp_plugin_version'] ) ) {
		?>
		<div class="wplng-notice notice notice-error is-dismissible">
			<p><?php _e( 'A problem has occurred with the API connection.', 'wplingua' ); ?></p>
		</div>
		<?php
		return;
	} elseif ( $api_info['wp_plugin_version'] === WPLNG_PLUGIN_VERSION ) {
		return;
	}

	?>
	<div class="wplng-notice notice notice-info is-dismissible">
		<p>
			<strong><?php _e( 'A new version of the wpLingua WordPress plugin is now available! You can download it from', 'wplingua' ); ?> <a href="https://wplingua.com/download/" target="_blank">https://wplingua.com/download/</a>.</strong>
			<br>
			<?php echo __( 'Installed version:', 'wplingua' ) . ' ' . esc_html( WPLNG_PLUGIN_VERSION ) . ' - '; ?> 
			<?php echo __( 'Available version:', 'wplingua' ) . ' ' . esc_html( $api_info['wp_plugin_version'] ); ?> 
		</p>
	</div>
	<?php
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
