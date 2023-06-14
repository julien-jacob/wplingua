<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_translate( $language_source, $language_target, $text ) {

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

	$request = wp_remote_post( WPLNG_API, $args );

	if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		error_log( print_r( $request, true ) );
		return '';
	}

	$response = json_decode( wp_remote_retrieve_body( $request ), true );

	if ( ! isset( $response['translation'] ) ) {
		// TODO : Check for remove or update
		return 'Erreur :: [' . $text . ']';
	}

	return (string) $response['translation'];
}


function wplng_parser_clear_html($html, $translations = array()) {

	$selector_clear = array(
		'style',
		'script',
		'svg',
	);

	$selector_exclude = array(
		'#wpadminbar',
		'.wplng-switcher',
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


function wplng_parser( $html, $translations = array() ) {

	$html = wplng_parser_clear_html($html, $translations);

	if (empty($html)) {
		return array();
	}

	$body = array(
		'api-key' => '1111111111111111',
		'r'       => 'parser',
		'source'  => wplng_get_language_website_id(),
		'target'  => wplng_get_language_current_id(),
		'text'    => $html,
	);
	$args = array(
		'method'    => 'POST',
		'timeout'   => 120,
		'sslverify' => false,
		'body'      => $body,
	);

	$request = wp_remote_post( WPLNG_API, $args );

	if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		error_log( print_r( $request, true ) );
		return array();
	}

	$response = json_decode( wp_remote_retrieve_body( $request ), true );
	
	if (NULL == $response) {
		// TODO : Bad Json - Error log
		return array();
	}

	return $response;
}
