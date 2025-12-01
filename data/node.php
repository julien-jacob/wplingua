<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Get selectors of excluded elements
 *
 * @return array
 */
function wplng_data_excluded_selector_default() {
	return array(
		// Default nodes
		'style',
		'svg',
		'canvas',
		'address',
		'iframe',
		'code',
		'xml',

		// Link tag
		'link[rel="EditURI"]',
		'link[title="oEmbed (JSON)"]',
		'link[title="oEmbed (XML)"]',
		'link[title="JSON"]',
		'link[type="application/rss+xml"]',

		// Default class
		'.no-translate',
		'.notranslate',

		// Wordpress
		'#wpadminbar',
		'.wp-embed-share-input',
		'[aria-label="HTML"]',
		'#media-views-js-extra',
		'#wp-api-request-js-extra',
		'body#error-page',
		'.wp-die-message',

		// wpLingua
		'link[hreflang]',
		'.wplng-switcher',
		'.wplingua-menu',
		'#wplng-modal-edit-container',

		// Comment
		'.comment-content',
		'.comment-author',
		'.comment_postinfo .fn',

		// Author name
		'.author.vcard',
		'.entry-author .fn',

		// Plugin: Query Monitor
		'#query-monitor',
		'#query-monitor-main',

		// Plugin: SecuPress
		'#secupress-donttranslate',

		// Plugin: Debug Bar
		'#querylist',

		// Plugin: Google Site Kit
		'#googlesitekit-base-data-js-extra',

		// Plugin: Smash Balloon - Social photo feed
		'#sb_instagram',

		// Plugin: Complianz GDPR
		'.cookies-per-purpose .name',

		// Plugin: SEOPress
		'#seopress-metabox-js-extra',

		// Plugin: WooCommerce
		'.woocommerce-Price-amount',
	);
}


/**
 * Get selectors of excluded elements in visual editor
 *
 * @return array
 */
function wplng_data_excluded_editor_link() {
	return apply_filters(
		'wplng_excluded_editor_link',
		array(
			'textarea',
			'pre',
			'option',
		)
	);
}


/**
 * Get selectors of excluded nodes text
 *
 * @return array
 */
function wplng_data_excluded_node_text() {
	return apply_filters(
		'wplng_excluded_node_text',
		array(
			'style',
			'svg',
			'canvas',
			'link',
			'script',
			'code',

			// Plugin: Contact Form 7
			'.wpcf7-textarea',
		)
	);
}
