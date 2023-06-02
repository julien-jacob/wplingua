<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


add_action( 'add_meta_boxes_mcv_translation', 'meta_box_for_products' );
function meta_box_for_products( $post ) {

	add_meta_box(
		'mcv_meta_box_id',
		__( 'Translation', 'machiavel' ),
		'mcv_translation_meta_box_html_output',
		'mcv_translation',
		'normal',
		'low'
	);

}

function mcv_translation_meta_box_html_output( $post ) {

	//used later for security
	wp_nonce_field(
		basename( __FILE__ ),
		'mcv_translation_meta_box_nonce'
	);

	$meta = get_post_meta( $post->ID );

	if ( ! empty( $meta['mcv_translation_original'][0] ) ) {
		?>
		<p><label for="mcv_translation"><?php _e( 'Original text:', 'textdomain' ); ?></label></p>
		<p><strong><?php echo esc_html( $meta['mcv_translation_original'][0] ); ?></strong></p>
		<hr>
		<?php
	}

	if ( ! empty( $meta['mcv_translation_translations'][0] ) ) {

		$translations = json_decode( $meta['mcv_translation_translations'][0], true );

		if ( empty( $translations ) ) {
			$translations = array();
		}

		// TODO : Compare [MCV_EMPTY]

		foreach ( $translations as $key => $translation ) :
			if ( ! empty( $translation['language_id'] ) && ! empty( $translation['translation'] ) ) :

				$language = mcv_get_language_by_id( $translation['language_id'] );

				// TODO : Remplacer par fonctions dédiées
				$emoji = '';
				if ( ! empty( $language['emoji'] ) ) {
					$emoji = $language['emoji'];
				}

				$name = $translation['language_id'];
				if ( ! empty( $language['name'] ) ) {
					$name = $language['name'];
				}

				$label    = esc_html( $emoji . ' ' . $name ) . __( ' - Traduction:', 'textdomain' );
				$textarea = esc_html( $translation['translation'] );

				?>
				<p>
					<label for="mcv_translation"><?php echo $label; ?></label>
					<br>
					<textarea name="mcv_translation" style="width:100%;"><?php echo $textarea; ?></textarea>
				</p>
				<?php
			endif;
		endforeach;
	}

	// echo '<pre>';
	// var_dump( $meta );
	// echo '</pre>';
	// return;

}

add_action( 'save_post_mcv_translation', 'mcv_translation_save_meta_boxes_data', 10, 2 );
function mcv_translation_save_meta_boxes_data( $post_id ) {

	// check for nonce to top xss
	if ( ! isset( $_POST['mcv_translation_meta_box_nonce'] )
		|| ! wp_verify_nonce( $_POST['mcv_translation_meta_box_nonce'], basename( __FILE__ ) )
	) {
		return;
	}

	// check for correct user capabilities - stop internal xss from customers
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// update fields
	if ( isset( $_REQUEST['mcv_translation_meta'] ) ) {

		update_post_meta(
			$post_id,
			'mcv_translation_meta',
			sanitize_text_field( $_POST['mcv_translation_meta'] )
		);

	}
}
