<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action( 'init', 'mcv_register_post_type_translation' );
function mcv_register_post_type_translation() {
	register_post_type(
		'mcv_translation',
		array(
			'labels'              => array(
				'name'          => __( 'Translations', 'machiavel' ),
				'singular_name' => __( 'Translation', 'machiavel' ),
			),
			'public'              => false,  // it's not public, it shouldn't have it's own permalink, and so on
			'publicly_queryable'  => true,  // you should be able to query it
			'show_ui'             => true,  // you should be able to edit it in wp-admin
			'exclude_from_search' => true,  // you should exclude it from search results
			'show_in_nav_menus'   => false,  // you shouldn't be able to add it to menus
			'has_archive'         => false,  // it shouldn't have archive page
			'rewrite'             => false,  // it shouldn't have rewrite rules
			'supports'            => array(
				'title',
				'revisions',
			),
			'capability_type'     => 'post',
			'capabilities'        => array(
				'create_posts' => false, // Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups )
			),
			'map_meta_cap'        => true, // Set to `false`, if users are not allowed to edit/delete existing posts
		)
	);
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

				$label    = esc_html( $translation['language_id'] ) . __( ' - Traduction:', 'textdomain' );
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

	echo '<pre>';
	var_dump( $meta );
	echo '</pre>';
	return;

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



function mcv_get_saved_translation_from_original( $original ) {

	$returned  = false;
	$args      = array(
		'post_type'    => 'mcv_translation',
		'meta_key'     => 'mcv_translation_original',
		'meta_value'   => $original,
		'meta_compare' => '=',
	);
	$the_query = new WP_Query( $args );

	// The Loop
	if ( $the_query->have_posts() ) {
		$the_query->the_post();
		$returned = get_post();
	}

	wp_reset_postdata();

	return $returned;
}



function mcv_get_saved_translations( $target_language_id ) {

	$translations = array();
	$args         = array(
		'post_type'      => 'mcv_translation',
		'posts_per_page' => -1,
	);
	$the_query    = new WP_Query( $args );

	// The Loop
	while ( $the_query->have_posts() ) {

		$translation = array();

		$the_query->the_post();

		$meta = get_post_meta( get_the_ID() );

		// Get translation for current language target

		if ( empty( $meta['mcv_translation_translations'][0] ) ) {
			continue;
		}

		$translations_meta = json_decode( $meta['mcv_translation_translations'][0], true );

		foreach ( $translations_meta as $key => $translation_meta ) {

			if ( ! empty( $translation_meta['language_id'] )
				&& $translation_meta['language_id'] === $target_language_id
				&& ! empty( $translation_meta['translation'] )
			) {

				$translation['translation'] = $translation_meta['translation'];
				break;

				// TODO : Prendre ne compte ranslation == [MCV_EMPTY]
				// TODO : Check si pas trouvé, continuer la boucle while have post
			}
		}

		// get source
		if ( empty( $meta['mcv_translation_original'][0] ) ) {
			continue;
		}
		$translation['source'] = $meta['mcv_translation_original'][0];

		if ( empty( $meta['mcv_translation_sr'][0] ) ) {
			continue;
		}

		$search_meta = json_decode( $meta['mcv_translation_sr'][0], true );

		// $translations[] = $search_meta;

		foreach ( $search_meta as $key => $search ) {
			if ( empty( $search['search'] ) || empty( $search['replace'] ) ) {
				continue;
			}

			$translation['search']  = $search['search'];
			$translation['replace'] = $search['replace'];
			$translations[]         = $translation;
		}

		// ! isset( $translation['source'] ) // Original text
		// || ! isset( $translation['translation'] ) // Translater text
		// || ! isset( $translation['search'] ) // Search
		// || ! isset( $translation['replace'] ) // Replace

	}

	wp_reset_postdata();

	return $translations;
}



function mcv_save_translation_new( $language_id, $original, $translation, $search, $replace ) {

	/**
	 * Make the title
	 */
	$tite_max_length = 100;
	$title           = substr( $original, 0, $tite_max_length );
	if ( strlen( $original ) > $tite_max_length ) {
		$title .= __( '...', 'machiavel' );
	}

	/**
	 * Create the post and get this ID
	 */
	$post_id = wp_insert_post(
		array(
			'post_title'  => $title,
			'post_type'   => 'mcv_translation',
			'post_status' => 'publish',
		)
	);

	if ( is_wp_error( $post_id ) ) {
		return false;
	}

	/**
	 * Make $translation_meta
	 */
	$languages_target = mcv_get_languages_target_ids();
	$translation_meta = array();

	foreach ( $languages_target as $key => $target_language ) {

		if ( $target_language === $language_id ) {
			$translation_meta[] = array(
				'language_id' => $target_language,
				'translation' => $translation,
			);
		} else {
			$translation_meta[] = array(
				'language_id' => $target_language,
				'translation' => '[MCV_EMPTY]',
			);
		}
	}

	/**
	 * Make search / replace meta
	 */
	// $search  = str_replace( '\\', '\\\\', preg_quote( $search ) );
	$escapers     = array( '\\', '/', '"', "\n", "\r", "\t", "\x08", "\x0c" );
	$replacements = array( '\\\\', '\\/', '\\"', "\\n", "\\r", "\\t", "\\f", "\\b" );
	$search       = str_replace( $escapers, $replacements, $search );

	$sr_meta[] = array(
		'search'  => $search,
		'replace' => $replace,
	);

	add_post_meta( $post_id, 'mcv_translation_original_language_id', mcv_get_language_website_id() );
	add_post_meta( $post_id, 'mcv_translation_original', $original );
	add_post_meta( $post_id, 'mcv_translation_sr', wp_json_encode( $sr_meta ) );
	add_post_meta( $post_id, 'mcv_translation_translations', wp_json_encode( $translation_meta ) );

}



function mcv_update_translation( $post, $language_id, $translation, $search, $replace ) {

	$meta = get_post_meta( $post->ID );

	$languages_target = mcv_get_languages_target_ids();
	$website_language = mcv_get_language_website_id();

	$translation_meta = ( empty( $meta['mcv_translation_translations'][0] ) )
		? array() :
		json_decode( $meta['mcv_translation_translations'][0], true );

	$original_language_id_meta = ( empty( $meta['mcv_translation_original_language_id'][0] ) )
		? mcv_get_language_website_id()
		: $meta['mcv_translation_original_language_id'][0];

	// If translation found is not valid
	if ( empty( $translation_meta ) || $website_language !== $original_language_id_meta ) {

		/**
		 * $original_language_id_meta must be the same as in option page
		 */
		update_post_meta( $post->ID, 'mcv_translation_original_language_id', $website_language );

		/**
		 * Make $translation_meta
		 */
		$translation_meta = array();
		foreach ( $languages_target as $key => $target_language ) {

			if ( $target_language === $language_id ) {
				$translation_meta[] = array(
					'language_id' => $target_language,
					'translation' => '[MCV_EMPTY]',
				);
			} else {
				$translation_meta[] = array(
					'language_id' => $target_language,
					'translation' => $translation,
				);
			}
		}

		update_post_meta( $post->ID, 'mcv_translation_translations', wp_json_encode( $translation_meta ) );

	} else { // Translation is valid

		$sr_meta = ( empty( $meta['mcv_translation_sr'][0] ) )
		? array() :
		json_decode( $meta['mcv_translation_sr'][0], true );

		$sr_already_in = false;
		foreach ( $sr_meta as $key => $sr ) {
			if ( ! empty( $sr['search'] ) && $sr['search'] === $search ) {
				$sr_already_in = true;
				break;
			}
		}
		if ( ! $sr_already_in ) {
			$sr_meta[] = array(
				'search'  => $search, //str_replace( '\\', '\\\\', preg_quote( $search ) ),
				'replace' => $replace,
			);
		}

		update_post_meta( $post->ID, 'mcv_translation_sr', wp_json_encode( $sr_meta ) );

		// Set or update new translation
		$translation_already_in = false;
		foreach ( $translation_meta as $key => $translation_e ) {
			if ( $translation_e['language_id'] == $language_id ) {
				$translation_already_in   = true;
				$translation_meta[ $key ] = array(
					'language_id' => $language_id,
					'translation' => $translation,
				);
				break;
			}
		}
		if ( ! $translation_already_in ) {
			$translation_meta[] = array(
				'language_id' => $language_id,
				'translation' => $translation,
			);
		}

		update_post_meta( $post->ID, 'mcv_translation_translations', wp_json_encode( $translation_meta ) );

	}

}




function mcv_save_translation( $target_language_id, $original, $translation, $search, $replace ) {

	$saved_translation = mcv_get_saved_translation_from_original( $original );

	if ( empty( $saved_translation ) ) {

		// Create new translation post
		mcv_save_translation_new(
			$target_language_id,
			$original,
			$translation,
			$search,
			$replace
		);
	} else {
		// Update the translation post
		mcv_update_translation(
			$saved_translation,
			$target_language_id,
			$translation,
			$search,
			$replace
		);
	}

}




// function mcv_get_saved_translations() {
// 	$x = '';

// 	$args      = array(
// 		'post_type'    => 'mcv_translation',
// 		'meta_key'     => 'mcv_language_id', // (string) - Custom field key.
// 		'meta_value'   => 'pt', // (string) - Custom field value.
// 		'meta_compare' => '=',
// 	);
// 	$the_query = new WP_Query( $args );

// 	// The Loop
// 	if ( $the_query->have_posts() ) :
// 		while ( $the_query->have_posts() ) :
// 			$the_query->the_post();
// 			$x .= get_the_title() . '<br>';
// 			// $x .= get_post_meta(get_the_ID(), "mcv_language_id", true) . '<hr>';
// 			$x .= var_export( get_post_meta( get_the_ID(), 'mcv_language_id', true ), true ) . '<hr>';
// 		endwhile;
// 	endif;

// 	// Reset Post Data
// 	wp_reset_postdata();

// 	return $x;
// }
