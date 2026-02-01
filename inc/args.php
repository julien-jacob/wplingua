<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Setup the arguments
 *
 * @param array $args
 * @return void
 */
function wplng_args_setup( &$args ) {

	$args_clear = array();

	/**
	 * Check language website ID
	 */

	if ( empty( $args['language_source'] )
		|| ! wplng_is_valid_language_id( $args['language_source'] )
	) {
		$args_clear['language_source'] = wplng_get_language_website_id();
	} else {
		$args_clear['language_source'] = $args['language_source'];
	}

	/**
	 * Check language website DIR
	 */

	if ( empty( $args['language_source_dir'] ) ) {

		$language = wplng_get_language_by_id( $args_clear['language_source'] );

		$args_clear['language_source_dir'] = $language['dir'];

	} else {
		$args_clear['language_source_dir'] = $args['language_source_dir'];
	}

	/**
	 * Check language target ID
	 */

	if ( empty( $args['language_target'] )
		|| ! wplng_is_valid_language_id( $args['language_target'] )
	) {
		$args_clear['language_target'] = wplng_get_language_current_id();
	} else {
		$args_clear['language_target'] = $args['language_target'];
	}

	/**
	 * Check language target DIR
	 */

	if ( empty( $args['language_target_dir'] ) ) {

		$language = wplng_get_language_by_id( $args_clear['language_target'] );

		$args_clear['language_target_dir'] = $language['dir'];

	} else {
		$args_clear['language_target_dir'] = $args['language_target_dir'];
	}

	/**
	 * Get count_texts
	 */

	if ( ! isset( $args['count_texts'] )
		|| ! is_int( $args['count_texts'] )
	) {
		$args_clear['count_texts'] = 0;
	} else {
		$args_clear['count_texts'] = $args['count_texts'];
	}

	if ( ! isset( $args['count_texts_unknow'] )
		|| ! is_int( $args['count_texts_unknow'] )
	) {
		$args_clear['count_texts_unknow'] = 0;
	} else {
		$args_clear['count_texts_unknow'] = $args['count_texts_unknow'];
	}

	/**
	 * Get overloaded
	 */

	$args_clear['overloaded'] = isset( $args['overloaded'] ) && ! empty( $args['overloaded'] );

	/**
	 * Get mode (vanilla/editor/list)
	 */

	if ( isset( $args['mode'] )
		&& (
			'editor' === $args['mode']
			|| 'list' === $args['mode']
		)
	) {

		$args_clear['mode'] = $args['mode'];

	} else {

		$args_clear['mode'] = 'vanilla';

	}

	/**
	 * Get load mode (enabled/progress/disabled)
	 */

	if ( isset( $args['load'] )
		&& (
			'enabled' === $args['load']
			|| 'progress' === $args['load']
			|| 'translated' === $args['load']
		)
	) {
		$args_clear['load'] = $args['load'];
	} else {
		$args_clear['load'] = 'disabled';
	}

	/**
	 * Check current URL
	 */

	if ( empty( $args['url_current'] ) ) {
		$args_clear['url_current'] = remove_query_arg(
			array(
				'wplng-mode',
				'wplng-load',
				'nocache',
			),
			wp_make_link_relative(
				wplng_get_url_current()
			)
		);
	} else {
		$args_clear['url_current'] = $args['url_current'];
	}

	/**
	 * Check translated URL
	 */

	if ( empty( $args['url_original'] ) ) {
		$args_clear['url_original'] = remove_query_arg(
			array(
				'wplng-mode',
				'wplng-load',
				'nocache',
			),
			wp_make_link_relative(
				wplng_get_url_original()
			)
		);
	} else {
		$args_clear['url_original'] = $args['url_original'];
	}

	/**
	 * Get parents (Use by JSON)
	 */

	if ( empty( $args['parents'] )
		|| ! is_array( $args['parents'] )
	) {
		$args_clear['parents'] = array();
	} else {
		$args_clear['parents'] = $args['parents'];
	}

	/**
	 * Check translations array
	 */

	if ( empty( $args['translations'] )
		|| ! is_array( $args['translations'] )
	) {
		$args_clear['translations'] = array();
	} else {
		$args_clear['translations'] = $args['translations'];
	}

	/**
	 * Check texts unknow array
	 */

	if ( empty( $args['texts_unknow'] )
		|| ! is_array( $args['texts_unknow'] )
	) {
		$args_clear['texts_unknow'] = array();
	} else {
		$args_clear['texts_unknow'] = $args['texts_unknow'];
	}

	$args = $args_clear;
}


/**
 * Return a translations array from a texts array
 * If text is unknow, call API and save it
 *
 * @param array $texts
 * @param array $args
 * @return void
 */
