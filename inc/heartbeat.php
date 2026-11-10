<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * HeartBeat:
 * - Slug automatic translation
 * - Clear bad translations and slugs
 *
 * @return void
 */
function wplng_ajax_heartbeat() {

	$last_beat = get_option( 'wplng_hb_last_update' );
	$now       = time();
	$counter   = 100;

	// Prevents frequent execution if the last heartbeat was within 10 minutes
	if ( ! empty( $last_beat )
		&& ( $last_beat + ( MINUTE_IN_SECONDS * 10 ) ) > $now
	) {
		wp_send_json_success();
		return;
	}

	// Update last heartbeat timestamp
	update_option( 'wplng_hb_last_update', $now );

	$counter = wplng_ajax_heartbeat_clear_bad_translations( $counter );
	$counter = wplng_ajax_heartbeat_clear_bad_slugs( $counter );

	wp_send_json_success();
}


/**
 * Clears invalid translations, translations with an incorrect source language,
 * and translations selected by the 'wplng_heartbeat_delete_translation' filter.
 *
 * This function iterates through all translation posts of type 'wplng_translation',
 * validates their metadata, and deletes those that are invalid, do not match the
 * current website language ID, or are selected by a filter. Deletions are limited
 * by the $counter parameter.
 *
 * @param int $counter The maximum number of translations to process and delete.
 * @return int The remaining counter after processing.
 */
function wplng_ajax_heartbeat_clear_bad_translations( $counter ) {

	if ( $counter <= 0 ) {
		return 0;
	}

	$language_website_id = wplng_get_language_website_id();

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

		$meta                    = get_post_meta( $id );
		$delete_this_translation = false;
		$message_for_debug       = '';

		if ( empty( $meta['wplng_translation_original'][0] )
			|| ! is_string( $meta['wplng_translation_original'][0] )
			|| trim( $meta['wplng_translation_original'][0] ) === ''
			|| empty( $meta['wplng_translation_translations'][0] )
			|| empty( $meta['wplng_translation_md5'][0] )
		) {
			// The translation metadata is invalid
			$delete_this_translation = true;
			$message_for_debug       = 'Invalid data';

		} elseif ( empty( $meta['wplng_translation_original_language_id'][0] )
			|| $meta['wplng_translation_original_language_id'][0] !== $language_website_id
		) {
			// The translation original language is empty or not the same as the website
			$delete_this_translation = true;
			$message_for_debug       = 'Incorrect original language';
		} elseif ( apply_filters( 'wplng_heartbeat_delete_translation', false, $id, $meta ) ) {
			// Allow filters to request deletion of an otherwise valid translation.
			$delete_this_translation = true;
			$message_for_debug       = 'Requested by filter';
		}

		if ( $delete_this_translation === true ) {
			--$counter;

			// Debug (if enabled)
			if ( true === WPLNG_DEBUG_BEAT ) {
				$debug = array(
					'title'      => 'wpLingua HeartBeat debug',
					'action'     => 'Delete translation - ' . $message_for_debug,
					'title_post' => get_the_title( $id ),
					'id'         => $id,
				);

				error_log( var_export( $debug, true ) );
			}

			// Permanently delete the translation
			wp_delete_post( $id, true );
		}
	}

	return $counter;
}


/**
 * Clears invalid slugs, slugs with an incorrect source language, and slugs selected
 * by the 'wplng_heartbeat_delete_slug' filter.
 *
 * This function iterates through all slug posts of type 'wplng_slug',
 * validates their metadata, and deletes those that are invalid, do not match the
 * current website language ID, or are selected by a filter. Deletions are limited
 * by the $counter parameter.
 *
 * @param int $counter The maximum number of slugs to process and delete.
 * @return int The remaining counter after processing.
 */
function wplng_ajax_heartbeat_clear_bad_slugs( $counter ) {

	if ( $counter <= 0 ) {
		return 0;
	}

	$language_website_id = wplng_get_language_website_id();

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

		$meta              = get_post_meta( $id );
		$delete_this_slug  = false;
		$message_for_debug = '';

		if ( empty( $meta['wplng_slug_original'][0] )
			|| ! is_string( $meta['wplng_slug_original'][0] )
			|| trim( $meta['wplng_slug_original'][0] ) === ''
			|| empty( $meta['wplng_slug_translations'][0] )
			|| empty( $meta['wplng_slug_md5'][0] )
		) {
			// The slug metadata is invalid
			$delete_this_slug  = true;
			$message_for_debug = 'Invalid data';

		} elseif ( empty( $meta['wplng_slug_original_language_id'][0] )
			|| $meta['wplng_slug_original_language_id'][0] !== $language_website_id
		) {
			// The slug original language is empty or not the same as the website
			$delete_this_slug  = true;
			$message_for_debug = 'Incorrect original language';
		} elseif ( apply_filters( 'wplng_heartbeat_delete_slug', false, $id, $meta ) ) {
			// Allow filters to request deletion of an otherwise valid slug.
			$delete_this_slug  = true;
			$message_for_debug = 'Requested by filter';
		}

		if ( $delete_this_slug === true ) {
			--$counter;

			// Debug (if enabled)
			if ( true === WPLNG_DEBUG_BEAT ) {
				$debug = array(
					'title'      => 'wpLingua HeartBeat debug',
					'action'     => 'Delete slug - ' . $message_for_debug,
					'title_post' => get_the_title( $id ),
					'id'         => $id,
				);

				error_log( var_export( $debug, true ) );
			}

			// Permanently delete the slug
			wp_delete_post( $id, true );
		}
	}

	return $counter;
}
