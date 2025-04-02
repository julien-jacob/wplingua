<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Modify dom for the editor mode
 *
 * @param object $dom
 * @param array  $args
 * @return object
 */
function wplng_dom_mode_editor( $dom, $args ) {

	wplng_args_setup( $args );

	if ( 'editor' !== $args['mode']
		|| 'disabled' !== $args['load']
	) {
		return $dom;
	}

	/**
	 * Add body class : wplingua-list
	 */

	foreach ( $dom->find( 'body[class]' ) as $element ) {
		$element->class = $element->class . ' wplingua-editor';
	}

	/**
	 * Add editor assets
	 */

	$asset_url = add_query_arg(
		'ver',
		WPLNG_PLUGIN_VERSION,
		plugins_url() . '/wplingua/assets/css/editor.css'
	);

	$asset  = '<link';
	$asset .= ' rel="stylesheet"';
	$asset .= ' id="wplingua-editor-css"';
	$asset .= ' href="' . esc_url( $asset_url ) . '"';
	$asset .= ' type="text/css"';
	$asset .= '/>';

	foreach ( $dom->find( 'head' ) as $element ) {
		$element->innertext = $element->innertext . $asset;
	}

	/**
	 * Replace <a> tags by <span>
	 */

	foreach ( $dom->find( 'a' ) as $element ) {

		$element->setAttribute( 'onclick', 'event.preventDefault()' );
		$class = 'wplingua-editor-link';

		if ( ! empty( $element->class ) ) {
			$class = esc_attr( $class . ' ' . $element->class );

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

		foreach ( $args['translations'] as $translation ) {

			if ( ! isset( $translation['post_id'] )
				|| ! isset( $translation['source'] )
				|| ! isset( $translation['translation'] )
			) {
				continue;
			}

			$source = wplng_text_esc( $translation['source'] );

			if ( $text !== $source ) {
				continue;
			}

			$class = 'wplng-edit-link';

			if ( ! empty( $translation['review'] ) ) {
				$class .= ' wplng-is-review';
			}

			$innertext  = '<span';
			$innertext .= ' class="' . esc_attr( $class ) . '"';
			$innertext .= ' data-wplng-post="' . esc_attr( $translation['post_id'] ) . '"';
			$innertext .= ' title="' . esc_attr__( 'Edit this translation', 'wplingua' ) . '"';
			$innertext .= '>';
			$innertext .= esc_html( wplng_text_esc( $translation['translation'] ) );
			$innertext .= '</span>';

			$element->innertext = $innertext;

			break;
		}
	}

	/**
	 * Place the translation edit modale
	 */

	foreach ( $dom->find( 'body' ) as $body ) {
		$html             = wplng_translation_edit_modal_get_html();
		$body->innertext .= $html;
	}

	return $dom;
}
