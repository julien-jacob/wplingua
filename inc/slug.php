<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_slug_original( $slug, $language_id, $slugs_translations = false ) {

	if ( ! wplng_text_is_translatable( $slug ) ) {
		return $slug;
	}

	if ( false === $slugs_translations ) {
		$slugs_translations = wplng_get_slugs();
	}

	foreach ( $slugs_translations as $slug_translations ) {

		if ( ! isset( $slug_translations['translations'][ $language_id ]['translation'] )
			|| $slug !== $slug_translations['translations'][ $language_id ]['translation']
		) {
			continue;
		}

		$slug = $slug_translations['source'];
		break;
	}

	return $slug;
}


function wplng_slug_original_path( $path, $language_id ) {

	/**
	 * Remove language ID
	 */

	if ( wplng_str_starts_with( $path, '/' . $language_id . '/' ) ) {
		$path = str_replace( '/' . $language_id . '/', '/', $path );
	} elseif ( wplng_str_starts_with( $path, $language_id . '/' ) ) {
		$path = str_replace( $language_id . '/', '/', $path );
	}

	/**
	 * Return parth if no contains slug
	 */

	if ( '/' === $path || '' === $path ) {
		return $path;
	}

	/**
	 * Transform the multi part slug to an slug array
	 */

	$slugs = explode( '/', $path );
	$slugs = array_filter( $slugs );

	/**
	 * Get the slug translation list and translate $path
	 */

	$slugs_translations = wplng_get_slugs_from_query();

	$path_original = '/';

	foreach ( $slugs as $slug ) {
		$path_original .= wplng_slug_original(
			$slug,
			$language_id,
			$slugs_translations
		) . '/';

	}

	/**
	 * Set the first and last '/' to $path_original like $path
	 */

	if ( ! wplng_str_starts_with( $path, '/' ) ) {
		$path_original = substr( $path_original, 1 );
	}

	if ( ! wplng_str_ends_with( $path, '/' ) ) {
		$path_original = substr( $path_original, 0, -1 );
	}

	return $path_original;

}


function wplng_slug_translate( $slug, $language_id, $slugs_translations = false ) {

	if ( ! wplng_text_is_translatable( $slug ) ) {
		return $slug;
	}

	if ( false === $slugs_translations ) {
		$slugs_translations = wplng_get_slugs();
	}

	$slug_translation_exist = false;

	foreach ( $slugs_translations as $key => $slug_translations ) {

		if ( $slug !== $slug_translations['source']
			|| ! isset( $slug_translations['translations'][ $language_id ] )
		) {
			continue;
		}

		$slug = $slug_translations['translations'][ $language_id ]['translation'];

		$slug_translation_exist = true;
		break;
	}

	/**
	 * Slug translation is not in slug cache
	 * Check if exist in DB
	 * 
	 * If not exist, create it
	 */

	if ( false === $slug_translation_exist
		&& false === wplng_get_slug_saved_from_original( $slug )
	) {
		wplng_create_slug( $slug );
	}

	return $slug;
}


function wplng_slug_translate_path( $path, $language_id ) {

	/**
	 * Remove language ID
	 */

	$target = wplng_get_languages_target_ids();

	foreach ( $target as $target_id ) {
		if ( wplng_str_starts_with( $path, '/' . $target_id . '/' ) ) {
			$path = str_replace( '/' . $target_id . '/', '/', $path );
		} elseif ( wplng_str_starts_with( $path, $target_id . '/' ) ) {
			$path = str_replace( $target_id . '/', '/', $path );
		}
	}

	/**
	 * Manage anchor ang $_GET parameters
	 */

	$path = str_replace(
		array( '#', '?' ),
		array( '/#', '/?' ),
		$path
	);

	/**
	 * Return parth if no contains slug
	 */

	if ( '/' === $path || '' === $path ) {
		return $path;
	}

	/**
	 * Transform the multi part slug to an slug array
	 */

	$slugs = explode( '/', $path );
	$slugs = array_filter( $slugs );

	/**
	 * Get the slug translation list and translate $path
	 */

	$slugs_translations = wplng_get_slugs();

	$path_translated = '/';

	foreach ( $slugs as $slug ) {
		if ( wplng_text_is_translatable( $slug )
			&& ! wplng_str_starts_with( $slug, '#' )
			&& ! wplng_str_starts_with( $slug, '?' )
		) {
			$path_translated .= wplng_slug_translate(
				$slug,
				$language_id,
				$slugs_translations
			) . '/';
		} else {
			$path_translated .= $slug . '/';
		}
	}

	/**
	 * Set the first and last '/' to $path_translated like $path
	 */

	if ( ! wplng_str_starts_with( $path, '/' ) ) {
		$path_translated = substr( $path_translated, 1 );
	}

	if ( ! wplng_str_ends_with( $path, '/' ) ) {
		$path_translated = substr( $path_translated, 0, -1 );
	}

	return $path_translated;

}




