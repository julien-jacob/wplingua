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
require_once 'inc/languages.php';
require_once 'inc/option-page.php';
require_once 'inc/switcher.php';
require_once 'inc/url.php';

// echo '<pre>';
// var_dump(mcv_get_url_current());
// echo '</pre>';
// die;

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


	/**
	 * OB and REQUEST_URI
	 */

	 // Manage URL with REQUEST_URI and start OB
	add_action( 'init', 'mcv_init' );

	// Stop OB at the end of the HTML
	add_action( 'after_body', 'ob_end_flush' );

}
mcv_start();




// add_filter('language_attributes', function( $attr ) {
// 	$attr = preg_replace('/lang=(\"|\')(..)-(..)(\"|\')/i', 'lang=$1$2$4', $attr);
// 	var_dump('-' . $attr . '-'); die;
// 	return $attr;
// });



function mcv_init() {
	
	if ( is_admin() || empty( mcv_get_language_current_id() ) ) {
		return;
	}

	global $mcv_request_uri;

	$current_path           = $mcv_request_uri;
	$origin_path            = '/' . substr( $current_path, 4, strlen( $current_path ) - 1 );
	$_SERVER['REQUEST_URI'] = $origin_path;

	ob_start( 'mcv_ob_callback' );
}




function mcv_ob_callback( $html ) {

	// return $html;
	// return '<pre>' . var_export(mcv_get_url_current(), true) . '</pre>';

	$mcv_language_target = mcv_get_language_current_id();
	$html_translated     = $html;

	// Clear useless part for HTML parsing
	$html = preg_replace( '/<!--.*-->/Uis', '', $html );
	$html = preg_replace( '/<style.*<\/style>/Uis', '', $html );
	$html = preg_replace( '/<script.*<\/script>/Uis', '', $html );
	$html = preg_replace( '/<svg.*<\/svg>/Uis', '', $html );
	// $html = str_replace( array( "\r", "\n", '  ', "\t" ), '', $html );

	$json_path        = MCV_UPLOADS_PATH . 'translations-' . $mcv_language_target . '.json';
	$translations     = [];
	$translations_new = [];

	// Get know translations
	if ( file_exists( $json_path ) ) {
		$translations = json_decode( file_get_contents( $json_path ), true );
		if ( empty( $translations ) ) {
			$translations = [];
		}
	} else {
		$default_json = json_encode(
			[
				'wpRock' => 'wpRock',
			]
		);
		mkdir( MCV_UPLOADS_PATH );
		file_put_contents( $json_path, $default_json );
	}

	// Clear HTML of know translation
	foreach ( $translations as $translation ) {

		// Check if translaton data is valid
		if ( ! isset( $translation['source'] ) // Original text
			|| ! isset( $translation['translation'] ) // Translater text
			|| ! isset( $translation['sb'] ) // Search before
			|| ! isset( $translation['sa'] ) // Search after
			|| ! isset( $translation['rb'] ) // Replace before
			|| ! isset( $translation['ra'] ) // Replace after
		) {
			continue;
		}

		// Replace knowing translation by empty string
		$regex = $translation['sb'] . preg_quote( $translation['source'] ) . $translation['sa'];

		$html = preg_replace( $regex, '', $html );
	}

	// Get new translation from API
	$translations_new = mcv_parser( $html );

	// TODO : Save new translation in WP

	// Merge know and new translations
	$translations = array_merge( $translations, $translations_new );
// return '<pre>' . var_export($translations, true) . '</pre>';
	// Replace original texts by translations
	foreach ( $translations as $translation ) {

		// Check if translaton data is valid
		if ( ! isset( $translation['source'] ) // Original text
			|| ! isset( $translation['translation'] ) // Translater text
			|| ! isset( $translation['sb'] ) // Search before
			|| ! isset( $translation['sa'] ) // Search after
			|| ! isset( $translation['rb'] ) // Replace before
			|| ! isset( $translation['ra'] ) // Replace after
		) {
			continue;
		}

		if (!empty($translation['source'])) {
			$regex   = $translation['sb'] . preg_quote( $translation['source'] ) . $translation['sa'];
			$replace = $translation['rb'] . $translation['translation'] . $translation['ra'];
	
			// Replace original text in HTML by translation
			$html_translated = preg_replace( $regex, $replace, $html_translated );
		}

		
	}

	// Save new translation file
	if ( ! empty( $translations_new ) ) {
		file_put_contents( $json_path, json_encode( array_merge( $translations, $translations_new ) ) );
	}

	// Set "<html lang=""> for current languages
	// TODO : Check if wp hook exist
	// $html_translated = preg_replace(
	// 	'/<html (.*?)?lang=(\"|\')(\S*)(\"|\')/',
	// 	'<html $1lang=$2' . $mcv_language_target . '$4',
	// 	$html_translated
	// );

	return $html_translated;
	// return '<pre>' . esc_html( var_export( $translations, true ) ) . '</pre>';
}

