<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * wpLingua OB Callback function : On page editor
 *
 * @param string $html
 * @return string
 */
function wplng_ob_callback_editor( $html ) {

	$html = apply_filters( 'wplng_html_intercepted', $html );

	if ( empty( $html ) ) {
		return $html;
	}

	/**
	 * Replace excluded HTML part by tag
	 */

	$excluded_elements = array();
	$html              = wplng_html_set_exclude_tag(
		$html,
		$excluded_elements
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
	 * Get new translated text from API
	 */

	$texts_unknow_translated = wplng_api_call_translate(
		$texts_unknow,
		false,
		$language_target_id
	);

	$texts_unknow = array_splice(
		$texts_unknow,
		0,
		WPLNG_MAX_TRANSLATIONS + 1
	);

	/**
	 * Save new translation as wplng_translation CPT
	 */

	$translations_new = array();

	foreach ( $texts_unknow as $key => $text_source ) {
		if ( isset( $texts_unknow_translated[ $key ] ) ) {

			$translated = $texts_unknow_translated[ $key ];
			$translated = esc_html( $translated );

			$translations_new[] = array(
				'source'      => $text_source,
				'translation' => $translated,
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
			if ( isset( $translation['source'] )
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
	 * Replace text by edit link in body
	 */

	$dom = wplng_sdh_str_get_html( $html );

	if ( empty( $dom ) ) {
		return $html;
	}

	/**
	 * Replace <a> tags by <span>
	 */

	foreach ( $dom->find( 'a' ) as $element ) {

		$element->setAttribute( 'onclick', 'event.preventDefault()' );
		$class = 'wplingua-editor-link';

		if ( ! empty( $element->class ) ) {
			$class = sanitize_html_class( $class . ' ' . $element->class );

			$element->class = $class;
		} else {
			$element->setAttribute( 'class', $class );
		}
	}

	/**
	 * Add edit links on text
	 */

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

		foreach ( $translations as $translation ) {

			if ( ! isset( $translation['post_id'] )
				|| ! isset( $translation['translation'] )
			) {
				continue;
			}

			$translated = wplng_text_esc( $translation['translation'] );

			if ( $text !== $translated ) {
				continue;
			}

			$edit_link = '';
			if ( ! empty( $translation['post_id'] ) ) {
				$edit_link = get_edit_post_link( $translation['post_id'] );
			} else {
				continue;
			}

			$onclick = 'window.open("' . esc_url( $edit_link ) . '", "_blank");';

			$innertext  = '<span ';
			$innertext .= 'class="wplng-edit-link" ';
			$innertext .= 'onclick="' . esc_js( $onclick ) . '" ';
			$innertext .= 'title="' . esc_attr__( 'Edit this translation', 'wplingua' ) . '">';
			$innertext .= esc_html( $text );
			$innertext .= '</span>';

			$element->innertext = $innertext;

		}
	}

	$dom->save();
	$html = (string) wplng_sdh_str_get_html( $dom );

	/**
	 * Replace tag by saved excluded HTML part
	 */

	$html = wplng_html_replace_exclude_tag( $html, $excluded_elements );

	/**
	 * Apply a filter before return $html
	 */

	$html = apply_filters( 'wplng_html_editor', $html );

	return $html;
}
