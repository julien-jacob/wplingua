<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_args_setup_with_html( &$args, $html ) {

	wplng_args_setup( $args );

	if ( empty( $args['translations'] ) ) {

		$texts = wplng_parse_html( $html );

		$args['translations'] = wplng_get_translations_from_texts(
			$texts,
			$args
		);

	}

}




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
	 * Get mode (vanilla/editor/list)
	 */

	if ( isset( $args['mode'] )
		&& (
			'vanilla' === $args['mode']
			|| 'editor' === $args['mode']
			|| 'list' === $args['mode']
		)
	) {

		$args_clear['mode'] = $args['mode'];

	} else {

		$args_clear['mode'] = 'vanilla';

		if ( current_user_can( 'edit_posts' )
			&& ! empty( $_GET['wplng-mode'] )
		) {

			switch ( $_GET['wplng-mode'] ) {

				case 'editor':
					$args_clear['mode'] = 'editor';
					break;

				case 'list':
					$args_clear['mode'] = 'list';
					break;
			}
		}
	}

	/**
	 * Get load mode (enabled/start/progress/disabled)
	 */

	// if ( isset( $args['load'] )
	// 	&& (
	// 		'enabled' === $args['load']
	// 		|| 'disabled' === $args['load']
	// 		|| 'start' === $args['load']
	// 		|| 'progress' === $args['load']
	// 		|| 'loading' === $args['load']
	// 	)
	// ) {

	// 	$args_clear['load'] = $args['load'];

	// } else {

	// 	if ( ! empty( $_GET['wplng-load'] )
	// 		&& apply_filters( 'wplng_enale_in_progress_feature', false )
	// 		&& current_user_can( 'edit_posts' )
	// 	) {

	// 		if ( 'enabled' === $_GET['wplng-load']
	// 			|| 'start' === $_GET['wplng-load']
	// 			|| 'progress' === $_GET['wplng-load']
	// 		) {
	// 			$args_clear['load'] = $_GET['wplng-load'];
	// 		} else {
	// 			$args_clear['load'] = 'disabled';
	// 		}
	// 	} else {

	// 		$args_clear['load'] = 'disabled';

	// 	}
	// }

	$args_clear['load'] = 'disabled';

	/**
	 * Check current URL
	 */

	if ( empty( $args['url_current'] ) ) {
		$args_clear['url_current'] = remove_query_arg(
			array(
				'wplng-mode',
				'wplng-load',
				'wplng-time',
			),
			wplng_get_url_current()
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
				'wplng-time',
			),
			wplng_get_url_original()
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
function wplng_get_translations_from_texts( $texts, &$args ) {

	$translations = array();

	if ( empty( $texts ) ) {
		return $translations;
	}

	/**
	 * Update args
	 */

	wplng_args_setup( $args );

	/**
	 * Get saved translation
	 */

	$translations = wplng_get_translations_target( $args['language_target'] );

	/**
	 * Get unknow texts
	*/

	$texts_unknow = array();

	foreach ( $texts as $text ) {

		$is_in = false;

		foreach ( $translations as $translation ) {
			if ( $text === $translation['source'] ) {
				$is_in = true;
				break;
			}
		}

		if ( ! $is_in ) {
			$texts_unknow[] = $text;
		}
	}

	/**
	 * Define $max_translations
	 */

	$max_translations = WPLNG_MAX_TRANSLATIONS + 1;

	// if ( $args['load'] === 'start'
	// 	|| $args['load'] === 'progress'
	// 	|| (
	//      $args['load'] === 'enable'
	// 		&& count( $texts_unknow ) > 10
	// 	)
	// ) {

	// 	$max_translations = 0;

	// } elseif ( $args['load'] === 'loading' ) {

	// 	$max_translations = 20;

	// }

	$texts_unknow = array_splice(
		$texts_unknow,
		0,
		$max_translations
	);

	/**
	 * Get new translated text
	*/

	$texts_translated = wplng_api_call_translate(
		$texts_unknow,
		$args['language_source'],
		$args['language_target']
	);

	error_log(var_export($texts_translated, true));

	/**
	 * Save new translation as wplng_translation CPT
	*/

	$translations_new = array();

	foreach ( $texts_unknow as $key => $text_source ) {
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
	 * Separate page translations
	 */

	$translations_in_page = array();

	foreach ( $translations as $translation ) {
		foreach ( $texts as $text ) {
			$text = wplng_text_esc( $text );
			if ( ! empty( $translation['source'] )
				&& $translation['source'] === $text
			) {
				$translations_in_page[] = $translation;
				break;
			}
		}
	}

	/**
	 * Merge know and new translations
	 */

	$translations = array_merge(
		$translations_in_page,
		$translations_new
	);

	return $translations;
}
