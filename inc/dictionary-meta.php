<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



function wplng_dictionary_add_meta_box( $post ) {

	add_meta_box(
		'wplng_meta_box_dictionary',
		__( 'Dictionary entry', 'wplingua' ),
		'wplng_dictionary_meta_box_html_output',
		'wplng_dictionary',
		'normal',
		'low'
	);

}

function wplng_dictionary_meta_box_html_output( $post ) {

	//used later for security
	wp_nonce_field(
		basename( __FILE__ ),
		'wplng_dictionary_meta_box_nonce'
	);

	$meta = get_post_meta( $post->ID );
	$html = '';


	// Display original text
	if ( ! empty( $meta['wplng_dictionary_original'][0] )
		&& ! empty( $meta['wplng_dictionary_original_language_id'][0] )
		&& ! empty( $meta['wplng_translation_translations'][0] )
	) {

		$language_id   = $meta['wplng_dictionary_original_language_id'][0];
		$emoji         = wplng_get_language_emoji( $language_id ); // Emoji already esc_html
		$language_name = wplng_get_language_name( $language_id ); // Name already esc_html

		$html .= '<p><label for="wplng_dictionary_source">';
		$html .= $emoji . ' ' . $language_name . __( ' - Original text:', 'textdomain' );
		$html .= '</label></p>';
		$html .= '<p><strong>';
		$html .= esc_html( $meta['wplng_dictionary_original'][0] );
		$html .= '</strong></p><hr>';


		// Foreach translation, display form textarea to edit
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

		

	} else {

		$language_id   = wplng_get_language_current_id();
		$emoji         = wplng_get_language_emoji( $language_id ); // Emoji already esc_html
		$language_name = wplng_get_language_name( $language_id ); // Name already esc_html

		$html .= '<p><label for="wplng_dictionary_source">';
		$html .= $emoji . ' ' . $language_name . __( ' - Original text:', 'textdomain' );
		$html .= '</label></p>';
		$html .= '<p><input type="text" style="width:100%;" required>';
		$html .= '</input></p>';

		// $html .= '<fieldset>';
		// $html .= '	<legend>Action : </legend>';

		// $html .= '	<input type="radio" id="wplng_dictionary_action_never_translate" name="wplng_dictionary_action" />';
		// $html .= '	<label for="wplng_dictionary_action_never_translate">Never translate</label>';

		// $html .= '	<input type="radio" id="wplng_dictionary_action_always_translate" name="wplng_dictionary_action" />';
		// $html .= '	<label for="wplng_dictionary_action_always_translate">Always translate with this texts</label>';

		// $html .= '</fieldset><hr>';
















$html .= '<span>' . __('Action:', 'wplingua') . '</span>';

$html .= '<fieldset>';
$html .= '	<legend class="screen-reader-text">';
$html .= '		<span>' . __('Never translate', 'wplingua') . '</span>';
$html .= '	</legend>';
$html .= '	<label for="wplng_dictionary_action_never_translate">';
$html .= '		<input type="radio" id="wplng_dictionary_action_never_translate" name="wplng_dictionary_action" /> ' . __('Never translate', 'wplingua');
$html .= '	</label>';
$html .= '</fieldset>';


$html .= '<fieldset>';
$html .= '	<legend class="screen-reader-text">';
$html .= '		<span>' . __('Always translate', 'wplingua') . '</span>';
$html .= '	</legend>';
$html .= '	<label for="wplng_dictionary_action_always_translate">';
$html .= '		<input type="radio" id="wplng_dictionary_action_always_translate" name="wplng_dictionary_action" /> ' . __('Always translate', 'wplingua');
$html .= '	</label>';
$html .= '</fieldset>';












		// Foreach translation, display form textarea to edit
		$languages_target = wplng_get_languages_target_ids();

		$html .= '<div id="wplng_languages">';
		$html .= '<hr>';

		foreach ( $languages_target as $language_target ) {

			$language_id   = esc_attr( $language_target );
			$emoji         = wplng_get_language_emoji( $language_id ); // Emoji already esc_html
			$language_name = wplng_get_language_name( $language_id ); // Name already esc_html
			$label         = $emoji . ' ' . $language_name . __( ' - Translation:', 'textdomain' );

			
			// $name          = esc_attr( 'wplng_translation_' . $language_id );

			$name = 'okok';

			$html .= '<p>';
			$html .= '<label for="' . $name . '">' . $label . '</label><br>';
			$html .= '<input type="text" name="' . $name . '" lang="' . $language_id . '" style="width:100%;"  rows="1">';

			$html .= '</input>';
			$html .= '</p>';
			
		} 

		$html .= '</div>';

	}

	echo $html;

	// echo '<pre>';
	// var_dump( json_decode($meta['wplng_translation_sr'][0], true) );
	// echo '</pre>';
	// return;
}


function wplng_dictionary_save_meta_boxes_data( $post_id ) {

	// // check for nonce to top xss
	// if ( ! isset( $_POST['wplng_dictionary_meta_box_nonce'] )
	// 	|| ! wp_verify_nonce( $_POST['wplng_dictionary_meta_box_nonce'], basename( __FILE__ ) )
	// ) {
	// 	return;
	// }

	// // check for correct user capabilities - stop internal xss from customers
	// if ( ! current_user_can( 'edit_post', $post_id ) ) {
	// 	return;
	// }

	// $meta = get_post_meta( $post_id );

	// if ( ! empty( $meta['wplng_translation_translations'][0] ) ) {

	// 	$translations = json_decode( $meta['wplng_translation_translations'][0], true );

	// 	// TODO : Revoir cette condition ? return ?
	// 	if ( empty( $translations ) ) {
	// 		$translations = array();
	// 	}

	// 	foreach ( $translations as $key => $translation ) {
	// 		if ( empty( $translation['language_id'] ) ) {
	// 			continue;
	// 		}

	// 		if ( ! wplng_is_valid_language_id( $translation['language_id'] ) ) {
	// 			continue;
	// 		}

	// 		$name = 'wplng_translation_' . $translation['language_id'];

	// 		if ( ! isset( $_REQUEST[ $name ] ) ) {
	// 			continue;
	// 		}

	// 		$temp = $_REQUEST[ $name ];
	// 		if ( empty( $temp ) ) {
	// 			$temp = '[WPLNG_EMPTY]';
	// 		}

	// 		$translations[ $key ]['translation'] = stripslashes( $temp );
	// 	}

	// 	update_post_meta(
	// 		$post_id,
	// 		'wplng_translation_translations',
	// 		wp_json_encode(
	// 			$translations,
	// 			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	// 		)
	// 	);
	// }

}



