<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Add meta box on wpLingua translations
 *
 * @param object $post
 * @return void
 */
function wplng_translation_add_meta_box( $post ) {

	add_meta_box(
		'wplng_meta_box_translation',
		__( 'Translation', 'wplingua' ),
		'wplng_translation_meta_box_html_output',
		'wplng_translation',
		'normal',
		'low'
	);

}


/**
 * Print HTML of wpLingua translations meta box
 *
 * @param object $post
 * @return string HTML
 */
function wplng_translation_meta_box_html_output( $post ) {

	//used later for security
	wp_nonce_field(
		basename( __FILE__ ),
		'wplng_translation_meta_box_nonce'
	);

	$meta = get_post_meta( $post->ID );

	// Display original text
	if ( ! empty( $meta['wplng_translation_original'][0] )
		&& is_string( $meta['wplng_translation_original'][0] )
		&& ! empty( $meta['wplng_translation_original_language_id'][0] )
		&& wplng_is_valid_language_id( $meta['wplng_translation_original_language_id'][0] )
	) {

		$language_id = $meta['wplng_translation_original_language_id'][0];
		$language    = wplng_get_language_by_id( $language_id );
		$alt         = __( 'Flag for language: ', 'wplingua' ) . $language['name'];

		$html  = '<div id="wplng-original-language" wplng-lang="' . esc_attr( $language_id ) . '">';
		$html .= '<div id="wplng-source-title">';
		$html .= '<img ';
		$html .= 'src="' . esc_url( $language['flag'] ) . '" ';
		$html .= 'alt="' . esc_attr( $alt ) . '" ';
		$html .= 'class="wplng-flag">';
		$html .= esc_html( $language['name'] );
		$html .= esc_html__( ' - Original text: ', 'wplingua' );
		$html .= '</div>'; // End #wplng-source-title
		$html .= '<div id="wplng-source">';
		$html .= esc_html( $meta['wplng_translation_original'][0] );
		$html .= '</div>'; // End #wplng-source
		$html .= '</div>'; // End #wplng-original-language

	}

	// Foreach translation, display form textarea to edit
	if ( ! empty( $meta['wplng_translation_translations'][0] )
		&& is_string( $meta['wplng_translation_translations'][0] )
	) {

		$translations_data = json_decode( $meta['wplng_translation_translations'][0], true );
		$languages_target  = wplng_get_languages_target_ids();
		$translations      = array();

		if ( empty( $translations_data ) ) {
			$translations_data = array();
		}

		foreach ( $languages_target as $language_target ) {

			$is_in = false;

			foreach ( $translations_data as $translation_data ) {

				if ( empty( $translation_data['language_id'] )
					|| ! wplng_is_valid_language_id( $translation_data['language_id'] )
					|| empty( $translation_data['translation'] )
					|| ! is_string( $translation_data['translation'] )
					|| ( $translation_data['language_id'] !== $language_target )
				) {
					continue;
				}

				$is_in          = true;
				$translations[] = $translation_data;

			}

			if ( ! $is_in ) {
				$translations[] = array(
					'language_id' => $language_target,
					'translation' => '[WPLNG_EMPTY]',
					'status'      => 'ungenerated',
				);
			}
		}

		foreach ( $translations as $translation ) {

			$language_id    = $translation['language_id'];
			$language       = wplng_get_language_by_id( $language_id );
			$textarea       = $translation['translation'];
			$name           = 'wplng_translation_' . $language_id;
			$container_id   = 'wplng-translation-' . $language_id;
			$generate_link  = __( 'Regenerate translation', 'wplingua' );
			$alt            = __( 'Flag for language: ', 'wplingua' ) . $language['name'];
			$class          = 'wplng-edit-language';
			$reviewed_title = __( 'Mark translation as review', 'wplingua' );
			$is_reviewed    = false;

			if ( '[WPLNG_EMPTY]' === $textarea ) {
				$textarea = '';
			}

			switch ( $translation['status'] ) {
				case 'ungenerated':
					$generate_link = __( 'Generate translation', 'wplingua' );
					$class        .= ' wplng-status-ungenerated';
					break;

				case 'generated':
					$class .= ' wplng-status-generated';
					break;

				default:
					if ( is_int( $translation['status'] ) ) {

						$class      .= ' wplng-status-reviewed';
						$is_reviewed = true;

						// Get and check date format
						$date_format = get_option( 'date_format' );
						if ( ! is_string( $date_format ) || empty( $date_format ) ) {
							$date_format = 'F j, Y';
						}

						// Get and check time format
						$time_format = get_option( 'time_format' );
						if ( ! is_string( $time_format ) || empty( $time_format ) ) {
							$time_format = 'g:i a';
						}

						$reviewed_title  = __( 'Reviewed on ', 'wplingua' );
						$reviewed_title .= esc_html(
							gmdate(
								$date_format,
								$translation['status']
							)
						);
						$reviewed_title .= ', ' . esc_html(
							gmdate(
								$time_format,
								$translation['status']
							)
						);
					}
					break;
			}

			$html .= '<div id="' . esc_attr( $container_id ) . '" ';
			$html .= 'class="' . esc_attr( $class ) . '" ';
			$html .= 'wplng-lang="' . esc_attr( $language_id ) . '" ';
			$html .= '>';
			$html .= '<label for="' . esc_attr( $name ) . '" class="wplng-target-title">';
			$html .= '<img ';
			$html .= 'src="' . esc_url( $language['flag'] ) . '" ';
			$html .= 'alt="' . esc_attr( $alt ) . '" ';
			$html .= 'class="wplng-flag">';
			$html .= esc_html( $language['name'] );
			$html .= esc_html__( ' - Translation: ', 'wplingua' );
			$html .= '</label>';
			$html .= '<textarea name="' . esc_attr( $name ) . '" ';
			$html .= 'id="' . esc_attr( $name ) . '" ';
			$html .= 'lang="' . esc_attr( $language_id ) . '" ';
			$html .= 'spellcheck="false">';
			$html .= esc_textarea( html_entity_decode( $textarea ) );
			$html .= '</textarea>';

			if ( empty( $translation['status'] ) ) {
				$html .= '</div>';
				continue;
			}

			$html .= '<div class="wplng-translation-footer">';
			$html .= '<div class="wplng-translation-footer-left">';

			$html .= '<fieldset class="wplng-mark-as-reviewed">';

			$html .= '<input ';
			$html .= 'type="checkbox" ';
			$html .= 'id="wplng_mark_as_reviewed_' . esc_attr( $language_id ) . '" ';
			$html .= 'name="wplng_mark_as_reviewed_' . esc_attr( $language_id ) . '" ';
			$html .= 'wplng-lang="' . esc_attr( $language_id ) . '" ';
			$html .= checked( $is_reviewed, true, false );
			$html .= '>';

			$html .= '<label ';
			$html .= 'for="wplng_mark_as_reviewed_' . esc_attr( $language_id ) . '" ';
			$html .= 'wplng-lang="' . esc_attr( $language_id ) . '" ';
			$html .= 'title="' . esc_attr( $reviewed_title ) . '" ';
			$html .= '>';
			$html .= esc_html__( 'Is reviewed', 'wplingua' );
			$html .= '</label>';

			$html .= '</fieldset>';

			$html .= '</div>'; // End .wplng-translation-footer-right
			$html .= '<div class="wplng-translation-footer-right">';

			$html .= '<span ';
			$html .= 'class="dashicons dashicons-update wplng-spin wplng-generate-spin" ';
			$html .= 'style="display: none;"></span> ';

			$html .= '<a ';
			$html .= 'href="javascript:void(0);" ';
			$html .= 'class="wplng-generate" ';
			$html .= 'wplng-lang="' . esc_attr( $language_id ) . '">';
			$html .= esc_html( $generate_link );
			$html .= '</a>';

			$html .= '</div>'; // End .wplng-translation-footer-right
			$html .= '</div>'; // End .wplng-translation-footer
			$html .= '</div>'; // End .wplng-edit-language

		}
	}

	echo $html;
}


