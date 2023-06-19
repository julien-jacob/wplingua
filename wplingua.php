<?php
/*
Plugin Name: wpLingua
description: Make your website multilingual and translated
Version: 0.0.4
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// define( 'WPLNG_API', 'http://machiavel-api.local/v0.0.2/last/' );
define( 'WPLNG_API', 'https://api.wplingua.com/v0.0/3/' );

require_once 'lib/simple_html_dom.php';

require_once 'inc/ob-callback/editor.php';
require_once 'inc/ob-callback/translate.php';
require_once 'inc/admin-bar.php';
require_once 'inc/api.php';
require_once 'inc/assets.php';
require_once 'inc/html-updater.php';
require_once 'inc/languages.php';
require_once 'inc/mail.php';
require_once 'inc/option-page.php';
require_once 'inc/search.php';
require_once 'inc/switcher.php';
require_once 'inc/translation-cpt.php';
require_once 'inc/translation-meta.php';
require_once 'inc/translation.php';
require_once 'inc/url.php';

global $wplng_request_uri;
$wplng_request_uri = $_SERVER['REQUEST_URI'];


function wplng_start() {

	/**
	 * CPT, taxo, meta
	 */
	// Register wplng_translation CPT
	add_action( 'init', 'wplng_register_post_type_translation' );

	// Add metabox for wplng_translation
	add_action( 'add_meta_boxes_wplng_translation', 'meta_box_for_products' );

	// Save metabox on posts saving
	add_action( 'save_post_wplng_translation', 'wplng_translation_save_meta_boxes_data', 10, 2 );

	/**
	 * Back office
	 */

	// Register plugin settings
	add_action( 'admin_init', 'wplng_register_settings' );

	// Add menu in back office
	add_action( 'admin_menu', 'wplng_create_menu' );

	// Add settings link in plugin list
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wplng_settings_link' );

	// Add admin Bar menu
	add_action( 'admin_bar_menu', 'wplng_admin_bar_menu', 100 );

	// Enqueue CSS and JS files
	add_action( 'admin_enqueue_scripts', 'wplng_enqueue_callback' );

	// Print head script (JSON with all languages informations)
	add_action( 'toplevel_page_wplingua/inc/option-page', 'wplng_inline_script_all_language' );

	/**
	 * Front
	 */

	// Enqueue CSS and JS files
	add_action( 'wp_enqueue_scripts', 'wplng_register_assets' );

	// Add languages switcher before </body>
	add_action( 'wp_footer', 'wplng_switcher_wp_footer' );

	// Change <html lang=""> if translated content
	add_filter( 'language_attributes', 'wplng_language_attributes' );

	// Set alternate links with hreflang parametters
	add_action( 'wp_head', 'wplng_link_alternate_hreflang' );

	// Set OG Local
	add_filter( 'wplng_html_translated', 'wplng_replace_og_local' );

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
	if ( ! empty( get_option( 'wplng_translate_mail' ) ) ) {
		add_filter( 'wp_mail', 'wplng_translate_wp_mail' );
	}

	// Search from translated languages
	if ( ! empty( get_option( 'wplng_translate_search' ) ) ) {
		add_action( 'parse_query', 'wplng_translate_search_query' );
	}

}
wplng_start();
