<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
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
	register_setting( 'wplng_settings', 'wplng_load_in_progress' );
	register_setting( 'wplng_settings', 'wplng_sitemap_xml' );
	register_setting( 'wplng_settings', 'wplng_sitemap_xsl_override' );
	register_setting( 'wplng_settings', 'wplng_hreflang' );
	register_setting( 'wplng_settings', 'wplng_browser_language_redirect' );
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
 * Determine whether the current page is one of
 * wpLingua's hidden option pages.
 */
function wplng_is_wplingua_settings_page() {

    if ( empty( $_GET['page'] ) ) {
        return false;
    }

    return in_array(
        sanitize_key( $_GET['page'] ),
        array(
            'wplingua-settings',
            'wplingua-switcher',
            'wplingua-exclusions',
            'wplingua-dictionary',
            'wplingua-link-media',
        ),
        true
    );
}


/**
 * Return true if is a wpLingua Admin page
 *
 * @return bool Is a wpLingua Admin page
 */
function wplng_is_wplingua_admin_page() {

	global $pagenow;
	global $post;

	/**
	 * Check if is a wpLingua option page
	 */

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
		return true;
	}

	/**
	 * Check if is a translations or slugs admin list
	 */

	if (
		'edit.php' === $pagenow
		&& isset( $_GET['post_type'] )
		&& (
			$_GET['post_type'] === 'wplng_translation'
			|| $_GET['post_type'] === 'wplng_slug'
		)
	) {
		return true;
	}

	/**
	 * Check if is translations or slugs edition
	 */

	if (
		'post.php' === $pagenow
		&& isset( $post->post_type )
		&& (
			$post->post_type === 'wplng_translation'
			|| $post->post_type === 'wplng_slug'
		)
	) {
		return true;
	}

	return false;
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
		'edit_posts',
		'wplingua-settings',
		'wplng_option_page_register',
		'dashicons-translation',
		31
	);
}


/**
 * Create wpLingua admin menu.
 */
function wplng_create_menu() {

    /*
     * Main wpLingua menu
     */
    add_menu_page(
        __( 'wpLingua: Settings', 'wplingua' ),
        __( 'wpLingua', 'wplingua' ),
        'edit_posts',
        'wplingua-settings',
        '',
        'dashicons-translation',
        31
    );


    /*
     * Settings
     *
     * This is the visible entry used as the parent
     * for all wpLingua option pages.
     */
    add_submenu_page(
        'wplingua-settings',
        __( 'wpLingua: Settings', 'wplingua' ),
        __( 'Settings', 'wplingua' ),
        'edit_posts',
        'wplingua-settings',
        'wplng_option_page_settings'
    );


    /*
     * Hidden option pages
     *
     * These pages remain registered as children of
     * wplingua-settings, but their menu entries are
     * hidden later with CSS.
     */

    add_submenu_page(
        'wplingua-settings',
        __( 'wpLingua: Switcher', 'wplingua' ),
        __( 'Switcher', 'wplingua' ),
        'edit_posts',
        'wplingua-switcher',
        'wplng_option_page_switcher'
    );

    add_submenu_page(
        'wplingua-settings',
        __( 'wpLingua: Exclusion', 'wplingua' ),
        __( 'Exclusion', 'wplingua' ),
        'edit_posts',
        'wplingua-exclusions',
        'wplng_option_page_exclusions'
    );

    add_submenu_page(
        'wplingua-settings',
        __( 'wpLingua: Dictionary', 'wplingua' ),
        __( 'Dictionary', 'wplingua' ),
        'edit_posts',
        'wplingua-dictionary',
        'wplng_option_page_dictionary'
    );

    add_submenu_page(
        'wplingua-settings',
        __( 'wplingua: Links & Medias', 'wplingua' ),
        __( 'Links & Medias', 'wplingua' ),
        'edit_posts',
        'wplingua-link-media',
        'wplng_option_page_link_media'
    );


    /*
     * Visible custom post type pages
     */

    add_submenu_page(
        'wplingua-settings',
        __( 'wpLingua: Website slugs', 'wplingua' ),
        __( 'Website slugs', 'wplingua' ),
        'edit_posts',
        'edit.php?post_type=wplng_slug',
        false
    );

    add_submenu_page(
        'wplingua-settings',
        __( 'wpLingua: Translations', 'wplingua' ),
        __( 'All translations', 'wplingua' ),
        'edit_posts',
        'edit.php?post_type=wplng_translation',
        false
    );
}


