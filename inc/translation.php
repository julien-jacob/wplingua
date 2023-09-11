<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_get_saved_translation_from_original( $original ) {

	$returned  = false;
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
		$returned = get_post();
	}

	wp_reset_postdata();

	return $returned;
}


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

		foreach ( $translations_meta as $key => $translation_meta ) {

			if ( ! empty( $translation_meta['language_id'] )
				&& $translation_meta['language_id'] === $target_language_id
				&& ! empty( $translation_meta['translation'] )
				&& $translation_meta['translation'] !== '[WPLNG_EMPTY]'
			) {

				$translation['translation'] = $translation_meta['translation'];
				break;

			}
		}

		if ( empty( $translation['translation'] ) ) {
			continue;
		}

		// get source
		if ( empty( $meta['wplng_translation_original'][0] ) ) {
			continue;
		}
		$translation['source'] = $meta['wplng_translation_original'][0];

		if ( empty( $meta['wplng_translation_sr'][0] ) ) {
			continue;
		}

		$search_meta = json_decode( $meta['wplng_translation_sr'][0], true );

		if ( empty( $search_meta ) ) {
			continue;
		}

		$translation['sr'] = $search_meta;
		$translations[]    = $translation;
	}

	wp_reset_postdata();

	return $translations;
}


function wplng_save_translation_new( $language_id, $original, $translation, $sr ) {

	if ( false !== wplng_get_saved_translation_from_original( $original ) ) {
		return false;
	}

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

	foreach ( $languages_target as $key => $target_language ) {

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
		'wplng_translation_sr',
		str_replace(
			'\\',
			'\\\\',
			wp_json_encode(
				$sr,
				JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
			)
		)
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


function wplng_update_translation( $post, $language_id, $translation, $sr ) {

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

		$sr_meta = ( empty( $meta['wplng_translation_sr'][0] ) )
		? array() :
		json_decode( $meta['wplng_translation_sr'][0], true );

		$sr_meta = array_merge( $sr_meta, $sr );
		$sr_meta = array_unique( $sr_meta, SORT_REGULAR );

		update_post_meta(
			$post->ID,
			'wplng_translation_sr',
			str_replace(
				'\\', '\\\\', wp_json_encode(
					$sr_meta,
					JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
				)
			)
		);

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


function wplng_save_translations( $translations, $language_target_id ) {

	if ( empty( $translations ) || ! is_array( $translations ) ) {
		return array();
	}

	foreach ( $translations as $key => $translation ) {

		if (
			! isset( $translation['source'] ) // Original text
			|| ! isset( $translation['translation'] ) // Translater text
			|| ! isset( $translation['sr'] ) // Search Replace
		) {
			continue;
		}

		$translations[ $key ]['post_id'] = wplng_save_translation(
			$language_target_id,
			$translation['source'],
			$translation['translation'],
			$translation['sr']
		);
	}

	return $translations;
}


function wplng_save_translation( $target_language_id, $original, $translation, $sr ) {

	$saved_translation = wplng_get_saved_translation_from_original( $original );

	if ( empty( $saved_translation ) ) {
		// Create new translation post
		return wplng_save_translation_new(
			$target_language_id,
			$original,
			$translation,
			$sr
		);
	} else {
		// Update the translation post
		return wplng_update_translation(
			$saved_translation,
			$target_language_id,
			$translation,
			$sr
		);
	}

}
