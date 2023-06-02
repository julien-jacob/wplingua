<?php

if ( ! defined( 'MACHIAVEL_API' ) ) {
	die;
}




function file_post_contents( $url, $data, $username = null, $password = null ) {
	$postdata = http_build_query( $data );

	$opts = array(
		'http' => array(
			'method'  => 'POST',
			'header'  => 'Content-type: application/x-www-form-urlencoded',
			'content' => $postdata,
		),
	);

	if ( $username && $password ) {
		$opts['http']['header'] = ( 'Authorization: Basic ' . base64_encode( "$username:$password" ) );
	}

	$context = stream_context_create( $opts );
	return file_get_contents( $url, false, $context );
}


function mcvapi_translate( $language_source, $language_target, $text ) {
	$x = file_post_contents(
		'https://libretranslate.com/translate',
		array(
			'q'       => $text,
			'source'  => $language_source,
			'target'  => $language_target,
			'format'  => 'text',
			'api_key' => '576e1336-c1d7-4dc9-a8a4-05cd75185263',
		)
	);

	$x = json_decode( $x, true );

	if ( ! empty( $x['translatedText'] ) ) {
		return $x['translatedText'];
	}

	return false;
}




























// function mcvapi_multiexplode( $delimiters, $string ) {
// 	$ready  = str_replace( $delimiters, $delimiters[0], $string );
// 	$launch = explode( $delimiters[0], $ready );
// 	return $launch;
// }




// function mcvapi_translate( $language_source, $language_target, $text ) {

// 	// $translaton_from_cache = mcvapi_translate_from_cache( $language_source, $language_target, $text );

// 	// if ( $translaton_from_cache !== false ) {
// 	// 	return $translaton_from_cache;
// 	// }

// 	// ---------------------

// 	// if ( strlen( $text ) < 80 ) {
// 	// 	return mcvapi_translate_google( $language_source, $language_target, $text );
// 	// }

// 	// $strings = mcvapi_multiexplode( array( '!', '.', '?', ':' ), $text );

// 	// foreach ( $strings as $key => $string ) {

// 	// 	$string = trim( $string );

// 	// 	if ( $string != '' ) {
// 	// 		$strings[ $key ] = mcvapi_translate_google( $language_source, $language_target, $string );
// 	// 	}
// 	// }

// 	// $translation = '';
// 	// foreach ( $strings as $key => $string ) {
// 	// 	$translation .= $string . ' ';
// 	// }

// 	// ---------------------

// 	// $translation = strtoupper( $text );

// 	// mcvapi_translate_add_cache(
// 	// 	$language_source,
// 	// 	$language_target,
// 	// 	$text,
// 	// 	$translation
// 	// );

// 	return $translation;
// }




// function mcvapi_translate_google( $language_source, $language_target, $text ) {

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

