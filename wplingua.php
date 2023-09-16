<?php
/*
Plugin Name: wpLingua
description: Make your website multilingual and translated
Version: 0.0.6
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WPLNG_API_URL', 'http://machiavel-api.local' );
define( 'WPLNG_API_VERSION', 'last' );
define( 'WPLNG_PLUGIN_VERSION', '0.0.6' );
define( 'WPLNG_PLUGIN_PATH', dirname( __FILE__ ) );
define( 'WPLNG_MAX_TRANSLATIONS', 256 );


require_once WPLNG_PLUGIN_PATH . '/loader.php';


function wplng_start() {

	global $wplng_request_uri;
	$wplng_request_uri = $_SERVER['REQUEST_URI'];

	// Register plugin settings
	add_action( 'admin_init', 'wplng_register_settings' );

	// Add settings link in plugin list
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wplng_settings_link' );

	// Print head script (JSON with all languages informations)
	add_action( 'toplevel_page_wplng-settings', 'wplng_inline_script_languages' );

	if ( empty( wplng_get_api_data() ) ) {

		// Add Register option page in back office menu
		add_action( 'admin_menu', 'wplng_create_menu_register' );

		// Enqueue CSS and JS files for register option pages
		add_action( 'admin_enqueue_scripts', 'wplng_option_page_register_assets' );

	} else {

		/**
		 * Back office
		 */

		// Add menu in back office
		add_action( 'admin_menu', 'wplng_create_menu' );

		// Add admin Bar menu
		add_action( 'admin_bar_menu', 'wplng_admin_bar_menu', 100 );

		// Enqueue CSS and JS files for option pages
		add_action( 'admin_enqueue_scripts', 'wplng_option_page_settings_assets' );
		add_action( 'admin_enqueue_scripts', 'wplng_option_page_exclusions_assets' );
		add_action( 'admin_enqueue_scripts', 'wplng_option_page_switcher_assets' );

		// Update flags URL
		add_action( 'update_option_wplng_flags_style', 'wplng_options_switcher_update_flags_style', 10, 2 );

		// Reset API data on API key changing
		add_action( 'update_option_wplng_api_key', 'wplng_on_update_option_wplng_api_key', 10, 2 );

		/**
		 * wplng_translation : CPT, taxo, meta
		 */

		// Register wplng_translation CPT
		add_action( 'init', 'wplng_register_post_type_translation' );

		// Add metabox for wplng_translation
		add_action( 'add_meta_boxes_wplng_translation', 'wplng_translation_add_meta_box' );

		// Save metabox on posts saving
		add_action( 'save_post_wplng_translation', 'wplng_translation_save_meta_boxes_data', 10, 2 );

		// Enqueue Script for wplng_translation admin
		add_action( 'admin_print_scripts-post-new.php', 'wplng_translation_assets' );
		add_action( 'admin_print_scripts-post.php', 'wplng_translation_assets' );

		// Remove Quick edit from translations list
		add_filter( 'post_row_actions', 'wplng_translation_remove_quick_edit', 10, 2 );

		// Ajax function for regenerate translation on edit page
		add_action( 'wp_ajax_wplng_ajax_translation', 'wplng_ajax_generate_translation' );

		/**
		 * Front
		 */

		// Enqueue CSS and JS files
		add_action( 'wp_enqueue_scripts', 'wplng_register_assets' );

		// Add languages switcher before </body>
		add_action( 'wp_footer', 'wplng_switcher_wp_footer' );

		// Set alternate links with hreflang parametters
		add_action( 'wp_head', 'wplng_link_alternate_hreflang' );

		/**
		 * OB and REQUEST_URI
		 */

		 // Manage URL with REQUEST_URI and start OB
		add_action( 'init', 'wplng_init' );

		// Stop OB at the end of the HTML
		add_action( 'after_body', 'ob_end_flush' );

		/**
		 * Features
		 */

		// Translate email
		if ( ! empty( get_option( 'wplng_translate_mail' ) )
			&& wplng_api_feature_is_allow( 'mail' )
		) {
			add_filter( 'wp_mail', 'wplng_translate_wp_mail' );
		}

		// Search from translated languages
		if ( ! empty( get_option( 'wplng_translate_search' ) )
			&& wplng_api_feature_is_allow( 'search' )
		) {
			add_action( 'parse_query', 'wplng_translate_search_query' );
		} else {
			add_filter( 'wplng_url_is_translatable', 'wplng_exclude_search', 20 );
		}

		// Woocommerce
		if ( empty( get_option( 'wplng_translate_woocommerce' ) ) ) {
			add_filter( 'wplng_url_exclude', 'wplng_exclude_woocommerce_url', 20 );
		}

		/**
		 * Shortcode
		 */
		add_shortcode( 'wplingua-switcher', 'wplng_shortcode_switcher' );
		add_shortcode( 'wplingua-notranslate', 'wplng_shortcode_notranslate' );
		add_shortcode( 'notranslate', 'wplng_shortcode_notranslate' );

	}

}
wplng_start();
