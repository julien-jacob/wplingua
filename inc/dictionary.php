<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Get dictionary entries
 *
 * @return array
 */
function wplng_dictionary_get_entries() {

	$entries_clear = array();
	$entries_json  = get_option( 'wplng_dictionary_entries' );
	$entries       = json_decode( $entries_json, true );

	if ( empty( $entries ) || ! is_array( $entries ) ) {
		return array();
	}

	foreach ( $entries as $entry ) {

		/**
		 * Get and check the source
		 */

		if ( ! isset( $entry['source'] )
			|| ! is_string( $entry['source'] )
			|| strlen( $entry['source'] ) >= 256
		) {
			continue;
		}

		$source_clear = wplng_text_esc( $entry['source'] );
		$source_clear = str_replace( '⊕', '', $source_clear );
		$source_clear = str_replace( '⊖', '', $source_clear );
		$source_clear = preg_replace( '#\[wplng_dictionary.*\]#', '', $source_clear );
		$source_clear = preg_replace( '#\[\/wplng_dictionary\]#', '', $source_clear );

		/**
		 * Check if rule already exist
		 */

		$already_in = false;
		foreach ( $entries_clear as $entry_clear ) {
			if ( $source_clear === $entry_clear['source'] ) {
				$already_in = true;
				break;
			}
		}

		if ( $already_in ) {
			continue;
		}

		/**
		 * Get and check the rules
		 */

		if ( ! isset( $entry['rules'] )
			|| ! is_array( $entry['rules'] )
		) {
			/**
			 * Create the clear entries
			 */

			$entries_clear[] = array(
				'source' => $source_clear,
			);
			continue;
		}

		$rules_clear = array();

		foreach ( $entry['rules'] as $language_id => $rule ) {
			if ( ! wplng_is_valid_language_id( $language_id )
				|| ! is_string( $rule )
				|| '' === trim( $rule )
				|| $rule === $source_clear
				|| strlen( $rule ) >= 256
			) {
				continue;
			}

			$rules_clear[ $language_id ] = wplng_text_esc( $rule );
		}

		/**
		 * Create the clear entries
		 */

		if ( empty( $rules_clear ) ) {
			$entries_clear[] = array(
				'source' => $source_clear,
			);
		} else {
			$entries_clear[] = array(
				'source' => $source_clear,
				'rules'  => $rules_clear,
			);
		}
	}

	/**
	 * Sort dictionary entries by sources length
	 */

	usort(
		$entries_clear,
		function ( $a, $b ) {
			return strlen( $b['source'] ) - strlen( $a['source'] );
		}
	);

	/**
	 * Apply wplng_dictionary_entries filter
	 */

	$entries_clear = apply_filters(
		'wplng_dictionary_entries',
		$entries_clear
	);

	return $entries_clear;
}


/**
 * Get dictionary entries in JSON format
 *
 * @return string JSON
 */
function wplng_dictionary_get_entries_json() {
	return wp_json_encode(
		wplng_dictionary_get_entries(),
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	);
}


/**
 * Add dictionary tag on a texts list
 *
 * @param array $texts
 * @param array $dictionary_entries
 * @return array Texts tagged
 */
function wplng_dictionary_add_tags( $texts, $language_target_id, $dictionary_entries = false ) {

	if ( false === $dictionary_entries ) {
		$dictionary_entries = wplng_dictionary_get_entries();
	}

	/**
	 * Preg quote sources texts
	 */

	foreach ( $dictionary_entries as $entry_key => $entry ) {
		$dictionary_entries[ $entry_key ]['source'] = preg_quote( $entry['source'] );
	}

	foreach ( $texts as $text_key => $text ) {

		/**
		 * Get used dictionary entry in current text
		 */

		$entries_used = array();

		foreach ( $dictionary_entries as $entry_key => $entry ) {

			$has_rules = isset( $entry['rules'] )
				&& is_array( $entry['rules'] )
				&& ! empty( $entry['rules'] );

			if ( $has_rules && empty( $entry['rules'][ $language_target_id ] ) ) {
				continue;
			}

			$preg_match = array();

			preg_match_all(
				'#' . $entry['source'] . '#i',
				$text,
				$preg_match
			);

			$preg_match = $preg_match[0];

			foreach ( $preg_match as $key => $match ) {

				$upper = 'none';
				if ( $match === strtoupper( $match ) ) {
					// Check uppercase
					$upper = 'all';
				} elseif ( $match === ucfirst( $match ) ) {
					// Check capitalize
					$upper = 'first';
				}

				$entry_current           = $entry;
				$entry_current['source'] = $match;
				$entry_current['key']    = $entry_key;
				$entry_current['upper']  = $upper;
				$entries_used[]          = $entry_current;
			}
		}

		/**
		 * Remove dupicate in used entries
		 */

		$entries_used = array_map( 'serialize', $entries_used );
		$entries_used = array_unique( $entries_used );
		$entries_used = array_map( 'unserialize', $entries_used );

		/**
		 * Put tempory tag in current text
		 */

		foreach ( $entries_used as $entry_key => $entry_used ) {
			$text = preg_replace(
				'#\b' . $entry_used['source'] . '\b#',
				'⊕' . str_repeat( '⊖', $entry_key + 1 ) . '⊕',
				$text
			);
		}

		/**
		 * Repace tempory tag by final tag
		 */

		foreach ( $entries_used as $entry_key => $entry ) {

			$search = '⊕' . str_repeat( '⊖', $entry_key + 1 ) . '⊕';

			$replace  = '[wplng_dictionary ';
			$replace .= 'key="' . $entry['key'] . '" ';
			$replace .= 'upper="' . $entry['upper'] . '"]';
			$replace .= $entry['source'];
			$replace .= '[/wplng_dictionary]';

			$text = str_replace(
				$search,
				$replace,
				$text
			);

		}

		/**
		 * Set updated text in text array
		 */

		$texts[ $text_key ] = $text;
	}

	return $texts;
}


