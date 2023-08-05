<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


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


function wplng_translation_meta_box_html_output( $post ) {

	//used later for security
	wp_nonce_field(
		basename( __FILE__ ),
		'wplng_translation_meta_box_nonce'
	);

	$meta = get_post_meta( $post->ID );

	// Display original text
	if ( ! empty( $meta['wplng_translation_original'][0] )
		&& ! empty( $meta['wplng_translation_original_language_id'][0] )
	) {

		$language_id   = esc_attr( $meta['wplng_translation_original_language_id'][0] );
		$emoji         = wplng_get_language_emoji( $language_id ); // Emoji already esc_html
		$language_name = wplng_get_language_name( $language_id ); // Name already esc_html

		$html  = '<div id="wplng-original-language" wplng-lang="' . $language_id . '">';
		$html .= '<label for="wplng_translation_source">';
		$html .= $emoji . ' ' . $language_name . __( ' - Original text:', 'textdomain' );
		$html .= '</label>';
		$html .= '<div class="wplng-source">';
		$html .= esc_html( $meta['wplng_translation_original'][0] );
		$html .= '</div>';
		$html .= '</div>';

	}

	// Foreach translation, display form textarea to edit
	if ( ! empty( $meta['wplng_translation_translations'][0] ) ) {

		$translations     = json_decode( $meta['wplng_translation_translations'][0], true );
		$languages_target = wplng_get_languages_target_ids();

		if ( empty( $translations ) ) {
			$translations = array();
		}

		foreach ( $languages_target as $language_target ) {

			foreach ( $translations as $translation ) {

				if ( $translation['language_id'] !== $language_target
					|| empty( $translation['language_id'] )
					|| empty( $translation['translation'] )
				) {
					continue;
				}

				$language_id        = esc_attr( $translation['language_id'] );
				$emoji              = wplng_get_language_emoji( $language_id ); // Emoji already esc_html
				$language_name      = wplng_get_language_name( $language_id ); // Name already esc_html
				$label              = $emoji . ' ' . $language_name . __( ' - Translation:', 'textdomain' );
				$textarea           = esc_html( $translation['translation'] );
				$name               = esc_attr( 'wplng_translation_' . $language_id );
				$container_id       = esc_attr( 'wplng-translation-' . $language_id );
				$generate_link_text = __( 'Regenerate translation', 'wplingua' );

				if ( '[WPLNG_EMPTY]' === $textarea ) {
					$textarea = '';
				}

				$html .= '<div id="' . $container_id . '" class="wplng-edit-language">';
				$html .= '<label for="' . $name . '">' . $label . '</label>';
				$html .= '<textarea name="' . $name . '" id="' . $name . '" lang="' . $language_id . '" spellcheck="false">';
				$html .= $textarea;
				$html .= '</textarea>';

				if ( empty( $translation['status'] ) ) {
					continue;
				}

				$html .= '<div class="wplng-translation-footer">';
				$html .= '<div class="wplng-translation-footer-left">';

				switch ( $translation['status'] ) {
					case 'ungenerated':
						$generate_link_text = __( 'Generate translation', 'wplingua' );

						$html .= '<span class="wplng-status">';
						$html .= __( 'Status: Ungenerated', 'wplingua' );
						$html .= '</span>';
						break;

					case 'generated':
						$html .= '<span class="wplng-status">';
						$html .= __( 'Status: Generated', 'wplingua' );
						$html .= '</span>';
						break;

					default:
						if ( is_int( $translation['status'] ) ) {
							// $generate_link_text = __( 'Regenerate translation', 'wplingua' );

							$html .= '<span class="wplng-status">';
							$html .= __( 'Status: Edited on', 'wplingua' ) . ' ';
							$html .= esc_html(
								date(
									get_option( 'date_format' ),
									$translation['status']
								)
							);
							$html .= ', ' . esc_html(
								date(
									get_option( 'time_format' ),
									$translation['status']
								)
							);
							$html .= '</span>';
						}
						break;
				}
				$html .= '</div>'; // End .wplng-translation-footer-right

				$html .= '<div class="wplng-translation-footer-right">';

				$html .= '<span class="dashicons dashicons-update wplng-spin wplng-generate-spin" style="display: none;"></span> ';
				$html .= '<a href="javascript:void(0);" class="wplng-generate" wplng-lang="' . $language_id . '">';
				$html .= $generate_link_text;
				$html .= '</a>';

				$html .= '</div>'; // End .wplng-translation-footer-right
				$html .= '</div>'; // End .wplng-translation-footer
				$html .= '</div>'; // End .wplng-edit-language
			}
		}
	}

	echo $html;
}


function wplng_translation_save_meta_boxes_data( $post_id ) {

	// check for nonce to top xss
	if ( ! isset( $_POST['wplng_translation_meta_box_nonce'] )
		|| ! wp_verify_nonce( $_POST['wplng_translation_meta_box_nonce'], basename( __FILE__ ) )
	) {
		return;
	}

	// check for correct user capabilities - stop internal xss from customers
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$meta = get_post_meta( $post_id );

	if ( empty( $meta['wplng_translation_translations'][0] ) ) {
		return;
	}

	$translations = json_decode( $meta['wplng_translation_translations'][0], true );

	if ( empty( $translations ) ) {
		$translations = array();
	}

	foreach ( $translations as $key => $translation ) {

		if ( empty( $translation['language_id'] )
			|| ! wplng_is_valid_language_id( $translation['language_id'] )
		) {
			continue;
		}

		$name = 'wplng_translation_' . $translation['language_id'];

		if ( ! isset( $_REQUEST[ $name ] ) ) {
			continue;
		}

		$temp = stripslashes( $_REQUEST[ $name ] );

		if ( empty( $temp ) ) {
			$temp = '[WPLNG_EMPTY]';
		} elseif ( $temp !== $translation['translation'] ) {
			$translations[ $key ]['status'] = time();
		}

		$translations[ $key ]['translation'] = $temp;
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


function wplng_ajax_generate_translation() {

	if ( ! empty( $_POST['language_source'] )
		&& ! empty( $_POST['language_target'] )
		&& ! empty( $_POST['text'] )
	) {
		$response = wplng_translate(
			$_POST['text'],
			$_POST['language_source'],
			$_POST['language_target']
		);

		wp_send_json_success( $response );

	} else {
		wp_send_json_error( __( 'Invalid parameters', 'wplingua' ) );
	}

}
