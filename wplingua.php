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

global $wplng_request_uri;
$wplng_request_uri = $_SERVER['REQUEST_URI'];

define( 'WPLNG_API', 'http://machiavel-api.local/v0.0/last/' );
// define( 'WPLNG_API', 'https://api.wplingua.com/v0.0/last/' );

// Require files in /lib/ folder 
require_once 'lib/simple_html_dom.php';

// Require files in /inc/admin/ folder 
require_once 'inc/admin/admin-bar.php';
require_once 'inc/admin/assets.php';
require_once 'inc/admin/dictionary-cpt.php';
require_once 'inc/admin/dictionary-meta.php';
require_once 'inc/admin/option-page.php';
require_once 'inc/admin/translation-cpt.php';
require_once 'inc/admin/translation-meta.php';

// Require files in /inc/ob-callback/ folder 
require_once 'inc/ob-callback/editor.php';
require_once 'inc/ob-callback/translate.php';

// Require files in /inc/ folder 
require_once 'inc/api.php';
require_once 'inc/assets.php';
require_once 'inc/html-updater.php';
require_once 'inc/languages.php';
require_once 'inc/mail.php';
require_once 'inc/search.php';
require_once 'inc/switcher.php';
require_once 'inc/translation.php';
require_once 'inc/url.php';




function wplng_start() {

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

	/**
	 * wplng_dictionary : CPT, taxo, meta
	 */

	// Register wplng_dictionary CPT
	add_action( 'init', 'wplng_register_post_type_dictionary' );

	// Add metabox for wplng_dictionary
	add_action( 'add_meta_boxes_wplng_dictionary', 'wplng_dictionary_add_meta_box' );

	// Save metabox on posts saving
	add_action( 'save_post_wplng_dictionary', 'wplng_dictionary_save_meta_boxes_data', 10, 2 );

	// Enqueue Script for wplng_dictionary admin
	add_action( 'admin_print_scripts-post-new.php', 'wplng_dictionary_assets' );
	add_action( 'admin_print_scripts-post.php', 'wplng_dictionary_assets' );

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
	add_action( 'toplevel_page_wplingua/inc/admin/option-page', 'wplng_inline_script_all_language' );

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
