<?php

if ( ! defined( 'MACHIAVEL_API' ) ) {
	die;
}




function mcvapi_translate_from_cache( $language_source, $language_target, $text ) {
	$path = './translation-cache/' . $language_source . '-' . $language_target . '.json';
	$json = file_get_contents( $path );

	if ( false !== $json ) {
		$translations = json_decode( $json, true );
		foreach ( $translations as $key => $translation ) {
			if (
				! empty( $translation['original'] )
				&& $translation['original'] == $text
				&& ! empty( $translation['translation'] )
			) {
				return $translation['translation'];
			}
		}
	}

	return false;
}

function mcvapi_translate_add_cache( $language_source, $language_target, $text, $translation ) {

	$path = './translation-cache/' . $language_source . '-' . $language_target . '.json';
	$json = file_get_contents( $path );

	if ( false === $json ) {
		$translations = array();
	} else {
		$translations = json_decode( $json, true );
	}

    $already_cached = false;
	foreach ( $translations as $key => $translation_e ) {
		if (
			! empty( $translation_e['original'] )
			&& $translation_e['original'] == $text
			&& ! empty( $translation_e['translation'] )
		) {
			$already_cached = true;
            break;
		}
	}

    if (!$already_cached) {
        $translations[] = array(
            'original'    => $text,
            'translation' => $translation,
        );
    
        $json = json_encode( $translations );
    
        file_put_contents( $path, $json );
    }
	

}

function mcvapi_multiexplode( $delimiters, $string ) {
	$ready  = str_replace( $delimiters, $delimiters[0], $string );
	$launch = explode( $delimiters[0], $ready );
	return $launch;
}


function mcvapi_translate( $language_source, $language_target, $text ) {

	$translaton_from_cache = mcvapi_translate_from_cache( $language_source, $language_target, $text );

	if ( $translaton_from_cache !== false ) {
		return $translaton_from_cache;
	}

	// if ( strlen( $text ) < 80 ) {
	// 	return mcvapi_translate_google( $language_source, $language_target, $text );
	// }

	// $strings = mcvapi_multiexplode( array( '!', '.', '?', ':' ), $text );

	// foreach ( $strings as $key => $string ) {

	// 	$string = trim( $string );

	// 	if ( $string != '' ) {
	// 		$strings[ $key ] = mcvapi_translate_google( $language_source, $language_target, $string );
	// 	}
	// }

	// $translation = '';
	// foreach ( $strings as $key => $string ) {
	// 	$translation .= $string . ' ';
	// }

	$translation = strtoupper( $text );

    mcvapi_translate_add_cache(
        $language_source,
        $language_target,
        $text,
        $translation
    );

	return $translation;
}




function mcvapi_translate_google( $language_source, $language_target, $text ) {

	return 'lock';

	$url = http_build_query(
		array(
			'client' => 'gtx',
			'sl'     => $language_source,
			'tl'     => $language_target,
			'dt'     => 't',
			'q'      => urlencode( $text ),
		), 'https://translate.googleapis.com/translate_a/single'
	);

	// $url = urlencode($url);
	// $x = wp_remote_get( $url );


     $handle = curl_init($url);
     curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
     $x = curl_exec($handle);



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

