<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Redirect page if is called wiht an untranslate slug to the translated URL
 *
 * @return void
 */
function wplng_redirect_translated_slug() {

	if ( is_404() ) {
		return;
	}

	$language_website_id = wplng_get_language_website_id();
	$language_current_id = wplng_get_language_current_id();

	if ( $language_website_id === $language_current_id ) {
		return;
	}

	if ( empty( $_COOKIE['wplingua'] )
		&& apply_filters( 'wplng_cookie_check', true )
	) {
		// Set HTTP no-cache header
		nocache_headers();

		// Disable cache for plugins
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
	}

	$url_current = wplng_get_url_current();

	if ( ! is_string( $url_current )
		|| '' === $url_current
		|| '/' === $url_current
	) {
		return;
	}

	$url_translated = wplng_get_url_current_for_language(
		$language_current_id
	);

	if ( $url_current === $url_translated ) {
		return;
	}

	wp_safe_redirect(
		wp_make_link_relative(
			$url_translated
		)
	);

	exit;
}


/**
 * wpLingua output buffering starting function
 *
 * @return void
 */
function wplng_ob_start() {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

		/**
		 * Is an AJAX call
		 */

		if ( empty( $_SERVER['HTTP_REFERER'] )
			|| ! is_string( $_SERVER['HTTP_REFERER'] )
		) {
			return;
		}

		$referer = sanitize_url( $_SERVER['HTTP_REFERER'] );

		// Check if the referer is clean
		if ( strtolower( esc_url_raw( $referer ) ) !== strtolower( $referer ) ) {
			return;
		}

		// Check AJAX action
		if ( ! empty( $_POST['action'] )
			&& in_array( $_POST['action'], wplng_data_excluded_ajax_action() )
		) {
			return;
		}

		global $wplng_request_uri;
		$wplng_request_uri = wp_make_link_relative( $referer );

		if ( ! wplng_url_is_translatable( $wplng_request_uri )
			|| wplng_get_language_website_id() === wplng_get_language_current_id()
		) {
			return;
		}

		ob_start( 'wplng_ob_callback_ajax' );

	} elseif ( wplng_url_is_sitemap_xml() ) {

		ob_start( 'wplng_ob_callback_sitemap_xml' );

	} else {

		/**
		 * Is a front call
		 */

		if ( wplng_get_language_website_id() === wplng_get_language_current_id() ) {
			return;
		}

		global $wplng_request_uri;
		$current_path = $wplng_request_uri;

		if ( ! is_string( $current_path ) ) {
			return;
		}

		$origin_path = wplng_get_url_original( $current_path );

		if ( ! wplng_url_is_translatable( $origin_path ) ) {
			wp_safe_redirect( $origin_path );
			exit;
		}

		$_SERVER['REQUEST_URI'] = $origin_path;

		ob_start( 'wplng_ob_callback_page' );
	}
}


/**
 * wpLingua OB Callback function : AJAX call
 *
 * @param string $output
 * @return string
 */
function wplng_ob_callback_ajax( $output ) {

	global $wplng_request_uri;

	if ( wplng_str_is_json( $output ) ) {

		$output_translated = wplng_translate_json( $output );

	} elseif ( wplng_str_is_html( $output ) ) {

		$output_translated = wplng_translate_html( $output );

	} else {
		$output_translated = $output;
	}

	// Print debug data in debug.log file
	if ( true === WPLNG_DEBUG_AJAX ) {

		$action = 'UNKNOW';
		if ( ! empty( $_POST['action'] ) && is_string( $_POST['action'] ) ) {
			$action = $_POST['action'];
		}

		$debug = array(
			'title'       => 'wpLingua AJAX debug',
			'action'      => $action,
			'request_uri' => $wplng_request_uri,
			'value'       => $output,
			'translated'  => $output_translated,
		);

		error_log(
			var_export(
				$debug,
				true
			)
		);
	}

	return $output_translated;
}


/**
 * Callback function for output buffering to process and modify sitemap XML content.
 *
 * @param string $content The original XML content.
 * @return string The modified XML content with added <xhtml:link> tags or the original content if no changes are made.
 */
