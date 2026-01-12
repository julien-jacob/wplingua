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
 * @param array  $args Additional arguments for translation processing.
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

	$js = wplng_translate_js_json_in_var( $js, $args );
	$js = wplng_translate_js_json_encoded_as_url( $js, $args );
	$js = wplng_translate_js_json_in_function_call( $js, $args );

	if ( wplng_str_is_script_i18n( $js ) ) {
		$js = wplng_translate_js_json_in_i18n_script( $js, $args );
	}

	return $js;  // Return the translated JavaScript
}


/**
 * Translate JSON contained in 'var', 'let' or 'window._'
 *
 * @param string $js The JavaScript code to translate.
 * @param array  $args Additional arguments for translation processing.
 * @return string The translated JavaScript code.
 */
function wplng_translate_js_json_in_var( $js, $args = array() ) {

	// Array to hold matched JSON objects
	$json = array();

	preg_match_all(
		'#(var\s|let\s|window\._)([A-Za-z0-9_]+)\s?=\s?(\{(?:[^{}"\'\\\\]+|"(?:\\\\.|[^"\\\\])*"|\'(?:\\\\.|[^\'\\\\])*\'|(?3))*\}|\[(?:[^\[\]"\'\\\\]+|"(?:\\\\.|[^"\\\\])*"|\'(?:\\\\.|[^\'\\\\])*\'|(?3))*\])\s*;#Us',
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

	return $js;  // Return the translated JavaScript
}


/**
 * Translate JSON in i18n script
 *
 * You can use wplng_parse_js_json_in_i18n_script( $js )
 * to check if the string is a i18n script
 *
 * @param string $js The JavaScript code to translate.
 * @param array  $args Additional arguments for translation processing.
 * @return string The translated JavaScript code.
 */
function wplng_translate_js_json_in_i18n_script( $js, $args = array() ) {

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

	return $js;  // Return the translated JavaScript
}


/**
 * Translate JSON encoded as URL
 *
 * @param string $js The JavaScript code to translate.
 * @param array  $args Additional arguments for translation processing.
 * @return string The translated JavaScript code.
 */
function wplng_translate_js_json_encoded_as_url( $js, $args = array() ) {

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

			$json_translated = rawurlencode( $json_translated );

			// Replace the original JSON with the translated version if different
			if ( $encoded_json != $json_translated ) {
				$js = str_replace(
					$encoded_json,
					$json_translated,
					$js
				);
			}
		}
	}

	return $js;  // Return the translated JavaScript
}


/**
 * Translate JSON passed as argument to function calls like jQuery.datepicker.setDefaults({...})
 *
 * @param string $js The JavaScript code to translate.
 * @param array  $args Additional arguments for translation processing.
 * @return string The translated JavaScript code.
 */
function wplng_translate_js_json_in_function_call( $js, $args = array() ) {

	$json = array();

	// Whitelist of function calls that contain translatable JSON
	$allowed_functions = wplng_data_json_in_js_functions();

	if ( empty( $allowed_functions ) ) {
		return array();
	}

	preg_match_all(
		'#([a-zA-Z_$][a-zA-Z0-9_$]*(?:\.[a-zA-Z_$][a-zA-Z0-9_$]*)+)\s*\(\s*(\{(?:[^{}"\'\\\\]+|"(?:\\\\.|[^"\\\\])*"|\'(?:\\\\.|[^\'\\\\])*\'|(?2))*\})\s*\)#Us',
		$js,
		$json
	);

	if ( ! empty( $json[1] ) && is_array( $json[1] ) ) {
		foreach ( $json[1] as $key => $func_name ) {

			$func_name = trim( $func_name );

			// Skip if not in whitelist
			if ( ! in_array( $func_name, $allowed_functions, true ) ) {
				continue;
			}

			if ( empty( $json[2][ $key ] ) ) {
				continue;
			}

			$var_json = trim( $json[2][ $key ] );

			// Prepare arguments for translation
			wplng_args_setup( $args );
			$args['parents'] = array( $func_name );

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

	return $js;  // Return the translated JavaScript
}
