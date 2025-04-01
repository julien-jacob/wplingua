<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Add a link for edit translations on page and post list
 *
 * @param array   $actions An array of row action links (string)
 * @param WP_Post $post The post object
 * @return array
 */
function wplng_row_edit_translation_link( $actions, $post ) {

	/**
	 * Check the current page
	 */

	if ( 'post' !== $post->post_type && 'page' !== $post->post_type ) {
		return $actions;
	}

	/**
	 * Check if a target language is defined
	 */

	$languages_target_ids = wplng_get_languages_target_ids();

	if ( empty( $languages_target_ids[0] ) ) {
		return $actions;
	}

	/**
	 * Make the edit translations link
	 */

	$url = get_permalink( $post );

	if ( empty( $url ) || ! wplng_url_is_translatable( $url ) ) {
		return $actions;
	}

	$url = add_query_arg(
		'wplng-mode',
		'list',
		wplng_url_translate(
			$url,
			$languages_target_ids[0]
		)
	);

	$html  = '<a';
	$html .= ' href="' . esc_url( $url ) . '"';
	$html .= ' aria-label="' . esc_attr__( 'Edit translations', 'wplingua' ) . '"';
	$html .= '>';
	$html .= esc_html__( 'Translations', 'wplingua' );
	$html .= '</a>';

	/**
	 * Add link and return updated actions
	 */

	$actions['wplng-edit-link'] = $html;

	return $actions;
}
