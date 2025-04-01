<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Add meta box for wpLingua Slugs
 *
 * This function adds a meta box to the wpLingua Slugs post type
 * in the WordPress admin interface. The meta box is used to
 * display and edit slug information for translations.
 *
 * @param WP_Post $post The post object.
 * @return void
 */
function wplng_slug_add_meta_box( $post ) {

	add_meta_box(
		'wplng_meta_box_slug',              // Unique ID for the meta box
		__( 'Slug', 'wplingua' ),           // Title of the meta box
		'wplng_slug_meta_box_html_output',  // Callback function to render the meta box HTML
		'wplng_slug',                       // Screen or post type where the meta box appears
		'normal',                           // Context where the meta box should appear
		'low'                               // Priority within the context
	);
}


/**
 * Print HTML of slugs editor meta box in back office
 *
 * This function prints the HTML of the slugs editor meta box in the WordPress admin interface.
 *
 * @param object $post The post object.
 * @return string HTML The HTML of the meta box.
 */
function wplng_slug_meta_box_html_output( $post ) {

	echo '<div id="wplng-slug-editor">';
	echo wplng_slug_editor_get_html( $post );
	echo '</div>';
}


/**
 * This function prints the HTML of the slugs editor meta box in the WordPress admin interface.
 *
 * @param WP_Post $post The post object.
 * @return string HTML The HTML of the meta box.
 */
function wplng_slug_editor_get_html( $post ) {

	// Used later for security
	$html = wp_nonce_field(
		basename( __FILE__ ),
		'wplng_slug_meta_box_nonce',
		true,
		false
	);

	$meta = get_post_meta( $post->ID );

	// Display original text
	if ( ! empty( $meta['wplng_slug_original'][0] )
		&& is_string( $meta['wplng_slug_original'][0] )
		&& ! empty( $meta['wplng_slug_original_language_id'][0] )
		&& wplng_is_valid_language_id( $meta['wplng_slug_original_language_id'][0] )
	) {

		$language_id = $meta['wplng_slug_original_language_id'][0];
		$language    = wplng_get_language_by_id( $language_id );
		$alt         = __( 'Flag for language: ', 'wplingua' ) . $language['name'];

		$slug = $meta['wplng_slug_original'][0];
		$slug = sanitize_title( $slug );
		$slug = urldecode( $slug );

		$html .= '<div id="wplng-original-language" wplng-lang="' . esc_attr( $language_id ) . '">';
		$html .= '<div id="wplng-source-title">';
		$html .= '<img';
		$html .= ' src="' . esc_url( $language['flag'] ) . '"';
		$html .= ' alt="' . esc_attr( $alt ) . '"';
		$html .= ' class="wplng-flag"';
		$html .= '>';
		$html .= esc_html( $language['name'] );
		$html .= esc_html__( ' - Original slug: ', 'wplingua' );
		$html .= '</div>'; // End #wplng-source-title
		$html .= '<div id="wplng-source">/';
		$html .= esc_html( $slug );
		$html .= '/</div>'; // End #wplng-source
		$html .= '</div>'; // End #wplng-original-language

	}

	// Foreach translation, display form textarea to edit
	if ( ! empty( $meta['wplng_slug_translations'][0] )
		&& is_string( $meta['wplng_slug_translations'][0] )
	) {

		$translations_data = json_decode( $meta['wplng_slug_translations'][0], true );
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

				$is_in = true;

				$translations[ $translation_data['language_id'] ] = array(
					'language_id' => $translation_data['language_id'],
					'translation' => $translation_data['translation'],
					'status'      => $translation_data['status'],
				);

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

		foreach ( $translations as $translation ) {

			$language_id    = $translation['language_id'];
			$language       = wplng_get_language_by_id( $language_id );
			$slug_input     = $translation['translation'];
			$name           = 'wplng_slug_' . $language_id;
			$container_id   = 'wplng-slug-' . $language_id;
			$generate_link  = __( 'Regenerate translation', 'wplingua' );
			$alt            = __( 'Flag for language: ', 'wplingua' ) . $language['name'];
			$class          = 'wplng-edit-language';
			$reviewed_title = __( 'Mark as review', 'wplingua' );
			$is_reviewed    = false;

			if ( '[WPLNG_EMPTY]' === $slug_input ) {
				$slug_input = $meta['wplng_slug_original'][0];
			}

			$slug_input = urldecode(
				sanitize_title(
					$slug_input
				)
			);

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

			$html .= '<div';
			$html .= ' id="' . esc_attr( $container_id ) . '"';
			$html .= ' class="' . esc_attr( $class ) . '"';
			$html .= ' wplng-lang="' . esc_attr( $language_id ) . '"';
			$html .= '>';
			$html .= '<label for="' . esc_attr( $name ) . '" class="wplng-target-title">';
			$html .= '<img';
			$html .= ' src="' . esc_url( $language['flag'] ) . '"';
			$html .= ' alt="' . esc_attr( $alt ) . '"';
			$html .= ' class="wplng-flag"';
			$html .= '>';
			$html .= esc_html( $language['name'] );
			$html .= esc_html__( ' - Slug translation: ', 'wplingua' );
			$html .= '</label>';
			$html .= '<input';
			$html .= ' type="text"';
			$html .= ' name="' . esc_attr( $name ) . '"';
			$html .= ' id="' . esc_attr( $name ) . '"';
			$html .= ' class="wplng-slug-input"';
			$html .= ' lang="' . esc_attr( $language_id ) . '"';
			$html .= ' spellcheck="false"';
			$html .= ' value="/' . esc_attr( $slug_input ) . '/"';
			$html .= '>';

			if ( empty( $translation['status'] ) ) {
				$html .= '</div>';
				continue;
			}

			$html .= '<div class="wplng-slug-footer">';
			$html .= '<div class="wplng-slug-footer-left">';

			$html .= '<fieldset class="wplng-mark-as-reviewed">';

			$html .= '<input';
			$html .= ' type="checkbox"';
			$html .= ' id="wplng_mark_as_reviewed_' . esc_attr( $language_id ) . '"';
			$html .= ' name="wplng_mark_as_reviewed_' . esc_attr( $language_id ) . '"';
			$html .= ' wplng-lang="' . esc_attr( $language_id ) . '"';
			$html .= checked( $is_reviewed, true, false );
			$html .= '>';

			$html .= '<label';
			$html .= ' for="wplng_mark_as_reviewed_' . esc_attr( $language_id ) . '"';
			$html .= ' wplng-lang="' . esc_attr( $language_id ) . '"';
			$html .= ' title="' . esc_attr( $reviewed_title ) . '"';
			$html .= '>';
			$html .= esc_html__( 'Is reviewed', 'wplingua' );
			$html .= '</label>';

			$html .= '</fieldset>';

			$html .= '</div>'; // End .wplng-translation-footer-right
			$html .= '<div class="wplng-slug-footer-right">';

			$html .= '<span';
			$html .= ' class="dashicons dashicons-update wplng-spin wplng-generate-spin"';
			$html .= ' style="display: none;"';
			$html .= '></span> ';

			$html .= '<a';
			$html .= ' href="javascript:void(0);"';
			$html .= ' class="wplng-generate"';
			$html .= ' wplng-lang="' . esc_attr( $language_id ) . '"';
			$html .= '>';
			$html .= esc_html( $generate_link );
			$html .= '</a>';

			$html .= '</div>'; // End .wplng-translation-footer-right
			$html .= '</div>'; // End .wplng-translation-footer
			$html .= '</div>'; // End .wplng-edit-language

		}
	}

	return $html;
}


