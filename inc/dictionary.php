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
			|| mb_strlen( $entry['source'] ) >= 256
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
				|| mb_strlen( $rule ) >= 256
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
			return mb_strlen( $b['source'] ) - mb_strlen( $a['source'] );
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
		$dictionary_entries[ $entry_key ]['source'] = preg_quote( $entry['source'], '#' );
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
				'#' . $entry['source'] . '#iu',
				$text,
				$preg_match
			);

			$preg_match = $preg_match[0];

			foreach ( $preg_match as $key => $match ) {

				$upper = 'none';
				// Caseless scripts (CJK, digits…) must not trigger uppercase detection:
				// mb_strtoupper('テスト') === 'テスト' is true, which would incorrectly
				// mark every CJK match as 'all' and uppercase the replacement.
				$is_caseless = mb_strtolower( $match ) === mb_strtoupper( $match );
				if ( ! $is_caseless ) {
					if ( $match === mb_strtoupper( $match ) ) {
						// Check uppercase
						$upper = 'all';
					} elseif ( $match === mb_strtoupper( mb_substr( $match, 0, 1 ) ) . mb_substr( $match, 1 ) ) {
						// Check capitalize
						$upper = 'first';
					}
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
			// CJK scripts (Japanese, Chinese, etc.) have no word separators,
			// so word boundaries cannot be used. For all other scripts (including
			// Greek), (*UCP) makes \b Unicode-aware so letters are treated as \w.
			if ( preg_match( '#[\x{2E80}-\x{9FFF}\x{F900}-\x{FAFF}]#u', $entry_used['source'] ) ) {
				$boundary_pattern = '#' . $entry_used['source'] . '#u';
			} else {
				$boundary_pattern = '#(*UCP)\b' . $entry_used['source'] . '\b#u';
			}
			$text = preg_replace(
				$boundary_pattern,
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

		// Ensure spaces around dictionary tags when adjacent to letters from scripts
		// that use word spaces (Latin, Greek, Cyrillic, Arabic, Hebrew, Devanagari…).
		// This fixes CJK-source → alphabetic-target replacements where the translation
		// API may not add spaces around the preserved tag (e.g. Japanese → French).
		// CJK scripts (U+2E80–U+9FFF, U+F900–U+FAFF, U+AC00–U+D7AF) are intentionally
		// excluded: they don't use word spaces, so no space should be inserted there.
		$text = preg_replace(
			'/(?<=[A-Za-z\x{00C0}-\x{04FF}\x{0590}-\x{097F}\d])\[wplng_dictionary /u',
			' [wplng_dictionary ',
			$text
		);
		$text = preg_replace(
			'/\[\/wplng_dictionary\](?=[A-Za-z\x{00C0}-\x{04FF}\x{0590}-\x{097F}\d])/u',
			'[/wplng_dictionary] ',
			$text
		);

		foreach ( $dictionary_entries as $key => $entry ) {

			// For ruls "Do not translate
			$replacement = $entry['source'];

			// For specific rule by language
			if ( ! empty( $entry['rules'][ $language_target_id ] ) ) {
				$replacement = $entry['rules'][ $language_target_id ];
			}

			// Replacement for text in uppercase
			$text = preg_replace(
				'#\[wplng_dictionary key="' . $key . '" upper="all"\].+\[\/wplng_dictionary\]#Uu',
				mb_strtoupper( $replacement ),
				$text
			);

			// Replacement for text when only the first letter is uppercase
			$text = preg_replace(
				'#\[wplng_dictionary key="' . $key . '" upper="first"\].+\[\/wplng_dictionary\]#Uu',
				mb_strtoupper( mb_substr( $replacement, 0, 1 ) ) . mb_substr( $replacement, 1 ),
				$text
			);

			// Replacement for other case
			$text = preg_replace(
				'#\[wplng_dictionary key="' . $key . '" upper="none"\].+\[\/wplng_dictionary\]#Uu',
				$replacement,
				$text
			);

		}

		// Cleaning of any residual rules
		$text = preg_replace(
			'#\[wplng_dictionary key=".*" upper=".*"\](.+)\[\/wplng_dictionary\]#Uu',
			'${1}',
			$text
		);

		$texts[ $text_key ] = $text;
	}

	return $texts;
}


/**
 * Fired when the 'wplng_dictionary_entries' option is saved.
 *
 * Compares the old and new dictionary entries to determine which source texts
 * and target languages are affected by the change. Builds $update_todo, a list
 * of items whose cached translations need to be regenerated.
 *
 * Each item in $update_todo has the shape:
 *   [ 'source' => string, 'impacted_languages' => 'all' | string[] ]
 *
 * 'impacted_languages' is the string 'all' when the entry has no per-language
 * rules (i.e., it is a "never translate" rule that applies to every language).
 *
 * @param string $old_value The previous JSON value of the option.
 * @param string $new_value The new JSON value of the option.
 */
function wplng_on_dictionary_entries_updated( $old_value, $new_value ) {

	// Entries are stored as a JSON string in the WordPress options table.
	// Decode them; fall back to an empty array if the value is absent or invalid.
	$old_entries = json_decode( $old_value, true );
	$new_entries = json_decode( $new_value, true );

	if ( ! is_array( $old_entries ) ) {
		$old_entries = array();
	}

	if ( ! is_array( $new_entries ) ) {
		$new_entries = array();
	}

	// Re-index entries by their source text for O(1) lookup in the loops below.
	// The dictionary is stored as a plain list; keying by source avoids nested loops.
	$old_by_source = array();
	foreach ( $old_entries as $entry ) {
		if ( isset( $entry['source'] ) ) {
			$old_by_source[ $entry['source'] ] = $entry;
		}
	}

	$new_by_source = array();
	foreach ( $new_entries as $entry ) {
		if ( isset( $entry['source'] ) ) {
			$new_by_source[ $entry['source'] ] = $entry;
		}
	}

	// Will hold the list of source/language pairs whose translations need refreshing.
	$update_todo = array();

	/**
	 * Detect removed entries.
	 *
	 * When a rule is deleted, cached translations built with that rule may be
	 * outdated (e.g., a forced translation that should revert to the default
	 * AI output). All languages covered by the old rule must be refreshed.
	 */

	foreach ( $old_by_source as $source => $old_entry ) {

		// Skip entries that still exist in the new value.
		if ( isset( $new_by_source[ $source ] ) ) {
			continue;
		}

		$impacted_languages = array();

		if ( empty( $old_entry['rules'] ) ) {
			// No per-language rules → this was a "never translate" rule affecting all languages.
			$impacted_languages = 'all';
		} else {
			// Collect every language that had a rule for this source.
			foreach ( $old_entry['rules'] as $language_id => $rule ) {
				$impacted_languages[] = $language_id;
			}
		}

		$update_todo[] = array(
			'source'             => $source,
			'impacted_languages' => $impacted_languages,
		);
	}

	/**
	 * Detect added and modified entries
	 */

	foreach ( $new_by_source as $source => $new_entry ) {
		if ( ! isset( $old_by_source[ $source ] ) ) {

			/**
			 * Added.
			 *
			 * Existing translations were produced without this rule, so they may
			 * not yet reflect the new forced translation or exclusion.
			 */

			$impacted_languages = array();

			if ( empty( $new_entry['rules'] ) ) {
				// No per-language rules → "never translate" rule affecting all languages.
				$impacted_languages = 'all';
			} else {
				// Collect every language that has a rule for this new source.
				foreach ( $new_entry['rules'] as $language_id => $rule ) {
					$impacted_languages[] = $language_id;
				}
			}

			$update_todo[] = array(
				'source'             => $source,
				'impacted_languages' => $impacted_languages,
			);

		} elseif ( $new_entry != $old_by_source[ $source ] ) {

			/**
			 * Modified.
			 *
			 * Loose comparison (!=) is intentional: strict (!==) would treat arrays
			 * with the same key/value pairs but different key order as unequal,
			 * causing false positives when WordPress re-encodes the JSON.
			 */

			$old_entry          = $old_by_source[ $source ];
			$impacted_languages = array();

			if ( empty( $new_entry['rules'] ) || empty( $old_entry['rules'] )
				|| ! is_array( $new_entry['rules'] ) || ! is_array( $old_entry['rules'] )
			) {
				// One side has no per-language rules, meaning the entry was or became a
				// "never translate" rule — all languages are affected by the change.
				$impacted_languages = 'all';
			} else {
				// Find every language whose rule value changed, was added, or was removed.
				// array_diff_assoc() catches rules that changed value or were newly added.
				// array_diff_key()  catches rules that existed in the old entry but were removed.
				// Merging both and taking the keys gives the full list of impacted languages.
				$impacted_languages = array_keys(
					array_merge(
						array_diff_assoc( $new_entry['rules'], $old_entry['rules'] ),
						array_diff_key( $old_entry['rules'], $new_entry['rules'] ),
					)
				);
			}

			if ( ! empty( $impacted_languages ) ) {
				$update_todo[] = array(
					'source'             => $source,
					'impacted_languages' => $impacted_languages,
				);
			}
		}
	}

	if ( empty( $update_todo ) ) {
		return;
	}

	// Normalize: if impacted_languages lists every active target language,
	// collapse it to 'all' to avoid unnecessary per-language filtering later.
	$all_target_ids = wplng_get_languages_target_ids();
	foreach ( $update_todo as &$item ) {
		if ( is_array( $item['impacted_languages'] )
			&& ! array_diff( $all_target_ids, $item['impacted_languages'] )
		) {
			$item['impacted_languages'] = 'all';
		}
	}
	unset( $item );

	/**
	 * Get all translations
	 */

	$translations           = wplng_get_translations();
	$translations_to_update = array();

	// Foreach translations
	foreach ( $translations as $index_strings => $translations_chunk ) {
		foreach ( $translations_chunk as $key => $translation ) {

			// Foreach $update_todo
			foreach ( $update_todo as $todo ) {
				$source_todo        = mb_strtolower( $todo['source'] );
				$source_translation = mb_strtolower( $translation['source'] );

				// Use a simple substring match for CJK sources (no word boundaries in CJK text).
				// For all other scripts, require word boundaries to avoid false matches (e.g. "men" in "women").
				$is_cjk  = (bool) preg_match( '#[\x{2E80}-\x{9FFF}\x{F900}-\x{FAFF}]#u', $source_todo );
				$pattern = $is_cjk
					? '#' . preg_quote( $source_todo, '#' ) . '#iu'
					: '#(*UCP)\b' . preg_quote( $source_todo, '#' ) . '\b#iu';

				if ( ! wplng_str_contains( $source_translation, $source_todo )
					|| ! preg_match( $pattern, $source_translation )
				) {
					continue;
				}

				$impacted_languages = 'all';

				$translation_review = $translation['review'] ?? array();

				if ( $todo['impacted_languages'] !== 'all' ) {

					$impacted_languages = array();

					foreach ( $translation['translations'] as $language_id => $value ) {
						if ( in_array( $language_id, $todo['impacted_languages'], true )
							&& ! in_array( $language_id, $translation_review, true )
						) {
							$impacted_languages[] = $language_id;
						}
					}
				} else {

					// Even when the dictionary rule impacts every language, translations
					// that were manually reviewed must still be protected from being
					// flagged for automatic regeneration.
					$impacted_languages = array();

					foreach ( $translation['translations'] as $language_id => $value ) {
						if ( ! in_array( $language_id, $translation_review, true ) ) {
							$impacted_languages[] = $language_id;
						}
					}
				}

				// If impacted_languages covers every language that has been translated,
				// collapse it to 'all' to avoid unnecessary per-language filtering later.
				if ( is_array( $impacted_languages ) ) {
					$temp_translations = $translation['translations'];
					foreach ( $impacted_languages as $language_id ) {
						unset( $temp_translations[ $language_id ] );
					}
					if ( empty( $temp_translations ) ) {
						$impacted_languages = 'all';
					}
				}

				$translations_to_update[] = array(
					'source'             => $translation['source'],
					'post_id'            => $translation['post_id'],
					'impacted_languages' => $impacted_languages,
				);

			}
		}
	}

	// Clear $translations_to_update when no loanguage impacted
	foreach ( $translations_to_update as $key => $value ) {
		if ( empty( $value['impacted_languages'] ) ) {
			unset( $translations_to_update[ $key ] );
		}
	}

	// Deduplicate entries by post_id: the same translation can match several
	// dictionary rules, producing multiple entries for the same post. Merge
	// their impacted_languages (union), collapsing to 'all' if any entry is 'all'.
	$translations_to_update_by_post = array();

	foreach ( $translations_to_update as $value ) {

		$post_id = $value['post_id'];

		if ( ! isset( $translations_to_update_by_post[ $post_id ] ) ) {
			$translations_to_update_by_post[ $post_id ] = $value;
			continue;
		}

		$existing = $translations_to_update_by_post[ $post_id ];

		if ( 'all' === $existing['impacted_languages']
			|| 'all' === $value['impacted_languages']
		) {
			$translations_to_update_by_post[ $post_id ]['impacted_languages'] = 'all';
		} else {
			$translations_to_update_by_post[ $post_id ]['impacted_languages'] = array_values(
				array_unique(
					array_merge( $existing['impacted_languages'], $value['impacted_languages'] )
				)
			);
		}
	}

	$translations_to_update = array_values( $translations_to_update_by_post );

	if ( ! empty( $translations_to_update ) ) {
		// Store the list in a short-lived transient so it survives the redirect
		// that options.php performs after saving. The page function reads and
		// immediately deletes it so it is only displayed once.
		set_transient( 'wplng_dictionary_updated_data', $translations_to_update, 30 );
	}
}


/**
 * AJAX handler: process one translation update triggered by a dictionary rule change.
 *
 * Receives post_id and impacted_languages from the JS queue.
 * For now, echoes the received data back so the JS loop can be validated end-to-end.
 *
 * @return void
 */
function wplng_ajax_dictionary_update_translations() {

	// ------------------------------------------------------------------------
	// Get and check basic data
	// ------------------------------------------------------------------------

	/**
	 * Check the nonce to prevent unauthorized requests
	 */

	check_ajax_referer( 'wplng_dictionary_update_translations', 'nonce' );

	/**
	 * Check the user's permissions
	 */

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error(
			array(
				'error'   => true,
				'message' => __( 'Error [1]: The current user is not authorized to edit translations', 'wplingua' ),
			)
		);
		return;
	}

	/**
	 * Check the post ID parameter
	 */

	if ( empty( $_POST['post_id'] ) ) {
		wp_send_json_error(
			array(
				'error'   => true,
				'message' => __( 'Error [2]: Invalid post ID parameter', 'wplingua' ),
			)
		);
		return;
	}

	$post_id = intval( $_POST['post_id'] );

	/**
	 * Check "impacted_languages" parameter
	 */

	if ( empty( $_POST['impacted_languages'] )
		|| ! is_string( $_POST['impacted_languages'] )
	) {
		wp_send_json_error(
			array(
				'error'   => true,
				'message' => __( 'Error [3]: Invalid impacted languages parameter', 'wplingua' ),
			)
		);
		return;
	}

	$impacted_languages = sanitize_text_field( wp_unslash( $_POST['impacted_languages'] ) );

	// Keep the raw (pre-decode) string: it is what was used to build the
	// "check" value server-side, and is needed below to verify it again.
	$impacted_languages_raw = $impacted_languages;

	if ( $impacted_languages !== 'all' ) {

		// We suppose it's a JSON with the list of impacted languages IDs
		$impacted_languages = json_decode( $impacted_languages, true );

		if ( ! is_array( $impacted_languages )
			|| ! wplng_is_valid_language_ids( $impacted_languages )
		) {
			wp_send_json_error(
				array(
					'error'   => true,
					'message' => __( 'Error [4]: Invalid impacted languages data', 'wplingua' ),
				)
			);
			return;
		}
	}

	/**
	 * Check the "check" parameter.
	 *
	 * It is an encrypted copy of "post_id-impacted_languages" generated when
	 * the HTML was rendered (see wplng_option_page_dictionary_update_translations_html()).
	 * Decrypting it and comparing it to the values actually received prevents
	 * the browser (or an attacker via devtools) from tampering with post_id
	 * or impacted_languages before the request is sent.
	 */

	if ( empty( $_POST['check'] ) || ! is_string( $_POST['check'] ) ) {
		wp_send_json_error(
			array(
				'error'   => true,
				'message' => __( 'Error [5]: Parameter "check" not found', 'wplingua' ),
			)
		);
		return;
	}

	$check_received = wplng_encryption_decrypt( sanitize_text_field( wp_unslash( $_POST['check'] ) ) );
	$check_expected = $post_id . '-' . $impacted_languages_raw;

	if ( '' === $check_received || ! hash_equals( $check_expected, $check_received ) ) {
		wp_send_json_error(
			array(
				'error'   => true,
				'message' => __( 'Error [6]: Invalid "check" parameter', 'wplingua' ),
			)
		);
		return;
	}

	/**
	 * Get and check the translation post
	 */

	$post_translation = get_post( $post_id, ARRAY_A );

	if ( ! isset( $post_translation['post_type'] )
		|| $post_translation['post_type'] !== 'wplng_translation'
	) {
		wp_send_json_error(
			array(
				'error'   => true,
				'message' => __( 'Error [7]: Invalid post ID or post type', 'wplingua' ),
			)
		);
		return;
	}

	// ------------------------------------------------------------------------
	// Apply the update to the relevant translation
	// ------------------------------------------------------------------------

	if ( $impacted_languages === 'all' ) {

		// ------------------------------------------------------------------------
		// Delete the post if all languages are impacted
		// ------------------------------------------------------------------------

		if ( empty( wp_delete_post( $post_id, true ) ) ) {
			wp_send_json_error(
				array(
					'error'   => true,
					'message' => __( 'Error [8]: Failed to delete the translation', 'wplingua' ),
				)
			);
			return;
		}

		wp_send_json_success(
			array(
				'post_id'            => $post_id,
				'impacted_languages' => $impacted_languages,
			)
		);
		return;

	} else {

		// ------------------------------------------------------------------------
		// Clear impacted translation in translation post
		// ------------------------------------------------------------------------

		/**
		 * Check post meta : All meta
		 */

		$meta = get_post_meta( $post_id );

		if ( ! isset( $meta['wplng_translation_translations'][0] )
			|| ! is_string( $meta['wplng_translation_translations'][0] )
		) {
			wp_send_json_error(
				array(
					'error'   => true,
					'message' => __( 'Error [9]: Translation meta "translations" not found', 'wplingua' ),
				)
			);
			return;
		}

		/**
		 * Check post meta : Translations meta
		 */

		$translation_meta = json_decode( $meta['wplng_translation_translations'][0], true );

		if ( empty( $translation_meta ) || ! is_array( $translation_meta ) ) {
			wp_send_json_error(
				array(
					'error'   => true,
					'message' => __( 'Error [10]: Invalid translation meta', 'wplingua' ),
				)
			);
			return;
		}

		/**
		 * Check post meta : Original language
		 */

		if ( empty( $meta['wplng_translation_original_language_id'][0] ) ) {
			wp_send_json_error(
				array(
					'error'   => true,
					'message' => __( 'Error [11]: Translation meta "original language" not found', 'wplingua' ),
				)
			);
			return;
		}

		$original_language_id_meta = $meta['wplng_translation_original_language_id'][0];

		if ( $original_language_id_meta !== wplng_get_language_website_id() ) {
			wp_send_json_error(
				array(
					'error'   => true,
					'message' => __( 'Error [12]: The language of the translation is not the same as the language of the site\'s settings', 'wplingua' ),
				)
			);
			return;
		}

		/**
		 * Generate the new translation meta
		 */

		$translation_meta_new = array();

		foreach ( $translation_meta as $key => $value ) {
			if ( ! isset( $value['language_id'] )
				|| ! wplng_is_valid_language_id( $value['language_id'] )
				|| ! isset( $value['translation'] )
				|| ! is_string( $value['translation'] )
			) {
				continue;
			}

			if ( ! in_array( $value['language_id'], $impacted_languages, true )
				|| ! isset( $value['status'] )
				|| $value['status'] !== 'generated'
			) {
				$translation_meta_new[] = $value;
			}
		}

		/**
		 * Update the translation meta
		 */

		if ( empty( $translation_meta_new ) ) {

			/**
			 * Delete the translation post if all translation is finaly impacted
			 */
			if ( empty( wp_delete_post( $post_id, true ) ) ) {
				wp_send_json_error(
					array(
						'error'   => true,
						'message' => __( 'Error [13]: Failed to delete the translation', 'wplingua' ),
					)
				);
				return;
			}
		} else {

			/**
			 * Update the translations post meta
			 */

			$meta_return = update_post_meta(
				$post_id,
				'wplng_translation_translations',
				wp_json_encode(
					$translation_meta_new,
					JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
				)
			);

			if ( false === $meta_return ) {
				wp_send_json_error(
					array(
						'error'   => true,
						'message' => __( 'Error [14]: Failed to update the translation meta', 'wplingua' ),
					)
				);
				return;
			}

			// Clear cache explicitly.
			wplng_clear_translations_cache();
		}
	}

	wp_send_json_success(
		array(
			'post_id'            => $post_id,
			'impacted_languages' => $impacted_languages,
		)
	);
}
