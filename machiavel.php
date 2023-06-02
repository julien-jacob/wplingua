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
require_once 'inc/storage.php';
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



function mcv_replace_og_local( $html ) {

	if ( ! mcv_url_current_is_translatable() ) {
		return $html;
	}

	$html = preg_replace(
		'#<meta (.*?)?property=(\"|\')og:locale(\"|\') (.*?)?>#',
		'<meta property=$2og:locale$2 content=$2' . mcv_get_language_current_id() . '$2>',
		$html
	);

	return $html;
}


function mcv_language_attributes( $attr ) {

	$language_current_id = mcv_get_language_current_id();

	if ( is_admin() || empty( $language_current_id ) ) {
		return $attr;
	}

	$attr = preg_replace(
		'#lang=(\"|\')(..)-(..)(\"|\')#i',
		'lang=$1' . esc_attr( $language_current_id ) . '$4',
		$attr
	);

	return $attr;
}



function mcv_link_alternate_hreflang() {

	$html = '';

	// Create alternate link for website language
	$language_website = mcv_get_language_website();
	$html            .= '<link rel="alternate" hreflang="' . esc_attr( $language_website['id'] ) . '" href="' . esc_url( mcv_get_url_original() ) . '">';

	// Create alternate link for each target languages
	$languages_target = mcv_get_languages_target();
	foreach ( $languages_target as $key => $language_target ) {
		$url   = mcv_get_url_current_for_language( $language_target['id'] );
		$html .= '<link rel="alternate" hreflang="' . esc_attr( $language_target['id'] ) . '" href="' . esc_url( $url ) . '">';
	}

	echo $html;
}



function mcv_init() {

	if ( ! mcv_url_current_is_translatable() ) {
		return;
	}

	global $mcv_request_uri;

	$current_path           = $mcv_request_uri;
	$origin_path            = '/' . substr( $current_path, 4, strlen( $current_path ) - 1 );
	$_SERVER['REQUEST_URI'] = $origin_path;

	ob_start( 'mcv_ob_callback' );
}


function mcv_ob_callback( $html ) {

	$mcv_language_target = mcv_get_language_current_id();
	$html_translated     = $html;

	// Clear useless part for HTML parsing
	$html = preg_replace( '#<!--.*-->#Uis', '', $html );
	$html = preg_replace( '#<style.*<\/style>#Uis', '', $html );
	$html = preg_replace( '#<script.*<\/script>#Uis', '', $html );
	$html = preg_replace( '#<svg.*<\/svg>#Uis', '', $html );
	// $html = str_replace( array( "\r", "\n", '  ', "\t" ), '', $html );

	$translations_new = array();
	$translations = mcv_get_saved_translations( $mcv_language_target );

	// Clear HTML of know translation
	foreach ( $translations as $translation ) {

		// Check if translaton data is valid
		if ( ! isset( $translation['source'] ) // Original text
			|| ! isset( $translation['translation'] ) // Translater text
			|| ! isset( $translation['search'] ) // Search
			|| ! isset( $translation['replace'] ) // Replace
		) {
			continue;
		}

		$regex = str_replace(
			'MCV',
			preg_quote( $translation['source'] ),
			stripslashes( $translation['search'] )
		);
		// Replace knowing translation by empty string

		$html = preg_replace( $regex, '', $html );
	}

	// return $html;

	// Get new translation from API
	$translations_new = mcv_parser( $html );

	// TODO : Save new translation in WP (fait ?!)

	// var_dump( $translations, $translations_new );
	// die;

	// Merge know and new translations
	$translations = array_merge( $translations, $translations_new );

	// Replace original texts by translations
	foreach ( $translations as $translation ) {

		// Check if translaton data is valid
		if ( ! isset( $translation['source'] ) // Original text
			|| ! isset( $translation['translation'] ) // Translater text
			|| ! isset( $translation['search'] ) // Search
			|| ! isset( $translation['replace'] ) // Replace
		) {
			continue;
		}

		if ( ! empty( $translation['source'] ) ) {

			$regex = str_replace(
				'MCV',
				preg_quote( $translation['source'] ),
				stripslashes( $translation['search'] )
			);

			$replace = str_replace(
				'MCV',
				$translation['translation'],
				$translation['replace']
			);

			// Replace original text in HTML by translation
			$html_translated = preg_replace( $regex, $replace, $html_translated );
		}
	}

	// Save new translation file
	if ( ! empty( $translations_new ) ) {

		// TODO : comment for current test
		// file_put_contents( $json_path, json_encode( array_merge( $translations, $translations_new ) ) );

		foreach ( $translations_new as $key => $translation ) {

			if ( ! isset( $translation['source'] ) // Original text
				|| ! isset( $translation['translation'] ) // Translater text
				|| ! isset( $translation['search'] ) // Search
				|| ! isset( $translation['replace'] ) // Replace
			) {
				continue;
			}

			mcv_save_translation(
				$mcv_language_target,
				$translation['source'],
				$translation['translation'],
				$translation['search'],
				$translation['replace']
			);
		}
	}

	$html_translated = apply_filters( 'mcv_html_translated', $html_translated );

	return $html_translated;
	// return mcv_get_translations_for_language();
	// return '<pre>' . esc_html( var_export( $translations, true ) ) . '</pre>';
}

