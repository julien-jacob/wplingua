<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * HeartBeat: Clear bad translations and slugs
 *
 * @return void
 */
function wplng_ajax_heartbeat() {

	$language_website_id = wplng_get_language_website_id();
	$last_beat           = get_option( 'wplng_hb_last_update' );
	$now                 = time();
	$counter             = 100;
	$deleted             = array();

	// Prevents frequent execution if the last heartbeat was within 10 minutes
	if ( ! empty( $last_beat )
		&& ( $last_beat + ( MINUTE_IN_SECONDS * 10 ) ) > $now
	) {
		wp_send_json_success();
		return;
	}

	// Update last heartbeat timestamp
	update_option( 'wplng_hb_last_update', $now );

	/**
	 * Check translations
	 */

	$translation_ids = get_posts(
		array(
			'post_type'              => 'wplng_translation',
			'posts_per_page'         => -1,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'cache_results'          => false,
			'fields'                 => 'ids',
		)
	);

	foreach ( $translation_ids as $id ) {

		if ( $counter <= 0 ) {
			break;
		}

		$meta = get_post_meta( $id );

		// Validate translation metadata
		if ( empty( $meta['wplng_translation_original'][0] )
			|| ! is_string( $meta['wplng_translation_original'][0] )
			|| trim( $meta['wplng_translation_original'][0] ) === ''
			|| empty( $meta['wplng_translation_translations'][0] )
			|| empty( $meta['wplng_translation_md5'][0] )
		) {

			--$counter;

			$deleted[] = array(
				'reason' => 'Delete translation - Invalid data',
				'title'  => get_the_title( $id ),
				'id'     => $id,
			);

			// Permanently delete the invalid translation
			wp_delete_post( $id, true );
			continue;
		}

		// Check language of the translation
		if ( empty( $meta['wplng_translation_original_language_id'][0] )
			|| $meta['wplng_translation_original_language_id'][0] !== $language_website_id
		) {

			--$counter;

			$deleted[] = array(
				'reason' => 'Delete translation - Incorrect original language',
				'title'  => get_the_title( $id ),
				'id'     => $id,
			);

			// Permanently delete the translation
			wp_delete_post( $id, true );
			continue;
		}
	}

	/**
	 * Check slugs
	 */

	$slug_ids = get_posts(
		array(
			'post_type'              => 'wplng_slug',
			'posts_per_page'         => -1,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'cache_results'          => false,
			'fields'                 => 'ids', // Retrieve only post IDs
		)
	);

	foreach ( $slug_ids as $id ) {

		if ( $counter <= 0 ) {
			break;
		}

		$meta = get_post_meta( $id );

		// Validate translation metadata
		if ( empty( $meta['wplng_slug_original'][0] )
			|| ! is_string( $meta['wplng_slug_original'][0] )
			|| trim( $meta['wplng_slug_original'][0] ) === ''
			|| empty( $meta['wplng_slug_translations'][0] )
			|| empty( $meta['wplng_slug_md5'][0] )
		) {

			--$counter;

			$deleted[] = array(
				'reason' => 'Delete slug - Invalid data',
				'title'  => get_the_title( $id ),
				'id'     => $id,
			);

			// Permanently delete the invalid slug
			wp_delete_post( $id, true );
			continue;
		}

		// Check language of the translation
		if ( empty( $meta['wplng_slug_original_language_id'][0] )
			|| $meta['wplng_slug_original_language_id'][0] !== $language_website_id
		) {

			--$counter;

			$deleted[] = array(
				'reason' => 'Delete slug - Incorrect original language',
				'title'  => get_the_title( $id ),
				'id'     => $id,
			);

			// Permanently delete the translation
			wp_delete_post( $id, true );
			continue;
		}
	}

	/**
	 * Debug logging (if enabled)
	 */

	if ( true === WPLNG_DEBUG_BEAT ) {
		$debug = array(
			'title'   => 'wpLingua HeartBeat debug',
			'time'    => date( 'Y-m-d H:i:s', $now ),
			'deleted' => $deleted,
		);

		error_log( var_export( $debug, true ) );
	}

	wp_send_json_success();
}
