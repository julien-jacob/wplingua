<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * wpLingua Parser : Get texts in a JS script
 *
 * @param string $js
 * @return array Texts
 */
function wplng_parse_js( $js ) {

	$js = trim( $js );

	// Check if $JS is empty or is wp emoji script
	if ( '' === $js
		|| wplng_str_contains( $js, '_wpemojiSettings' )
	) {
		return array();
	}

	$texts = array_merge(
		wplng_parse_js_json_in_var( $js ),
		wplng_parse_js_json_encoded_as_url( $js ),
	);

	if ( wplng_str_is_script_i18n( $js ) ) {
		$texts = array_merge(
			$texts,
			wplng_parse_js_json_in_i18n_script( $js )
		);
	}

	return $texts;
}


/**
 * Parse JSON contained in 'var', 'let' or 'window._'
 *
 * @param string $js
 * @return array Texts
 */
function wplng_parse_js_json_in_var( $js ) {

	$texts = array();
	$json  = array();

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

			$var_json = trim( $json[3][ $key ] );

			$texts = array_merge(
				$texts,
				wplng_parse_json(
					$var_json,
					array( $var_name )
				)
			);

		}
	}

	return $texts;
}


/**
 * Parse JSON in i18n script
 *
 * You can use wplng_parse_js_json_in_i18n_script( $js )
 * to check if the string is a i18n script
 *
 * @param string $js
 * @return array Texts
 */
function wplng_parse_js_json_in_i18n_script( $js ) {

	$texts = array();
	$json  = array();

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

			$texts = array_merge(
				$texts,
				wplng_parse_json(
					$var_json,
					array( $var_name )
				)
			);

		}
	}

	return $texts;
}


/**
 * Parse JSON encoded as URL
 *
 * @param string $js
 * @return array Texts
 */
function wplng_parse_js_json_encoded_as_url( $js ) {

	$texts = array();
	$json  = array();

	/**
	 * URL encoded JSON
	 */

	preg_match_all(
		'#JSON\.parse\(\sdecodeURIComponent\(\s\'(.*)\'\s\)\s\)#Ui',
		$js,
		$json
	);

	if ( ! empty( $json[1] ) && is_array( $json[1] ) ) {

		foreach ( $json[1] as $key => $encoded_json ) {

			$var_json = urldecode( $encoded_json );

			$texts = array_merge(
				$texts,
				wplng_parse_json(
					$var_json,
					array( 'EncodedAsURL' )
				)
			);

		}
	}

	return $texts;
}
