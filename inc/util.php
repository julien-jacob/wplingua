<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_str_is_url( $str ) {

	if ( parse_url( $str, PHP_URL_SCHEME ) != '' ) {
		// URL has http/https/...
		return ! ( filter_var( $str, FILTER_VALIDATE_URL ) === false );
	} else {
		// PHP filter_var does not support relative urls, so we simulate a full URL
		// Feel free to replace wplingua.com with any other URL, it won't matter!
		return ! ( filter_var( 'https://wplingua.com/' . ltrim( $str, '/' ), FILTER_VALIDATE_URL ) === false );
	}

}


function wplng_text_is_translatable( $text ) {

	// Check if it's a mail address
	if ( filter_var( $text, FILTER_VALIDATE_EMAIL ) ) {
		return false;
	}

	return ! empty(
		preg_replace(
			'#[^\p{L}]#',
			'',
			$text
		)
	);

}

function wplng_text_esc( $text ) {

	$text = trim( $text );
	$text = html_entity_decode( $text );
	$text = preg_replace( '#\s+#', ' ', $text );

	return $text;
}


function wplng_str_is_html( $str ) {
	if ( $str != strip_tags( $str ) ) {
		// is HTML
		return true;
	}

	// not HTML
	return false;
}



function wplng_str_is_json( $str ) {
	return ( json_decode( $str ) == null ) ? false : true;
}