function wplng_args_update_from_texts( &$args, $texts ) {

	if ( empty( $texts ) ) {
		$args['translations'] = array();
		return;
	}

	/**
	 * Texts: Sanitize and clear duplicated
	 */

	foreach ( $texts as $key => $text ) {

		if ( ! is_string( $text ) ) {
			continue;
		}

		$text = wplng_text_esc( $text );

		if ( '' !== $text ) {
			$texts[ $key ] = $text;
		}
	}

	$texts = array_unique( $texts ); // Remove duplicate

	/**
	 * Update args
	 */

	wplng_args_setup( $args );

	/**
	 * Get all translations for all languages
	 */

	$translations_all_languages = get_transient( 'wplng_cached_translations' );

	if ( empty( $translations_all_languages )
		|| ! is_array( $translations_all_languages )
	) {
		$translations_all_languages = wplng_get_translations_from_query();
	}

	/**
	 * Get unknow texts & Separate page translations
	 */

	$texts_unknow         = array();
	$translations_in_page = array();

	foreach ( $texts as $text ) {

		$is_in     = false;
		$detection = wplng_api_feature_is_allow( 'detection' );

		$array_index  = (string) mb_substr( $text, 0, 1 );
		$array_index .= (string) strlen( $text );

		if ( ! empty( $translations_all_languages[ $array_index ] ) ) {
			foreach ( $translations_all_languages[ $array_index ] as $translation ) {

				// $source = $translation['source'];

				if ( $text === $translation['source']
					&& isset( $translation['translations'][ $args['language_target'] ] )
					&& is_string( $translation['translations'][ $args['language_target'] ] )
					&& isset( $translation['review'] )
					&& is_array( $translation['review'] )
					&& isset( $translation['post_id'] )
					&& is_int( $translation['post_id'] )
				) {

					$is_in = true;

					$review = in_array(
						$args['language_target'],
						$translation['review'],
						true
					);

					$translated_text = wplng_text_esc(
						$translation['translations'][ $args['language_target'] ]
					);

					$translations_in_page[] = array(
						'source'      => $text,
						'post_id'     => $translation['post_id'],
						'review'      => $review,
						'translation' => $translated_text,
					);

					break;
				}
			}
		}

		if ( ! $is_in && $detection ) {
			$texts_unknow[] = $text;
		}
	}

	$args['texts_unknow'] = $texts_unknow;

	/**
	 * Limit $texts_unknow for a total of 4200 char
	 */

	$current_length       = 0;
	$texts_unknow_limited = array();

	foreach ( $texts_unknow as $text ) {
		$text_length = strlen( $text );
		if ( $current_length + $text_length > WPLNG_MAX_TRANSLATIONS_CHAR ) {
			break;
		}
		$texts_unknow_limited[] = $text;
		$current_length        += $text_length + 8;
	}

	/**
	 * Get count_texts
	 */

	$args['count_texts']        = count( $texts );
	$args['count_texts_unknow'] = count( $texts_unknow );

	/**
	 * Check if the current page is overloaded
	 */

	$args['overloaded'] = wplng_get_api_overloaded() && ! empty( $args['count_texts_unknow'] );

	/**
	 * Define $max_translations
	 */

	$max_translations = WPLNG_MAX_TRANSLATIONS_STR + 1;

	if ( $args['load'] === 'translated' ) {

		/**
		 * This is the page reloaded after "Load in progress" was complete
		 */

		$max_translations = 0;

	} elseif ( $args['load'] === 'enabled'
		&& $args['count_texts_unknow'] > 10
		&& ! $args['overloaded']
		&& wplng_api_feature_is_allow( 'detection' )
		&& current_user_can( 'edit_posts' )
	) {

		/**
		 * Current page identified as requiring "in progress" mode
		 */

		$args['load']     = 'progress';
		$max_translations = 0;

	} else {
		$args['load'] = 'disabled';
	}

	if ( $args['load'] !== 'disabled' ) {

		wp_cache_flush();

		// Set HTTP no-cache header
		nocache_headers();

		// Disable cache for plugins
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}

		if ( function_exists( 'do_action' ) ) {
			do_action( 'litespeed_purge_all' );
		}
	}

	$texts_unknow_limited = array_splice(
		$texts_unknow_limited,
		0,
		$max_translations
	);

	/**
	 * Get new translated text
	 */

	$texts_translated = wplng_api_call_translate(
		$texts_unknow_limited,
		$args['language_source'],
		$args['language_target']
	);

	$texts_unknow = array_diff_assoc( $texts_unknow, $texts_unknow_limited );

	$args['texts_unknow'] = $texts_unknow;

	/**
	 * Save new translation as wplng_translation CPT
	 */

	$translations_new = array();

	foreach ( $texts_unknow_limited as $key => $text_source ) {
		if ( isset( $texts_translated[ $key ] ) ) {
			$translations_new[] = array(
				'source'      => $text_source,
				'translation' => $texts_translated[ $key ],
			);
		}
	}

	$translations_new = wplng_save_translations(
		$translations_new,
		$args['language_target']
	);

	/**
	 * Merge know and new translations
	 */

	$args['translations'] = array_merge(
		$translations_in_page,
		$translations_new
	);
}