/**
 * Transform dictionary tags to translated text
 *
 * @param array $texts
 * @param array $dictionary_entries
 * @return array Texts untagged
 */
function wplng_dictionary_replace_tags( $texts, $language_target_id, $dictionary_entries = false ) {

	if ( false === $dictionary_entries ) {
		$dictionary_entries = wplng_dictionary_get_entries();
	}

	foreach ( $texts as $text_key => $text ) {
		foreach ( $dictionary_entries as $key => $entry ) {

			// For ruls "Do not translate
			$replacement = $entry['source'];

			// For specific rule by language
			if ( ! empty( $entry['rules'][ $language_target_id ] ) ) {
				$replacement = $entry['rules'][ $language_target_id ];
			}

			// Replacement for text in uppercase
			$text = preg_replace(
				'#\[wplng_dictionary key="' . $key . '" upper="all"\].+\[\/wplng_dictionary\]#U',
				strtoupper( $replacement ),
				$text
			);

			// Replacement for text when only the first letter is uppercase
			$text = preg_replace(
				'#\[wplng_dictionary key="' . $key . '" upper="first"\].+\[\/wplng_dictionary\]#U',
				ucfirst( $replacement ),
				$text
			);

			// Replacement for other case
			$text = preg_replace(
				'#\[wplng_dictionary key="' . $key . '" upper="none"\].+\[\/wplng_dictionary\]#U',
				$replacement,
				$text
			);

		}

		// Cleaning of any residual rules
		$text = preg_replace(
			'#\[wplng_dictionary key=".*" upper=".*"\](.+)\[\/wplng_dictionary\]#U',
			'${1}',
			$text
		);

		$texts[ $text_key ] = $text;
	}

	return $texts;
}


/**
 * Detect which dictionary sources changed between two serialized entry lists,
 * and return the language IDs affected by each change.
 *
 * @param array $old_entries Decoded old entries array.
 * @param array $new_entries Decoded new entries array.
 * @return array [ 'source_text' => [ 'lang_id', … ], … ]
 */
function wplng_dictionary_detect_changes( $old_entries, $new_entries ) {

	$old_by_source = array();
	foreach ( $old_entries as $entry ) {
		if ( ! empty( $entry['source'] ) ) {
			$old_by_source[ $entry['source'] ] = $entry;
		}
	}

	$new_by_source = array();
	foreach ( $new_entries as $entry ) {
		if ( ! empty( $entry['source'] ) ) {
			$new_by_source[ $entry['source'] ] = $entry;
		}
	}

	$all_sources = array_unique(
		array_merge(
			array_keys( $old_by_source ),
			array_keys( $new_by_source )
		)
	);

	$changes = array();

	foreach ( $all_sources as $source ) {
		$old_entry = isset( $old_by_source[ $source ] ) ? $old_by_source[ $source ] : null;
		$new_entry = isset( $new_by_source[ $source ] ) ? $new_by_source[ $source ] : null;

		if ( $old_entry === $new_entry ) {
			continue;
		}

		$affected_ids = wplng_dictionary_get_affected_language_ids( $old_entry, $new_entry );

		if ( ! empty( $affected_ids ) ) {
			$changes[ $source ] = $affected_ids;
		}
	}

	return $changes;
}


/**
 * Determine which target-language IDs are affected by a dictionary entry change.
 *
 * Rules:
 * – If either the old or new entry carries no language-specific rules
 *   (i.e. "never translate"), every target language is affected.
 * – Otherwise only the languages explicitly listed in old/new rules are affected.
 *
 * @param array|null $old_entry Previous entry (null = entry did not exist before).
 * @param array|null $new_entry New entry      (null = entry has been deleted).
 * @return array Language ID strings.
 */
