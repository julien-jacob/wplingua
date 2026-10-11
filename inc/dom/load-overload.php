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

	if ( empty( $args['overloaded'] )
		|| ! current_user_can( 'edit_posts' )
	) {
		return $dom;
	}

	$api_data = wplng_get_api_data();

	$html = '<div id="wplng-overloaded-container">';

	// Icon
	$html .= '<span class="dashicons dashicons-info-outline"></span> ';

	if ( ( $api_data['expired'] === true ) && ! empty( $api_data['plan'] ) && is_string( $api_data['plan'] ) ) {

		/**
		 * wpLingua plan expired
		 */

		// Link "Learn more"
		$html_link = '<a';
		$html_link .= ' href="' . esc_url( admin_url( 'admin.php?page=wplingua-settings' ) ) . '"';
		$html_link .= '>';
		$html_link .= esc_html__( 'Learn more', 'wplingua' );
		$html_link .= '</a>';

		// Mobile text
		$html .= '<span id="wplng-overloaded-text-mobile">';
		$html .= esc_html__( 'wpLingua plan expired.', 'wplingua' );
		$html .= ' ' . $html_link;
		$html .= '</span>';

		// Desktop text
		$html .= '<span id="wplng-overloaded-text-desktop">';

		$html .= sprintf(
			/* translators: 1: plan name (STARTER or PREMIUM), 2: expiration date */
			esc_html__( 'Your wpLingua %1$s plan expired on %2$s. Automatic translations are no longer available.', 'wplingua' ),
			esc_html( $api_data['plan'] ),
			esc_html( ! empty( $api_data['expiration'] ) ? $api_data['expiration'] : '' )
		);

		$html .= ' ' . $html_link;

		$html .= '</span>';

	} elseif ( empty( $api_data['status'] ) || $api_data['status'] === 'FREE' ) {

		/**
		 * API overload message for FREE users
		 */

		// Mobile text
		$html .= '<span id="wplng-overloaded-text-mobile">';
		$html .= esc_html__( 'Translation API overloaded', 'wplingua' );
		$html .= '</span>';

		// Desktop text
		$html .= '<span id="wplng-overloaded-text-desktop">';
		$html .= esc_html__( 'Translation API overloaded. Retry in a few minutes or ', 'wplingua' );
		$html .= '<a href="https://wplingua.com/pricing/" target="_blank" rel="noopener noreferrer">';
		$html .= esc_html__( 'upgrade your API key', 'wplingua' );
		$html .= '</a>';
		$html .= esc_html__( ' to get priority API access.', 'wplingua' );
		$html .= '</span>';
	} else {

		/**
		 * API overload message for PRO users
		 */

		// Mobile text
		$html .= '<span id="wplng-overloaded-text-mobile">';
		$html .= esc_html__( 'Translation API overloaded', 'wplingua' );
		$html .= '</span>';

		// Desktop text
		$html .= '<span id="wplng-overloaded-text-desktop">';
		$html .= esc_html__( 'Translation API overloaded. Please retry in a few minutes.', 'wplingua' );
		$html .= '</span>';
	}

	// Close icon / button
	$html .= '<span';
	$html .= ' id="wplng-overloaded-close"';
	$html .= ' class="dashicons dashicons-no-alt"';
	$html .= ' title="' . esc_attr__( 'Close', 'wplingua' ) . '"';
	$html .= '></span>';

	$html .= '</div>'; // End #wplng-overloaded-container

	foreach ( $dom->find( 'body' ) as $body ) {

		// Add body clas .wplingua-api-overloaded
		if ( isset( $body->class ) ) {
			$body->class .= ' wplingua-api-overloaded';
		} else {
			$body->class = 'wplingua-api-overloaded';
		}

		// Add "API overloaded" HTML bar to body
		$body->innertext .= $html;
	}

	return $dom;
}
