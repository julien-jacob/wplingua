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

	$selector_clear = array(
		'style',
		'script',
		'svg',
	);

	$selector_exclude = array(
		'#wpadminbar',
	);

	/**
	 * Clear HTML for API call
	 */

	// Remove comments from HTML Clear
	$html_clear = preg_replace( '#<!--.*-->#Uis', '', $html );

	// Remove useless and excluded elements from HTML clear
	$dom = str_get_html( $html_clear );

	if ( $dom === false ) {
		return $html;
	}

	$selector_to_remove = array_merge( $selector_exclude, $selector_clear );

	foreach ( $selector_to_remove as $key => $selector ) {
		foreach ( $dom->find( $selector ) as $element ) {
			$element->outertext = '';
		}
	}

	$dom->save();
	$html_clear = (string) str_get_html( $dom );

	/**
	 * Get saved translation
	 */
	$mcv_language_target = mcv_get_language_current_id();
	$translations        = mcv_get_saved_translations( $mcv_language_target );
	// return '<pre >' . var_export($html_clear, true) . '</pre>';

	/**
	 * Remove saved translation from HTML clear
	 */
	foreach ( $translations as $translation ) {

		// Check if translaton data is valid
		if (
			! isset( $translation['source'] ) // Original text
			|| ! isset( $translation['translation'] ) // Translater text
			|| ! isset( $translation['search'] ) // Search
			|| ! isset( $translation['replace'] ) // Replace
		) {
			continue;
		}

		// TODO : Mettre preg_quote() plutôt sur $regex ?
		$regex = str_replace(
			'MCV',
			preg_quote( $translation['source'] ),
			// '#>(\s*)MCV(\s*)<#Uis'
			$translation['search']
		);

		$replace = str_replace(
			'MCV',
			'',
			$translation['replace']
		);

		// Replace knowing translation by empty string
		$html_clear = preg_replace(
			$regex,
			$replace,
			$html_clear
		);
	}
	// return '<pre >' . var_export($html_clear, true) . '</pre>';


	/**
	 * Get new translation from API
	 */
	$translations_new = mcv_parser( $html_clear );


	/**
	 * Save new translation as mcv_translation CPT
	 */
	if ( ! empty( $translations_new ) ) {

		foreach ( $translations_new as $key => $translation ) {

			if (
				! isset( $translation['source'] ) // Original text
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

	
	/**
	 * Merge know and new translations
	 */
	$translations = array_merge( $translations, $translations_new );


	/**
	 * Replace excluded HTML part by tab
	 */
	$excluded_elements = array();
	$dom               = str_get_html( $html );

	if ( $dom === false ) {
		return $html;
	}

	foreach ( $selector_exclude as $key => $selector ) {
		foreach ( $dom->find( $selector ) as $element ) {
			$excluded_elements[] = $element->outertext;
			$attr                = count( $excluded_elements ) - 1;
			$element->outertext  = '<div mcv-tag-exclude="' . esc_attr( $attr ) . '"></div>';
		}
	}

	$dom->save();
	$html = (string) str_get_html( $dom );

	
	/**
	 * Replace original texts by translations
	 */
	foreach ( $translations as $translation ) {

		// Check if translaton data is valid
		if (
			! isset( $translation['source'] ) // Original text
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
				$translation['search']
			);

			$replace = str_replace(
				'MCV',
				$translation['translation'],
				$translation['replace']
			);

			// Replace original text in HTML by translation
			$html = preg_replace( $regex, $replace, $html );
		}
	}


	/**
	 * Replace tag by saved excluded HTML part
	 */
	foreach ( $excluded_elements as $key => $element ) {
		$s    = '<div mcv-tag-exclude="' . esc_attr( $key ) . '"></div>';
		$html = str_replace( $s, $element, $html );
	}

	$html = apply_filters( 'mcv_html_translated', $html );

	return $html;
}
