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

	// Return early if the provided JavaScript is empty or consists only of whitespace
	if ( empty( trim( $js ) ) ) {
		return $js;
	}

	// Array to hold matched JSON objects
	$json = array();

	// Regex to match JavaScript variable or window object assignment containing JSON
	preg_match_all(
		'#(var\s|let\s|window\._)(.*)\s?=\s?(\{.*\});?#Ui',
		$js,
		$json
	);

	// Check if regex found a valid match
	if ( ! empty( $json[2][0] ) && ! empty( $json[3][0] ) ) {

		$var_name = $json[2][0];  // Variable name
		$var_json = $json[3][0];  // JSON string

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

	return $js;  // Return the translated JavaScript
}
