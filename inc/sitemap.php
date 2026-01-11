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

	// Replace XSL stylesheet URL if option enabled
	if ( get_option( 'wplng_sitemap_xsl_override', false ) ) {
		$wplng_xsl_url = plugins_url( 'assets/sitemap.xsl', __DIR__ );

		// Find existing xml-stylesheet processing instruction
		$xsl_found = false;
		foreach ( $dom->childNodes as $node ) {
			if ( $node->nodeType === XML_PI_NODE && $node->target === 'xml-stylesheet' ) {
				$node->data = 'type="text/xsl" href="' . esc_url( $wplng_xsl_url ) . '"';
				$xsl_found  = true;
				break;
			}
		}

		// If no xml-stylesheet found, add one before the root element
		if ( ! $xsl_found ) {
			$xsl_pi = $dom->createProcessingInstruction(
				'xml-stylesheet',
				'type="text/xsl" href="' . esc_url( $wplng_xsl_url ) . '"'
			);
			$dom->insertBefore( $xsl_pi, $dom->documentElement );
		}
	}

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
		return $dom->saveXML() . $signature;
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
 * Modify XSL stylesheet to display translated links in sitemap.
 *
 * @param string $content The original XSL content.
 * @return string The modified XSL content with translations displayed under each URL.
 */
