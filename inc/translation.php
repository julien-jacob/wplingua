<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Get translation data from original text
 *
 * @param string $original
 * @return array Translation data
 */
function wplng_get_translation_saved_from_original( $original ) {

	$translation = false;
	$original    = wplng_text_esc( $original );

	$args      = array(
		'post_type'    => 'wplng_translation',
		'meta_key'     => 'wplng_translation_original',
		'meta_value'   => $original,
		'meta_compare' => '=',
	);
	$the_query = new WP_Query( $args );

	// The Loop
	if ( $the_query->have_posts() ) {
		$the_query->the_post();
		$translation = get_post();
	}

	wp_reset_postdata();

	return $translation;
}


/**
 * Get all data of saved translations
 *
 * @param string $target_language_id
 * @return void
 */
function wplng_get_translations_saved( $target_language_id ) {

	$translations = array();
	$args         = array(
		'post_type'      => 'wplng_translation',
		'posts_per_page' => -1,
		'order'          => 'ASC',
	);

	$the_query = new WP_Query( $args );

	while ( $the_query->have_posts() ) {

		$the_query->the_post();

		$translation = array();
		$meta        = get_post_meta( get_the_ID() );

		$translation['post_id'] = get_the_ID();

		// Get translation for current language target
		if ( empty( $meta['wplng_translation_translations'][0] ) ) {
			continue;
		}

		$translations_meta = json_decode(
			$meta['wplng_translation_translations'][0],
			true
		);

		foreach ( $translations_meta as $translation_meta ) {
			if ( ! empty( $translation_meta['language_id'] )
				&& $translation_meta['language_id'] === $target_language_id
				&& ! empty( $translation_meta['translation'] )
				&& $translation_meta['translation'] !== '[WPLNG_EMPTY]'
			) {
				$translation['translation'] = $translation_meta['translation'];
				break;
			}
		}

		if ( empty( $translation['translation'] )
			|| empty( $meta['wplng_translation_original'][0] )
		) {
			continue;
		}

		$translation['source'] = $meta['wplng_translation_original'][0];

		$translations[] = $translation;
	}

	wp_reset_postdata();

	return $translations;
}


/**
 * Save a translation
 *
 * @param string $language_id
 * @param string $original
 * @param array $translation
 * @return mixed Post ID or false
 */
function wplng_save_translation_new( $language_id, $original, $translation ) {

	/**
	 * Make the title
	 */
	$tite_max_length = 100;
	$title           = substr( $original, 0, $tite_max_length );
	if ( strlen( $original ) > $tite_max_length ) {
		$title .= __( '...', 'wplingua' );
	}

	/**
	 * Create the post and get this ID
	 */
	$post_id = wp_insert_post(
		array(
			'post_title'  => $title,
			'post_type'   => 'wplng_translation',
			'post_status' => 'publish',
		)
	);

	if ( is_wp_error( $post_id ) ) {
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
				'translation' => esc_html( esc_attr( $translation ) ),
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

	add_post_meta(
		$post_id,
		'wplng_translation_original',
		$original
	);

	add_post_meta(
		$post_id,
		'wplng_translation_original_language_id',
		wplng_get_language_website_id()
	);

	add_post_meta(
		$post_id,
		'wplng_translation_translations',
		wp_json_encode(
			$translation_meta,
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		)
	);

	return $post_id;
}


/**
 * Update a translation
 *
 * @param object $post
 * @param string $language_id
 * @param array $translation
 * @return int Post ID
 */
function wplng_update_translation( $post, $language_id, $translation ) {

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
		update_post_meta(
			$post->ID,
			'wplng_translation_original_language_id',
			$website_language
		);

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
					'translation' => esc_html( esc_attr( $translation ) ),
					'status'      => 'generated',
				);
			}
		}

		update_post_meta(
			$post->ID,
			'wplng_translation_translations',
			wp_json_encode(
				$translation_meta,
				JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
			)
		);

	} else { // Translation is valid

		// Set or update new translation
		$translation_already_in = false;

		foreach ( $translation_meta as $key => $translation_e ) {
			if ( $translation_e['language_id'] == $language_id ) {
				$translation_already_in   = true;
				$translation_meta[ $key ] = array(
					'language_id' => $language_id,
					'translation' => esc_html( esc_attr( $translation ) ),
					'status'      => 'generated',
				);
				break;
			}
		}

		if ( ! $translation_already_in ) {
			$translation_meta[] = array(
				'language_id' => $language_id,
				'translation' => esc_html( esc_attr( $translation ) ),
				'status'      => 'generated',
			);
		}

		update_post_meta(
			$post->ID,
			'wplng_translation_translations',
			wp_json_encode(
				$translation_meta,
				JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
			)
		);

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
			$translation['translation']
		);
	}

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
function wplng_save_translation( $target_language_id, $original, $translation ) {

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

}
