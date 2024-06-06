<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_translation_edit_modal_get_html() {

	$return_button  = '<a ';
	$return_button .= 'href="javascript:void(0);" ';
	$return_button .= 'title="' . esc_attr__( 'Return on page', 'wplingua' ) . '" ';
	$return_button .= 'class="wplng-button-icon wplng-button-return" ';
	$return_button .= 'id="wplng-modal-edit-return">';
	$return_button .= '<span class="dashicons dashicons-no"></span>';
	$return_button .= '</a>';

	$html  = '';
	$html .= '<div id="wplng-modal-edit-container">';

	$html .= '<div id="wplng-modal-edit">';

	$html .= '<div id="wplng-modal-header">';
	$html .= '<span id="wplng-modal-title">';
	$html .= '<span class="dashicons dashicons-translation wplng-modal-header-icon"></span> ';
	$html .= esc_html__( 'Edit translation', 'wplingua' );
	$html .= '</span>';
	$html .= $return_button;
	$html .= '</div>';

	$html .= '<div id="wplng-modal-edit-main">';
	$html .= '<div id="wplng-translation-editor">';
	// $html .= wplng_translation_meta_box_html_output( get_post( 6536 ), array(), true );
	$html .= '</div>'; // End #wplng-translation-editor

	// $html .= '<button id="wplng-modal-edit-all-target" disabled>';
	// $html .= esc_html__('Show all languages', 'wplingua');
	// $html .= '</button>';

	$html .= '<button id="wplng-modal-edit-save" disabled>';
	$html .= esc_html__( 'Save', 'wplingua' );
	$html .= '</button>';

	$html .= '</div>'; // End #wplng-modal-edit-main

	$html .= '</div>'; // End #wplng-modal-edit
	$html .= '</div>'; // End #wplng-modal-edit-container

	return $html;

}



/**
 * Send the translation editor HTML for AJAX call
 *
 * @return void
 */
function wplng_ajax_edit_modal() {

	/**
	 * Check and sanitize data
	 */

	if ( empty( $_POST['post_id'] )
		|| ! current_user_can( 'edit_post', $_POST['post_id'] )
	) {
		wp_send_json_error( __( 'Invalid translation ID', 'wplingua' ) );
		return;
	}

	/**
	 * Get and check the translation post
	 */

	$translation_post = get_post( $_POST['post_id'] );

	if ( empty( $translation_post ) ) {
		wp_send_json_error( __( 'Invalid translation post', 'wplingua' ) );
		return;
	}

	/**
	 * Get the translation editor HTML
	 */

	$data['wplng_edit_html'] = esc_html(
		wplng_translation_editor_get_html(
			$translation_post
		)
	);

	/**
	 * Send the translation editor HTML
	 */

	wp_send_json_success(
		wp_json_encode(
			$data,
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		)
	);
}



/**
 * Save translation on AJAX call of modal edit
 *
 * @return void
 */
function wplng_ajax_save_modal() {

	/**
	 * Check and sanitize data
	 */

	if ( empty( $_POST['post_id'] )
		|| ! current_user_can( 'edit_post', $_POST['post_id'] )
	) {
		wp_send_json_error( __( 'Invalid translation ID', 'wplingua' ) );
		return;
	}

	/**
	 * Try to save the post
	 */

	$saved = wplng_translation_save_meta_boxes_data( $_POST['post_id'] );

	/**
	 * Send AJAX success or error
	 */

	if ( $saved ) {
		wp_send_json_success( null );
	} else {
		wp_send_json_error( __( 'Translation saving failed', 'wplingua' ) );
	}

}