/**
 * Save meta box data of wpLingua translations
 *
 * @param int $post_id
 * @return void
 */
function wplng_translation_save_meta_boxes_data( $post_id ) {

	// Check if nonce is set
	if ( ! isset( $_POST['wplng_translation_meta_box_nonce'] ) ) {
		return;
	}

	// Sanitize the nonce
	$nonce = $_POST['wplng_translation_meta_box_nonce'];
	$nonce = sanitize_text_field( wp_unslash( $nonce ) );

	// Check for nonce to top xss
	if ( ! wp_verify_nonce( $nonce, basename( __FILE__ ) ) ) {
		return;
	}

	// check for correct user capabilities - stop internal xss from customers
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	wplng_clear_translations_cache();

	$meta = get_post_meta( $post_id );

	if ( empty( $meta['wplng_translation_translations'][0] ) ) {
		return;
	}

	$languages_target = wplng_get_languages_target_ids();
	$translations     = json_decode(
		$meta['wplng_translation_translations'][0],
		true
	);

	if ( empty( $translations ) ) {
		$translations = array();
	}

	foreach ( $languages_target as $language_target ) {

		$is_in = false;

		foreach ( $translations as $translation ) {

			if ( empty( $translation['language_id'] )
				|| ! wplng_is_valid_language_id( $translation['language_id'] )
				|| ! isset( $translation['translation'] )
				|| ! is_string( $translation['translation'] )
				|| ( $translation['language_id'] !== $language_target )
			) {
				continue;
			}

			$is_in = true;
			break;
		}

		if ( ! $is_in ) {
			$translations[] = array(
				'language_id' => $language_target,
				'translation' => '[WPLNG_EMPTY]',
				'status'      => 'ungenerated',
			);
		}
	}

	foreach ( $translations as $key => $translation ) {

		if ( empty( $translation['language_id'] )
			|| ! wplng_is_valid_language_id( $translation['language_id'] )
		) {
			continue;
		}

		$name     = 'wplng_translation_' . $translation['language_id'];
		$reviewed = 'wplng_mark_as_reviewed_' . $translation['language_id'];

		if ( ! isset( $_REQUEST[ $name ] ) ) {
			continue;
		}

		$temp = stripslashes( sanitize_textarea_field( $_REQUEST[ $name ] ) );

		if ( empty( $temp ) ) {
			$temp                           = '[WPLNG_EMPTY]';
			$translations[ $key ]['status'] = 'ungenerated';
		} else {

			if ( $temp !== $translation['translation'] ) {
				$temp = str_replace( '\\', '', $temp );
			}

			if ( ! empty( $_POST[ $reviewed ] ) ) {
				$translations[ $key ]['status'] = time();
			} else {
				$translations[ $key ]['status'] = 'generated';
			}
		}

		$translations[ $key ]['translation'] = esc_html( $temp );
	}

	update_post_meta(
		$post_id,
		'wplng_translation_translations',
		wp_json_encode(
			$translations,
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		)
	);

}


