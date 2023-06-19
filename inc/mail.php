<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_translate_wp_mail( $args ) {

	$language_website = wplng_get_language_website_id();
	$language_current = wplng_get_language_current_id();

	if ( $language_website == $language_website ) {
		return $args;
	}

	if ( empty( $args['message'] ) ) {
		return $args;
	}

	$translations = wplng_parser( $args['message'] );
	// error_log(var_export($translations, true));

	/**
	 * Replace original texts by translations
	 */
	foreach ( $translations as $translation ) {

		// Check if translaton data is valid
		if (
		! isset( $translation['source'] ) // Original text
		|| ! isset( $translation['translation'] ) // Translater text
		|| ! isset( $translation['sr'] ) // Search Replace
		) {
			continue;
		}

		if ( ! empty( $translation['source'] ) ) {

			foreach ( $translation['sr'] as $key => $sr ) {
				$regex = str_replace(
					'WPLNG',
					preg_quote( $translation['source'] ),
					$sr['search']
				);

				$replace = str_replace(
					'WPLNG',
					str_replace( '$', '&#36;', $translation['translation'] ),
					$sr['replace']
				);

				// Replace original text in HTML by translation
				$args['message'] = preg_replace( $regex, $replace, $args['message'] );
			}
		}
	}

	if ( ! empty( $args['subject'] ) ) {
		$args['subject'] = wplng_translate( $args['subject'] );
	}

	return $args;
}
