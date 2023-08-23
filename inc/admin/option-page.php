<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_create_menu_register() {

	add_menu_page(
		__( 'wpLingua : Register', 'wplingua' ),
		__( 'wpLingua', 'wplingua' ),
		'administrator',
		'wplng-settings',
		'wplng_option_page_register',
		'dashicons-admin-site'
	);

}


function wplng_create_menu() {

	add_menu_page(
		__( 'wpLingua : Settings', 'wplingua' ),
		__( 'wpLingua', 'wplingua' ),
		'administrator',
		'wplng-settings',
		'wplng_option_page_settings',
		'dashicons-admin-site'
	);

	add_submenu_page(
		'wplng-settings',
		__( 'wplingua : Switcher', 'wplingua' ),
		__( 'Switcher', 'wplingua' ),
		'administrator',
		'wplng-switcher',
		'wplng_option_page_switcher'
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




/**
 * register settings
 *
 * @return void
 */
function wplng_register_settings() {

	register_setting( 'wplng_settings', 'wplng_website_language' );
	register_setting( 'wplng_settings', 'wplng_website_flag' );
	register_setting( 'wplng_settings', 'wplng_target_languages' );
	register_setting( 'wplng_settings', 'wplng_translate_mail' );
	register_setting( 'wplng_settings', 'wplng_translate_search' );
	register_setting( 'wplng_settings', 'wplng_translate_woocommerce' );
	register_setting( 'wplng_settings', 'wplng_api_key' );
	register_setting( 'wplng_settings', 'wplng_request_free_key' );

	register_setting( 'wplng_exclusions', 'wplng_excluded_selectors' );
	register_setting( 'wplng_exclusions', 'wplng_excluded_url' );

	register_setting( 'wplng_switcher', 'wplng_insert' );
	register_setting( 'wplng_switcher', 'wplng_theme' );
	register_setting( 'wplng_switcher', 'wplng_style' );
	register_setting( 'wplng_switcher', 'wplng_name_format' );
	register_setting( 'wplng_switcher', 'wplng_flags_style' );
	register_setting( 'wplng_switcher', 'wplng_custom_css' );

}


function wplng_show_api_message() {

	$api_info = wplng_api_informations();

	if ( ! empty( $api_info['global_message'] ) 
		&& is_string( $api_info['global_message'] ) 
	) {
		?>
		<div class="wplng-notice notice notice-info is-dismissible">
			<p><?php echo esc_html( $api_info['global_message'] ); ?></p>
		</div>
		<?php
	}

	if ( empty( $api_info['wp_plugin_version'] )
		|| $api_info['wp_plugin_version'] === WPLNG_PLUGIN_VERSION
	) {
		return;
	}

	?>
	<div class="wplng-notice notice notice-info is-dismissible">
		<p>
			<strong><?php _e( 'A new version of the wpLingua WordPress plugin is now available! You can download it from', 'wplingua' ); ?> <a href="https://wplingua.com/download/" target="_blank">https://wplingua.com/download/</a>.</strong>
			<br>
			<?php echo __( 'Installed version:', 'wplingua' ) . ' ' . esc_html(WPLNG_PLUGIN_VERSION) . ' - '; ?> 
			<?php echo __( 'Available version:', 'wplingua' ) . esc_html($api_info['wp_plugin_version']); ?> 
		</p>
	</div>
	<?php
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
			'wplng-settings',
			get_admin_url() . 'admin.php'
		)
	);

	$settings[] = '<a href="' . $url . '">' . __( 'Settings', 'wplingua' ) . '</a>';

	return $settings;
}
