<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Get the translated text from translations array
 *
 * @param string $text
 * @param array $translations
 * @return string
 */
function wplng_get_translated_text_from_translations( $text, $translations ) {

	if ( empty( trim( $text ) ) ) {
		return $text;
	}

	// Manage non breaking space
	$text = str_replace(
		array( '&nbsp;', html_entity_decode( '&nbsp;' ) ),
		array( ' ', ' ' ),
		$text
	);

	/**
	 * Get spaces before and after text
	 */
	$temp          = array();
	$spaces_before = '';
	$spaces_after  = '';

	preg_match( '/^(\s*).*/', $text, $temp );
	if ( ! empty( $temp[1] ) ) {
		$spaces_before = $temp[1];
	}

	preg_match( '/.*(\s*)$/U', $text, $temp );
	if ( ! empty( $temp[1] ) ) {
		$spaces_after = $temp[1];
	}

	$text       = wplng_text_esc( $text );
	$translated = $text;

	if ( wplng_text_is_translatable( $text ) ) {
		foreach ( $translations as $translation ) {
			if ( $text === $translation['source'] ) {
				$translated = $translation['translation'];
				break;
			}
		}
	}

	return $spaces_before . $translated . $spaces_after;
}


/**
 * Get translation data from original text
 *
 * @param string $original
 * @return array Translation data
 */
function wplng_get_translation_saved_from_original( $original ) {

	$translation = false;

	$args = array(
		'post_type'      => 'wplng_translation',
		'posts_per_page' => -1,
		'meta_query'     => array(
			array(
				'key'     => 'wplng_translation_md5',
				'value'   => md5( $original ),
				'compare' => '=',
			),
		),
	);

	$the_query = new WP_Query( $args );

	// The Loop
	while ( $the_query->have_posts() ) {

		$the_query->the_post();

		$meta = get_post_meta( get_the_ID() );

		if ( ! isset( $meta['wplng_translation_original'][0] )
			|| ! is_string( $meta['wplng_translation_original'][0] )
		) {
			continue;
		}

		if ( $meta['wplng_translation_original'][0] === $original ) {
			$translation = get_post();
			break;
		}
	}

	wp_reset_postdata();

	return $translation;
}


/**
 * Get all saved translations from a wp_query for all languages
 *
 * @return array
 */
function wplng_get_translations_from_query() {

	$translations = array();
	$args         = array(
		'post_type'              => 'wplng_translation',
		'posts_per_page'         => -1,
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false,
		'cache_results'          => false,
	);

	$the_query = new WP_Query( $args );

	while ( $the_query->have_posts() ) {

		$the_query->the_post();

		$id   = get_the_ID();
		$meta = get_post_meta( $id );

		if ( ! isset( $meta['wplng_translation_original'][0] )
			|| ! is_string( $meta['wplng_translation_original'][0] )
			|| $meta['wplng_translation_original'][0] !== trim( $meta['wplng_translation_original'][0] )
		) {
			continue;
		}

		$source = wplng_text_esc( $meta['wplng_translation_original'][0] );

		$translation = array(
			'source'       => $source,
			'post_id'      => $id,
			'review'       => array(),
			'translations' => array(),
		);

		// Get translation for current language target
		if ( empty( $meta['wplng_translation_translations'][0] ) ) {
			continue;
		}

		$translations_meta = json_decode(
			$meta['wplng_translation_translations'][0],
			true
		);

		foreach ( $translations_meta as $translation_meta ) {

			if ( empty( $translation_meta['language_id'] )
				|| ! wplng_is_valid_language_id( $translation_meta['language_id'] )
				|| $translation_meta['translation'] === '[WPLNG_EMPTY]'
			) {
				continue;
			}

			$language_id = sanitize_key( $translation_meta['language_id'] );

			if ( isset( $translation_meta['translation'] )
				&& is_string( $translation_meta['translation'] )
			) {
				$translated_text = wplng_text_esc( $translation_meta['translation'] );

				$translation['translations'][ $language_id ] = $translated_text;
			}

			if ( isset( $translation_meta['status'] ) && is_int( $translation_meta['status'] ) ) {
				$translation['review'][] = $language_id;
			}
		}

		if ( empty( $translation['translations'] ) ) {
			continue;
		}

		$translations[] = $translation;
	}

	wp_reset_postdata();

	set_transient(
		'wplng_cached_translations',
		$translations,
		MONTH_IN_SECONDS
	);

	return $translations;
}


/**
 * Get all saved translations for a target language
 *
 * @param string $target_language_id
 * @return array
 */