function wplng_sitemap_modify_xsl( $content ) {

	if ( empty( $content ) ) {
		return $content;
	}

	// Check if this XSL already has translations support
	if ( strpos( $content, 'wplng-translations' ) !== false ) {
		return $content;
	}

	// Load XSL into DOMDocument
	$dom                     = new DOMDocument( '1.0', 'UTF-8' );
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput       = true;

	// Enable internal error handling
	$previous_libxml_setting = libxml_use_internal_errors( true );

	if ( ! $dom->loadXML( $content ) ) {
		libxml_clear_errors();
		libxml_use_internal_errors( $previous_libxml_setting );
		return $content;
	}

	libxml_use_internal_errors( $previous_libxml_setting );

	$xpath = new DOMXPath( $dom );
	$xpath->registerNamespace( 'xsl', 'http://www.w3.org/1999/XSL/Transform' );

	// Add xhtml namespace to xsl:stylesheet if not present
	$stylesheet = $dom->documentElement;
	if ( ! $stylesheet->hasAttribute( 'xmlns:xhtml' ) ) {
		$stylesheet->setAttribute( 'xmlns:xhtml', 'http://www.w3.org/1999/xhtml' );
	}

	// Add CSS to existing <style> element or create one in <head>
	$css = '
        .wplng-translations { margin-top: 8px; padding-top: 8px; border-top: 1px dashed #ddd; }
        .wplng-translation-row { display: block; margin: 4px 0; font-size: 13px; }
        .wplng-lang-badge { display: inline-block; min-width: 24px; padding: 2px 6px; margin-right: 8px; background: #0073aa; color: white; border-radius: 3px; font-size: 11px; text-align: center; font-weight: bold; }
        .wplng-translation-row a { color: #0073aa; text-decoration: none; }
        .wplng-translation-row a:hover { text-decoration: underline; }
        .wplng-credit { margin-top: 20px; padding: 10px; background: #f5f5f5; border-radius: 4px; font-size: 12px; color: #666; text-align: center; }
        .wplng-credit a { color: #0073aa; }
    ';

	$style_nodes = $xpath->query( '//style' );
	if ( $style_nodes->length > 0 ) {
		$style_node             = $style_nodes->item( 0 );
		$style_node->nodeValue .= $css;
	} else {
		$head_nodes = $xpath->query( '//head' );
		if ( $head_nodes->length > 0 ) {
			$style_element = $dom->createElement( 'style', $css );
			$head_nodes->item( 0 )->appendChild( $style_element );
		}
	}

	// Create the XSL fragment for translations
	$xsl_ns = 'http://www.w3.org/1999/XSL/Transform';

	// Find the <td> containing the URL (class="loc" or containing sitemap:loc)
	$loc_cells = $xpath->query( '//td[contains(@class, "loc")]' );

	if ( $loc_cells->length > 0 ) {
		$loc_cell = $loc_cells->item( 0 );

		// Create xsl:if element
		$xsl_if = $dom->createElementNS( $xsl_ns, 'xsl:if' );
		$xsl_if->setAttribute( 'test', "xhtml:link[@rel='alternate']" );

		// Create wrapper div
		$div = $dom->createElement( 'div' );
		$div->setAttribute( 'class', 'wplng-translations' );

		// Create xsl:for-each element
		$xsl_foreach = $dom->createElementNS( $xsl_ns, 'xsl:for-each' );
		$xsl_foreach->setAttribute( 'select', "xhtml:link[@rel='alternate']" );

		// Create span.wplng-translation-row
		$span_row = $dom->createElement( 'span' );
		$span_row->setAttribute( 'class', 'wplng-translation-row' );

		// Create span.wplng-lang-badge with xsl:value-of
		$span_badge = $dom->createElement( 'span' );
		$span_badge->setAttribute( 'class', 'wplng-lang-badge' );
		$xsl_value_lang = $dom->createElementNS( $xsl_ns, 'xsl:value-of' );
		$xsl_value_lang->setAttribute( 'select', '@hreflang' );
		$span_badge->appendChild( $xsl_value_lang );

		// Create anchor element
		$anchor = $dom->createElement( 'a' );
		$anchor->setAttribute( 'href', '{@href}' );
		$xsl_value_href = $dom->createElementNS( $xsl_ns, 'xsl:value-of' );
		$xsl_value_href->setAttribute( 'select', '@href' );
		$anchor->appendChild( $xsl_value_href );

		// Assemble the structure
		$span_row->appendChild( $span_badge );
		$span_row->appendChild( $anchor );
		$xsl_foreach->appendChild( $span_row );
		$div->appendChild( $xsl_foreach );
		$xsl_if->appendChild( $div );

		// Append to loc cell
		$loc_cell->appendChild( $xsl_if );
	}

	// Add credit message to the page footer
	$body_nodes = $xpath->query( '//body' );
	if ( $body_nodes->length > 0 ) {
		$body = $body_nodes->item( 0 );

		// Find the main container div or append to body
		$container_nodes = $xpath->query( '//div[@id="sitemap"] | //div[@id="content"] | //div[contains(@class, "sitemap")]' );
		$target          = ( $container_nodes->length > 0 ) ? $container_nodes->item( 0 ) : $body;

		$credit_div = $dom->createElement( 'p' );
		$credit_div->setAttribute( 'class', 'wplng-credit' );

		$credit_text = $dom->createTextNode( 'Multilingual sitemap powered by ' );
		$credit_div->appendChild( $credit_text );

		$credit_link = $dom->createElement( 'a', 'wpLingua' );
		$credit_link->setAttribute( 'href', 'https://wplingua.com' );
		$credit_link->setAttribute( 'target', '_blank' );
		$credit_link->setAttribute( 'rel', 'noopener' );
		$credit_div->appendChild( $credit_link );

		$target->appendChild( $credit_div );
	}

	$result = $dom->saveXML();

	if ( empty( $result ) ) {
		return $content;
	}

	// Add XML comment at the end
	$result .= PHP_EOL . '<!-- XSL Sitemap stylesheet is made multilingual by wpLingua -->';

	return $result;
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

	// Add translated versions (including original language for self-reference)
	$entry['languages'] = array();

	// Add original language (self-referencing hreflang)
	$entry['languages'][] = array(
		'language' => $language_website_id,
		'location' => $post_url,
	);

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

	// Add translated versions (including original language for self-reference)
	$entry['languages'] = array();

	// Add original language (self-referencing hreflang)
	$entry['languages'][] = array(
		'language' => $language_website_id,
		'location' => $term_url,
	);

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