function wplng_dictionary_get_affected_language_ids( $old_entry, $new_entry ) {

	$languages_target = wplng_get_languages_target();
	$all_language_ids = array_column( $languages_target, 'id' );

	// "Never translate" = no 'rules' key or empty rules → all languages affected.
	$old_is_global = null !== $old_entry
		&& ( ! isset( $old_entry['rules'] ) || empty( $old_entry['rules'] ) );
	$new_is_global = null !== $new_entry
		&& ( ! isset( $new_entry['rules'] ) || empty( $new_entry['rules'] ) );

	if ( $old_is_global || $new_is_global ) {
		return $all_language_ids;
	}

	$affected = array();

	if ( null !== $old_entry && ! empty( $old_entry['rules'] ) ) {
		$affected = array_merge( $affected, array_keys( $old_entry['rules'] ) );
	}

	if ( null !== $new_entry && ! empty( $new_entry['rules'] ) ) {
		$affected = array_merge( $affected, array_keys( $new_entry['rules'] ) );
	}

	return array_values( array_unique( $affected ) );
}



/**
 * For a single wplng_translation post, remove every non-reviewed translation
 * entry that belongs to one of the affected $language_ids.
 * If no entries remain the post is deleted entirely.
 *
 * A translation is considered "reviewed" when its status is an integer
 * (Unix timestamp set by the admin).
 *
 * @param int   $post_id
 * @param array $language_ids Target language IDs to invalidate.
 * @return void
 */
function wplng_dictionary_reset_translation_post( $post_id, $language_ids ) {

	$translations_json = get_post_meta( $post_id, 'wplng_translation_translations', true );
	$translations      = json_decode( $translations_json, true );

	if ( empty( $translations ) || ! is_array( $translations ) ) {
		return;
	}

	$kept = array();

	foreach ( $translations as $translation ) {

		if ( ! isset( $translation['language_id'] ) ) {
			$kept[] = $translation;
			continue;
		}

		$is_affected = in_array( $translation['language_id'], $language_ids, true );

		if ( $is_affected ) {
			// Keep only reviewed translations (integer Unix timestamp).
			if ( isset( $translation['status'] ) && is_int( $translation['status'] ) ) {
				$kept[] = $translation;
			}
			// Non-reviewed: drop the entry so it gets re-generated.
		} else {
			$kept[] = $translation;
		}
	}

	if ( empty( $kept ) ) {
		wp_delete_post( $post_id, true );
	} else {
		update_post_meta(
			$post_id,
			'wplng_translation_translations',
			wp_json_encode( $kept, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
		);
	}
}


/**
 * Count the translation posts that would be affected by a set of source changes.
 * Uses one COUNT query per changed source (may overcount if a post matches
 * multiple sources, but provides a fast, good-enough estimate for the UI).
 *
 * @param array $changes [ source => [lang_ids] ]
 * @return int
 */
function wplng_dictionary_count_affected( $changes ) {

	global $wpdb;

	if ( empty( $changes ) ) {
		return 0;
	}

	$website_lang = wplng_get_language_website_id();
	$total        = 0;

	foreach ( $changes as $source => $language_ids ) {

		$source_like = '%' . $wpdb->esc_like( $source ) . '%';

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$count = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(DISTINCT p.ID)
				FROM {$wpdb->posts} p
				INNER JOIN {$wpdb->postmeta} pm_orig ON p.ID = pm_orig.post_id
				INNER JOIN {$wpdb->postmeta} pm_lang ON p.ID = pm_lang.post_id
				WHERE p.post_type   = 'wplng_translation'
				AND   p.post_status = 'publish'
				AND   pm_orig.meta_key   = 'wplng_translation_original'
				AND   pm_orig.meta_value LIKE %s
				AND   pm_lang.meta_key   = 'wplng_translation_original_language_id'
				AND   pm_lang.meta_value = %s",
				$source_like,
				$website_lang
			)
		);
		// phpcs:enable

		$total += $count;
	}

	return $total;
}


/**
 * AJAX — Preview dictionary changes.
 *
 * Detects which sources changed between the old and new entries JSON,
 * counts the translation posts that would be affected, stores pending
 * state in a transient, and returns the count + a session token.
 *
 * @return void
 */
