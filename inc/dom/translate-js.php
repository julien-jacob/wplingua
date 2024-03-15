<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_dom_translate_js( $dom, $args ) {

	foreach ( $dom->find( 'script' ) as $element ) {

		$translated_js = wplng_translate_js(
			$element->innertext,
			$args
		);

		$element->innertext = $translated_js;
	}

	return $dom;
}
