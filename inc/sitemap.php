<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Add hreflang alternate links to XML sitemap for multilingual support.
 *
 * @param string $content The original XML sitemap content.
 * @return string The modified XML content with added xhtml:link alternate tags.
 */
function wplng_sitemap_add_hreflang_links( $content ) {

	// Return the content as is if it's empty or not valid XML.
	if ( empty( $content ) || ! wplng_str_is_xml( $content ) ) {
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
		if ( defined( 'WPLNG_DEBUG_XML' ) && true === WPLNG_DEBUG_XML ) {
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
 * Filter All In One SEO sitemap post entries to add multilingual alternate URLs.
 *
 * @param array $entry   The sitemap entry data.
 * @param int   $post_id The post ID.
 * @return array The modified sitemap entry with language alternatives.
 */
function wplng_aioseo_filter_sitemap_post( $entry, $post_id ) {

	// Get the post URL
	$post_url = get_permalink( $post_id );

	if ( empty( $post_url ) || ! wplng_url_is_translatable( $post_url ) ) {
		return $entry;
	}

	// Get language data
	$language_website_id  = wplng_get_language_website_id();
	$languages_target_ids = wplng_get_languages_target_ids();

	if ( empty( $language_website_id ) || empty( $languages_target_ids ) ) {
		return $entry;
	}

	// Set the main language
	$entry['language'] = $language_website_id;

	// Add translated versions
	$entry['languages'] = array();

	foreach ( $languages_target_ids as $language_id ) {
		$translated_url = wplng_url_translate( $post_url, $language_id );

		if ( ! empty( $translated_url ) && filter_var( $translated_url, FILTER_VALIDATE_URL ) ) {
			$entry['languages'][] = array(
				'language' => $language_id,
				'location' => $translated_url,
			);
		}
	}

	return $entry;
}


/**
 * Filter All In One SEO sitemap term entries to add multilingual alternate URLs.
 *
 * @param array $entry   The sitemap entry data.
 * @param int   $term_id The term ID.
 * @return array The modified sitemap entry with language alternatives.
 */
function wplng_aioseo_filter_sitemap_term( $entry, $term_id ) {

	// Get the term object
	$term = get_term( $term_id );

	if ( is_wp_error( $term ) || empty( $term ) ) {
		return $entry;
	}

	// Get the term URL
	$term_url = get_term_link( $term );

	if ( is_wp_error( $term_url ) || empty( $term_url ) || ! wplng_url_is_translatable( $term_url ) ) {
		return $entry;
	}

	// Get language data
	$language_website_id  = wplng_get_language_website_id();
	$languages_target_ids = wplng_get_languages_target_ids();

	if ( empty( $language_website_id ) || empty( $languages_target_ids ) ) {
		return $entry;
	}

	// Set the main language
	$entry['language'] = $language_website_id;

	// Add translated versions
	$entry['languages'] = array();

	foreach ( $languages_target_ids as $language_id ) {
		$translated_url = wplng_url_translate( $term_url, $language_id );

		if ( ! empty( $translated_url ) && filter_var( $translated_url, FILTER_VALIDATE_URL ) ) {
			$entry['languages'][] = array(
				'language' => $language_id,
				'location' => $translated_url,
			);
		}
	}

	return $entry;
}
