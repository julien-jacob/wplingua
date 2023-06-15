<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



function wplng_ob_callback_translate( $html ) {

	$html = apply_filters( 'wplng_html_intercepted', $html );

	/**
	 * Get saved translation
	 */
	$language_target_id = wplng_get_language_current_id();
	$translations       = wplng_get_translations_saved( $language_target_id );
	// return '<pre >' . var_export( $translations, true ) . '</pre>';

	/**
	 * Get new translation from API
	 */
	$start_time       = microtime( true );
	$translations_new = wplng_parser( $html, $translations );

	// Calculate script execution time
	$end_time       = microtime( true );
	$execution_time = ( $end_time - $start_time );
	// return var_export( $translations_new, true ) . ' Execution time of script = ' . $execution_time . ' sec';

	/**
	 * Save new translation as wplng_translation CPT
	 */
	wplng_save_translations( $translations_new, $language_target_id );

	/**
	 * Merge know and new translations
	 */
	$translations = array_merge( $translations, $translations_new );
	// return var_export($translations, true);

	/**
	 * Replace excluded HTML part by tab
	 */
	$excluded_elements = array();
	$html              = wplng_html_set_exclude_tag( $html, $excluded_elements );
	// return '<pre >' . var_export( $excluded_elements, true ) . '</pre>';

	/**
	 * Translate links
	 */
	$html = wplng_html_translate_links( $html, $language_target_id );

	/**
	 * Replace original texts by translations
	 */
	foreach ( $translations as $translation ) {

		// Check if translaton data is valid
		if (
			! isset( $translation['source'] ) // Original text
			|| ! isset( $translation['translation'] ) // Translater text
			|| ! isset( $translation['sr'] ) // Search Replace
			|| ! isset( $translation['post_id'] ) // Search Replace
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
				// $html = preg_replace( $regex, $replace, $html );
				

				if (preg_match($regex, $html)) {

					// Replace original text in HTML by translation
					$html = preg_replace( $regex, $replace, $html );

					// return wplng_get_slug();
					
					// MANAGE CAT
					if ( ! term_exists( wplng_get_slug(), 'wprock_htmlentity_cat' ) ) {
						wp_insert_term(
							wp_make_link_relative(wplng_get_url_original()),
							'wplng_url',
							array(
								'description' => '',
								'slug'        => wplng_get_slug(),
							)
						);
					}
					wp_set_object_terms( 
						$translation['post_id'], 
						array( wplng_get_slug() ), 
						'wplng_url',
						true
					);

				}

			}
		}
	}



	/**
	 * Replace tag by saved excluded HTML part
	 */
	$html = wplng_html_replace_exclude_tag( $html, $excluded_elements );

	$html = apply_filters( 'wplng_html_translated', $html );

	return $html;
}
