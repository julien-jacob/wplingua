<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_translate_wp_mail( $args ) {

	$language_website = wplng_get_language_website_id();
	$language_current = wplng_get_language_current_id();

	if ( $language_website === $language_current
		|| empty( $args['message'] )
	) {
		return $args;
	}

	if ( strip_tags( $args['message'] ) === $args['message'] ) {
		/**
		 * If is text
		 */
		$args['message'] = wplng_translate( $args['message'] );
	} else {
		/**
		 * If is HTML
		 */
		$args['message'] = wplng_ob_callback_translate( $args['message'] );
	}

	if ( ! empty( $args['subject'] ) ) {
		$args['subject'] = wplng_translate( $args['subject'] );
	}

	return $args;
}