/**
 * Keep wpLingua as the active parent menu
 * on all wpLingua option pages.
 *
 * @param string $parent_file The current parent menu file.
 *
 * @return string The parent menu file.
 */
function wplng_option_page_parent_file( $parent_file ) {

    if ( wplng_is_wplingua_settings_page() ) {
        return 'wplingua-settings';
    }

    return $parent_file;
}


/**
 * Keep "Settings" as the active submenu
 * on all wpLingua option pages.
 *
 * @param string $submenu_file The current submenu file.
 *
 * @return string The submenu file.
 */
function wplng_option_page_submenu_file( $submenu_file ) {

    if ( wplng_is_wplingua_settings_page() ) {
        return 'wplingua-settings';
    }

    return $submenu_file;
}


/**
 * Hide the option pages from the wpLingua submenu.
 *
 * The pages themselves remain registered in WordPress.
 * Therefore, they are still accessible directly by URL
 * and remain correctly associated with the wpLingua menu.
 */
function wplng_option_page_hide_submenu() {

	$css = '<style>';
	$css .= '#adminmenu .wp-submenu a[href*="page=wplingua-switcher"], ';
	$css .= '#adminmenu .wp-submenu a[href*="page=wplingua-exclusions"], ';
	$css .= '#adminmenu .wp-submenu a[href*="page=wplingua-dictionary"], ';
	$css .= '#adminmenu .wp-submenu a[href*="page=wplingua-link-media"] {';
	$css .= 'display: none;';
	$css .= '}';
	$css .= '</style>';

	echo $css;
}



/**
 * Generates the navigation menu for wpLingua option pages.
 *
 * Creates a list of links to the different wpLingua settings pages,
 * highlighting the current page with the `button-primary` class.
 *
 * @param bool $display_none Whether to hide the menu by default.
 *
 * @return string The HTML markup for the option pages navigation menu.
 */
function wplng_option_page_settings_menu( $display_none = false ) {

	$data = array(
		array(
			'page'     => 'wplingua-settings',
			'title'    => __( 'General settings', 'wplingua' ),
			'dashicon' => 'dashicons-admin-generic',
		),
		array(
			'page'     => 'wplingua-switcher',
			'title'    => __( 'Language switcher', 'wplingua' ),
			'dashicon' => 'dashicons-admin-settings',
		),
		array(
			'page'     => 'wplingua-exclusions',
			'title'    => __( 'Exclusion', 'wplingua' ),
			'dashicon' => 'dashicons-filter',
		),
		array(
			'page'     => 'wplingua-dictionary',
			'title'    => __( 'Dictionary', 'wplingua' ),
			'dashicon' => 'dashicons-book',
		),
		array(
			'page'     => 'wplingua-link-media',
			'title'    => __( 'Links & Medias', 'wplingua' ),
			'dashicon' => 'dashicons-format-gallery',
		),
	);

	$style = '';
	if ( $display_none === true ) {
		$style = ' style="display: none;"';
	}

	$html  = '<div class="wplng-option-page-menu"' . $style . '>';
	$html .= '<ul>';

	foreach ( $data as $value ) {

		$url = add_query_arg(
			'page',
			$value['page'],
			admin_url( 'admin.php' )
		);

		$class_attr = '';
		if ( $_GET['page'] === $value['page'] ) {
			$class_attr = ' button-primary';
		}

		$html .= '<li>';
		$html .= '<a href="' . esc_url( $url ) . '" class="button' . $class_attr . '">';
		$html .= '<span class="dashicons ' . esc_attr( $value['dashicon'] ) . '">';
		$html .= '</span> ';
		$html .= esc_html( $value['title'] );
		$html .= '</a>';
		$html .= '</li>';
	}

	$html .= '</ul>';
	$html .= '</div>'; // End .wplng-option-page-menu

	return $html;
}


/**
 * Customize the admin footer text displayed on wpLingua option pages.
 *
 * @param string $text The default footer text.
 * @return string The customized footer text for wpLingua pages.
 */
