<?php

if ( ! defined( 'WPLINGUA_API' ) ) {
	die;
}


function wplngapi_request_parser() {

	if ( empty( $_POST['source'] ) ) {
		wplngapi_error_die( 6 );
	}

	if ( empty( $_POST['target'] ) ) {
		wplngapi_error_die( 7 );
	}

	if ( empty( $_POST['text'] ) ) {
		wplngapi_error_die( 8 );
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

	// foreach ( $dom->find( '.mcv-switcher' ) as $element ) {
	// 	$element->outertext = '';
	// }

	foreach ( $dom->find( 'style' ) as $element ) {
		$element->outertext = '';
	}

	foreach ( $dom->find( 'script' ) as $element ) {
		$element->outertext = '';
	}

	$dom->save();
	$html = str_get_html( $dom );

	// $translations       = array();
	// foreach ( $dom->find( 'text' ) as $element ) {
	// 	$s = trim( $element->innertext() );

	// 	if ( empty( $s ) ) {
	// 		continue;
	// 	}

	// 	// Check if text is already in $translations
	// 	$already_in = false;
	// 	foreach ( $translations as $key => $translation ) {
	// 		if ( $translation['source'] === $s ) {
	// 			$already_in = true;
	// 			break;
	// 		}
	// 	}
	// 	if ( $already_in ) {
	// 		continue;
	// 	}
	// 	// End Check if text is already in $translations

	// 	// $translation = wplngapi_translate( $_POST['source'], $_POST['target'], $s );

	// 	$translations[] = array(
	// 		'source'      => $s,
	// 		'translation' => '',
	// 		'search'      => '#>(\s*)MCV(\s*)<#Us',
	// 		'replace'     => '>$1MCV$2<',
	// 	);

	// }

	// Clear useless part for HTML parsing
	// $html = preg_replace( '#<!--.*-->#Uis', '', $html );
	// $html = preg_replace( '#<style.*<\/style>#Uis', '', $html );
	// $html = preg_replace( '#<script.*<\/script>#Uis', '', $html );
	// $html = preg_replace( '#<svg.*<\/svg>#Uis', '', $html );

	// $html = preg_replace( '#<svg.*<\/svg>#Uis', '', $html );
	// $html = str_replace( array( "\r", "\n", '  ', "\t" ), '', $html );

	$originals_text = array();
	preg_match_all( '#>\s*(.*)\s*<#Uis', $html, $originals_text );

	// TODO : Check
	$originals_text = $originals_text[0];

	foreach ( $originals_text as $key => $original_text ) {
		$s = array(
			'#>\s*(.*)\s*<#Uis',
		);
		$r = array(
			'$1',
		);

		$originals_text[ $key ] = preg_replace( $s, $r, $original_text );
		$originals_text[ $key ] = trim( $originals_text[ $key ] );
	}

	$originals_text = array_values( array_filter( $originals_text ) ); // Remove empty
	$originals_text = array_unique( $originals_text ); // Remove duplicate

	$translations = array();
	foreach ( $originals_text as $key => $original_text ) {

		$translations[] = array(
			'source'      => str_replace("\\", "\\\\",$original_text),
			'translation' => '',
			'search'      => '#>(\s*)WPLNG(\s*)<#Uis',
			'replace'     => '>$1WPLNG$2<',
		);

	}

	// return json_encode( $translations );

	// $start_time = microtime( true );

	$translated   = '';
	$to_translate = '';
	// $temp         = '';
	foreach ( $translations as $key => $translation ) {

		$text = '';
		if ( strlen( $translation['source'] ) >= 1600 ) {
			$text = substr( $translation['source'], 0, 1600 );
			// TODO : Gérer grands textes en plusieur requetes
		} else {
			$text = $translation['source'];
		}
		$text = '<p>' . $text . '</p>';

		$temp = $to_translate . $text;

		if ( strlen( $temp ) >= 1600 ) {

			// $yy .= $to_translate . "\n";
			$translated  .= wplngapi_translate( $_POST['source'], $_POST['target'], $to_translate );
			$to_translate = $text;
		} else {
			$to_translate = $temp;
		}
	}

	// $yy .= $to_translate . "\n";
	$translated .= wplngapi_translate( $_POST['source'], $_POST['target'], $to_translate );

	// $end_time       = microtime( true );
	// $execution_time = $end_time - $start_time;
	// $translated     .= ' -- Time : ' . $execution_time;

	$translated = substr( $translated, strlen( '<p>' ), strlen( $translated ) );
	$translated = substr( $translated, 0, strlen( $translated ) - strlen( '</p>' ) );

	$translated = explode( '</p><p>', $translated );

	foreach ( $translations as $key => $translation ) {
		if ( isset( $translated[ $key ] ) ) {
			$translations[ $key ]['translation'] = str_replace("\\", "\\\\", $translated[ $key ]);
		}
	}

	return json_encode( $translations );

}

