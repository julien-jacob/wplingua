<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}




function wplng_parse_html( $html ) {

	$texts = array();
	$dom   = str_get_html( $html );

	if ( empty( $dom ) ) {
		return $html;
	}

	/**
	 * Parse Node text
	 */
	foreach ( $dom->find( 'text' ) as $element ) {

		if ( in_array( $element->parent->tag, array( 'style', 'svg', 'script', 'canvas', 'link' ) ) ) {
			continue;
		}

		$text = wplng_text_esc( $element->innertext );

		if ( ! wplng_text_is_translatable( $text ) ) {
			continue;
		}

		$texts[] = $text;

	}

	/**
	 * Parse attr
	 */
	foreach ( $dom->find( '*' ) as $element ) {

		if ( empty( $element->attr ) ) {
			continue;
		}

		foreach ( $element->attr as $attr => $value ) {
			if ( ! in_array( $attr, array( 'alt', 'title', 'placeholder', 'aria-label' ) )
				|| empty( $value )
			) {
				continue;
			}

			$text = wplng_text_esc( $value );

			if ( ! wplng_text_is_translatable( $text ) ) {
				continue;
			}

			$texts[] = $text;
		}
	}

	// Remove duplicate
	$texts = array_unique( $texts );

	return $texts;
	// return var_export( $x, true );

	// $dom->save();
	// $html = (string) str_get_html( $dom );

}

function wplng_parse_js( $html, $translations = array() ) {

}

function wplng_parse_json( $html, $translations = array() ) {

}


// function wplng_parser( $html, $language_source_id = '', $language_target_id = '', $translations = array() ) {

// 	$api_key = wplng_get_api_key();
// 	if ( empty( $api_key ) ) {
// 		return array();
// 	}

// 	if ( empty( $html ) ) {
// 		return array();
// 	}

// 	if ( empty( $language_target_id ) ) {
// 		$language_target_id = wplng_get_language_current_id();
// 	}

// 	if ( empty( $language_source_id ) ) {
// 		$language_source_id = wplng_get_language_website_id();
// 	}

// 	$body = array(
// 		'api_key' => $api_key,
// 		'request' => 'parser',
// 		'version' => WPLNG_API_VERSION,
// 		'source'  => $language_source_id,
// 		'target'  => $language_target_id,
// 		'html'    => $html,
// 	);
// 	$args = array(
// 		'method'    => 'POST',
// 		'timeout'   => 120,
// 		'sslverify' => false,
// 		'body'      => $body,
// 	);

// 	$request = wp_remote_post(
// 		WPLNG_API_URL . '/app/',
// 		$args
// 	);

// 	if ( is_wp_error( $request )
// 		|| wp_remote_retrieve_response_code( $request ) != 200
// 	) {
// 		return array();
// 	}

// 	$response = json_decode( wp_remote_retrieve_body( $request ), true );

// 	if ( empty( $response ) ) {
// 		return array();
// 	}

// 	return $response;
// }
