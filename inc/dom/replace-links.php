<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Modify links in dom for translated pages
 *
 * @param object $dom
 * @param array  $args
 * @return object
 */
function wplng_dom_replace_links( $dom, $args ) {

	wplng_args_setup( $args );

	if ( 'loading' === $args['load'] ) {
		return $dom;
	}

	/**
	 * Translate and replace links
	 */

	$attr_url_to_translate = wplng_data_attr_url_to_translate();

	foreach ( $attr_url_to_translate as $attr ) {
		foreach ( $dom->find( $attr['selector'] ) as $element ) {

			if ( empty( $element->attr[ $attr['attr'] ] ) ) {
				continue;
			}

			$link = sanitize_url( $element->attr[ $attr['attr'] ] );

			$translated_url = wplng_url_translate(
				$link,
				$args['language_target']
			);

			$element->attr[ $attr['attr'] ] = esc_url( $translated_url );
		}
	}

	/**
	 * Apply "link & media" rules and replace links
	 */

	foreach ( $dom->find( 'img[srcset]' ) as $element ) {

		if ( empty( $element->attr['srcset'] ) ) {
			continue;
		}

		$link = esc_attr( $element->attr['srcset'] );

		$url_link_media_applied = wplng_link_media_apply_rules(
			$link,
			$args['language_target']
		);

		if ( $url_link_media_applied !== $link ) {
			$element->attr['srcset'] = esc_attr( $url_link_media_applied );
		}
	}

	return $dom;
}
