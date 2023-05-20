<?php

if ( ! defined( 'MACHIAVEL_API' ) ) {
	die;
}


function mcvapi_request_translate() {

	if ( empty( $_POST['source'] ) ) {
		mcvapi_error_die( 6 );
	}

	if ( empty( $_POST['target'] ) ) {
		mcvapi_error_die( 7 );
	}

	if ( empty( $_POST['text'] ) ) {
		mcvapi_error_die( 8 );
	}

	$translation = mcvapi_translate( $_POST['source'], $_POST['target'], $_POST['text'] );

	$response = array(
		'translation' => $translation,
	);

	echo json_encode($response);

}


function mcvapi_multiexplode( $delimiters, $string ) {
	$ready  = str_replace( $delimiters, $delimiters[0], $string );
	$launch = explode( $delimiters[0], $ready );
	return $launch;
}


function mcvapi_translate( $language_source, $language_target, $text ) {

	return $text;

	if ( strlen( $text ) < 80 ) {
		return mcvapi_translate_google( $language_source, $language_target, $text );
	}

	$strings = mcvapi_multiexplode( array( '!', '.', '?', ':' ), $text );

	foreach ( $strings as $key => $string ) {

		$string = trim( $string );

		if ( $string != '' ) {
			$strings[ $key ] = mcvapi_translate_google( $language_source, $language_target, $string );
		}
	}

	$translation = '';
	foreach ( $strings as $key => $string ) {
		$translation .= $string . ' ';
	}

	return $translation;
}




function mcvapi_translate_google( $language_source, $language_target, $text ) {

	return 'lock';

	$url = add_query_arg(
		array(
			'client' => 'gtx',
			'sl'     => $language_source,
			'tl'     => $language_target,
			'dt'     => 't',
			'q'      => urlencode( $text ),
		), 'https://translate.googleapis.com/translate_a/single'
	);

	// $url = urlencode($url);
	$x = wp_remote_get( $url );

	if ( ! empty( $x['body'] ) ) {
		$x = json_decode( $x['body'] );
	}

	if ( ! empty( $x[0][0][0] ) ) {
		$x = $x[0][0][0];
	} else {
		$x = 'ERROR';
	}

	return $x;
}