function wplng_ajax_dictionary_preview() {

	check_ajax_referer( 'wplng_dictionary_ajax', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Unauthorized', 'wplingua' ) ), 403 );
		return;
	}

	// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$old_json = isset( $_POST['old_entries'] ) ? wp_unslash( $_POST['old_entries'] ) : '';
	$new_json = isset( $_POST['new_entries'] ) ? wp_unslash( $_POST['new_entries'] ) : '';
	// phpcs:enable

	$old_entries = array();
	if ( ! empty( $old_json ) ) {
		$decoded = json_decode( $old_json, true );
		if ( is_array( $decoded ) ) {
			$old_entries = $decoded;
		}
	}

	$new_entries = array();
	if ( ! empty( $new_json ) ) {
		$decoded = json_decode( $new_json, true );
		if ( is_array( $decoded ) ) {
			$new_entries = $decoded;
		}
	}

	$changes = wplng_dictionary_detect_changes( $old_entries, $new_entries );
	$total   = wplng_dictionary_count_affected( $changes );

	$token        = wp_generate_password( 32, false );
	$sources_list = array_keys( $changes );

	set_transient(
		'wplng_dict_pending_' . $token,
		array(
			'changes'      => $changes,
			'new_entries'  => $new_json,
			'sources_list' => $sources_list,
			'src_idx'      => 0,
			'src_offset'   => 0,
			'total'        => $total,
			'processed'    => 0,
		),
		HOUR_IN_SECONDS
	);

	wp_send_json_success(
		array(
			'count' => $total,
			'token' => $token,
		)
	);
}


/**
 * AJAX — Apply one batch of translation resets, then save the dictionary
 * entries once all affected posts have been processed.
 *
 * Expected POST params: token (string).
 *
 * Returns JSON: { processed, total, done }.
 *
 * @return void
 */
function wplng_ajax_dictionary_apply_batch() {

	check_ajax_referer( 'wplng_dictionary_ajax', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Unauthorized', 'wplingua' ) ), 403 );
		return;
	}

	$token = isset( $_POST['token'] ) ? sanitize_text_field( wp_unslash( $_POST['token'] ) ) : '';

	if ( empty( $token ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid token', 'wplingua' ) ) );
		return;
	}

	$transient_key = 'wplng_dict_pending_' . $token;
	$pending       = get_transient( $transient_key );

	if ( false === $pending || ! is_array( $pending ) ) {
		wp_send_json_error( array( 'message' => __( 'Session expired, please try again.', 'wplingua' ) ) );
		return;
	}

	global $wpdb;

	$batch_size   = 50;
	$changes      = $pending['changes'];
	$sources_list = $pending['sources_list'];
	$src_idx      = (int) $pending['src_idx'];
	$src_offset   = (int) $pending['src_offset'];
	$total        = (int) $pending['total'];
	$processed    = (int) $pending['processed'];
	$website_lang = wplng_get_language_website_id();

	if ( empty( $changes ) || $src_idx >= count( $sources_list ) ) {

		// Nothing left to process — save and finish.
		delete_transient( $transient_key );
		update_option( 'wplng_dictionary_entries', $pending['new_entries'] );
		wplng_clear_translations_cache();

		wp_send_json_success(
			array(
				'processed' => $processed,
				'total'     => $total,
				'done'      => true,
			)
		);
		return;
	}

	$source       = $sources_list[ $src_idx ];
	$language_ids = $changes[ $source ];
	$source_like  = '%' . $wpdb->esc_like( $source ) . '%';

	// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
	$post_ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT p.ID
			FROM {$wpdb->posts} p
			INNER JOIN {$wpdb->postmeta} pm_orig ON p.ID = pm_orig.post_id
			INNER JOIN {$wpdb->postmeta} pm_lang ON p.ID = pm_lang.post_id
			WHERE p.post_type   = 'wplng_translation'
			AND   p.post_status = 'publish'
			AND   pm_orig.meta_key   = 'wplng_translation_original'
			AND   pm_orig.meta_value LIKE %s
			AND   pm_lang.meta_key   = 'wplng_translation_original_language_id'
			AND   pm_lang.meta_value = %s
			LIMIT %d OFFSET %d",
			$source_like,
			$website_lang,
			$batch_size,
			$src_offset
		)
	);
	// phpcs:enable

	$fetched = count( $post_ids );

	foreach ( $post_ids as $post_id ) {
		wplng_dictionary_reset_translation_post( (int) $post_id, $language_ids );
		++$processed;
	}

	if ( $fetched < $batch_size ) {
		++$src_idx;
		$src_offset = 0;
	} else {
		$src_offset += $batch_size;
	}

	$is_done = ( $src_idx >= count( $sources_list ) );

	if ( $is_done ) {
		delete_transient( $transient_key );
		update_option( 'wplng_dictionary_entries', $pending['new_entries'] );
		wplng_clear_translations_cache();
	} else {
		$pending['src_idx']    = $src_idx;
		$pending['src_offset'] = $src_offset;
		$pending['processed']  = $processed;
		set_transient( $transient_key, $pending, HOUR_IN_SECONDS );
	}

	wp_send_json_success(
		array(
			'processed' => $processed,
			'total'     => $total,
			'done'      => $is_done,
		)
	);
}

