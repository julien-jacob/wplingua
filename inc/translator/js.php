<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * wpLingua translate : Get translated JS
 *
 * This function translates JavaScript code containing JSON objects.
 * It uses regex to find JavaScript variables and translates the JSON part.
 *
 * @param string $js The JavaScript code to translate.
 * @param array $args Additional arguments for translation processing.
 * @return string The translated JavaScript code.
 */
function wplng_translate_js( $js, $args = array() ) {

	// Return early if the provided JavaScript is empty
	// or consists only of whitespace
	// Or is the wp emoji script
	if ( empty( trim( $js ) )
		|| wplng_str_contains( $js, '_wpemojiSettings' )
	) {
		return $js;
	}

	// Array to hold matched JSON objects
	$json = array();

	/**
	 * Get the first 'var', 'let' or 'window._' declaration
	 */

	preg_match_all(
		'#(var\s|let\s|window\._)(.*)\s?=\s?(\[.*\]|\{.*\});#Ui',
		$js,
		$json
	);

	if ( ! empty( $json[2] ) && is_array( $json[2] ) ) {
		foreach ( $json[2] as $key => $var_name ) {

			$var_name = trim( $var_name );

			if ( empty( $var_name ) || empty( $json[3][ $key ] ) ) {
				continue;
			}

			// Get the JSON string
			$var_json = trim( $json[3][ $key ] );

			// Prepare arguments for translation
			wplng_args_setup( $args );
			$args['parents'] = array( $var_name );

			// Translate the JSON string
			$json_translated = wplng_translate_json(
				$var_json,
				$args
			);

			// Replace the original JSON with the translated version if different
			if ( $var_json != $json_translated ) {
				$js = str_replace(
					$var_json,
					$json_translated,
					$js
				);
			}
		}
	}

	/**
	 * Translate i18n JSON
	 */

	$json = array();

	preg_match_all(
		'#\(\s?["|\'](.*)["|\'],\s?(.*)\s?\);#Ui',
		$js,
		$json
	);

	if ( ! empty( $json[1] ) && is_array( $json[1] ) ) {
		foreach ( $json[1] as $key => $var_name ) {

			$var_name = trim( $var_name );

			if ( empty( $var_name ) || empty( $json[2][ $key ] ) ) {
				continue;
			}

			$var_json = trim( $json[2][ $key ] );

			// Prepare arguments for translation
			wplng_args_setup( $args );
			$args['parents'] = array( $var_name );

			// Translate the JSON string
			$json_translated = wplng_translate_json(
				$var_json,
				$args
			);

			// Replace the original JSON with the translated version if different
			if ( $var_json != $json_translated ) {
				$js = str_replace(
					$var_json,
					$json_translated,
					$js
				);
			}
		}
	}

	/**
	 * URL encoded JSON
	 */

	$json = array();

	preg_match_all(
		'#JSON\.parse\(\sdecodeURIComponent\(\s\'(.*)\'\s\)\s\)#Ui',
		$js,
		$json
	);

	if ( ! empty( $json[1] ) && is_array( $json[1] ) ) {
		foreach ( $json[1] as $key => $encoded_json ) {

			$var_json = urldecode( $encoded_json );

			// Prepare arguments for translation
			wplng_args_setup( $args );
			$args['parents'] = array( 'EncodedAsURL' );

			// Translate the JSON string
			$json_translated = wplng_translate_json(
				$var_json,
				$args
			);

			$json_translated = urlencode($json_translated);

			// Replace the original JSON with the translated version if different
			if ( $encoded_json != $json_translated ) {
				$js = str_replace(
					$var_json,
					$json_translated,
					$js
				);
			}

		}
	}

	return $js;  // Return the translated JavaScript
}
