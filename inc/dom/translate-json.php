<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_dom_translate_json( $dom, $args ) {

	wplng_args_setup( $args );

	if ( 'loading' === $args['load'] ) {
		return $dom;
	}

	foreach ( $dom->find( 'script[type="application/ld+json"]' ) as $element ) {

		$translated_json = wplng_translate_json(
			$element->innertext,
			$args
		);

		$element->innertext = $translated_json;
	}

	return $dom;
}
