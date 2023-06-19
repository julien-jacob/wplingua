<?php

if ( ! defined( 'WPLINGUA_API' ) ) {
	die;
}


function wplngapi_is_translatable_text( $text ) {

	$match = array(
		'#^(\*|\$|\.|\,|\-|\_)*$#',
		'#^([0-9]*(\.|\,|\-)?)*$#', // Numbers separate by ',', '.' or '-'
	);

	foreach ( $match as $key => $match_element ) {
		if ( preg_match( $match_element, $text ) ) {
			return false;
		}
	}

	return true;
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

	/**
	 * Get text node
	 */
	$originals_text = array();
	$sources        = array();
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

	$originals_text_temp = array();

	foreach ( $originals_text as $key => $text ) {
		if ( wplngapi_is_translatable_text( $text ) ) {
			$originals_text_temp[] = $text;
		}
	}

	$originals_text = $originals_text_temp;

	foreach ( $originals_text as $key => $original_text ) {

		$sources[] = array(
			'source'  => str_replace( '\\', '\\\\', $original_text ),
			'search'  => '#>(\s*)?WPLNG(\s*)?<#Uis',
			'replace' => '>${1}WPLNG${2}<',
		);

	}

	/**
	 * Get attribute ALT
	 */

	$attributes_to_translate = array(
		'alt',
		'title',
		// 'aria-label',
	);

	foreach ( $attributes_to_translate as $key => $attrubute ) {
		$originals_text = array();
		preg_match_all( '# ' . $attrubute . '=[\'|\"](.*)[\'|\"]#Uis', $html, $originals_text );

		// TODO : Check
		$originals_text = $originals_text[0];

		foreach ( $originals_text as $key => $original_text ) {
			$s = array(
				'# ' . $attrubute . '=[\'|\"](.*)[\'|\"]#Uis',
			);
			$r = array(
				'$1',
			);

			$originals_text[ $key ] = preg_replace( $s, $r, $original_text );
			$originals_text[ $key ] = trim( $originals_text[ $key ] );
		}

		$originals_text = array_values( array_filter( $originals_text ) ); // Remove empty
		$originals_text = array_unique( $originals_text ); // Remove duplicate

		// $sources = array();
		foreach ( $originals_text as $key => $original_text ) {

			$sources[] = array(
				'source'  => str_replace( '\\', '\\\\', $original_text ),
				'search'  => '# ' . $attrubute . "=('|\")(\s*?)WPLNG(\s*?)('|\")#Uis",
				'replace' => ' ' . $attrubute . '=${1}${2}WPLNG${3}${4}',
			);

		}
	}

	$translations_formated = array();

	foreach ( $sources as $key => $source ) {

		$already_in = false;
		foreach ( $translations_formated as $key => $translation ) {
			if ( $translation['source'] == $source['source'] ) {
				$translations_formated[ $key ]['sr'][] = array(
					'search'  => $source['search'],
					'replace' => $source['replace'],
				);
				$already_in                            = true;
			}
		}

		if ( ! $already_in ) {

			$translations_formated[] = array(
				'source'      => $source['source'],
				'translation' => '',
				'sr'          => array(
					array(
						'search'  => $source['search'],
						'replace' => $source['replace'],
					),
				),
			);
		}

		// $translations_formated[] = $translation_formated;

	}

	// return json_encode( $translations_formated );

	// $start_time = microtime( true );

	$translated   = '';
	$to_translate = '';
	// $temp         = '';
	foreach ( $translations_formated as $key => $translation ) {

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

			$translated  .= wplngapi_translate( 
				$_POST['source'], 
				$_POST['target'], 
				$to_translate 
			);

			$to_translate = $text;
			
		} else {
			$to_translate = $temp;
		}
	}

	
	$translated .= wplngapi_translate( 
		$_POST['source'], 
		$_POST['target'], 
		$to_translate 
	);

	$translated = substr( $translated, strlen( '<p>' ), strlen( $translated ) );
	$translated = substr( $translated, 0, strlen( $translated ) - strlen( '</p>' ) );

	$translated = explode( '</p><p>', $translated );

	foreach ( $translations_formated as $key => $translation ) {
		if ( isset( $translated[ $key ] ) ) {
			$translations_formated[ $key ]['translation'] = str_replace( '\\', '\\\\', $translated[ $key ] );
		}
	}

	return json_encode( $translations_formated );
}