/**
 * Save meta box data of wpLingua translations
 *
 * This function is responsible for saving the slug translations
 * from the meta box in the WordPress admin interface.
 *
 * @param int $post_id The ID of the post being saved.
 * @return bool Returns true if meta data is successfully updated, false otherwise.
 */
function wplng_slug_save_meta_boxes_data( $post_id ) {

	// Check if nonce is set
	if ( ! isset( $_POST['wplng_slug_meta_box_nonce'] ) ) {
		return false;
	}

	// Sanitize the nonce
	$nonce = $_POST['wplng_slug_meta_box_nonce'];
	$nonce = sanitize_text_field( wp_unslash( $nonce ) );

	// Check for nonce to prevent XSS
	if ( ! wp_verify_nonce( $nonce, basename( __FILE__ ) ) ) {
		return false;
	}

	// Check for correct user capabilities - stop internal XSS from customers
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return false;
	}

	$meta = get_post_meta( $post_id );

	if ( empty( $meta['wplng_slug_translations'][0] )
		|| empty( $meta['wplng_slug_original'][0] )
	) {
		return false;
	}

	$slug_original    = sanitize_title( $meta['wplng_slug_original'][0] );
	$languages_target = wplng_get_languages_target_ids();
	$translations     = json_decode(
		$meta['wplng_slug_translations'][0],
		true
	);

	if ( empty( $translations ) ) {
		$translations = array();
	}

	// Loop through all target languages and save the slug translations
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

	// Save the slug translations - this part is run after the translations have been validated
	foreach ( $translations as $key => $translation ) {

		if ( empty( $translation['language_id'] )
			|| ! wplng_is_valid_language_id( $translation['language_id'] )
		) {
			continue;
		}

		$name     = 'wplng_slug_' . $translation['language_id'];
		$reviewed = 'wplng_mark_as_reviewed_' . $translation['language_id'];

		if ( ! isset( $_REQUEST[ $name ] ) ) {
			continue;
		}

		$temp = sanitize_title( $_REQUEST[ $name ] );

		if ( empty( $temp ) || $slug_original === $temp ) {
			$temp = '[WPLNG_EMPTY]';
		} elseif ( $temp !== $translation['translation'] ) {
			$temp = str_replace( '\\', '', $temp );
		}

		if ( ! empty( $_POST[ $reviewed ] ) ) {
			if ( 'false' !== $_POST[ $reviewed ] ) {
				$translations[ $key ]['status'] = time();
			} else {
				$translations[ $key ]['status'] = 'generated';
			}
		} else {
			$translations[ $key ]['status'] = 'generated';
		}

		// Check if another slug is translated with the same string

		if ( '[WPLNG_EMPTY]' !== $temp ) {

			$saved_slugs        = wplng_get_slugs();
			$has_same_slugs     = true;
			$same_slugs_counter = 0;

			while ( $has_same_slugs ) {

				$has_same_slugs = false;

				foreach ( $saved_slugs as $saved_slug ) {

					if ( $slug_original === $saved_slug['source'] ) {
						continue;
					}

					$saved_slug_translated = $saved_slug['source'];

					if ( isset( $saved_slug['translations'][ $translation['language_id'] ] ) ) {
						$saved_slug_translated = $saved_slug['translations'][ $translation['language_id'] ];
					}

					if ( $temp !== $saved_slug_translated ) {
						continue;
					}

					$has_same_slugs = true;
					++$same_slugs_counter;

					$temp = preg_replace(
						'#(.*)-(\d*)$#',
						'$1',
						$temp
					);

					$temp = $temp . '-' . $same_slugs_counter;

					break;

				}
			}
		}

		$translations[ $key ]['translation'] = esc_html( $temp );
	}

	wplng_clear_slugs_cache();

	// Save the translations in the post meta in JSON.
	return true === update_post_meta(
		$post_id,
		'wplng_slug_translations',
		wp_json_encode(
			$translations,
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		)
	);
}