/**
 * wpLingua AJAX function to get translations on CPT edit page
 *
 * @return void
 */
function wplng_ajax_generate_translation() {

	/**
	 * Check and sanitize data
	 */

	// Check data

	if ( empty( $_POST['language_source'] )
		|| ! is_string( $_POST['language_source'] )
		|| empty( $_POST['language_target'] )
		|| ! is_string( $_POST['language_target'] )
		|| ! isset( $_POST['text'] )
		|| ! is_string( $_POST['text'] )
	) {
		wp_send_json_error( __( 'Invalid parameters', 'wplingua' ) );
		return;
	}

	// Check and sanitize sources and target languages

	$language_source = sanitize_key( $_POST['language_source'] );
	$language_target = sanitize_key( $_POST['language_target'] );

	if ( ! wplng_is_valid_language_id( $language_source )
		|| ! wplng_is_valid_language_id( $language_target )
		|| $language_source === $language_target
	) {
		wp_send_json_error( __( 'Invalid languages parameters', 'wplingua' ) );
		return;
	}

	// Check and sanitize text to translate
	// (And convert img emoji to emoji)

	$text = wp_kses(
		$_POST['text'],
		array(
			'img' => array(
				'alt' => array(),
			),
		)
	);

	$text = preg_replace(
		'/<img alt=\\"(.*)\\">/U',
		'$1',
		$text
	);

	$text = sanitize_text_field( $text );
	$text = esc_attr( $text );

	if ( ! wplng_text_is_translatable( $text ) ) {
		wp_send_json_success( $text );
		return;
	}

	/**
	 * Call API and get translation
	 */

	$response = wplng_api_call_translate(
		array( $text ),
		$language_source,
		$language_target
	);

	if ( ! isset( $response[0] ) ) {
		wp_send_json_error( __( 'Invalid API response', 'wplingua' ) );
		return;
	}

	wp_send_json_success( $response[0] );
}
