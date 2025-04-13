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
		__( 'wpLingua: Register', 'wplingua' ),
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
		__( 'wpLingua: Settings', 'wplingua' ),
		__( 'wpLingua', 'wplingua' ),
		'administrator',
		'wplingua-settings',
		'',
		'dashicons-translation',
		31
	);

	add_submenu_page(
		'wplingua-settings',
		__( 'wpLingua: Settings', 'wplingua' ),
		__( 'General settings', 'wplingua' ),
		'administrator',
		'wplingua-settings',
		'wplng_option_page_settings'
	);

	add_submenu_page(
		'wplingua-settings',
		__( 'wpLingua: Switcher', 'wplingua' ),
		__( 'Switcher', 'wplingua' ),
		'administrator',
		'wplingua-switcher',
		'wplng_option_page_switcher'
	);

	add_submenu_page(
		'wplingua-settings',
		__( 'wpLingua: Exclusion', 'wplingua' ),
		__( 'Exclusion', 'wplingua' ),
		'administrator',
		'wplingua-exclusions',
		'wplng_option_page_exclusions'
	);

	add_submenu_page(
		'wplingua-settings',
		__( 'wpLingua: Dictionary', 'wplingua' ),
		__( 'Dictionary', 'wplingua' ),
		'administrator',
		'wplingua-dictionary',
		'wplng_option_page_dictionary'
	);

	add_submenu_page(
		'wplingua-settings',
		__( 'wplingua: Links & Medias', 'wplingua' ),
		__( 'Links & Medias', 'wplingua' ),
		'administrator',
		'wplingua-link-media',
		'wplng_option_page_link_media'
	);

	add_submenu_page(
		'wplingua-settings',
		__( 'wpLingua: Website slugs', 'wplingua' ),
		__( 'Website slugs', 'wplingua' ),
		'administrator',
		'edit.php?post_type=wplng_slug',
		false
	);

	add_submenu_page(
		'wplingua-settings',
		__( 'wpLingua: Translations', 'wplingua' ),
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

	// Option page : Links & Medias
	register_setting( 'wplng_link_media', 'wplng_link_media_entries' );
}


/**
 * Customize the admin footer text displayed on wpLingua option pages.
 *
 * @param string $text The default footer text.
 * @return string The customized footer text for wpLingua pages.
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
			|| $_GET['page'] === 'wplingua-link-media'
		)
	) {

		$text = '<span class="dashicons dashicons-heart"></span> ';

		if ( empty( wplng_get_api_data() ) ) {
			$text .= esc_html__( 'Thank you for choosing wpLingua!', 'wplingua' );
		} else {
			$text .= sprintf(
				esc_html__( 'If you like wpLingua please leave us a %1$s rating. A huge thanks!', 'wplingua' ),
				'<a href="https://wordpress.org/support/plugin/wplingua/reviews/?filter=5" target="_blank" class="wc-rating-link" aria-label="' . esc_attr__( 'five stars', 'wplingua' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
			);
		}
	}

	return $text;
}


/**
 * Set custom update_footer text on wpLingua options pages
 *
 * @param string $text The default footer text to be modified.
 * @return string The customized footer text for wpLingua pages.
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
			|| $_GET['page'] === 'wplingua-link-media'
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
 * Redirect to the wpLingua settings page upon plugin activation.
 *
 * @param string $plugin The plugin file path that was activated.
 * @return void
 */
function wplng_plugin_activation_redirect( $plugin ) {

	if ( ! wp_doing_ajax() && WPLNG_PLUGIN_FILE === $plugin ) {
		wp_safe_redirect(
			admin_url( 'admin.php?page=wplingua-settings' )
		);
		exit();
	}
}


/**
 * Display a notice if the plugin is activate but not configured
 *
 * @return void
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
	$html .= esc_html__( 'wpLingua is installed, but not yet configured. You are just a few clicks away from making your website multilingual!', 'wplingua' );
	$html .= '<br> ';
	$html .= '<a href="' . esc_url( $url ) . '">';
	$html .= esc_html__( 'Go to the configuration page', 'wplingua' );
	$html .= '</a>';
	$html .= '</p>';
	$html .= '</div>';

	echo $html;
}


/**
 * Get the list of activated incompatible plugins
 *
 * @return array [Plugin name => Plugin file]
 */
function wplng_get_incompatible_plugins() {

	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . '/wp-admin/includes/plugin.php';
	}

	/**
	 * Get incompatible plugins
	 */

	$incompatible_list     = array();
	$incompatible_detected = array();

	$incompatible_list = array(
		'Automatic Translator'      => 'auto-translate/auto-translate.php',
		'Autoglot'                  => 'autoglot/autoglot.php',
		'clonable'                  => 'clonable/clonable-wp.php',
		'ConveyThis Translate'      => 'conveythis-translate/index.php',
		'Falang'                    => 'falang/falang.php',
		'Google Website Translator' => 'google-website-translator/google-website-translator.php',
		'Google Translator'         => 'google-language-translator/google-language-translator.php',
		'Gtranslate'                => 'gtranslate/gtranslate.php',
		'linguise'                  => 'linguise/linguise.php',
		'localizejs'                => 'localizejs/localizejs.php',
		'Multilanguage'             => 'multilanguage/multilanguage.php',
		'Polylang'                  => 'polylang/polylang.php',
		'TranslatePress'            => 'translatepress-multilingual/index.php',
		'WEGLOT'                    => 'weglot/weglot.php',
		'WPML'                      => 'sitepress-multilingual-cms/sitepress.php',
		'WP Multilang'              => 'wp-multilang/wp-multilang.php',
	);

	foreach ( $incompatible_list as $name => $file ) {
		if ( is_plugin_active( $file ) ) {
			$incompatible_detected[ $name ] = $file;
		}
	}

	return $incompatible_detected;
}


