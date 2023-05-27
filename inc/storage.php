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

	// $original      = get_post_meta( $post->ID, 'mcv_source', true );
	// $translation = get_post_meta( $post->ID, 'mcv_translation', true );

	$translation = get_post_meta( $post->ID, 'mcv_translation_meta', true );
	// $translation = get_post_meta( $post->ID );

	echo '<pre>';
	var_dump( $translation );
	var_dump( json_decode($translation) );
	echo '</pre>';
	return;
	?>
	<p>
		<label for="mcv_translation"><?php _e( 'Source text:', 'textdomain' ); ?></label>
		<br>
		<?php /* echo esc_html( $original ); */ ?>
	</p>
	<p>
		<label for="mcv_translation"><?php _e( 'Traduction:', 'textdomain' ); ?></label>
		<br>
		<textarea name="mcv_translation" style="width:100%;"><?php echo esc_textarea( $translation ); ?></textarea>
	</p>
	<?php

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


// TODO
function mcv_get_saved_translation_from_original( $original, $langage_id = '' ) {

	$x = '';

	$args      = array(
		'post_type'    => 'mcv_translation',
		'meta_key'     => 'mcv_language_id', // (string) - Custom field key.
		'meta_value'   => 'pt', // (string) - Custom field value.
		'meta_compare' => '=',
	);
	$the_query = new WP_Query( $args );

	// The Loop
	if ( $the_query->have_posts() ) :
		while ( $the_query->have_posts() ) :
			$the_query->the_post();
			$x .= get_the_title() . '<br>';
			// $x .= get_post_meta(get_the_ID(), "mcv_language_id", true) . '<hr>';
			$x .= var_export( get_post_meta( get_the_ID(), 'mcv_language_id', true ), true ) . '<hr>';
		endwhile;
	endif;

}


function mcv_save_translation( $language_id, $original, $translation, $search, $replace ) {

	// Make the title
	$length     = strlen( $original );
	$max_length = 100;
	$title      = substr( $original, 0, $max_length );
	if ( $length > $max_length ) {
		$title .= __( '...', 'machiavel' );
	}

	// TODO : Make it if translation not already exist
	// Create the post and get this ID
	$post_id = wp_insert_post(
		array(
			'post_title'  => $title,
			'post_type'   => 'mcv_translation',
			'post_status' => 'publish',
		)
	);

	// TODO : Check if is wp error !

	$translation_meta_json = get_post_meta( get_the_ID(), 'mcv_language_id', true );
	$translation_meta      = json_decode( $translation_meta_json );
	$target_languages      = mcv_get_languages_target_ids();

	if ( 
		empty( $translation_meta ) 
		|| $target_languages !== $translation_meta['original_language_id']
	) {

		$translations = array();

		foreach ( $target_languages as $key => $target_language ) {

			if ( $target_language === $language_id ) {
				$translations[] = array(
					'language_id' => $target_language,
					'translation' => '[MCV_EMPTY_TRANSLATION]',
				);
			} else {
				$translations[] = array(
					'language_id' => $target_language,
					'translation' => $translation,
				);
			}
		}

		$translation_meta = array(
			'original_language_id' => mcv_get_language_website_id(),
			'original'             => $original,
			'search'               => array(
				str_replace( '\\', '\\\\', $search ),
			),
			'replace'              => array(
				$replace,
			),
			'translations'         => $translations,
		);

	} else { // Translation already exist


		// Add $search in $translation_meta['search']
		$search_meta = $translation_meta['search'];
		$search_is_in_meta = false;
		foreach ($search_meta as $key => $search_element) {
			if ($search == $search_element) {
				$search_is_in_meta = true;
				break;
			}
		}
		if (! $search_is_in_meta) {
			$translation_meta['search'][] = str_replace( '\\', '\\\\', $search );
		}

		// Add $replace in $translation_meta['replace']
		$replace_meta = $translation_meta['search'];
		$replace_is_in_meta = false;
		foreach ($replace_meta as $key => $replace_element) {
			if ($replace == $replace_element) {
				$replace_is_in_meta = true;
				break;
			}
		}
		if (! $replace_is_in_meta) {
			$translation_meta['replace'][] = $replace;
		}


		// Set or update new translation
		$translations = $translation_meta['$original_language_id'];

		foreach ( $translations as $key => $translation ) {
			if ( $translation['language_id'] == $language_id ) {
				$translations[] = array(
					'language_id' => $translation['language_id'],
					'translation' => $translation,
				);
			} 
		}

		$translation_meta['translations'] = $translations;

	}

	$translation_meta = wp_json_encode( $translation_meta );

	// TODO : !!! Check json escaping !!!
	add_post_meta(
		$post_id,
		'mcv_translation_meta',
		$translation_meta
		// sanitize_text_field( $language_id )
	);

	// update_post_meta(
	// 	$post_id,
	// 	'mcv_language_id',
	// 	sanitize_text_field( $language_id )
	// );

	// add_post_meta(
	// 	$post_id,
	// 	'mcv_source',
	// 	sanitize_text_field( $original )
	// );

}

// mcv_insert_translation();




function mcv_get_translations_for_language() {
	$x = '';

	$args      = array(
		'post_type'    => 'mcv_translation',
		'meta_key'     => 'mcv_language_id', // (string) - Custom field key.
		'meta_value'   => 'pt', // (string) - Custom field value.
		'meta_compare' => '=',
	);
	$the_query = new WP_Query( $args );

	// The Loop
	if ( $the_query->have_posts() ) :
		while ( $the_query->have_posts() ) :
			$the_query->the_post();
			$x .= get_the_title() . '<br>';
			// $x .= get_post_meta(get_the_ID(), "mcv_language_id", true) . '<hr>';
			$x .= var_export( get_post_meta( get_the_ID(), 'mcv_language_id', true ), true ) . '<hr>';
		endwhile;
	endif;

	// Reset Post Data
	wp_reset_postdata();

	return $x;
}
