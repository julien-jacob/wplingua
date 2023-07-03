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

		$language_id   = $meta['wplng_translation_original_language_id'][0];
		$emoji         = wplng_get_language_emoji( $language_id ); // Emoji already esc_html
		$language_name = wplng_get_language_name( $language_id ); // Name already esc_html

		$html  = '<p><label for="wplng_translation_source">';
		$html .= $emoji . ' ' . $language_name . __( ' - Original text:', 'textdomain' );
		$html .= '</label></p>';
		$html .= '<p><strong>';
		$html .= esc_html( $meta['wplng_translation_original'][0] );
		$html .= '</strong></p><hr>';

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

				if ( $translation['language_id'] !== $language_target ) {
					continue;
				}

				if ( ! empty( $translation['language_id'] ) && ! empty( $translation['translation'] ) ) {

					$language_id   = esc_attr( $translation['language_id'] );
					$emoji         = wplng_get_language_emoji( $language_id ); // Emoji already esc_html
					$language_name = wplng_get_language_name( $language_id ); // Name already esc_html
					$label         = $emoji . ' ' . $language_name . __( ' - Translation:', 'textdomain' );
					$textarea      = esc_html( $translation['translation'] );
					$name          = esc_attr( 'wplng_translation_' . $language_id );

					if ( '[WPLNG_EMPTY]' === $textarea ) {
						$textarea = '';
					}

					$html .= '<p>';
					$html .= '<label for="' . $name . '">' . $label . '</label><br>';
					$html .= '<textarea name="' . $name . '" lang="' . $language_id . '" style="width:100%;">';
					$html .= $textarea;
					$html .= '</textarea>';
					$html .= '</p>';

				}
			}
		}
	}

	echo $html;

	// echo '<pre>';
	// var_dump( json_decode($meta['wplng_translation_sr'][0], true) );
	// echo '</pre>';
	// return;
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

	if ( ! empty( $meta['wplng_translation_translations'][0] ) ) {

		$translations = json_decode( $meta['wplng_translation_translations'][0], true );

		// TODO : Revoir cette condition ? return ?
		if ( empty( $translations ) ) {
			$translations = array();
		}

		foreach ( $translations as $key => $translation ) {
			if ( empty( $translation['language_id'] ) ) {
				continue;
			}

			if ( ! wplng_is_valid_language_id( $translation['language_id'] ) ) {
				continue;
			}

			$name = 'wplng_translation_' . $translation['language_id'];

			if ( ! isset( $_REQUEST[ $name ] ) ) {
				continue;
			}

			$temp = $_REQUEST[ $name ];
			if ( empty( $temp ) ) {
				$temp = '[WPLNG_EMPTY]';
			}

			$translations[ $key ]['translation'] = stripslashes( $temp );
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

	// Récupérer meta des traduction : wplng_translation_translations

	// Récupérer liste language target
	// pour chaque laguage target
	// 		Si $_REQUEST['wplng_translation_ LANG '] est OK

	// Update post meta

	// update fields
	// if ( isset( $_REQUEST['wplng_translation_es'] ) ) {

	// 	update_post_meta(
	// 		$post_id,
	// 		'wplng_translation_meta',
	// 		sanitize_text_field( $_POST['wplng_translation_es'] )
	// 	);

	// }
}
