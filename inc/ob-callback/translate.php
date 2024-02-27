<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * wpLingua OB Callback function : Translate pages
 *
 * @param string $html
 * @return string
 */
function wplng_ob_callback_translate( $html ) {

	$html = apply_filters( 'wplng_html_intercepted', $html );

	if ( wplng_str_is_json( $html ) ) {
		$html = wplng_ob_callback_translate_json( $html );
	} elseif ( wplng_str_is_html( $html ) ) {
		$html = wplng_ob_callback_translate_html( $html );
	}

	$html = apply_filters( 'wplng_html_translated', $html );

	return $html;
}


/**
 * wpLingua OB Callback function : Translate JSON
 *
 * @param string $json
 * @return string
 */
function wplng_ob_callback_translate_json( $json ) {

	if ( empty( $json ) ) {
		return $json;
	}

	/**
	 * Get all texts in JSON
	 */

	$texts = wplng_parse_json( $json );

	/**
	 * Get saved translation
	 */

	$language_target_id = wplng_get_language_current_id();
	$translations       = array();

	if ( ! empty( $texts ) ) {
		$translations = wplng_get_translations_target( $language_target_id );
	}

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

	$texts_unknow = array_splice(
		$texts_unknow,
		0,
		WPLNG_MAX_TRANSLATIONS + 1
	);

	/**
	 * Get new translated text
	 */

	$texts_unknow = array();

	$texts_translated = wplng_api_call_translate(
		$texts_unknow,
		false,
		$language_target_id
	);

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
		$language_target_id
	);

	/**
	 * Merge know and new translations
	 */

	$translations = array_merge(
		$translations_new,
		$translations
	);

	/**
	 * Replace original texts by translations
	 * Translate links
	 * Replace locale ID in data
	 */

	$json = wplng_translate_json(
		$json,
		$translations
	);

	return $json;
}


/**
 * wpLingua OB Callback function : Translate HTML
 *
 * @param string $html
 * @return void
 */
function wplng_ob_callback_translate_html( $html ) {

	if ( empty( $html ) ) {
		return $html;
	}

	/**
	 * Replace excluded HTML part by tag
	 */

	$excluded = array();

	$html = wplng_html_set_exclude_tag(
		$html,
		$excluded
	);

	/**
	 * Get all texts in HTML
	 */

	$texts = wplng_parse_html( $html );

	/**
	 * Get saved translation
	 */

	$language_target_id = wplng_get_language_current_id();
	$translations       = array();

	if ( ! empty( $texts ) ) {
		$translations = wplng_get_translations_target( $language_target_id );
	}

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
	 * Manage "Translation in progress" feature
	 */

	$number_texts        = count( $texts );
	$number_untranslated = count( $texts_unknow );
	$number_translated   = $number_texts - $number_untranslated;
	$show_progress       = false;
	$max_translations    = WPLNG_MAX_TRANSLATIONS + 1;

	if ( current_user_can( 'edit_posts' )
		&& empty( $_GET['wplingua-load-all'] )
	) {

		if ( $number_untranslated > 10
			&& empty( $_GET['wplingua-preload'] )
		) {
			$max_translations = 0;
			$show_progress    = true;
		} elseif ( ! empty( $_GET['wplingua-preload'] ) ) {
			$max_translations = 20;
		}
	}

	/**
	 * Change $texts_unknow number of elements
	 */

	$texts_to_translate = array_splice(
		$texts_unknow,
		0,
		$max_translations
	);

	/**
	 * Get new translated text
	 */

	$texts_translated = wplng_api_call_translate(
		$texts_to_translate,
		false,
		$language_target_id
	);

	/**
	 * Save new translation as wplng_translation CPT
	 */

	$translations_new = array();

	foreach ( $texts_to_translate as $key => $text_source ) {
		if ( isset( $texts_translated[ $key ] ) ) {
			$translations_new[] = array(
				'source'      => $text_source,
				'translation' => $texts_translated[ $key ],
			);
		}
	}

	$translations_new = wplng_save_translations(
		$translations_new,
		$language_target_id
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

	/**
	 * Replace original texts by translations
	 */

	$html = wplng_translate_html(
		$html,
		false,
		$language_target_id,
		$translations
	);

	/**
	 * If page is show on "translation in progress" mode
	 */

	if ( true === $show_progress ) {

		/**
		 * Add effect on unknow texts
		 */

		$dom = wplng_sdh_str_get_html( $html );

		if ( empty( $dom ) ) {
			return $html;
		}

		$edit_link_excluded = wplng_data_excluded_editor_link();
		$node_text_excluded = wplng_data_excluded_node_text();

		foreach ( $dom->find( 'body text' ) as $element ) {

			if ( in_array( $element->parent->tag, $edit_link_excluded )
				|| in_array( $element->parent->tag, $node_text_excluded )
			) {
				continue;
			}

			$text = wplng_text_esc( $element->innertext );

			if ( ! wplng_text_is_translatable( $text ) ) {
				continue;
			}

			foreach ( $texts_unknow as $text_unknow ) {

				$source = wplng_text_esc( $text_unknow );

				if ( $text !== $source ) {
					continue;
				}

				$innertext  = '<span ';
				$innertext .= 'class="wplng-in-progress-text" ';
				$innertext .= 'title="' . esc_attr__( 'Translation in progress', 'wplingua' ) . '">';
				$innertext .= esc_html( $text );
				$innertext .= '</span>';

				$element->innertext = $innertext;

				break;
			}
		}

		$dom->save();
		$html = (string) wplng_sdh_str_get_html( $dom );

		/**
		 * Add HTML of progress bar message and hidden preloading iframe
		 */

		$js_in_progress = wplng_get_html_in_progress(
			$number_translated,
			$number_texts
		);

		$html = str_replace(
			'</body>',
			$js_in_progress . '</body>',
			$html
		);

	}

	/**
	 * Replace tag by saved excluded HTML part
	 */

	$html = wplng_html_replace_exclude_tag(
		$html,
		$excluded
	);

	return $html;
}



/**
 * Get HTML of progress bar message and hidden preloading iframe
 *
 * @param int $number_translated
 * @param int $number_texts
 * @return string HTML
 */
function wplng_get_html_in_progress( $number_translated, $number_texts ) {

	$percentage = (int) ( ( $number_translated / $number_texts ) * 100 );

	$url = add_query_arg(
		'wplingua-preload',
		'1',
		wplng_get_url_current()
	);

	$html = '<div id="wplng-in-progress-container">';

	$html .= '<div id="wplng-in-progress-message">';
	$html .= '<span class="dashicons dashicons-update wplng-spin"></span> ';
	$html .= esc_html__( 'Translation in progress', 'wplingua' );
	$html .= ' - ';
	$html .= esc_html( $percentage );
	$html .= ' %';
	$html .= '</div>'; // End #wplng-translation-in-progress

	$html .= '<div id="wplng-progress-bar">';
	$html .= '<div id="wplng-progress-bar-value" ';
	$html .= 'style="width: ' . esc_attr( $percentage ) . '%">';
	$html .= '</div>'; // End #wplng-progress-bar-value
	$html .= '</div>'; // End #wplng-progress-bar

	$html .= '</div>'; // End #wplng-in-progress-container

	$html .= '<iframe id="wplng-in-progress-iframe" ';
	$html .= 'src="' . esc_url( $url ) . '" ';
	$html .= 'style="display: none !important;">';
	$html .= '</iframe>'; // End #wplng-translation-in-progress

	return $html;
}
