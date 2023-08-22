<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_parser_clear_html( $html, $translations = array() ) {

	$selector_clear   = wplng_get_selector_clear();
	$selector_exclude = wplng_get_selector_exclude();

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

	// Clear HTML from multiple space and tab
	$html_clear = preg_replace( '#\s+#', ' ', $html_clear );

	// Clear HTML from useless attributes
	$html_clear = preg_replace( '# (src|srcset|rel|class|href|target|itemscope|style|name|media|loading|decoding|role|height|width|itemprop|type|itemtype|sizes|onchange|onclick|datetime|selected|value)=(\"|\').*(\"|\')#Uis', '', $html_clear );

	/**
	 * Remove saved translation from HTML clear
	 */
	foreach ( $translations as $translation ) {

		// Check if translaton data is valid
		if (
			! isset( $translation['source'] ) // Original text
			|| ! isset( $translation['translation'] ) // Translater text
			|| ! isset( $translation['sr'] ) // Search Replace
		) {
			continue;
		}

		foreach ( $translation['sr'] as $key => $sr ) {

			$regex = str_replace(
				'WPLNG',
				preg_quote( $translation['source'] ),
				$sr['search']
			);

			$replace = str_replace(
				'WPLNG',
				'',
				$sr['replace']
			);

			// Replace knowing translation by empty string
			$html_clear = preg_replace(
				$regex,
				$replace,
				$html_clear
			);

		}
	}
	$html_clear = preg_replace( '#>\s*<#Uis', '><', $html_clear );

	return $html_clear;
}


function wplng_parser( $html, $language_source_id = '', $language_target_id = '', $translations = array() ) {

	$api_key = wplng_get_api_key();
	if ( empty( $api_key ) ) {
		return array();
	}

	$html = wplng_parser_clear_html( $html, $translations );

	if ( empty( $html ) ) {
		return array();
	}

	if ( empty( $language_target_id ) ) {
		$language_target_id = wplng_get_language_current_id();
	}

	if ( empty( $language_source_id ) ) {
		$language_source_id = wplng_get_language_website_id();
	}

	$body = array(
		'api_key' => $api_key,
		'request'       => 'parser',
		'version'       => WPLNG_API_VERSION,
		'source'  => $language_source_id,
		'target'  => $language_target_id,
		'html'    => $html,
	);
	$args = array(
		'method'    => 'POST',
		'timeout'   => 120,
		'sslverify' => false,
		'body'      => $body,
	);

	$request = wp_remote_post(
		WPLNG_API_URL . '/app/',
		$args
	);

	if ( is_wp_error( $request )
		|| wp_remote_retrieve_response_code( $request ) != 200
	) {
		return array();
	}

	$response = json_decode( wp_remote_retrieve_body( $request ), true );

	if ( empty( $response ) ) {
		return array();
	}

	return $response;
}