function wplng_create_slug( $slug ) {

	$slug = sanitize_title( $slug );

	if ( '' === $slug ) {
		return false;
	}

	/**
	 * Make the title
	 */

	$tite_max_length = 100;
	$title           = mb_substr( $slug, 0, $tite_max_length );
	$title           = sanitize_title( $slug );

	if ( strlen( $slug ) > $tite_max_length ) {
		$title = '/' . $title . __( '...', 'wplingua' );
	} else {
		$title = '/' . $title . '/';
	}

	/**
	 * Create the post and get this ID
	 */

	$new_post_id = wp_insert_post(
		array(
			'post_title'  => esc_html( $title ),
			'post_type'   => 'wplng_slug',
			'post_status' => 'publish',
			'post_name'   => sanitize_title(
				'wplingua-slug-' . md5( $title ) . '-' . $title
			),
		)
	);

	if ( is_wp_error( $new_post_id ) ) {
		return false;
	}

	/**
	 * Make $translation_meta
	 */

	$languages_target = wplng_get_languages_target_ids();
	$slug_meta        = array();

	foreach ( $languages_target as $target_language ) {
		$slug_meta[] = array(
			'language_id' => $target_language,
			'translation' => $slug,
			'status'      => 'generated',
		);
	}

	add_post_meta(
		$new_post_id,
		'wplng_slug_original',
		$slug
	);

	add_post_meta(
		$new_post_id,
		'wplng_slug_md5',
		md5( $slug )
	);

	add_post_meta(
		$new_post_id,
		'wplng_slug_original_language_id',
		wplng_get_language_website_id()
	);

	add_post_meta(
		$new_post_id,
		'wplng_slug_translations',
		wp_json_encode(
			$slug_meta,
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		)
	);

	wplng_clear_slugs_cache();

	return $new_post_id;
}


/**
 * Get all saved slugs from a wp_query
 *
 * @return array
 */
function wplng_get_slugs_from_query() {

	$slugs = array();
	$args  = array(
		'post_type'              => 'wplng_slug',
		'posts_per_page'         => -1,
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false,
		'cache_results'          => false,
	);

	$the_query = new WP_Query( $args );

	while ( $the_query->have_posts() ) {

		$the_query->the_post();

		$meta = get_post_meta( get_the_ID() );

		/**
		 * Check and clear source slug
		 */

		if ( ! isset( $meta['wplng_slug_original'][0] )
			|| ! is_string( $meta['wplng_slug_original'][0] )
		) {
			continue;
		}

		$source = esc_attr( $meta['wplng_slug_original'][0] );

		/**
		 * Check and clear slug translations, setup review array
		 */

		if ( empty( $meta['wplng_slug_translations'][0] ) ) {
			continue;
		}

		$translations_meta = json_decode(
			$meta['wplng_slug_translations'][0],
			true
		);

		$translations = array();
		$review       = array();

		foreach ( $translations_meta as $translation_meta ) {

			/**
			 * Language ID
			 */

			if ( empty( $translation_meta['language_id'] )
				|| ! wplng_is_valid_language_id( $translation_meta['language_id'] )
			) {
				continue;
			}

			$language_id = sanitize_key( $translation_meta['language_id'] );

			/**
			 * Status and Review
			 */

			$status = 'ungenerated';

			if ( isset( $translation_meta['status'] ) ) {
				if ( 'generated' === $translation_meta['status'] ) {
					$status = 'generated';
				} elseif ( is_int( $translation_meta['status'] ) ) {
					$status   = $translation_meta['status'];
					$review[] = $language_id;
				}
			}

			/**
			 * Slug translation
			 */

			if ( empty( $translation_meta['translation'] )
				|| ! is_string( $translation_meta['translation'] )
				|| $translation_meta['translation'] === '[WPLNG_EMPTY]'
				|| $translation_meta['translation'] === $source
			) {
				continue;
			}

			$translation = sanitize_title( $translation_meta['translation'] );

			/**
			 * Add slug translated to slug translations
			 */

			$translations[ $language_id ] = array(
				'language_id' => $language_id,
				'translation' => $translation,
				'status'      => $status,
			);
		}

		$slugs[] = array(
			'source'       => $source,
			'post_id'      => get_the_ID(),
			'review'       => $review,
			'translations' => $translations,
		);

	}

	wp_reset_postdata();

	set_transient(
		'wplng_cached_slugs',
		$slugs
	);

	return $slugs;
}


// TODO : Retourner les traduction de slug par langue ?
// TODO : Checker les data
// TODO : Retourner directement si ça vient de wplng_get_slugs_from_query() ?
function wplng_get_slugs() {

	$slugs_from_data = get_transient( 'wplng_cached_slugs' );

	if ( ! is_array( $slugs_from_data ) ) {
		$slugs_from_data = wplng_get_slugs_from_query();
	}

	$slugs = array();

	foreach ( $slugs_from_data as $slug ) {

		if ( empty( $slug['source'] )
			|| ! is_string( $slug['source'] )
			|| empty( $slug['post_id'] )
			// || ! is_array( $slug['translations'] )
			// || empty( $slug['translations'] )
			// || ! is_string( $slug['translations'][ $target_language_id ] )
			|| ! isset( $slug['review'] )
			|| ! is_array( $slug['review'] )
		) {
			continue;
		}

		$slugs[] = $slug;

	}

	return $slugs;
}


/**
 * Get slug data from original slug
 *
 * @param string $original
 * @return array Translation data
 */
function wplng_get_slug_saved_from_original( $original ) {

	$translation = false;

	$args = array(
		'post_type'      => 'wplng_slug',
		'posts_per_page' => -1,
		'meta_query'     => array(
			array(
				'key'     => 'wplng_slug_md5',
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

		if ( ! isset( $meta['wplng_slug_original'][0] )
			|| ! is_string( $meta['wplng_slug_original'][0] )
		) {
			continue;
		}

		if ( $meta['wplng_slug_original'][0] === $original ) {
			$translation = get_post();
			break;
		}
	}

	wp_reset_postdata();

	return $translation;
}


/**
 * Clear cached slugs
 *
 * @return void
 */
function wplng_clear_slugs_cache() {
	delete_transient( 'wplng_cached_slugs' );
}


/**
 * Clear cached translations if $post_id parametter is a slug
 *
 * @return void
 */
function wplng_clear_slugs_cache_trash_untrash( $post_id ) {

	if ( 'wplng_slug' !== get_post_type( $post_id ) ) {
		return;
	}

	wplng_clear_slugs_cache();
}
