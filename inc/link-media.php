<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Get links and medias entries
 *
 * @return array
 */
function wplng_link_media_get_entries() {

	global $wplng_link_media_entries;

	if ( null != $wplng_link_media_entries ) {
		return $wplng_link_media_entries;
	}

	$entries_clear = array();
	$entries_json  = get_option( 'wplng_link_media_entries' );
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

		$source_clear = esc_attr( $entry['source'] );
		$source_clear = str_replace(
			'[WPLNG_BACKSLASH]',
			'\\',
			$source_clear
		);

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
		 * Get and check the modde
		 */

		$mode_clear = 'exactly';

		if ( ! empty( $entry['mode'] )
			&& (
				'exactly' === $entry['mode']
				|| 'partially' === $entry['mode']
				|| 'regex' === $entry['mode']
			)
		) {
			$mode_clear = $entry['mode'];
		}

		/**
		 * Get and check the rules
		 */

		if ( ! isset( $entry['rules'] )
			|| ! is_array( $entry['rules'] )
		) {
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
			continue;
		}

		$entries_clear[] = array(
			'source' => $source_clear,
			'mode'   => $mode_clear,
			'rules'  => $rules_clear,
		);
	}

	/**
	 * Sort links and medias entries by sources length
	 */

	usort(
		$entries_clear,
		function ( $a, $b ) {
			return strlen( $b['source'] ) - strlen( $a['source'] );
		}
	);

	/**
	 * Apply wplng_link_media_entries filter
	 */

	$entries_clear = apply_filters(
		'wplng_link_media_entries',
		$entries_clear
	);

	$wplng_link_media_entries = $entries_clear;

	return $entries_clear;
}


/**
 * Get links and medias entries in JSON format
 *
 * @return string JSON
 */
function wplng_link_media_get_entries_json() {
	return wp_json_encode(
		wplng_link_media_get_entries(),
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	);
}


/**
 * Apply media rules to a given URL for a specific language.
 *
 * @param string $url The URL to which the rules will be applied.
 * @param string $language_id The target language ID for which rules should be applied.
 * @return string The URL after applying applicable media rules.
 */
function wplng_link_media_apply_rules( $url, $language_id ) {

	$entries       = wplng_link_media_get_entries();
	$url_processed = $url;

	foreach ( $entries as $entry ) {

		if ( ! isset( $entry['rules'][ $language_id ] ) ) {
			continue;
		}

		switch ( $entry['mode'] ) {
			case 'exactly':
				if ( $url === $entry['source'] ) {
					$url_processed = $entry['rules'][ $language_id ];
				}
				break;

			case 'partially':
				$url_processed = str_replace(
					$entry['source'],
					$entry['rules'][ $language_id ],
					$url
				);
				break;

			case 'regex':
				$url_processed = preg_replace(
					'#' . $entry['source'] . '#',
					$entry['rules'][ $language_id ],
					$url
				);
				break;
		}

		// Apply only one entry rule
		if ( $url_processed !== $url ) {
			break;
		}
	}

	return $url_processed;
}
