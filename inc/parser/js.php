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
	$js    = trim( $js );

	// Check if $JS is empty or is wp emoji script
	if ( '' === trim( $js )
		|| wplng_str_contains( $js, '_wpemojiSettings' )
	) {
		return array();
	}

	// Get the first 'var', 'let' or 'window._' declaration
	preg_match_all(
		'#(var\s|let\s|window\._)(.*)\s?=\s?(\[.*\]|\{.*\});#Ui',
		$js,
		$json
	);

	if ( empty( $json[2] ) || ! is_array( $json[2] ) ) {
		return $texts;
	}

	foreach ( $json[2] as $key => $var_name ) {

		if ( empty( $var_name ) || empty( $json[3][ $key ] ) ) {
			continue;
		}

		$var_json = $json[3][ $key ];

		$texts = array_merge(
			$texts,
			wplng_parse_json(
				$var_json,
				array( $var_name )
			)
		);

	}

	return $texts;
}