function wplng_ob_callback_sitemap_xml( $content ) {

	// Return the content as is if it's empty or not valid XML.
	if ( ! get_option( 'wplng_sitemap_xml', false )
		|| empty( $content )
		|| ! wplng_str_is_xml( $content )
	) {
		return $content;
	}

	// Check if multilingual XML sitemap is enabled.
	$sitemap_xml_enabled = apply_filters(
		'wplng_enable_sitemap_xml_feature',
		get_option( 'wplng_sitemap_xml', false )
	);

	if ( empty( $sitemap_xml_enabled ) ) {
		return $content;
	}

	// Get language data.
	$language_website_id  = wplng_get_language_website_id();
	$languages_target_ids = wplng_get_languages_target_ids();

	if ( empty( $language_website_id ) || empty( $languages_target_ids ) ) {
		return $content;
	}

	// Load the XML content into a DOMDocument.
	$dom                     = new DOMDocument( '1.0', 'UTF-8' );
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput       = true;

	// Enable internal error handling
	$previous_libxml_setting = libxml_use_internal_errors( true ); 
	
	// Check XML errors
	if ( ! $dom->loadXML( $content ) ) {
		if ( defined( 'WPLNG_DEBUG_JSON' ) && true === WPLNG_DEBUG_JSON ) {
			$errors = array();

			foreach ( libxml_get_errors() as $error ) {
				$errors[] = $error->message;
			}

			libxml_clear_errors();

			$debug = array(
				'title'  => 'wpLingua XML debug - Invalid XML content',
				'errors' => $errors,
			);

			error_log( var_export( $debug, true ) );
		}

		return $content;
	}

	// Restore previous setting
	libxml_use_internal_errors( $previous_libxml_setting ); 

	$signature = '<!-- XML Sitemap is made multilingual by wpLingua -->' . PHP_EOL;

	// Register namespaces and prepare XPath.
	$xpath = new DOMXPath( $dom );
	$xpath->registerNamespace( 'sm', 'http://www.sitemaps.org/schemas/sitemap/0.9' );
	$xpath->registerNamespace( 'xhtml', 'http://www.w3.org/1999/xhtml' );

	// Ensure the root element declares the required namespaces.
	$urlset = $dom->documentElement;
	if ( ! $urlset->hasAttribute( 'xmlns' ) ) {
		$urlset->setAttribute( 'xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9' );
	}
	if ( ! $urlset->hasAttribute( 'xmlns:xhtml' ) ) {
		$urlset->setAttribute( 'xmlns:xhtml', 'http://www.w3.org/1999/xhtml' );
	}

	// Get all <url> nodes.
	$url_nodes = $xpath->query( '//sm:url' );

	if ( empty( $url_nodes ) ) {
		return $content . $signature;
	}

	foreach ( $url_nodes as $url_node ) {

		// Get original URL.
		$loc_node = $xpath->query( 'sm:loc', $url_node )->item( 0 );

		if ( empty( $loc_node ) ) {
			continue;
		}

		$url_original = trim( $loc_node->nodeValue );

		if ( '' === $url_original || ! wplng_url_is_translatable( $url_original ) ) {
			continue;
		}

		// Add link for original language.
		$link_node = $dom->createElement( 'xhtml:link' );
		$link_node->setAttribute( 'rel', 'alternate' );
		$link_node->setAttribute( 'hreflang', esc_attr( $language_website_id ) );
		$link_node->setAttribute( 'href', esc_url( $url_original ) );
		$url_node->appendChild( $link_node );

		// Add links for target languages.
		foreach ( $languages_target_ids as $language_id ) {

			$translated_url = wplng_url_translate( 
				$url_original, 
				$language_id 
			);

			// Validate the translated URL.
			if ( empty( $translated_url ) 
				|| ! filter_var( $translated_url, FILTER_VALIDATE_URL ) 
			) {
				continue;
			}

			$link_node = $dom->createElement( 'xhtml:link' );
			$link_node->setAttribute( 'rel', 'alternate' );
			$link_node->setAttribute( 'hreflang', esc_attr( $language_id ) );
			$link_node->setAttribute( 'href', esc_url( $translated_url ) );
			$url_node->appendChild( $link_node );
		}
	}

	$sitemap = $dom->saveXML();

	if ( empty( $sitemap ) ) {
		return $content;
	}

	return $sitemap . $signature;
}


/**
 * wpLingua OB Callback function : Translate pages
 *
 * @param string $html
 * @return string
 */
function wplng_ob_callback_page( $content ) {

	if ( wplng_str_is_json( $content ) ) {

		$content = apply_filters( 'wplng_intercepted_json', $content );
		$content = wplng_translate_json( $content );
		$content = apply_filters( 'wplng_translated_json', $content );

	} elseif ( wplng_str_is_html( $content ) ) {

		$args = array();

		if ( current_user_can( 'edit_posts' ) ) {

			if ( ! empty( $_GET['wplng-mode'] ) ) {

				switch ( $_GET['wplng-mode'] ) {

					case 'editor':
						$args['mode'] = 'editor';
						break;

					case 'list':
						$args['mode'] = 'list';
						break;
				}
			}

			$load_in_progress_enabled = apply_filters(
				'wplng_enable_in_progress_feature',
				get_option( 'wplng_load_in_progress', false )
			);

			if ( $load_in_progress_enabled ) {

				$args['load'] = 'enabled';

				if ( ! empty( $_GET['wplng-load'] )
					&& (
						$_GET['wplng-load'] === 'loading'
						|| $_GET['wplng-load'] === 'progress'
						|| $_GET['wplng-load'] === 'disabled'
					)
				) {

					$args['load'] = $_GET['wplng-load'];

					wp_cache_flush();
				}
			}
		}

		$content = apply_filters( 'wplng_intercepted_html', $content );
		$content = wplng_translate_html( $content, $args );
		$content = apply_filters( 'wplng_translated_html', $content );

	}

	return $content;
}
