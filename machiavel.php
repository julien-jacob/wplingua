<?php
/*
Plugin Name: Machiavel
description: Aussi est-il nécessaire au Prince qui se veut conserver qu'il apprenne à pouvoir n'être pas bon...
Version: 0.1
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'MCV_UPLOADS_PATH', WP_CONTENT_DIR . '/uploads/machiavel/' );
define( 'MCV_API', 'http://machiavel-api.local/v0.1/last/' );

require_once 'inc/api.php';
require_once 'inc/assets.php';
require_once 'inc/html-updater.php';
require_once 'inc/languages.php';
require_once 'inc/option-page.php';
require_once 'inc/switcher.php';
require_once 'inc/translation-cpt.php';
require_once 'inc/translation-meta.php';
require_once 'inc/translation.php';
require_once 'inc/url.php';

global $mcv_request_uri;
$mcv_request_uri = $_SERVER['REQUEST_URI'];

function mcv_start() {

	/**
	 * Back office
	 */

	// Register plugin settings
	add_action( 'admin_init', 'mcv_register_settings' );

	// Add menu in back office
	add_action( 'admin_menu', 'mcv_create_menu' );

	// Add settings link in plugin list
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mcv_settings_link' );

	// Enqueue CSS and JS files
	add_action( 'admin_enqueue_scripts', 'mcv_enqueue_callback' );

	// Print head script (JSON with all languages informations)
	add_action( 'toplevel_page_machiavel/inc/option-page', 'mcv_inline_script_all_language' );

	/**
	 * Front
	 */

	// Enqueue CSS and JS files
	add_action( 'wp_enqueue_scripts', 'mcv_register_assets' );

	// Add languages switcher before </body>
	add_action( 'wp_footer', 'mcv_switcher_wp_footer' );

	// Change <html lang=""> if translated content
	add_filter( 'language_attributes', 'mcv_language_attributes' );

	// Set alternate links with hreflang parametters
	add_action( 'wp_head', 'mcv_link_alternate_hreflang' );

	// Set OG Local
	add_filter( 'mcv_html_translated', 'mcv_replace_og_local' );

	/**
	 * OB and REQUEST_URI
	 */

	 // Manage URL with REQUEST_URI and start OB
	add_action( 'init', 'mcv_init' );

	// Stop OB at the end of the HTML
	add_action( 'after_body', 'ob_end_flush' );

}
mcv_start();
