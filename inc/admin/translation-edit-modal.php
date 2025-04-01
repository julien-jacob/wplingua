<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Get HTML of translation edit modal
 *
 * @return void
 */
function wplng_translation_edit_modal_get_html() {

	$return_button  = '<a';
	$return_button .= ' href="javascript:void(0);"';
	$return_button .= ' title="' . esc_attr__( 'Return on page', 'wplingua' ) . '"';
	$return_button .= ' class="wplng-button-icon wplng-button-return"';
	$return_button .= ' id="wplng-modal-edit-return"';
	$return_button .= '>';
	$return_button .= '<span class="dashicons dashicons-no"></span>';
	$return_button .= '</a>';

	$all_languages_button  = '<a';
	$all_languages_button .= ' href="javascript:void(0);"';
	$all_languages_button .= ' id="wplng-modal-edit-show-all"';
	$all_languages_button .= '>';
	$all_languages_button .= esc_attr__( 'All languages', 'wplingua' );
	$all_languages_button .= '</a>';

	$edit_link_template = add_query_arg(
		array(
			'post'   => 'WPLNG_TRANSLATION_ID',
			'action' => 'edit',
		),
		get_admin_url() . 'post.php'
	);

	$edit_link_button  = '<a';
	$edit_link_button .= ' href="#"';
	$edit_link_button .= ' title="' . esc_attr__( 'Open edit page', 'wplingua' ) . '"';
	$edit_link_button .= ' class="wplng-button-icon"';
	$edit_link_button .= ' id="wplng-modal-edit-post"';
	$edit_link_button .= ' target="_blank"';
	$edit_link_button .= ' data-wplng-edit-template="' . esc_attr( $edit_link_template ) . '"';
	$edit_link_button .= '>';
	$edit_link_button .= '<span class="dashicons dashicons-external"></span>';
	$edit_link_button .= '</a>';

	$html = '<div id="wplng-modal-edit-container">';

	$html .= '<div id="wplng-modal-edit">';

	$html .= '<div id="wplng-modal-header">';
	$html .= '<span id="wplng-modal-title">';
	$html .= '<span class="dashicons dashicons-translation wplng-modal-header-icon"></span> ';
	$html .= esc_html__( 'Edit translation', 'wplingua' );
	$html .= '</span>';
	$html .= $all_languages_button;
	$html .= $edit_link_button;
	$html .= $return_button;

	$html .= '</div>';

	$html .= '<div id="wplng-modal-edit-main">';
	$html .= '<div id="wplng-translation-editor">';
	$html .= '</div>'; // End #wplng-translation-editor

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
 * Process the AJAX request to save a translation from modal edit,
 * validate and sanitize the input data, and call the function to
 * save the translation.
 *
 * @return void
 */
function wplng_ajax_save_modal() {

	/**
	 * Check and sanitize data
	 *
	 * Check that the post ID and current user can edit the post
	 * before attempting to save the translation.
	 */

	if ( empty( $_POST['post_id'] )
		|| ! current_user_can( 'edit_post', $_POST['post_id'] )
	) {
		wp_send_json_error( __( 'Invalid translation ID', 'wplingua' ) );
		return;
	}

	/**
	 * Try to save the post
	 *
	 * Call the function to save the translation and store the result
	 * in variable $saved.
	 */

	$saved = wplng_translation_save_meta_boxes_data( $_POST['post_id'] );

	/**
	 * Send AJAX success or error
	 *
	 * If the saving was successful, send a JSON success response.
	 * Otherwise, send a JSON error response with a message.
	 */

	if ( $saved ) {
		wp_send_json_success( null );
	} else {
		wp_send_json_error( __( 'Translation saving failed', 'wplingua' ) );
		return;
	}
}
