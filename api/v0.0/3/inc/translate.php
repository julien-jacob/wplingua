<?php

if ( ! defined( 'WPLINGUA_API' ) ) {
	die;
}



function wplngapi_translate( $language_source, $language_target, $text ) {

	$ch = curl_init();
	// curl_setopt( $ch, CURLOPT_URL, 'http://127.0.0.1:5000/translate' );
	curl_setopt( $ch, CURLOPT_URL, 'https://www.libretranslate.com/translate' );
	curl_setopt( $ch, CURLOPT_POST, 1 );
	curl_setopt(
		$ch,
		CURLOPT_POSTFIELDS,
		http_build_query(
			[
				'q'       => $text,
				'source'  => $language_source,
				'target'  => $language_target,
				'format'  => 'html',
				'api_key' => '576e1336-c1d7-4dc9-a8a4-05cd75185263',
			]
		)
	);

	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

	$server_output = json_decode( curl_exec( $ch ), true );
	
	curl_close( $ch );

	if ( ! isset( $server_output['translatedText'] ) ) {
		return '';
	}

	return $server_output['translatedText'];
}




























// function wplngapi_multiexplode( $delimiters, $string ) {
// 	$ready  = str_replace( $delimiters, $delimiters[0], $string );
// 	$launch = explode( $delimiters[0], $ready );
// 	return $launch;
// }




// function wplngapi_translate( $language_source, $language_target, $text ) {

// 	// $translaton_from_cache = wplngapi_translate_from_cache( $language_source, $language_target, $text );

// 	// if ( $translaton_from_cache !== false ) {
// 	// 	return $translaton_from_cache;
// 	// }

// 	// ---------------------

// 	// if ( strlen( $text ) < 80 ) {
// 	// 	return wplngapi_translate_google( $language_source, $language_target, $text );
// 	// }

// 	// $strings = wplngapi_multiexplode( array( '!', '.', '?', ':' ), $text );

// 	// foreach ( $strings as $key => $string ) {

// 	// 	$string = trim( $string );

// 	// 	if ( $string != '' ) {
// 	// 		$strings[ $key ] = wplngapi_translate_google( $language_source, $language_target, $string );
// 	// 	}
// 	// }

// 	// $translation = '';
// 	// foreach ( $strings as $key => $string ) {
// 	// 	$translation .= $string . ' ';
// 	// }

// 	// ---------------------

// 	// $translation = strtoupper( $text );

// 	// wplngapi_translate_add_cache(
// 	// 	$language_source,
// 	// 	$language_target,
// 	// 	$text,
// 	// 	$translation
// 	// );

// 	return $translation;
// }




// function wplngapi_translate_google( $language_source, $language_target, $text ) {

// 	return 'lock';

// 	$url = http_build_query(
// 		array(
// 			'client' => 'gtx',
// 			'sl'     => $language_source,
// 			'tl'     => $language_target,
// 			'dt'     => 't',
// 			'q'      => urlencode( $text ),
// 		), 'https://translate.googleapis.com/translate_a/single'
// 	);

// 	// $url = urlencode($url);
// 	// $x = wp_remote_get( $url );

// 	 $handle = curl_init( $url );
// 	 curl_setopt( $handle, CURLOPT_RETURNTRANSFER, true );
// 	 $x = curl_exec( $handle );

// 	if ( ! empty( $x['body'] ) ) {
// 		$x = json_decode( $x['body'] );
// 	}

// 	if ( ! empty( $x[0][0][0] ) ) {
// 		$x = $x[0][0][0];
// 	} else {
// 		$x = 'ERROR';
// 	}

// 	return $x;
// }