/**
 * Display a notice if an incompatible plugin is detected.
 *
 * @return void|string Outputs the admin notice or returns nothing if no conflicts are found.
 */
function wplng_admin_notice_incompatible_plugin() {

	$incompatible_plugins = wplng_get_incompatible_plugins();

	if ( empty( $incompatible_plugins ) ) {
		return;
	}

	$html  = '<div ';
	$html .= 'class="wplng-notice notice notice-error is-dismissible" ';
	$html .= 'style="background-color: rgba(255, 0, 0, .1);">';
	$html .= '<p style="font-weight: 600;">';
	$html .= '<span class="dashicons dashicons-translation"></span> ';
	$html .= esc_html__( 'wpLingua - Incompatible plugin detected', 'wplingua' );
	$html .= '</p>';
	$html .= '<p>';
	$html .= esc_html__( 'You have several translation plugins. This may result in unpredictable or incorrect behavior. For best results, use only one translation plugin at a time. These plugins can cause problems with wpLingua: ', 'wplingua' );

	$html .= '<ul style="list-style: disc; margin-left: 15px;">';
	foreach ( $incompatible_plugins as $name => $file ) {

		$deactivate_url = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'deactivate',
					'plugin' => urlencode( $file ),
				),
				get_admin_url() . 'plugins.php'
			),
			'deactivate-plugin_' . $file
		);

		$deactivate_title = sprintf(
			esc_html__( 'Deactivate plugin: %1$s', 'wplingua' ),
			$name
		);

		$html .= '<li>';
		$html .= '<strong>' . esc_html( $name ) . '</strong> | ';
		$html .= '<a ';
		$html .= 'href="' . esc_url( $deactivate_url ) . '" ';
		$html .= 'title="' . esc_attr( $deactivate_title ) . '">';
		$html .= esc_html__( 'Deactivate', 'wplingua' );
		$html .= '</a>';
		$html .= '</li>';
	}
	$html .= '</ul>';
	$html .= '</p>';

	$url_manage_plugins = add_query_arg(
		'plugin_status',
		'active',
		get_admin_url() . 'plugins.php'
	);

	$html .= '<a ';
	$html .= 'href="' . esc_url( $url_manage_plugins ) . '" ';
	$html .= 'class="button button-primary" ';
	$html .= 'style="margin-bottom: 10px;">';
	$html .= esc_html__( 'Manage active plugins', 'wplingua' );
	$html .= '</a>';

	$html .= '</div>'; // End .notice

	echo $html;
}


/**
 * Display a notice if is a multisite
 *
 * @return void|string Outputs the admin notice if applicable, or returns void if no action is needed.
 */
function wplng_admin_notice_incompatible_multisite() {

	if ( ! is_multisite() ) {
		return;
	}

	$html  = '<div ';
	$html .= 'class="wplng-notice notice notice-error is-dismissible" ';
	$html .= 'style="background-color: rgba(255, 0, 0, .1);">';
	$html .= '<p style="font-weight: 600;">';
	$html .= '<span class="dashicons dashicons-translation"></span> ';
	$html .= esc_html__( 'wpLingua - Incompatible with multisite', 'wplingua' );
	$html .= '</p>';
	$html .= '<p>';
	$html .= esc_html__( 'wpLingua is not compatible with multisite.', 'wplingua' );
	$html .= '</p>';
	$html .= '</div>'; // End .notice

	echo $html;
}


/**
 * Display a notice if the WordPress installed in a subfolder
 *
 * @return void|string Outputs the admin notice if applicable, or returns void if no notice is needed.
 */
function wplng_admin_notice_incompatible_sub_folder() {

	if ( ! wplng_website_in_sub_folder() ) {
		return;
	}

	$html  = '<div ';
	$html .= 'class="wplng-notice notice notice-error is-dismissible" ';
	$html .= 'style="background-color: rgba(255, 0, 0, .1);">';
	$html .= '<p style="font-weight: 600;">';
	$html .= '<span class="dashicons dashicons-translation"></span> ';
	$html .= esc_html__( 'wpLingua - Incompatible with WordPress installed in a subfolder', 'wplingua' );
	$html .= '</p>';
	$html .= '<p>';
	$html .= esc_html__( 'wpLingua is not compatible with WordPress installed in a subfolder.', 'wplingua' );
	$html .= '</p>';
	$html .= '</div>'; // End .notice

	echo $html;
}


/**
 * Display a notice if the PHP version is incompatible
 *
 * @return void|string Outputs the admin notice if applicable, or returns void if no notice is required.
 */
function wplng_admin_notice_incompatible_php_version() {

	if ( version_compare( PHP_VERSION, WPLNG_PHP_MIN_VERSION ) >= 0 ) {
		return;
	}

	$html  = '<div ';
	$html .= 'class="wplng-notice notice notice-error is-dismissible" ';
	$html .= 'style="background-color: rgba(255, 0, 0, .1);">';
	$html .= '<p style="font-weight: 600;">';
	$html .= '<span class="dashicons dashicons-translation"></span> ';
	$html .= esc_html__( 'wpLingua - Incompatible PHP version', 'wplingua' );
	$html .= '</p>';
	$html .= '<p>';
	$html .= esc_html__( 'wpLingua is not compatible with your PHP version. wpLingua requires PHP 7.4 or higher.', 'wplingua' );
	$html .= '</p>';
	$html .= '</div>'; // End .notice

	echo $html;
}