/**
 * wpLingua AJAX function to get slugs on CPT edit page
 *
 * This function processes an AJAX request to generate a slug for a custom post type (CPT) edit page.
 * It validates and sanitizes input data, checks language parameters, and calls an external API
 * to translate the given text into a slug format.
 *
 * @return void
 */
function wplng_ajax_generate_slug() {

	/**
	 * Check and sanitize data
	 */

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

	// Check and sanitize source and target languages
	$language_source = sanitize_key( $_POST['language_source'] );
	$language_target = sanitize_key( $_POST['language_target'] );

	if ( ! wplng_is_valid_language_id( $language_source )
		|| ! wplng_is_valid_language_id( $language_target )
		|| $language_source === $language_target
	) {
		wp_send_json_error( __( 'Invalid languages parameters', 'wplingua' ) );
		return;
	}

	/**
	 * Check and sanitize text to translate
	 */

	$text = $_POST['text'];
	$text = wplng_text_esc( $text );

	// Replace certain characters for slug compatibility
	$text = str_replace(
		array( '/', '-', '_' ),
		array( '', ' ', ' ' ),
		$text
	);

	// Remove img emoji added by WordPress
	$text = wp_kses(
		$text,
		array(
			'img' => array(
				'alt' => array(),
			),
		)
	);

	// Remove image alt attributes from text
	$text = preg_replace(
		'/<img alt=\\"(.*)\\">/U',
		'',
		$text
	);

	// Check if text is translatable
	if ( ! wplng_text_is_translatable( $text ) ) {
		wp_send_json_success(
			sanitize_title(
				'/' . $text . '/'
			)
		);
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

	// Validate API response
	if ( ! isset( $response[0] ) ) {
		wp_send_json_error( __( 'Invalid API response', 'wplingua' ) );
		return;
	}

	// Process and return translated slug
	$response = $response[0];
	$response = sanitize_title( $response );
	$response = urldecode( $response );
	$response = '/' . $response . '/';

	wp_send_json_success( $response );
}
