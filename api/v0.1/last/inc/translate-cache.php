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