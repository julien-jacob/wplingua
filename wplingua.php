<?php
/**
 * Plugin Name: wpLingua
 * Plugin URI: https://github.com/julien-jacob/wplingua
 * Description: An all-in-one solution that makes your websites multilingual and translates them automatically, without word or page limits. The highlights: a free first language, an on-page visual editor for editing translations, a customizable language switcher, search engine optimization (SEO), self-hosted data and more!
 * Author: wpLingua Team
 * Author URI: https://wplingua.com/
 * Text Domain: wplingua
 * Domain Path: /languages/
 * Version: 1.1.1
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


// Define wpLingua constants
define( 'WPLNG_API_URL', 'https://api.wplingua.com' );
define( 'WPLNG_API_VERSION', '1.0' );
define( 'WPLNG_API_SSLVERIFY', true );
define( 'WPLNG_PLUGIN_VERSION', '1.1.1' );
define( 'WPLNG_PLUGIN_FILE', plugin_basename(__FILE__) );
define( 'WPLNG_PLUGIN_PATH', dirname( __FILE__ ) );
define( 'WPLNG_MAX_TRANSLATIONS', 256 );
define( 'WPLNG_MAX_FILE_SIZE', 1000000 );
define( 'WPLNG_LOG_JSON_DEBUG', false );
define( 'WPLNG_LOG_AJAX_DEBUG', false );


// Load plugin text domain
load_plugin_textdomain(
	'wplingua',
	false,
	basename( dirname( __FILE__ ) ) . '/languages'
);


// Load all needed PHP files
require_once WPLNG_PLUGIN_PATH . '/loader.php';


/**
 * Register all wpLingua Hook
 *
 * @return void
 */
function wplng_start() {

	// Define $wplng_request_uri
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {

		$request_uri = sanitize_url( $_SERVER['REQUEST_URI'] );

		// Check if the referer is clean
		if ( strtolower( esc_url_raw( $request_uri ) ) !== strtolower( $request_uri ) ) {
			return;
		}

		global $wplng_request_uri;
		$wplng_request_uri = $request_uri;

	}

	// Display a notice if an incompatible plugin is detected
	add_action( 'admin_notices', 'wplng_admin_notice_incompatible_plugin', 1 );

	// Register plugin settings
	add_action( 'admin_init', 'wplng_register_settings' );

	// Add settings link in plugin list
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wplng_settings_link' );

	// Redirect to the settings page on plugin activation
	add_action( 'activated_plugin', 'wplng_plugin_activation_redirect' );

	// Set footer text for options pages
	add_filter( 'admin_footer_text', 'wplng_admin_footer_text', 11 );
	add_filter( 'update_footer', 'wplng_update_footer', 11 );

	// Print head script (JSON with all languages informations)
	add_action( 'toplevel_page_wplingua-settings', 'wplng_inline_script_languages' );

	if ( empty( wplng_get_api_data() ) ) {

		// Add Register option page in back office menu
		add_action( 'admin_menu', 'wplng_create_menu_register' );

		// Enqueue CSS and JS files for register option pages
		add_action( 'admin_enqueue_scripts', 'wplng_option_page_register_assets' );

		// Display a notice if the plugin is activate but not configured
		add_action( 'admin_notices', 'wplng_admin_notice_no_key_set', 1 );

	} else {

		/**
		 * Back office
		 */

		// Add menu in back office
		add_action( 'admin_menu', 'wplng_create_menu' );

		// Add admin Bar menu
		add_action( 'admin_bar_menu', 'wplng_admin_bar_menu', 81 );

		// Enqueue CSS and JS files for option pages
		add_action( 'admin_enqueue_scripts', 'wplng_option_page_settings_assets' );
		add_action( 'admin_enqueue_scripts', 'wplng_option_page_switcher_assets' );
		add_action( 'admin_enqueue_scripts', 'wplng_option_page_exclusions_assets' );
		add_action( 'admin_enqueue_scripts', 'wplng_option_page_dictionary_assets' );

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

		// Clear translation cache on trash / untrash
		add_action( 'trashed_post', 'wplng_clear_translations_cache_trash_untrash' );
		add_action( 'untrash_post', 'wplng_clear_translations_cache_trash_untrash' );
		add_action( 'delete_post', 'wplng_clear_translations_cache_trash_untrash' );

		// Enqueue Script for wplng_translation admin
		add_action( 'admin_print_scripts-post-new.php', 'wplng_translation_assets' );
		add_action( 'admin_print_scripts-post.php', 'wplng_translation_assets' );

		// Remove Quick edit from translations list
		add_filter( 'post_row_actions', 'wplng_translation_remove_quick_edit', 10, 2 );

		// Ajax function for regenerate translation on edit page
		add_action( 'wp_ajax_wplng_ajax_translation', 'wplng_ajax_generate_translation' );

		// Display 100 translation in admin area by default
		add_filter( 'get_user_option_edit_wplng_translation_per_page', 'wplng_translation_per_page' );

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
		add_action( 'init', 'wplng_ob_start', 1 );

		/**
		 * Features
		 */

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
			add_filter( 'wplng_url_exclude_regex', 'wplng_exclude_woocommerce_url', 20 );
		}

		/**
		 * Shortcode
		 */

		add_shortcode( 'wplng_switcher', 'wplng_shortcode_switcher' );
		add_shortcode( 'wplng_notranslate', 'wplng_shortcode_notranslate' );
		add_shortcode( 'wplng_only', 'wplng_shortcode_only' );

	}

}
wplng_start();
