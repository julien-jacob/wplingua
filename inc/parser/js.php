<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * wpLingua Parser : Get texts in an JS
 *
 * @param string $js
 * @return array Texts
 */
function wplng_parse_js( $js ) {

	$texts = array();
	$json  = array();

	if ( empty( trim( $js ) ) ) {
		return array();
	}

	preg_match_all(
		'#var\s(.*)\s?=\s?(\{.*\});#Ui',
		$js,
		$json
	);

	if ( ! empty( $json[1][0] ) && ! empty( $json[2][0] ) ) {

		$var_name = $json[1][0];
		$var_json = $json[2][0];

		$texts = wplng_parse_json(
			$var_json,
			array( $var_name )
		);

	}

	$texts = array_unique( $texts ); // Remove duplicate

	return $texts;
}