function wplng_get_translations_target( $target_language_id ) {

	$translations_all_languages = get_transient( 'wplng_cached_translations' );

	if ( empty( $translations_all_languages )
		|| ! is_array( $translations_all_languages )
	) {
		$translations_all_languages = wplng_get_translations_from_query();
	}

	$translations_target = array();

	foreach ( $translations_all_languages as $translation ) {
		if ( empty( $translation['source'] )
			|| ! is_string( $translation['source'] )
			|| empty( $translation['post_id'] )
			|| empty( $translation['translations'][ $target_language_id ] )
			|| ! is_string( $translation['translations'][ $target_language_id ] )
			|| ! isset( $translation['review'] )
			|| ! is_array( $translation['review'] )
		) {
			continue;
		}

		$review = in_array(
			$target_language_id,
			$translation['review'],
			true
		);

		$translation_text = wplng_text_esc(
			$translation['translations'][ $target_language_id ]
		);

		$translations_target[] = array(
			'source'      => wplng_text_esc( $translation['source'] ),
			'post_id'     => $translation['post_id'],
			'review'      => $review,
			'translation' => $translation_text,
		);
	}

	return $translations_target;
}


/**
 * Save a translation
 *
 * @param string $language_id
 * @param string $original
 * @param array $translation
 * @return int|false Post ID or false on failure
 */
function wplng_save_translation_new( $language_id, $original, $translation ) {

	$translation = str_replace( '\\', '', $translation );

	/**
	 * Make the title
	 */

	$tite_max_length = 100;
	$title           = mb_substr( $original, 0, $tite_max_length );
	$title           = wplng_text_esc_displayed( $title );
	$title           = wp_encode_emoji( $title );
	if ( strlen( $original ) > $tite_max_length ) {
		$title .= __( '...', 'wplingua' );
	}

	/**
	 * Create the post and get this ID
	 */

	$new_post_id = wp_insert_post(
		array(
			'post_title'  => esc_html( $title ),
			'post_type'   => 'wplng_translation',
			'post_status' => 'publish',
			'post_name'   => sanitize_title(
				'wplingua-translation-' . md5( $title ) . '-' . $title
			),
		)
	);

	if ( is_wp_error( $new_post_id ) || empty( $new_post_id ) ) {
		return false;
	}

	/**
	 * Make $translation_meta
	 */

	$languages_target = wplng_get_languages_target_ids();
	$translation_meta = array();

	foreach ( $languages_target as $target_language ) {

		if ( $target_language === $language_id ) {
			$translation_meta[] = array(
				'language_id' => $target_language,
				'translation' => esc_html( $translation ),
				'status'      => 'generated',
			);
		} else {
			$translation_meta[] = array(
				'language_id' => $target_language,
				'translation' => '[WPLNG_EMPTY]',
				'status'      => 'ungenerated',
			);
		}
	}

	/**
	 * Get the URL where the translation was discovered
	 */

	$discovery_url = wplng_get_url_original();
	$discovery_url = wp_make_link_relative( $discovery_url );
	$discovery_url = remove_query_arg(
		array( 'wplng-mode', 'wplng-load', 'wplng-nocache' ),
		$discovery_url
	);

	/**
	 * Save meta: Discovered URL
	 */

	$meta_return = add_post_meta(
		$new_post_id,
		'wplng_translation_discovery_url',
		$discovery_url
	);

	if ( false === $meta_return ) {
		wp_delete_post( $new_post_id, true );
		return false;
	}

	/**
	 * Save meta: original language ID
	 */

	$meta_return = add_post_meta(
		$new_post_id,
		'wplng_translation_original_language_id',
		wplng_get_language_website_id()
	);

	if ( false === $meta_return ) {
		wp_delete_post( $new_post_id, true );
		return false;
	}

	/**
	 * Save meta: original text MD5
	 */

	$meta_return = add_post_meta(
		$new_post_id,
		'wplng_translation_md5',
		md5( $original )
	);

	if ( false === $meta_return ) {
		wp_delete_post( $new_post_id, true );
		return false;
	}

	/**
	 * Save meta: original text
	 */

	$meta_return = add_post_meta(
		$new_post_id,
		'wplng_translation_original',
		$original
	);

	if ( false === $meta_return ) {

		// Try to save it with encoded emoji
		$original = wp_encode_emoji( $original );

		$meta_return = add_post_meta(
			$new_post_id,
			'wplng_translation_original',
			$original
		);

		if ( false === $meta_return ) {
			wp_delete_post( $new_post_id, true );
			return false;
		}
	}

	/**
	 * Save meta: Translation array as JSON
	 */

	$meta_return = add_post_meta(
		$new_post_id,
		'wplng_translation_translations',
		wp_json_encode(
			$translation_meta,
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		)
	);

	if ( false === $meta_return ) {

		// Try to save it with encoded emoji
		foreach ( $translation_meta as $key => $translation ) {

			if ( empty( $translation['translation'] )
				|| '[WPLNG_EMPTY]' === $translation['translation']
			) {
				continue;
			}

			$translation_meta[ $key ]['translation'] = wp_encode_emoji( $translation['translation'] );
		}

		$meta_return = add_post_meta(
			$new_post_id,
			'wplng_translation_translations',
			wp_json_encode(
				$translation_meta,
				JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
			)
		);

		if ( false === $meta_return ) {
			wp_delete_post( $new_post_id, true );
			return false;
		}
	}

	return $new_post_id;
}


