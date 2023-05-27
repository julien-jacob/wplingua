<?php

if ( ! defined( 'MACHIAVEL_API' ) ) {
	die;
}


function mcvapi_request_parser() {

	if ( empty( $_POST['source'] ) ) {
		mcvapi_error_die( 6 );
	}

	if ( empty( $_POST['target'] ) ) {
		mcvapi_error_die( 7 );
	}

	if ( empty( $_POST['text'] ) ) {
		mcvapi_error_die( 8 );
	}

	require_once './lib/simple_html_dom.php';

	$html = $_POST['text'];

	$dom = str_get_html( $html );

	if ( $dom === false ) {
		return $html;
	}

	foreach ( $dom->find( '#wpadminbar' ) as $element ) {
		$element->outertext = '';
	}

	foreach ( $dom->find( '.mcv-switcher' ) as $element ) {
		$element->outertext = '';
	}

	foreach ( $dom->find( 'style' ) as $element ) {
		$element->outertext = '';
	}

	foreach ( $dom->find( 'script' ) as $element ) {
		$element->outertext = '';
	}

	$dom->save();
	$dom = str_get_html( $dom );

	$translations = array();

	foreach ( $dom->find( 'text' ) as $element ) {
		$s = trim( $element->innertext() );

		if ( empty( $s ) ) {
			continue;
		}

		// Check if text is already in $translations
		$already_in = false;
		foreach ( $translations as $key => $translation ) {
			if ( $translation['source'] === $s ) {
				$already_in = true;
				break;
			}
		}
		if ($already_in) {
			continue;
		}
		// End Check if text is already in $translations

		$translation = mcvapi_translate( $_POST['source'], $_POST['target'], $s );

		// 'search'  => "/>\s*MCV\s*</Us",
		$translations[] = array(
			'source' => $s,
			'translation' => $translation,
			'search'  => "#>(\s*)MCV(\s*)<#Us",
			'replace'  => '>$1MCV$2<',
		);
	}

	return json_encode( $translations );

	foreach ( $strings as $key => $string ) {
		$strings[ $key ] = trim( $string );
	}

	$strings = array_filter( $strings ); // Remove empty
	$strings = array_unique( $strings ); // Remove duplicate

	// TODO: Si texte est un nombre, retirer

	// $response = array(
	// 	'ok' => 'okokok',
	// );

	return json_encode( $strings );

}
