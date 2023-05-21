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

// require_once 'inc/translation-storage.php';

global $machiavel_language_target;
$machiavel_language_target = false;


function mcv_get_language_source() {
	return 'fr';
}

function mcv_get_language_target() {
	// return 'de';
	// var_dump($_SERVER); die;
	global $machiavel_language_target;

	if ( $machiavel_language_target !== false ) {
		return $machiavel_language_target;
	}

	$current_path         = $_SERVER['REQUEST_URI'];
	$mcv_language_target  = false;
	$mcv_languages_target = [
		'en',
		'de',
		'pt',
		'es',
	];

	foreach ( $mcv_languages_target as $key => $language ) {
		if ( str_starts_with( $current_path, '/' . $language . '/' ) ) {
			$mcv_language_target = $language;
			break;
		}
	}

	$machiavel_language_target = $mcv_language_target;

	return $mcv_language_target;
}

add_action( 'init', 'mcv_init' );
function mcv_init() {

	if ( is_admin() || empty( mcv_get_language_target() ) ) {
		return;
	}

	$current_path           = $_SERVER['REQUEST_URI'];
	$origin_path            = '/' . substr( $current_path, 4, strlen( $current_path ) - 1 );
	$_SERVER['REQUEST_URI'] = $origin_path;
	ob_start( 'mcv_ob_callback' );
}

add_action( 'after_body', 'mcv_after_body' );
function mcv_after_body() {
	ob_end_flush();
}



function mcv_parser( $html ) {

	$body = array(
		'api-key' => '1111111111111111',
		'r'       => 'parser',
		'source'  => 'fr',
		'target'  => 'pt',
		'text'    => $html,
	);
	$args = array(
		'method'    => 'POST',
		'timeout'   => 5,
		'sslverify' => false,
		'body'      => $body,
	);

	error_log( var_export( $body, true ) );

	$request = wp_remote_post( MCV_API, $args );

	if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		error_log( print_r( $request, true ) );
		return '';
	}

	// return (wp_remote_retrieve_body( $request ));
	$response = json_decode( wp_remote_retrieve_body( $request ), true );

	return $response;

}



function mcv_ob_callback( $html ) {

	$mcv_language_target = mcv_get_language_target();
	$html_translated     = $html;

	// Clear useless part for HTML parsing
	// $html = str_replace( array( "\r", "\n", '  ', "\t" ), '', $html );
	$html = preg_replace( '/<!--.*-->/Uis', '', $html );
	$html = preg_replace( '/<style.*<\/style>/Uis', '', $html );
	$html = preg_replace( '/<script.*<\/script>/Uis', '', $html );
	$html = preg_replace( '/<svg.*<\/svg>/Uis', '', $html );

	$json_path = MCV_UPLOADS_PATH . 'translations-' . $mcv_language_target . '.json';
	$translations = [];
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
	foreach ($translations as $key => $translation) {

		if ( 
			! isset( $translation['source'] ) 
			|| ! isset( $translation['translation'] ) 
			|| ! isset( $translation['sb'] ) 
			|| ! isset( $translation['sa'] ) 
			|| ! isset( $translation['rb'] ) 
			|| ! isset( $translation['ra'] ) 
		) {
			continue;
		}

		$regex = $translation['sb'] . preg_quote( $translation['source'] ) . $translation['sa'];

		$html = preg_replace(
			$regex,
			'',
			$html
		);
	}

	// Get new translation from API
	$translations_new = mcv_parser( $html );

	// TODO : Save new translation in WP

	// Merge know and new translations
	$translations = array_merge($translations, $translations_new);

	// Replace original texts by translations
	foreach ( $translations as $key => $translation ) {

		if ( 
			! isset( $translation['source'] ) 
			|| ! isset( $translation['translation'] ) 
			|| ! isset( $translation['sb'] ) 
			|| ! isset( $translation['sa'] ) 
			|| ! isset( $translation['rb'] ) 
			|| ! isset( $translation['ra'] ) 
		) {
			continue;
		}

		$regex = $translation['sb'] . preg_quote( $translation['source'] ) . $translation['sa'];
		$replace = $translation['rb'] . $translation['translation'] . $translation['ra'];

		$html_translated = preg_replace(
			$regex,
			$replace,
			$html_translated
		);
	}

	// Save new translation file
	if (!empty($translations_new)) {
		file_put_contents( $json_path, json_encode( array_merge( $translations, $translations_new ) ) );
	}
	
	// Set "<html lang=""> for current languages
	// TODO : Check if wp hook exist
	$html_translated = preg_replace(
		'/<html (.*?)?lang=(\"|\')(\S*)(\"|\')/',
		'<html $1lang=$2' . $mcv_language_target . '$4',
		$html_translated
	);

	return $html_translated;
	// return '<pre>' . esc_html( var_export( $translations, true ) ) . '</pre>';
}


function mcv_translate( $language_source, $language_target, $text ) {

	$body = array(
		'api-key' => '1111111111111111',
		'r'       => 'translate',
		'source'  => $language_source,
		'target'  => $language_target,
		'text'    => $text,
	);
	$args = array(
		'method'    => 'POST',
		'timeout'   => 5,
		'sslverify' => false,
		'body'      => $body,
	);

	error_log( var_export( $body, true ) );

	$request = wp_remote_post( MCV_API, $args );

	if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		error_log( print_r( $request, true ) );
		return '';
	}

	$response = json_decode( wp_remote_retrieve_body( $request ), true );

	if ( ! isset( $response['translation'] ) ) {
		return 'Erreur :: [' . $text . ']';
	}

	return (string) $response['translation'];
}

add_action( 'wp_enqueue_scripts', 'mcv_register_assets' );
function mcv_register_assets() {

	if ( is_admin() ) {
		return;
	}

	wp_enqueue_script( 'jquery' );

	wp_enqueue_script(
		'machiavel',
		plugins_url( 'js/script.js', __FILE__ ),
		array( 'jquery' ),
		'1.0',
		true
	);

	wp_enqueue_style(
		'machiavel',
		plugins_url( 'css/front.css', __FILE__ )
	);
}