/**
 * Update a translation
 *
 * @param object $post
 * @param string $language_id
 * @param array $translation
 * @return int|false Post ID, false on failure
 */
function wplng_update_translation( $post, $language_id, $translation ) {

	$translation      = str_replace( '\\', '', $translation );
	$meta             = get_post_meta( $post->ID );
	$languages_target = wplng_get_languages_target_ids();
	$website_language = wplng_get_language_website_id();

	$translation_meta = ( empty( $meta['wplng_translation_translations'][0] ) )
		? array() :
		json_decode( $meta['wplng_translation_translations'][0], true );

	$original_language_id_meta = ( empty( $meta['wplng_translation_original_language_id'][0] ) )
		? wplng_get_language_website_id()
		: $meta['wplng_translation_original_language_id'][0];

	// If translation found is not valid
	if ( empty( $translation_meta )
		|| $website_language !== $original_language_id_meta
	) {

		/**
		 * $original_language_id_meta must be the same as in option page
		 */

		$meta_return = update_post_meta(
			$post->ID,
			'wplng_translation_original_language_id',
			$website_language
		);

		if ( false === $meta_return ) {
			return false;
		}

		/**
		 * Make $translation_meta
		 */

		$translation_meta = array();
		foreach ( $languages_target as $key => $target_language ) {

			if ( $target_language === $language_id && $translation !== '[WPLNG_EMPTY]' ) {
				$translation_meta[] = array(
					'language_id' => $target_language,
					'translation' => '[WPLNG_EMPTY]',
					'status'      => 'ungenerated',
				);
			} else {
				$translation_meta[] = array(
					'language_id' => $target_language,
					'translation' => esc_html( $translation ),
					'status'      => 'generated',
				);
			}
		}
	} else { // Translation is valid

		// Set or update new translation
		$translation_already_in = false;

		foreach ( $translation_meta as $key => $translation_e ) {
			if ( $translation_e['language_id'] == $language_id ) {
				$translation_already_in   = true;
				$translation_meta[ $key ] = array(
					'language_id' => $language_id,
					'translation' => esc_html( $translation ),
					'status'      => 'generated',
				);
				break;
			}
		}

		if ( ! $translation_already_in ) {
			$translation_meta[] = array(
				'language_id' => $language_id,
				'translation' => esc_html( $translation ),
				'status'      => 'generated',
			);
		}
	}

	/**
	 * Update the translations post meta
	 */

	$meta_return = update_post_meta(
		$post->ID,
		'wplng_translation_translations',
		wp_json_encode(
			$translation_meta,
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		)
	);

	if ( false === $meta_return ) {

		// Try to save it with encoded emoji
		foreach ( $translation_meta as $key => $translation ) {

			if ( empty( $translation['translation'] )
				|| '[WPLNG_EMPTY]' === $translation['translation']
			) {
				continue;
			}

			$translation_meta[ $key ]['translation'] = wp_encode_emoji( $translation['translation'] );
		}

		$meta_return = update_post_meta(
			$post->ID,
			'wplng_translation_translations',
			wp_json_encode(
				$translation_meta,
				JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
			)
		);

		if ( false === $meta_return ) {
			return false;
		}
	}

	return $post->ID;
}


/**
 * Save a list of translations
 *
 * @param array $translations
 * @param string $language_target_id
 * @return array $translations with post IDs
 */
function wplng_save_translations( $translations, $language_target_id ) {

	if ( empty( $translations ) || ! is_array( $translations ) ) {
		return array();
	}

	foreach ( $translations as $key => $translation ) {

		if (
			! isset( $translation['source'] ) // Original text
			|| ! isset( $translation['translation'] ) // Translater text
		) {
			continue;
		}

		$translations[ $key ]['post_id'] = wplng_save_translation(
			$language_target_id,
			$translation['source'],
			$translation['translation'],
			false
		);
	}

	wplng_clear_translations_cache();

	return $translations;
}


/**
 * Save translation
 *
 * @param string $target_language_id
 * @param string $original
 * @param array $translation
 * @return array $translation with post ID
 */
function wplng_save_translation( $target_language_id, $original, $translation, $clear_cache = true ) {

	$saved_translation = wplng_get_translation_saved_from_original( $original );

	if ( empty( $saved_translation ) ) {
		// Create new translation post
		return wplng_save_translation_new(
			$target_language_id,
			$original,
			$translation
		);
	} else {
		// Update the translation post
		return wplng_update_translation(
			$saved_translation,
			$target_language_id,
			$translation
		);
	}

	if ( $clear_cache ) {
		wplng_clear_translations_cache();
	}

}


/**
 * Clear cached translations
 *
 * @return void
 */
function wplng_clear_translations_cache() {
	delete_transient( 'wplng_cached_translations' );
}


/**
 * Clear cached translations if $post_id parametter is a translation
 *
 * @return void
 */
function wplng_clear_translations_cache_trash_untrash( $post_id ) {

	if ( 'wplng_translation' !== get_post_type( $post_id ) ) {
		return;
	}

	wplng_clear_translations_cache();
}
