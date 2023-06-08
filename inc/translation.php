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
	);
	$the_query    = new WP_Query( $args );

	// The Loop
	while ( $the_query->have_posts() ) {

		$the_query->the_post();

		$translation = array();

		$meta = get_post_meta( get_the_ID() );

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

				// TODO : Check si pas trouvé, continuer la boucle while have post
			}
		}

		// get source
		if ( empty( $meta['wplng_translation_original'][0] ) ) {
			continue;
		}
		$translation['source'] = $meta['wplng_translation_original'][0];

		if ( empty( $meta['wplng_translation_sr'][0] ) ) {
			continue;
		}

		// var_dump($meta['wplng_translation_sr'][0]);

		$search_meta = json_decode( $meta['wplng_translation_sr'][0], true );

		foreach ( $search_meta as $key => $search ) {
			if ( empty( $search['search'] ) || empty( $search['replace'] ) ) {
				continue;
			}

			$translation['search']  = $search['search'];
			$translation['replace'] = $search['replace'];
			$translations[]         = $translation;
		}
	}

	wp_reset_postdata();

	return $translations;
}






function wplng_save_translation_new( $language_id, $original, $translation, $search, $replace ) {

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
				'translation' => $translation,
			);
		} else {
			$translation_meta[] = array(
				'language_id' => $target_language,
				'translation' => '[WPLNG_EMPTY]',
			);
		}
	}

	/**
	 * Make search / replace meta
	 */
	$escapers     = array( '\\', '/', '"', "\n", "\r", "\t", "\x08", "\x0c" );
	$replacements = array( '\\\\', '\\/', '\\"', "\\n", "\\r", "\\t", "\\f", "\\b" );
	$search       = str_replace( $escapers, $replacements, $search );

	$sr_meta[] = array(
		'search'  => $search,
		'replace' => $replace,
	);

	add_post_meta(
		$post_id,
		'wplng_translation_original',
		$original
	);

	add_post_meta(
		$post_id,
		'wplng_translation_sr',
		wp_json_encode( $sr_meta )
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



function wplng_update_translation( $post, $language_id, $translation, $search, $replace ) {

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
		update_post_meta( $post->ID, 'wplng_translation_original_language_id', $website_language );

		/**
		 * Make $translation_meta
		 */
		$translation_meta = array();
		foreach ( $languages_target as $key => $target_language ) {

			if ( $target_language === $language_id && $translation !== '[WPLNG_EMPTY]' ) {
				$translation_meta[] = array(
					'language_id' => $target_language,
					'translation' => '[WPLNG_EMPTY]',
				);
			} else {
				$translation_meta[] = array(
					'language_id' => $target_language,
					'translation' => $translation,
				);
			}
		}

		update_post_meta(
			$post->ID, 'wplng_translation_translations', wp_json_encode(
				$translation_meta,
				JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
			)
		);

	} else { // Translation is valid

		$sr_meta = ( empty( $meta['wplng_translation_sr'][0] ) )
		? array() :
		json_decode( $meta['wplng_translation_sr'][0], true );

		$sr_already_in = false;
		foreach ( $sr_meta as $key => $sr ) {
			if ( ! empty( $sr['search'] ) && $sr['search'] === $search ) {
				$sr_already_in = true;
				break;
			}
		}
		if ( ! $sr_already_in ) {
			$escapers     = array( '\\', '/', '"', "\n", "\r", "\t", "\x08", "\x0c" );
			$replacements = array( '\\\\', '\\/', '\\"', "\\n", "\\r", "\\t", "\\f", "\\b" );
			$search       = str_replace( $escapers, $replacements, $search );

			$sr_meta[] = array(
				'search'  => $search, //str_replace( '\\', '\\\\', preg_quote( $search ) ),
				'replace' => $replace,
			);
		}

		update_post_meta( $post->ID, 'wplng_translation_sr', wp_json_encode( $sr_meta ) );

		// Set or update new translation
		$translation_already_in = false;
		foreach ( $translation_meta as $key => $translation_e ) {
			if ( $translation_e['language_id'] == $language_id ) {
				$translation_already_in   = true;
				$translation_meta[ $key ] = array(
					'language_id' => $language_id,
					'translation' => $translation,
				);
				break;
			}
		}
		if ( ! $translation_already_in ) {
			$translation_meta[] = array(
				'language_id' => $language_id,
				'translation' => $translation,
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




function wplng_save_translation( $target_language_id, $original, $translation, $search, $replace ) {

	$saved_translation = wplng_get_saved_translation_from_original( $original );

	if ( empty( $saved_translation ) ) {

		// Create new translation post
		return wplng_save_translation_new(
			$target_language_id,
			$original,
			$translation,
			$search,
			$replace
		);
	} else {
		// Update the translation post
		return wplng_update_translation(
			$saved_translation,
			$target_language_id,
			$translation,
			$search,
			$replace
		);
	}

}
