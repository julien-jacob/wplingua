<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

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
	$translations     = mcv_get_saved_translations( $mcv_language_target );

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
}
