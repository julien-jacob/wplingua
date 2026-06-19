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
				'#' . $entry['source'] . '#iu',
				$text,
				$preg_match
			);

			$preg_match = $preg_match[0];

			foreach ( $preg_match as $key => $match ) {

				$upper = 'none';
				if ( $match === mb_strtoupper( $match ) ) {
					// Check uppercase
					$upper = 'all';
				} elseif ( $match === mb_strtoupper( mb_substr( $match, 0, 1 ) ) . mb_substr( $match, 1 ) ) {
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
				'#\[wplng_dictionary key="' . $key . '" upper="all"\].+\[\/wplng_dictionary\]#U',
				mb_strtoupper( $replacement ),
				$text
			);

			// Replacement for text when only the first letter is uppercase
			$text = preg_replace(
				'#\[wplng_dictionary key="' . $key . '" upper="first"\].+\[\/wplng_dictionary\]#U',
				mb_strtoupper( mb_substr( $replacement, 0, 1 ) ) . mb_substr( $replacement, 1 ),
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
