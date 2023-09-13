<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}




/**
 * Return true if text is translatable
 * - Not a number
 * - Not a price
 * - Not a mail
 * - ...
 *
 * @param string $text
 * @return void
 */
function wplng_text_is_translatable( $text ) {

	// Check if it's a mail address
	if ( filter_var( $text, FILTER_VALIDATE_EMAIL ) ) {
		return false;
	}

	return ! empty(
		preg_replace(
			'#[^a-zA-Z]#',
				'',
			$text
		)
	);

}

function wplng_text_esc( $text ) {

	$text = trim( $text );
	$text = html_entity_decode( $text );
	$text = trim( $text );

	return $text;
}
