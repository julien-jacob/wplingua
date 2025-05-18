<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Overloads the DOM with a notification if the Translation API is overloaded.
 *
 * This function modifies the DOM to include a notification message when the
 * Translation API is overloaded. It checks the provided arguments and user
 * permissions before making changes to the DOM.
 *
 * @param object $dom  The DOM object to be modified.
 * @param array  $args An array of arguments, including 'load' and 'overloaded' keys.
 * @return object      The modified DOM object.
 */
function wplng_dom_load_overload( $dom, $args ) {

	wplng_args_setup( $args );

	if ( 'loading' === $args['load']
		|| empty( $args['overloaded'] )
		|| ! current_user_can( 'edit_posts' )
	) {
		return $dom;
	}

	$html = '<div id="wplng-overloaded-container">';

	$html .= '<span class="dashicons dashicons-info-outline"></span> ';

	$html .= '<span id="wplng-overloaded-text-mobile">';
	$html .= esc_html__( 'Translation API overloaded', 'wplingua' );
	$html .= '</span>';

	$html .= '<span id="wplng-overloaded-text-desktop">';
	$html .= esc_html__( 'Translation API overloaded. Retry in a few minutes.', 'wplingua' );
	$html .= '</span>';

	$html .= '<span';
	$html .= ' id="wplng-overloaded-close"';
	$html .= ' class="dashicons dashicons-no-alt"';
	$html .= ' title="' . esc_attr__( 'Close', 'wpLingua' ) . '"';
	$html .= '></span>';

	$html .= '</div>'; // End #wplng-overloaded-container

	foreach ( $dom->find( 'body' ) as $body ) {
		$body->innertext .= $html;
	}

	$dom = wplng_sdh_str_get_html( $dom );

	return $dom;
}