function wplng_admin_footer_text( $text ) {

	if ( wplng_is_wplingua_admin_page() ) {

		$text = '<span class="dashicons dashicons-heart"></span> ';

		if ( empty( wplng_get_api_data() ) ) {
			$text .= esc_html__( 'Thank you for choosing wpLingua!', 'wplingua' );
		} else {
			$text .= sprintf(
				esc_html__( 'If you like wpLingua please leave us a %1$s rating. A huge thanks!', 'wplingua' ),
				'<a href="https://wordpress.org/support/plugin/wplingua/reviews/?filter=5" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr__( 'five stars', 'wplingua' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
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

	if ( wplng_is_wplingua_admin_page() ) {
		$text  = '<a href="https://wplingua.com/" target="_blank" rel="noopener noreferrer">';
		$text .= 'wplingua.com';
		$text .= '</a> | ';
		$text .= '<a href="https://github.com/julien-jacob/wplingua" target="_blank" rel="noopener noreferrer">';
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

	$incompatible_list = array(
		'Automatic Translator'      => 'auto-translate/auto-translate.php',
		'Autoglot'                  => 'autoglot/autoglot.php',
		'clonable'                  => 'clonable/clonable-wp.php',
		'ConveyThis Translate'      => 'conveythis-translate/index.php',
		'Falang'                    => 'falang/falang.php',
		'Google Translator'         => 'google-language-translator/google-language-translator.php',
		'Google Website Translator' => 'google-website-translator/google-website-translator.php',
		'Gtranslate'                => 'gtranslate/gtranslate.php',
		'linguise'                  => 'linguise/linguise.php',
		'Lokalise'                  => 'lokalise/lokalise.php',
		'localizejs'                => 'localizejs/localizejs.php',
		'Multilanguage'             => 'multilanguage/multilanguage.php',
		'Polylang'                  => 'polylang/polylang.php',
		'TranslatePress'            => 'translatepress-multilingual/index.php',
		'WEGLOT'                    => 'weglot/weglot.php',
		'WPML'                      => 'sitepress-multilingual-cms/sitepress.php',
		'WP Multilang'              => 'wp-multilang/wp-multilang.php',
	);

	$incompatible_detected = array();

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
 * Note: wpLingua does not officially support multisite installations and
 * does not guarantee compatibility. Advanced users can bypass this
 * incompatibility check by returning true from the
 * `wplng_bypass_multisite_incompatibility` filter.
 *
 * @see apply_filters( 'wplng_bypass_multisite_incompatibility', false )
 *
 * @return void|string Outputs the admin notice if applicable, or returns void if no action is needed.
 */
function wplng_admin_notice_incompatible_multisite() {

	// Advanced users can bypass the multisite incompatibility notice by
	// returning true to this filter. Note that bypassing does NOT make
	// wpLingua officially compatible with multisite installations.
	if ( ! is_multisite()
		|| apply_filters( 'wplng_bypass_multisite_incompatibility', false )
	) {
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


/**
 * Check if the HTACCESS file contain code of another plugin who break translated pages
 *
 * @return bool HTACCESS file is valid for wpLingua
 */
function wplng_htaccess_is_valid() {

	$htaccess_path = ABSPATH . '.htaccess';

	if ( ! file_exists( $htaccess_path ) ) {
		return true;
	}

	$htaccess_content = file_get_contents( $htaccess_path );

	return ! (
		strpos( $htaccess_content, 'GTranslate' ) !== false
		|| strpos( $htaccess_content, 'LINGUISE' ) !== false
	);
}


/**
 * Display a notice if the HTACCESS file is incompatible
 *
 * @return void|string Outputs the admin notice if applicable, or returns void if no notice is required.
 */
function wplng_admin_notice_incompatible_htaccess() {

	$htaccess_path = ABSPATH . '.htaccess';

	if ( ! file_exists( $htaccess_path ) ) {
		return;
	}

	$htaccess_content = file_get_contents( $htaccess_path );
	$message          = '';

	if ( strpos( $htaccess_content, 'GTranslate' ) !== false ) {
		$message .= '### BEGIN GTranslate config ###' . PHP_EOL;
		$message .= '...' . PHP_EOL;
		$message .= '### END GTranslate config ###';
	} elseif ( strpos( $htaccess_content, 'LINGUISE' ) !== false ) {
		$message .= '#### LINGUISE DO NOT EDIT ####' . PHP_EOL;
		$message .= '...' . PHP_EOL;
		$message .= '#### LINGUISE DO NOT EDIT END ####';
	}

	if ( ! empty( $message ) ) {

		$html  = '<div ';
		$html .= 'class="wplng-notice notice notice-error is-dismissible" ';
		$html .= 'style="background-color: rgba(255, 0, 0, .1);">';
		$html .= '<p style="font-weight: 600;">';
		$html .= '<span class="dashicons dashicons-translation"></span> ';
		$html .= esc_html__( 'wpLingua - Incompatible HTACCESS file', 'wplingua' );
		$html .= '</p>';
		$html .= '<p>';
		$html .= esc_html__( 'It seems that a plugin was incorrectly deactivated and corrupted the site\'s HTACCESS file, preventing the correct redirection of translated pages. Please edit the ".htaccess" file located at the root of the site and delete the lines between :', 'wplingua' );
		$html .= '<pre style="border: 1px solid #c3c4c7; padding: 8px; background-color: rgba(0,0,0,.05);">';
		$html .= $message;
		$html .= '</pre>';
		$html .= '</p>';
		$html .= '</div>'; // End .notice

		echo $html;
	}
}


/**
 * Display a notice if the permalink structure is set to Plain
 *
 * @return void|string Outputs the admin notice if applicable, or returns void if no notice is required.
 */
function wplng_admin_notice_incompatible_plain_permalink() {

	if ( ! empty( get_option( 'permalink_structure' ) ) ) {
		return;
	}

	$url_permalinks = get_admin_url() . 'options-permalink.php';

	$html  = '<div ';
	$html .= 'class="wplng-notice notice notice-error is-dismissible" ';
	$html .= 'style="background-color: rgba(255, 0, 0, .1);">';
	$html .= '<p style="font-weight: 600;">';
	$html .= '<span class="dashicons dashicons-translation"></span> ';
	$html .= esc_html__( 'wpLingua - Incompatible permalink structure', 'wplingua' );
	$html .= '</p>';
	$html .= '<p>';
	$html .= esc_html__( 'wpLingua is not compatible with the "Plain" permalink structure. Please update your permalink settings to use "Post name" or "Custom Structure".', 'wplingua' );
	$html .= '</p>';
	$html .= '<a ';
	$html .= 'href="' . esc_url( $url_permalinks ) . '" ';
	$html .= 'class="button button-primary" ';
	$html .= 'style="margin-bottom: 10px;">';
	$html .= esc_html__( 'Manage permalink settings', 'wplingua' );
	$html .= '</a>';
	$html .= '</div>'; // End .notice

	echo $html;
}


/**
 * Display a notice for obtaining the PRO version of the plugin
 * - Only for non free user
 * - Only on wpLingua option's pages
 * - Only if PRO plugin not already activated
 *
 * @return void|string Outputs the admin notice if applicable, or returns void if no notice is required.
 */
function wplng_admin_notice_get_pro_version() {

	if ( is_plugin_active( 'wplingua-pro/wplingua-pro.php' ) ) {
		return;
	}

	$data = wplng_get_api_data();
	
	if ( empty( $data['status'] ) 
		|| $data['status'] === 'FREE'
	) {
		return;
	}

	$html  = '<div';
	$html .= ' class="wplng-notice notice notice-warning is-dismissible"';
	$html .= ' style="text-align: center;"';
	$html .= '>';
	$html .= '<p style="font-weight: 600;">';
	$html .= '<span class="dashicons dashicons-translation"></span> ';
	$html .= esc_html__( 'wpLingua - Unlock PRO Features', 'wplingua' );
	$html .= '</p>';
	$html .= '<hr>';
	$html .= '<p>';
	$html .= esc_html__( 'Extend wpLingua with additional features and advanced translation capabilities.', 'wplingua' );
	$html .= '</p>';
	$html .= '<p>';
	$html .= esc_html__( 'Download and install wpLingua PRO to access the features included with your wpLingua plan.', 'wplingua' );
	$html .= '</p>';

	$html .= '<br>';
	$html .= '<a';
	$html .= ' href="https://wplingua.com/download/"';
	$html .= ' target="_blank"';
	$html .= ' rel="noopener noreferrer"';
	$html .= ' class="button button-primary"';
	$html .= '>';
	$html .= esc_html__( 'wpLingua.com : Download PRO plugin', 'wplingua' );
	$html .= '</a>';
	$html .= '</div>'; // End .notice

	echo $html;
}