<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Get attributes and selectors of JSON to translate
 *
 * @return array
 */
function wplng_data_attr_json_to_translate() {
	return apply_filters(
		'wplng_attr_json_to_translate',
		array(
			// Theme: Divi
			array(
				'attr'     => 'data-et-multi-view',
				'selector' => '[data-et-multi-view]',
			),

			// Theme: my-listing
			array(
				'attr'     => ':choices',
				'selector' => 'order-filter[:choices]',
			),
			array(
				'attr'     => ':choices',
				'selector' => 'checkboxes-filter[:choices]',
			),

			// Plugin: WooCommerce
			array(
				'attr'     => 'data-wc-context',
				'selector' => '[data-wc-context]',
			),
		)
	);
}


/**
 * Get attributes and selectors of texts in attribute to translate
 *
 * @return array
 */
function wplng_data_attr_text_to_translate() {
	return apply_filters(
		'wplng_attr_text_to_translate',
		array(
			// Default tags
			array(
				'attr'     => 'alt',
				'selector' => '[alt]',
			),
			array(
				'attr'     => 'title',
				'selector' => '[title]',
			),
			array(
				'attr'     => 'placeholder',
				'selector' => '[placeholder]',
			),
			array(
				'attr'     => 'label',
				'selector' => '[label]',
			),
			array(
				'attr'     => 'aria-label',
				'selector' => '[aria-label]',
			),
			array(
				'attr'     => 'value',
				'selector' => '[type="submit"]',
			),
			array(
				'attr'     => 'value',
				'selector' => 'input[type="button"]',
			),

			// WordPress
			array(
				'attr'     => 'content',
				'selector' => 'meta[property="article:tag"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[property="article:section"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="description"]',
			),

			// Open Graph
			array(
				'attr'     => 'content',
				'selector' => 'meta[property="og:title"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[property="og:description"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[property="og:site_name"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[property="og:image:alt"]',
			),

			// Dublin Core
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="dc.title"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="dc.description"]',
			),

			// Twitter
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="twitter:title"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="twitter:description"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="twitter:label1"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="twitter:data1"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="twitter:label2"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="twitter:data2"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="twitter:image:alt"]',
			),

			// Plugin: Fluent Forms tooltips
			array(
				'attr'     => 'data-content',
				'selector' => '.ff-el-tooltip[data-content]',
			),

			// Plugin: Forminator
			array(
				'attr'     => 'data-placeholder',
				'selector' => '[data-placeholder]',
			),
			array(
				'attr'     => 'data-search-placeholde',
				'selector' => '[data-search-placeholde]',
			),

			// Plugin: Smart Slider 3
			array(
				'attr'     => 'data-title',
				'selector' => '[data-title]',
			),

			// Plugin: WooCommerce
			array(
				'attr'     => 'data-order_button_text',
				'selector' => '[data-order_button_text]',
			),
		)
	);
}


/**
 * Get attributes and selectors of HTML in attribute to translate
 *
 * @return array
 */
function wplng_data_attr_html_to_translate() {
	return apply_filters(
		'wplng_attr_html_to_translate',
		array(
			array(
				'attr'     => 'data-sub-html',
				'selector' => '[data-sub-html]',
			),
		)
	);
}


/**
 * Get attributes and selectors of URL to translate
 *
 * @return array
 */
function wplng_data_attr_url_to_translate() {
	return apply_filters(
		'wplng_attr_url_to_translate',
		array(
			array(
				'attr'     => 'href',
				'selector' => 'a[href]',
			),
			array(
				'attr'     => 'action',
				'selector' => 'form[action]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[property="og:url"]',
			),
			array(
				'attr'     => 'href',
				'selector' => 'link[rel="canonical"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="dc.relation"]',
			),
			array(
				'attr'     => 'src',
				'selector' => 'img[src]',
			),
			array(
				'attr'     => 'src',
				'selector' => 'iframe[src]',
			),
			array(
				'attr'     => 'src',
				'selector' => 'video source[src]',
			),

			// Plugin: Divi Supreme
			array(
				'attr'     => 'data-mfp-src',
				'selector' => '[data-mfp-src]',
			),
		)
	);
}


/**
 * GEt attributes and selector of elements where translate language ID
 *
 * @return array
 */
function wplng_data_attr_lang_id_to_replace() {
	return apply_filters(
		'wplng_attr_lang_id_to_replace',
		array(
			array(
				'attr'     => 'lang',
				'selector' => 'html',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[property="og:locale"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="dc.language"]',
			),
		)
	);
}
