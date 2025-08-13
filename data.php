<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


// ------------------------------------------------------------------------
// Data : Parse and translate
// ------------------------------------------------------------------------

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
		'#error-page',
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


/**
 * Get JSON elements to translate
 *
 * @return array
 */
function wplng_data_json_to_translate() {
	return apply_filters(
		'wplng_json_to_translate',
		array(
			// Theme: Divi
			array( 'DIVI', 'item_count' ),
			array( 'DIVI', 'items_count' ),
			array( 'et_pb_custom', 'subscription_failed' ),
			array( 'et_pb_custom', 'fill_message' ),
			array( 'et_pb_custom', 'contact_error_message' ),
			array( 'et_pb_custom', 'invalid' ),
			array( 'et_pb_custom', 'captcha' ),
			array( 'et_pb_custom', 'prev' ),
			array( 'et_pb_custom', 'previous' ),
			array( 'et_pb_custom', 'next' ),
			array( 'et_pb_custom', 'wrong_captcha' ),
			array( 'et_pb_custom', 'wrong_checkbox' ),

			// Plugin: WooCommerce
			array( 'wc_add_to_cart_params', 'i18n_view_cart' ),
			array( 'wc_country_select_params', 'i18n_select_state_text' ),
			array( 'wc_country_select_params', 'i18n_no_matches' ),
			array( 'wc_country_select_params', 'i18n_ajax_error' ),
			array( 'wc_country_select_params', 'i18n_input_too_short_1' ),
			array( 'wc_country_select_params', 'i18n_input_too_short_n' ),
			array( 'wc_country_select_params', 'i18n_input_too_long_1' ),
			array( 'wc_country_select_params', 'i18n_input_too_long_n' ),
			array( 'wc_country_select_params', 'i18n_selection_too_long_1' ),
			array( 'wc_country_select_params', 'i18n_selection_too_long_n' ),
			array( 'wc_country_select_params', 'i18n_load_more' ),
			array( 'wc_country_select_params', 'i18n_searching' ),
			array( 'wc_address_i18n_params', 'i18n_required_text' ),
			array( 'wc_address_i18n_params', 'i18n_optional_text' ),

			// Plugin: YITH
			array( 'yith_wcwl_l10n', 'labels', 'cookie_disabled' ),

			// Plugin: WF Cookie Consent
			array( 'wfCookieConsentSettings', 'wf_cookietext' ),
			array( 'wfCookieConsentSettings', 'wf_dismisstext' ),
			array( 'wfCookieConsentSettings', 'wf_linktext' ),

			// Plugin: complianz
			array( 'complianz', 'categories', 'statistics' ),
			array( 'complianz', 'categories', 'marketing' ),
			array( 'complianz', 'placeholdertext' ),
			array( 'complianz', 'page_links', 'eu', 'privacy-statement', 'title' ),
			array( 'complianz', 'aria_label' ),

			// Plugin: ultimate-post-kit
			array( 'UltimatePostKitConfig', 'mailchimp', 'subscribing' ),

			// Plugin: royal-elementor-addons
			array( 'WprConfig', 'addedToCartText' ),
			array( 'WprConfig', 'viewCart' ),
			array( 'WprConfig', 'chooseQuantityText' ),
			array( 'WprConfig', 'input_empty' ),
			array( 'WprConfig', 'select_empty' ),
			array( 'WprConfig', 'file_empty' ),
			array( 'WprConfig', 'recaptcha_error' ),
			array( 'WprConfig', 'recaptcha_error' ),

			// Plugin: WP Grid Builder
			array( 'wpgb_settings', 'resultMsg', 'plural' ),
			array( 'wpgb_settings', 'resultMsg', 'singular' ),
			array( 'wpgb_settings', 'resultMsg', 'none' ),

			array( 'wpgb_settings', 'lightbox', 'errorMsg' ),
			array( 'wpgb_settings', 'lightbox', 'prevLabel' ),
			array( 'wpgb_settings', 'lightbox', 'nextLabel' ),
			array( 'wpgb_settings', 'lightbox', 'closeLabel' ),

			array( 'wpgb_settings', 'combobox', 'search' ),
			array( 'wpgb_settings', 'combobox', 'loading' ),
			array( 'wpgb_settings', 'combobox', 'cleared' ),
			array( 'wpgb_settings', 'combobox', 'expanded' ),
			array( 'wpgb_settings', 'combobox', 'noResults' ),
			array( 'wpgb_settings', 'combobox', 'collapsed' ),
			array( 'wpgb_settings', 'combobox', 'toggleLabel' ),
			array( 'wpgb_settings', 'combobox', 'clearLabel' ),
			array( 'wpgb_settings', 'combobox', 'selected' ),
			array( 'wpgb_settings', 'combobox', 'deselected' ),

			array( 'wpgb_settings', 'autocomplete', 'open' ),
			array( 'wpgb_settings', 'autocomplete', 'input' ),
			array( 'wpgb_settings', 'autocomplete', 'clear' ),
			array( 'wpgb_settings', 'autocomplete', 'noResults' ),
			array( 'wpgb_settings', 'autocomplete', 'loading' ),
			array( 'wpgb_settings', 'autocomplete', 'clearLabel' ),
			array( 'wpgb_settings', 'autocomplete', 'select' ),

			array( 'wpgb_settings', 'range', 'minLabel' ),
			array( 'wpgb_settings', 'range', 'maxLabel' ),
		)
	);
}


/**
 * Get JSON to exclude from translation
 *
 * @return array
 */
function wplng_data_excluded_json() {
	return apply_filters(
		'wplng_excluded_json',
		array(
			// wpLingua: Ajax edit modal
			array( 'data', 'wplng_edit_html' ),
			array( 'wplngI18nTranslation' ),
			array( 'wplngI18nSlug' ),
			array( 'wplngI18nGutenberg' ),

			// Plugin: WooCommerce
			array( 'wc_country_select_params', 'countries' ),

			// Plugin: Google Site Kit
			array( '_googlesitekitBaseData' ),
		)
	);
}


/**
 * Get AJAX action to exclude from translation
 *
 * @return array
 */
function wplng_data_excluded_ajax_action() {
	return apply_filters(
		'wplng_excluded_ajax_action',
		array(
			// WordPress
			'heartbeat',

			// Plugin: wpLingua
			'wplng_ajax_heartbeat',
			'wplng_ajax_translation',
			'wplng_ajax_edit_modal',
			'wplng_ajax_save_modal',
			'wplng_ajax_slug',
		)
	);
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


// ------------------------------------------------------------------------
// Data : Switcher options
// ------------------------------------------------------------------------

/**
 * Get options for switcher insertion
 *
 * @return array
 */
function wplng_data_switcher_valid_insert() {
	return array(
		'bottom-right'  => __( 'Bottom right', 'wplingua' ),
		'bottom-center' => __( 'Bottom center', 'wplingua' ),
		'bottom-left'   => __( 'Bottom left', 'wplingua' ),
		'none'          => __( 'None', 'wplingua' ),
	);
}


/**
 * Get options for switcher themes
 *
 * @return array
 */
function wplng_data_switcher_valid_theme() {
	return array(
		'light-double-smooth'     => __( 'Light - Double - Smooth', 'wplingua' ),
		'light-double-square'     => __( 'Light - Double - Square', 'wplingua' ),
		'light-simple-smooth'     => __( 'Light - Simple - Smooth', 'wplingua' ),
		'light-simple-square'     => __( 'Light - Simple - Square', 'wplingua' ),
		'grey-double-smooth'      => __( 'Grey - Double - Smooth', 'wplingua' ),
		'grey-double-square'      => __( 'Grey - Double - Square', 'wplingua' ),
		'grey-simple-smooth'      => __( 'Grey - Simple - Smooth', 'wplingua' ),
		'grey-simple-square'      => __( 'Grey - Simple - Square', 'wplingua' ),
		'dark-double-smooth'      => __( 'Dark - Double - Smooth', 'wplingua' ),
		'dark-double-square'      => __( 'Dark - Double - Square', 'wplingua' ),
		'dark-simple-smooth'      => __( 'Dark - Simple - Smooth', 'wplingua' ),
		'dark-simple-square'      => __( 'Dark - Simple - Square', 'wplingua' ),
		'blurblack-double-smooth' => __( 'Blur Black - Double - Smooth', 'wplingua' ),
		'blurblack-double-square' => __( 'Blur Black - Double - Square', 'wplingua' ),
		'blurblack-simple-smooth' => __( 'Blur Black - Simple - Smooth', 'wplingua' ),
		'blurblack-simple-square' => __( 'Blur Black - Simple - Square', 'wplingua' ),
		'blurwhite-double-smooth' => __( 'Blur White - Double - Smooth', 'wplingua' ),
		'blurwhite-double-square' => __( 'Blur White - Double - Square', 'wplingua' ),
		'blurwhite-simple-smooth' => __( 'Blur White - Simple - Smooth', 'wplingua' ),
		'blurwhite-simple-square' => __( 'Blur White - Simple - Square', 'wplingua' ),
	);
}


/**
 * Get options for switcher style
 *
 * @return array
 */
function wplng_data_switcher_valid_style() {
	return array(
		'list'     => __( 'Inline list', 'wplingua' ),
		'block'    => __( 'Block', 'wplingua' ),
		'dropdown' => __( 'Dropdown', 'wplingua' ),
	);
}


/**
 * Get options for switcher name format
 *
 * @return array
 */
function wplng_data_switcher_valid_name_format() {
	return array(
		'original' => __( 'Original name', 'wplingua' ),
		'name'     => __( 'Translated name', 'wplingua' ),
		'id'       => __( 'Language ID', 'wplingua' ),
		'none'     => __( 'No display', 'wplingua' ),
	);
}


/**
 * Get options for switcher flags style
 *
 * @return array
 */
function wplng_data_switcher_valid_flags_style() {
	return array(
		'circle'      => __( 'Circle', 'wplingua' ),
		'rectangular' => __( 'Rectangular', 'wplingua' ),
		'wave'        => __( 'Wave', 'wplingua' ),
		'none'        => __( 'No display', 'wplingua' ),
	);
}


// ------------------------------------------------------------------------
// Data : Switcher nav menu options
// ------------------------------------------------------------------------

/**
 * Get options for switcher name format in nav menu
 *
 * @return array
 */
function wplng_data_switcher_nav_menu_valid_name_format() {
	return array(
		'o' => __( 'Original name', 'wplingua' ),
		't' => __( 'Translated name', 'wplingua' ),
		'i' => __( 'Language ID', 'wplingua' ),
		'n' => __( 'No display', 'wplingua' ),
	);
}


/**
 * Get options for switcher flags style in nav menu
 *
 * @return array
 */
function wplng_data_switcher_nav_menu_valid_flags_style() {
	return array(
		'y' => __( 'Display', 'wplingua' ),
		'n' => __( 'No display', 'wplingua' ),
	);
}


/**
 * Get options for switcher layout in nav menu
 *
 * @return array
 */
function wplng_data_switcher_nav_menu_valid_layout() {
	return array(
		't' => __( 'List without current language', 'wplingua' ),
		'l' => __( 'List without active class', 'wplingua' ),
		'a' => __( 'List with active class', 'wplingua' ),
		's' => __( 'Sub-list', 'wplingua' ),
	);
}


// ------------------------------------------------------------------------
// Data : Languages
// ------------------------------------------------------------------------

/**
 * Get all languages ID
 *
 * @return array Languages ID
 */
function wplng_data_languages_id() {
	return array(
		'ar', // Arabic
		'da', // Danish
		'de', // German
		'en', // English
		'el', // Greek
		'es', // Spanish
		'fi', // Finnish
		'fr', // French
		'hi', // Hindi
		'hu', // Hungarian
		'id', // Indonesian
		'it', // Italian
		'he', // Hebrew
		'ja', // Japanese
		'ko', // Korean
		'nl', // Dutch
		'pl', // Polish
		'pt', // Portuguese
		'ru', // Russian
		'sk', // Slovak
		'sv', // Swedish
		'zh', // Chinese
		'tr', // Turkish
		'uk', // Ukrainian
		'vi', // Vietnamese
	);
}


/**
 * Get all languages data
 *
 * @return array Languages data
 */
function wplng_data_languages() {
	return array(
		array(
			'name'             => __( 'Arabic', 'wplingua' ),
			'id'               => 'ar',
			'dir'              => 'rtl',
			'flag'             => '_a',
			'flags'            => array(
				array(
					'name' => __( 'Global', 'wplingua' ),
					'id'   => '_a',
					'flag' => '_a',
				),
				array(
					'name' => __( 'Egypt', 'wplingua' ),
					'id'   => 'eg',
					'flag' => 'eg',
				),
				array(
					'name' => __( 'Saudi Arabia', 'wplingua' ),
					'id'   => 'sa',
					'flag' => 'sa',
				),
				array(
					'name' => __( 'Algeria', 'wplingua' ),
					'id'   => 'dz',
					'flag' => 'dz',
				),
				array(
					'name' => __( 'Bahrain', 'wplingua' ),
					'id'   => 'bh',
					'flag' => 'bh',
				),
				array(
					'name' => __( 'Chad', 'wplingua' ),
					'id'   => 'td',
					'flag' => 'td',
				),
				array(
					'name' => __( 'Comoros', 'wplingua' ),
					'id'   => 'km',
					'flag' => 'km',
				),
				array(
					'name' => __( 'Djibouti', 'wplingua' ),
					'id'   => 'dj',
					'flag' => 'dj',
				),
				array(
					'name' => __( 'Iraq', 'wplingua' ),
					'id'   => 'iq',
					'flag' => 'iq',
				),
				array(
					'name' => __( 'Jordan', 'wplingua' ),
					'id'   => 'jo',
					'flag' => 'jo',
				),
				array(
					'name' => __( 'Kuwait', 'wplingua' ),
					'id'   => 'kw',
					'flag' => 'kw',
				),
				array(
					'name' => __( 'Lebanon', 'wplingua' ),
					'id'   => 'lb',
					'flag' => 'lb',
				),
				array(
					'name' => __( 'Libya', 'wplingua' ),
					'id'   => 'ly',
					'flag' => 'ly',
				),
				array(
					'name' => __( 'Mauritania', 'wplingua' ),
					'id'   => 'mr',
					'flag' => 'mr',
				),
				array(
					'name' => __( 'Morocco', 'wplingua' ),
					'id'   => 'ma',
					'flag' => 'ma',
				),
				array(
					'name' => __( 'Oman', 'wplingua' ),
					'id'   => 'om',
					'flag' => 'om',
				),
				array(
					'name' => __( 'Palestine', 'wplingua' ),
					'id'   => 'ps',
					'flag' => 'ps',
				),
				array(
					'name' => __( 'Qatar', 'wplingua' ),
					'id'   => 'qa',
					'flag' => 'qa',
				),
				array(
					'name' => __( 'Somalia', 'wplingua' ),
					'id'   => 'so',
					'flag' => 'so',
				),
				array(
					'name' => __( 'Sudan', 'wplingua' ),
					'id'   => 'sd',
					'flag' => 'sd',
				),
				array(
					'name' => __( 'Syria', 'wplingua' ),
					'id'   => 'sy',
					'flag' => 'sy',
				),
				array(
					'name' => __( 'Tunisia', 'wplingua' ),
					'id'   => 'tn',
					'flag' => 'tn',
				),
				array(
					'name' => __( 'United Arab Emirates', 'wplingua' ),
					'id'   => 'ae',
					'flag' => 'ae',
				),
				array(
					'name' => __( 'Yemen', 'wplingua' ),
					'id'   => 'ye',
					'flag' => 'ye',
				),
			),
			'name_translation' => array(
				'ar' => 'العربية',
				'da' => 'Arabisk',
				'de' => 'Arabisch',
				'en' => 'Arabic',
				'es' => 'Árabe',
				'fi' => 'Arabia',
				'fr' => 'Arabe',
				'el' => 'Αραβικά',
				'hi' => 'अरबी',
				'hu' => 'Arab',
				'id' => 'Arab',
				'it' => 'Araba',
				'he' => 'עֲרָבִית',
				'ja' => 'アラビア語',
				'ko' => '아랍어',
				'nl' => 'Arabisch',
				'pl' => 'Arabski',
				'pt' => 'Árabe',
				'ru' => 'Арабский',
				'se' => 'Arabiska',
				'sk' => 'Arabčina',
				'zh' => '阿拉伯',
				'tr' => 'Arapça',
				'uk' => 'Арабський',
				'vi' => 'Ả Rập',
			),
		),
		array(
			'name'             => __( 'Chinese', 'wplingua' ),
			'id'               => 'zh',
			'flag'             => '_c',
			'flags'            => array(
				array(
					'name' => __( 'Global', 'wplingua' ),
					'id'   => '_c',
					'flag' => '_c',
				),
				array(
					'name' => __( 'China', 'wplingua' ),
					'id'   => 'cn',
					'flag' => 'cn',
				),
				array(
					'name' => __( 'Hong Kong', 'wplingua' ),
					'id'   => 'hk',
					'flag' => 'hk',
				),
				array(
					'name' => __( 'Malaysia', 'wplingua' ),
					'id'   => 'my',
					'flag' => 'my',
				),
				array(
					'name' => __( 'Singapore', 'wplingua' ),
					'id'   => 'sg',
					'flag' => 'sg',
				),
			),
			'name_translation' => array(
				'ar' => 'الصينية',
				'da' => 'Kinesisk',
				'de' => 'Chinesisch',
				'en' => 'Chinese',
				'es' => 'Chino',
				'fi' => 'Kiinalainen',
				'fr' => 'Chinois',
				'el' => 'Κινέζικα',
				'hi' => 'चीनी',
				'hu' => 'Kínai',
				'id' => 'Cina',
				'it' => 'Cinese',
				'he' => 'סִינִית',
				'ja' => '中国語',
				'ko' => '중국어',
				'nl' => 'Chinees',
				'pl' => 'Chiński',
				'pt' => 'Chinês',
				'ru' => 'Китайский',
				'se' => 'Kinesiska',
				'sk' => 'Čínština',
				'zh' => '中文',
				'tr' => 'Çince',
				'uk' => 'Китайський',
				'vi' => 'Trung',
			),
		),
		array(
			'name'             => __( 'Danish', 'wplingua' ),
			'id'               => 'da',
			'flag'             => 'dk',
			'flags'            => array(
				array(
					'name' => __( 'Denmark', 'wplingua' ),
					'id'   => 'dk',
					'flag' => 'dk',
				),
			),
			'name_translation' => array(
				'ar' => 'الدنماركية',
				'da' => 'Dansk',
				'de' => 'Dänisch',
				'en' => 'Danish',
				'es' => 'Danés',
				'fi' => 'Tanskalainen',
				'fr' => 'Danois',
				'el' => 'Δανικά',
				'hi' => 'दानिश',
				'hu' => 'Dán',
				'id' => 'Dansk',
				'it' => 'Danese',
				'he' => 'דַנִי',
				'ja' => 'デンマーク語',
				'ko' => '덴마크어',
				'nl' => 'Deens',
				'pl' => 'Duński',
				'pt' => 'Dinamarquesa',
				'ru' => 'Датский',
				'se' => 'Danska',
				'sk' => 'Dánčina',
				'zh' => '丹麦语',
				'tr' => 'Danca',
				'uk' => 'Датський',
				'vi' => 'Đan Mạch',
			),
		),
		array(
			'name'             => __( 'Dutch', 'wplingua' ),
			'id'               => 'nl',
			'flag'             => 'nl',
			'flags'            => array(
				array(
					'name' => __( 'Netherlands', 'wplingua' ),
					'id'   => 'nl',
					'flag' => 'nl',
				),
				array(
					'name' => __( 'Belgium', 'wplingua' ),
					'id'   => 'be',
					'flag' => 'be',
				),
				array(
					'name' => __( 'Suriname', 'wplingua' ),
					'id'   => 'sr',
					'flag' => 'sr',
				),
			),
			'name_translation' => array(
				'ar' => 'الهولندية',
				'da' => 'Hollandsk',
				'de' => 'Niederländisch',
				'en' => 'Dutch',
				'es' => 'Holandés',
				'fi' => 'Hollantilainen',
				'fr' => 'Néerlandais',
				'el' => 'Ολλανδικά',
				'hi' => 'डच',
				'hu' => 'Holland',
				'id' => 'Belanda',
				'it' => 'Olandese',
				'he' => 'הוֹלַנדִי',
				'ja' => 'オランダ語',
				'ko' => '네덜란드어',
				'nl' => 'Nederlands',
				'pl' => 'Holenderski',
				'pt' => 'Holandês',
				'ru' => 'Голландий',
				'se' => 'Nederländska',
				'sk' => 'Holandčina',
				'zh' => '荷兰语',
				'tr' => 'Hollandaca',
				'uk' => 'Голландський',
				'vi' => 'Hà Lan',
			),
		),
		array(
			'name'             => __( 'Slovak', 'wplingua' ),
			'id'               => 'sk',
			'flag'             => 'sk',
			'flags'            => array(
				array(
					'name' => __( 'Slovakia', 'wplingua' ),
					'id'   => 'sk',
					'flag' => 'sk',
				),
				array(
					'name' => __( 'Czech Republic', 'wplingua' ),
					'id'   => 'cz',
					'flag' => 'cz',
				),
			),
			'name_translation' => array(
				'ar' => 'السلوفاكية',
				'da' => 'Slovakisk',
				'de' => 'Slowakisch',
				'en' => 'Slovak',
				'es' => 'Eslovaquia',
				'fi' => 'Slovakian',
				'fr' => 'Slovaque',
				'el' => 'Σλοβακία',
				'hi' => 'स्लोवाक',
				'hu' => 'Szlovák',
				'id' => 'Slowakia',
				'it' => 'Slovacco',
				'he' => 'סלובקית',
				'ja' => 'スロバキア語',
				'ko' => '슬로바키아어',
				'nl' => 'Slowaaks',
				'pl' => 'Słowacki',
				'pt' => 'Eslovaco',
				'ru' => 'Словацкий',
				'se' => 'Slovakiska',
				'sk' => 'Slovenská',
				'zh' => '斯洛伐克语',
				'tr' => 'Slovakça',
				'uk' => 'Словацька',
				'vi' => 'Slovak',
			),
		),
		array(
			'name'             => __( 'English', 'wplingua' ),
			'id'               => 'en',
			'flag'             => '_e',
			'flags'            => array(
				array(
					'name' => __( 'US / GB', 'wplingua' ),
					'id'   => '_e',
					'flag' => '_e',
				),
				array(
					'name' => __( 'United Kingdom', 'wplingua' ),
					'id'   => 'gb',
					'flag' => 'gb',
				),
				array(
					'name' => __( 'United States', 'wplingua' ),
					'id'   => 'us',
					'flag' => 'us',
				),
				array(
					'name' => __( 'Australia', 'wplingua' ),
					'id'   => 'au',
					'flag' => 'au',
				),
				array(
					'name' => __( 'Canada', 'wplingua' ),
					'id'   => 'ca',
					'flag' => 'ca',
				),
				array(
					'name' => __( 'Ireland', 'wplingua' ),
					'id'   => 'ie',
					'flag' => 'ie',
				),
				array(
					'name' => __( 'New Zealand', 'wplingua' ),
					'id'   => 'nz',
					'flag' => 'nz',
				),
				array(
					'name' => __( 'Nigeria', 'wplingua' ),
					'id'   => 'ng',
					'flag' => 'ng',
				),
				array(
					'name' => __( 'South Africa', 'wplingua' ),
					'id'   => 'za',
					'flag' => 'za',
				),
				array(
					'name' => __( 'Kenya', 'wplingua' ),
					'id'   => 'ke',
					'flag' => 'ke',
				),
				array(
					'name' => __( 'Ghana', 'wplingua' ),
					'id'   => 'gh',
					'flag' => 'gh',
				),
				array(
					'name' => __( 'South Sudan', 'wplingua' ),
					'id'   => 'ss',
					'flag' => 'ss',
				),
				array(
					'name' => __( 'Sierra Leone', 'wplingua' ),
					'id'   => 'sl',
					'flag' => 'sl',
				),
				array(
					'name' => __( 'Singapore', 'wplingua' ),
					'id'   => 'sg',
					'flag' => 'sg',
				),
				array(
					'name' => __( 'Liberia', 'wplingua' ),
					'id'   => 'lr',
					'flag' => 'lr',
				),
				array(
					'name' => __( 'Jamaica', 'wplingua' ),
					'id'   => 'jm',
					'flag' => 'jm',
				),
			),
			'name_translation' => array(
				'ar' => 'الإنجليزية',
				'da' => 'Engelsk',
				'de' => 'Englisch',
				'en' => 'English',
				'es' => 'Inglés',
				'fi' => 'Englanti',
				'fr' => 'Anglais',
				'el' => 'Αγγλικά',
				'hi' => 'अंग्रेज़ी',
				'hu' => 'Angol',
				'id' => 'Inggris',
				'it' => 'Inglese',
				'he' => 'אנגלית',
				'ja' => '英語',
				'ko' => '영어',
				'nl' => 'Engels',
				'pl' => 'Angielski',
				'pt' => 'Inglês',
				'ru' => 'Английский',
				'se' => 'Engelska',
				'sk' => 'Angličtina',
				'zh' => '英语',
				'tr' => 'İngilizce',
				'uk' => 'Англійський',
				'vi' => 'Anh',
			),
		),
		array(
			'name'             => __( 'Finnish', 'wplingua' ),
			'id'               => 'fi',
			'flag'             => 'fi',
			'flags'            => array(
				array(
					'name' => __( 'Finland', 'wplingua' ),
					'id'   => 'fi',
					'flag' => 'fi',
				),
			),
			'name_translation' => array(
				'ar' => 'الفنلندية',
				'da' => 'Finsk',
				'de' => 'Finnisch',
				'en' => 'Finnish',
				'es' => 'Finlandés',
				'fi' => 'Suomi',
				'fr' => 'Finlandais',
				'el' => 'Φινλανδική',
				'hi' => 'फिनिश',
				'hu' => 'Finn',
				'id' => 'Finlandia',
				'it' => 'Finlandese',
				'he' => 'פִינִית',
				'ja' => 'フィンランド語 ',
				'ko' => '핀란드어',
				'nl' => 'Fins',
				'pl' => 'Fiński',
				'pt' => 'Finlandês',
				'ru' => 'Финский',
				'se' => 'Finska',
				'sk' => 'Fínska',
				'zh' => '芬兰语',
				'tr' => 'Fince',
				'uk' => 'Фінський',
				'vi' => 'Phần Lan',
			),
		),
		array(
			'name'             => __( 'French', 'wplingua' ),
			'id'               => 'fr',
			'flag'             => 'fr',
			'flags'            => array(
				array(
					'name' => __( 'France', 'wplingua' ),
					'id'   => 'fr',
					'flag' => 'fr',
				),
				array(
					'name' => __( 'Belgium', 'wplingua' ),
					'id'   => 'be',
					'flag' => 'be',
				),
				array(
					'name' => __( 'Quebec', 'wplingua' ),
					'id'   => '_q',
					'flag' => '_q',
				),
				array(
					'name' => __( 'Canada', 'wplingua' ),
					'id'   => 'ca',
					'flag' => 'ca',
				),
				array(
					'name' => __( 'Congo - Kinshasa', 'wplingua' ),
					'id'   => 'cd',
					'flag' => 'cd',
				),
				array(
					'name' => __( 'Congo - Brazzaville', 'wplingua' ),
					'id'   => 'cg',
					'flag' => 'cg',
				),
				array(
					'name' => __( 'Cameroon', 'wplingua' ),
					'id'   => 'cm',
					'flag' => 'cm',
				),
				array(
					'name' => __( 'Ivory Coast', 'wplingua' ),
					'id'   => 'ci',
					'flag' => 'ci',
				),
				array(
					'name' => __( 'Switzerland', 'wplingua' ),
					'id'   => 'ch',
					'flag' => 'ch',
				),
			),
			'name_translation' => array(
				'ar' => 'الفرنسية',
				'da' => 'Fransk',
				'de' => 'Französisch',
				'en' => 'French',
				'es' => 'Francés',
				'fi' => 'Ranskan',
				'fr' => 'Français',
				'el' => 'Γαλλικά',
				'hi' => 'फ़्रेंच',
				'hu' => 'Francia',
				'id' => 'Prancis',
				'it' => 'Francese',
				'he' => 'צָרְפָתִית',
				'ja' => 'フランス語',
				'ko' => '프랑스어',
				'nl' => 'Frans',
				'pl' => 'Francuski',
				'pt' => 'Francês',
				'ru' => 'Французский',
				'se' => 'Franska',
				'sk' => 'Francúzština',
				'zh' => '法语',
				'tr' => 'Fransızca',
				'uk' => 'Французький',
				'vi' => 'Pháp',
			),
		),
		array(
			'name'             => __( 'German', 'wplingua' ),
			'id'               => 'de',
			'flag'             => 'de',
			'flags'            => array(
				array(
					'name' => __( 'Germany', 'wplingua' ),
					'id'   => 'de',
					'flag' => 'de',
				),
				array(
					'name' => __( 'Austria', 'wplingua' ),
					'id'   => 'at',
					'flag' => 'at',
				),
				array(
					'name' => __( 'Switzerland', 'wplingua' ),
					'id'   => 'ch',
					'flag' => 'ch',
				),
				array(
					'name' => __( 'Liechtenstein', 'wplingua' ),
					'id'   => 'li',
					'flag' => 'li',
				),
			),
			'name_translation' => array(
				'ar' => 'الألمانية',
				'da' => 'Tysk',
				'de' => 'Deutsch',
				'en' => 'German',
				'es' => 'Alemán',
				'fi' => 'Saksan',
				'fr' => 'Allemand',
				'el' => 'Γερμανικά',
				'hi' => 'जर्मन',
				'hu' => 'Német',
				'id' => 'Jerman',
				'it' => 'Tedesco',
				'he' => 'גֶרמָנִיָת',
				'ja' => 'ドイツ語',
				'ko' => '독일어',
				'nl' => 'Duits',
				'pl' => 'Niemiecki',
				'pt' => 'Alemão',
				'ru' => 'Немецкий',
				'se' => 'Tyska',
				'sk' => 'Nemčina',
				'zh' => '德国',
				'tr' => 'Almanca',
				'uk' => 'Німецький',
				'vi' => 'Đức',
			),
		),
		array(
			'name'             => __( 'Greek', 'wplingua' ),
			'id'               => 'el',
			'flag'             => 'gr',
			'flags'            => array(
				array(
					'name' => __( 'Greece', 'wplingua' ),
					'id'   => 'gr',
					'flag' => 'gr',
				),
				array(
					'name' => __( 'Cyprus', 'wplingua' ),
					'id'   => 'cy',
					'flag' => 'cy',
				),
			),
			'name_translation' => array(
				'ar' => 'اليونانية',
				'da' => 'Græsk',
				'de' => 'Griechisch',
				'en' => 'Greek',
				'es' => 'Griego',
				'fi' => 'Kreikkalainen',
				'fr' => 'Grecque',
				'el' => 'Ελληνική',
				'hi' => 'यूनानी',
				'hu' => 'Görög',
				'id' => 'Yunani',
				'it' => 'Greco',
				'he' => 'יווני',
				'ja' => 'ギリシャ語',
				'ko' => '그리스어',
				'nl' => 'Grieks',
				'pl' => 'Grecki',
				'pt' => 'Grego',
				'ru' => 'Греческий',
				'se' => 'Grekiska',
				'sk' => 'Gréčtina',
				'zh' => '希腊文',
				'tr' => 'Yunanca',
				'uk' => 'Грецький',
				'vi' => 'Hy Lạp',
			),
		),
		array(
			'name'             => __( 'Hebrew', 'wplingua' ),
			'id'               => 'he',
			'dir'              => 'rtl',
			'flag'             => 'il',
			'flags'            => array(
				array(
					'name' => __( 'Israel', 'wplingua' ),
					'id'   => 'il',
					'flag' => 'il',
				),
			),
			'name_translation' => array(
				'ar' => 'العبرية',
				'da' => 'Hebræisk',
				'de' => 'Hebräisch',
				'en' => 'Hebrew',
				'es' => 'Hebreo',
				'fi' => 'Heprea',
				'fr' => 'Hébreu',
				'el' => 'Εβραϊκά',
				'hi' => 'यहूदी',
				'hu' => 'Héber',
				'id' => 'Ibrani',
				'it' => 'Ebraica',
				'he' => 'עִברִית',
				'ja' => 'ヘブライ語',
				'ko' => '히브리어',
				'nl' => 'Hebreeuws',
				'pl' => 'Hebrajski',
				'pt' => 'Hebraico',
				'ru' => 'Еврейский',
				'se' => 'Hebreiska',
				'sk' => 'Hebrejčina',
				'zh' => '希伯来文',
				'tr' => 'İbranice',
				'uk' => 'Єврейський',
				'vi' => 'Do Thái',
			),
		),
		array(
			'name'             => __( 'Hindi', 'wplingua' ),
			'id'               => 'hi',
			'flag'             => 'in',
			'flags'            => array(
				array(
					'name' => __( 'India', 'wplingua' ),
					'id'   => 'in',
					'flag' => 'in',
				),
				array(
					'name' => __( 'Pakistan', 'wplingua' ),
					'id'   => 'pk',
					'flag' => 'pk',
				),
			),
			'name_translation' => array(
				'ar' => 'الهندية',
				'da' => 'Hindi',
				'de' => 'Hindi',
				'en' => 'Hindi',
				'es' => 'Hindi',
				'fi' => 'Hindi',
				'fr' => 'Hindi',
				'el' => 'Χίντι',
				'hi' => 'हिंदी',
				'hu' => 'Hindi',
				'id' => 'Hindi',
				'it' => 'Hindi',
				'he' => 'הינדי',
				'ja' => 'ヒンディー語',
				'ko' => '힌디어',
				'nl' => 'Hindi',
				'pl' => 'Hindi',
				'pt' => 'Hindi',
				'ru' => 'Хинди',
				'se' => 'Hindi',
				'sk' => 'Hindčina',
				'zh' => '北印度语',
				'tr' => 'Hintçe',
				'uk' => 'Хінді',
				'vi' => 'Hin-ddi',
			),
		),
		array(
			'name'             => __( 'Hungarian', 'wplingua' ),
			'id'               => 'hu',
			'flag'             => 'hu',
			'flags'            => array(
				array(
					'name' => __( 'Hungary', 'wplingua' ),
					'id'   => 'hu',
					'flag' => 'hu',
				),
			),
			'name_translation' => array(
				'ar' => 'الهنغارية',
				'da' => 'Ungarsk',
				'de' => 'Ungarisch',
				'en' => 'Hungarian',
				'es' => 'Húngaro',
				'fi' => 'Unkari',
				'fr' => 'Hongrois',
				'el' => 'Ουγγρικό',
				'hi' => 'हंगेरी',
				'hu' => 'Magyar',
				'id' => 'Hongaria',
				'it' => 'Ungherese',
				'he' => 'הוּנגָרִי',
				'ja' => 'ハンガリー語',
				'ko' => '헝가리어',
				'nl' => 'Hongaars',
				'pl' => 'Węgierski',
				'pt' => 'Húngaro',
				'ru' => 'Венгерский',
				'se' => 'Ungerska',
				'sk' => 'Maďarčina',
				'zh' => '匈牙利语',
				'tr' => 'Macarca',
				'uk' => 'Угорський',
				'vi' => 'Hungary',
			),
		),
		array(
			'name'             => __( 'Indonesian', 'wplingua' ),
			'id'               => 'id',
			'flag'             => 'id',
			'flags'            => array(
				array(
					'name' => __( 'Indonesia', 'wplingua' ),
					'id'   => 'id',
					'flag' => 'id',
				),
			),
			'name_translation' => array(
				'ar' => 'الإندونيسية',
				'da' => 'Indonesisk',
				'de' => 'Indonesisch',
				'en' => 'Indonesian',
				'es' => 'Indonesia',
				'fi' => 'Indonesia',
				'fr' => 'Indonésien',
				'el' => 'Ινδονησιακή',
				'hi' => 'इन्डोनेशियाई',
				'hu' => 'Indonéz',
				'id' => 'Indonesia',
				'it' => 'Indonesiano',
				'he' => 'אינדונזית',
				'ja' => 'インドネシア語',
				'ko' => '인도네시아어',
				'nl' => 'Indonesisch',
				'pl' => 'Indonezyjski',
				'pt' => 'Indonésio',
				'ru' => 'Индонезийский',
				'se' => 'Indonesiska',
				'sk' => 'Indonézska',
				'zh' => '印尼语',
				'tr' => 'Endonezce',
				'uk' => 'Індонезійський',
				'vi' => 'Indonesia',
			),
		),
		array(
			'name'             => __( 'Italian', 'wplingua' ),
			'id'               => 'it',
			'flag'             => 'it',
			'flags'            => array(
				array(
					'name' => __( 'Italy', 'wplingua' ),
					'id'   => 'it',
					'flag' => 'it',
				),
			),
			'name_translation' => array(
				'ar' => 'الإيطالية',
				'da' => 'Italiensk',
				'de' => 'Italienisch',
				'en' => 'Italian',
				'es' => 'Italiano',
				'fi' => 'Italia kieli',
				'fr' => 'Italien',
				'el' => 'Ιταλικά',
				'hi' => 'इतालवी',
				'hu' => 'Olasz',
				'id' => 'Italia',
				'it' => 'Italiano',
				'he' => 'אִיטַלְקִית',
				'ja' => 'イタリア語',
				'ko' => '이탈리아어',
				'nl' => 'Italiaans',
				'pl' => 'Włoski',
				'pt' => 'Italiano',
				'ru' => 'Итальянский',
				'se' => 'Italienska',
				'sk' => 'Talianska',
				'zh' => '意大利',
				'tr' => 'İtalyanca',
				'uk' => 'Італійський',
				'vi' => 'Ý',
			),
		),
		array(
			'name'             => __( 'Japanese', 'wplingua' ),
			'id'               => 'ja',
			'flag'             => 'jp',
			'flags'            => array(
				array(
					'name' => __( 'Japan', 'wplingua' ),
					'id'   => 'jp',
					'flag' => 'jp',
				),
			),
			'name_translation' => array(
				'ar' => 'اليابانية',
				'da' => 'Japansk',
				'de' => 'Japanisch',
				'en' => 'Japanese',
				'es' => 'Japonés',
				'fi' => 'Japanilainen',
				'fr' => 'Japonais',
				'el' => 'Ιαπωνικά',
				'hi' => 'जापानी',
				'hu' => 'Japán',
				'id' => 'Jepang',
				'it' => 'Giapponese',
				'he' => 'יַפָּנִית',
				'ja' => '日本語',
				'ko' => '일본어',
				'nl' => 'Japans',
				'pl' => 'Japoński',
				'pt' => 'Japonês',
				'ru' => 'Японский',
				'se' => 'Japanska',
				'sk' => 'Japončina',
				'zh' => '日语',
				'tr' => 'Japonca',
				'uk' => 'Японський',
				'vi' => 'Nhật',
			),
		),
		array(
			'name'             => __( 'Korean', 'wplingua' ),
			'id'               => 'ko',
			'flag'             => 'kr',
			'flags'            => array(
				array(
					'name' => __( 'South Korea', 'wplingua' ),
					'id'   => 'kr',
					'flag' => 'kr',
				),
				array(
					'name' => __( 'North Korea', 'wplingua' ),
					'id'   => 'kp',
					'flag' => 'kp',
				),
			),
			'name_translation' => array(
				'ar' => 'الكورية',
				'da' => 'Koreansk',
				'de' => 'Koreanisch',
				'en' => 'Korean',
				'es' => 'Coreano',
				'fi' => 'Korean',
				'fr' => 'Coréen',
				'el' => 'Κορεάτικα',
				'hi' => 'कोरियाई',
				'hu' => 'Koreai',
				'id' => 'Korea',
				'it' => 'Coreano',
				'he' => 'קוריאנית',
				'ja' => '韓国語',
				'ko' => '한국어',
				'nl' => 'Koreaans',
				'pl' => 'Korejski',
				'pt' => 'Coreano',
				'ru' => 'Корейский',
				'se' => 'Koreanska',
				'sk' => 'Kórejčina',
				'zh' => '韩语',
				'tr' => 'Korece',
				'uk' => 'Корейський',
				'vi' => 'Hàn',
			),
		),
		array(
			'name'             => __( 'Polish', 'wplingua' ),
			'id'               => 'pl',
			'flag'             => 'pl',
			'flags'            => array(
				array(
					'name' => __( 'Poland', 'wplingua' ),
					'id'   => 'pl',
					'flag' => 'pl',
				),
			),
			'name_translation' => array(
				'ar' => 'البولندية',
				'da' => 'Polsk',
				'de' => 'Polnisch',
				'en' => 'Polish',
				'es' => 'Polaco',
				'fi' => 'Puolalainen',
				'fr' => 'Polonais',
				'el' => 'Πολωνικά',
				'hi' => 'पोलिश',
				'hu' => 'Lengyel',
				'id' => 'Polen',
				'it' => 'Polacco',
				'he' => 'פולנית',
				'ja' => 'ポーランド語',
				'ko' => '폴란드어',
				'nl' => 'Pools',
				'pl' => 'Polski',
				'pt' => 'Polonês',
				'ru' => 'Польский',
				'se' => 'Polska',
				'sk' => 'Poľský',
				'zh' => '波兰语',
				'tr' => 'Lehçe',
				'uk' => 'Польський',
				'vi' => 'Ba Lan',
			),
		),
		array(
			'name'             => __( 'Portuguese', 'wplingua' ),
			'id'               => 'pt',
			'flag'             => 'pt',
			'flags'            => array(
				array(
					'name' => __( 'Portugal', 'wplingua' ),
					'id'   => 'pt',
					'flag' => 'pt',
				),
				array(
					'name' => __( 'Brazil', 'wplingua' ),
					'id'   => 'br',
					'flag' => 'br',
				),
				array(
					'name' => __( 'Mozambique', 'wplingua' ),
					'id'   => 'mz',
					'flag' => 'mz',
				),
				array(
					'name' => __( 'Angola', 'wplingua' ),
					'id'   => 'ao',
					'flag' => 'ao',
				),
			),
			'name_translation' => array(
				'ar' => 'البرتغالية',
				'da' => 'Portugisisk',
				'de' => 'Portugiesisch',
				'en' => 'Portuguese',
				'es' => 'Portugués',
				'fi' => 'Portugalin',
				'fr' => 'Portugais',
				'el' => 'Πορτογαλικά',
				'hi' => 'पुर्तगाली',
				'hu' => 'Portugál',
				'id' => 'Portugis',
				'it' => 'Portoghese',
				'he' => 'פורטוגזית',
				'ja' => 'ポルトガル語',
				'ko' => '포르투갈어',
				'nl' => 'Portugees',
				'pl' => 'Portugalski',
				'pt' => 'Português',
				'ru' => 'Португальский',
				'se' => 'Portugisiska',
				'sk' => 'Portugalčina',
				'zh' => '葡语',
				'tr' => 'Portekizce',
				'uk' => 'Португальський',
				'vi' => 'Bồ Đào Nha',
			),
		),
		array(
			'name'             => __( 'Russian', 'wplingua' ),
			'id'               => 'ru',
			'flag'             => 'ru',
			'flags'            => array(
				array(
					'name' => __( 'Russia', 'wplingua' ),
					'id'   => 'ru',
					'flag' => 'ru',
				),
				array(
					'name' => __( 'Kazakhstan', 'wplingua' ),
					'id'   => 'kz',
					'flag' => 'kz',
				),
				array(
					'name' => __( 'Belarus', 'wplingua' ),
					'id'   => 'by',
					'flag' => 'by',
				),
				array(
					'name' => __( 'Kyrgyzstan', 'wplingua' ),
					'id'   => 'kg',
					'flag' => 'kg',
				),
				array(
					'name' => __( 'Tajikistan', 'wplingua' ),
					'id'   => 'tj',
					'flag' => 'tj',
				),
			),
			'name_translation' => array(
				'ar' => 'الروسية',
				'da' => 'Russisk',
				'de' => 'Russisch',
				'en' => 'Russian',
				'es' => 'Ruso',
				'fi' => 'Venäläinen',
				'fr' => 'Russe',
				'el' => 'Ρωσικά',
				'hi' => 'रूसी',
				'hu' => 'Orosz',
				'id' => 'Rusia',
				'it' => 'Russo',
				'he' => 'רוּסִי',
				'ja' => 'ロシア語',
				'ko' => '러시아어',
				'nl' => 'Russisch',
				'pl' => 'Rosyjski',
				'pt' => 'Russo',
				'ru' => 'Русский',
				'se' => 'Ryska',
				'sk' => 'Ruská',
				'zh' => '俄语',
				'tr' => 'Rusça',
				'uk' => 'Російський',
				'vi' => 'Nga',
			),
		),
		array(
			'name'             => __( 'Spanish', 'wplingua' ),
			'id'               => 'es',
			'flag'             => 'es',
			'flags'            => array(
				array(
					'name' => __( 'Spain', 'wplingua' ),
					'id'   => 'es',
					'flag' => 'es',
				),
				array(
					'name' => __( 'Mexico', 'wplingua' ),
					'id'   => 'mx',
					'flag' => 'mx',
				),
				array(
					'name' => __( 'Argentina', 'wplingua' ),
					'id'   => 'ar',
					'flag' => 'ar',
				),
				array(
					'name' => __( 'Colombia', 'wplingua' ),
					'id'   => 'co',
					'flag' => 'co',
				),
				array(
					'name' => __( 'Peru', 'wplingua' ),
					'id'   => 'pe',
					'flag' => 'pe',
				),
				array(
					'name' => __( 'Ecuador', 'wplingua' ),
					'id'   => 'ec',
					'flag' => 'ec',
				),
				array(
					'name' => __( 'Bolivia', 'wplingua' ),
					'id'   => 'bo',
					'flag' => 'bo',
				),
				array(
					'name' => __( 'Chile', 'wplingua' ),
					'id'   => 'cl',
					'flag' => 'cl',
				),
				array(
					'name' => __( 'Equatorial Guinea', 'wplingua' ),
					'id'   => 'gq',
					'flag' => 'gq',
				),
				array(
					'name' => __( 'Guatemala', 'wplingua' ),
					'id'   => 'gt',
					'flag' => 'gt',
				),
				array(
					'name' => __( 'Cuba', 'wplingua' ),
					'id'   => 'cu',
					'flag' => 'cu',
				),
				array(
					'name' => __( 'Dominican Republic', 'wplingua' ),
					'id'   => 'do',
					'flag' => 'do',
				),
				array(
					'name' => __( 'Honduras', 'wplingua' ),
					'id'   => 'hn',
					'flag' => 'hn',
				),
				array(
					'name' => __( 'Paraguay', 'wplingua' ),
					'id'   => 'py',
					'flag' => 'py',
				),
				array(
					'name' => __( 'El Salvador', 'wplingua' ),
					'id'   => 'sv',
					'flag' => 'sv',
				),
				array(
					'name' => __( 'Nicaragua', 'wplingua' ),
					'id'   => 'ni',
					'flag' => 'ni',
				),
				array(
					'name' => __( 'Costa Rica', 'wplingua' ),
					'id'   => 'cr',
					'flag' => 'cr',
				),
				array(
					'name' => __( 'Panama', 'wplingua' ),
					'id'   => 'pa',
					'flag' => 'pa',
				),
				array(
					'name' => __( 'Uruguay', 'wplingua' ),
					'id'   => 'uy',
					'flag' => 'uy',
				),
			),
			'name_translation' => array(
				'ar' => 'الإسبانية',
				'da' => 'Spansk',
				'de' => 'Spanisch',
				'en' => 'Spanish',
				'es' => 'Español',
				'fi' => 'Espanjan',
				'fr' => 'Espagnol',
				'el' => 'Ισπανικά',
				'hi' => 'स्पैनिश',
				'hu' => 'Spanisch',
				'id' => 'Spanyol',
				'it' => 'Spagnolo',
				'he' => 'ספרדית',
				'ja' => 'スペイン語',
				'ko' => '스페인어',
				'nl' => 'Spaans',
				'pl' => 'Hiszpański',
				'pt' => 'Espanhol',
				'ru' => 'Испанский',
				'se' => 'Spanska',
				'sk' => 'Španielčina',
				'zh' => '西班牙语',
				'tr' => 'İspanyolca',
				'uk' => 'Іспанський',
				'vi' => 'Tây Ban Nha',
			),
		),
		array(
			'name'             => __( 'Swedish', 'wplingua' ),
			'id'               => 'sv',
			'flag'             => 'sv',
			'flags'            => array(
				array(
					'name' => __( 'Sweden', 'wplingua' ),
					'id'   => 'se',
					'flag' => 'se',
				),
				array(
					'name' => __( 'Finland', 'wplingua' ),
					'id'   => 'fi',
					'flag' => 'fi',
				),
			),
			'name_translation' => array(
				'ar' => 'السويدية',
				'da' => 'Svensk',
				'de' => 'Schwedisch',
				'en' => 'Swedish',
				'es' => 'Sueco',
				'fi' => 'Ruotsalainen',
				'fr' => 'Suédois',
				'el' => 'Σουηδικά',
				'hi' => 'स्वीडिश',
				'hu' => 'Svéd',
				'id' => 'Swedia',
				'it' => 'Svedese',
				'he' => 'שוודית',
				'ja' => 'スウェーデン語',
				'ko' => '스웨덴어',
				'nl' => 'Zweeds',
				'pl' => 'Szwedzki',
				'pt' => 'Sueco',
				'ru' => 'Шведский',
				'se' => 'Svenska',
				'sk' => 'Švédčina',
				'zh' => '瑞典语',
				'tr' => 'İsveççe',
				'uk' => 'Шведська',
				'vi' => 'Thụy Điển',
			),
		),
		array(
			'name'             => __( 'Turkish', 'wplingua' ),
			'id'               => 'tr',
			'flag'             => 'tr',
			'flags'            => array(
				array(
					'name' => __( 'Turkey', 'wplingua' ),
					'id'   => 'tr',
					'flag' => 'tr',
				),
				array(
					'name' => __( 'Cyprus', 'wplingua' ),
					'id'   => 'cy',
					'flag' => 'cy',
				),
			),
			'name_translation' => array(
				'ar' => 'تركي ',
				'da' => 'Tyrkisk',
				'de' => 'Türkisch',
				'en' => 'Turkish',
				'es' => 'Turco',
				'fi' => 'Turkki',
				'fr' => 'Turc',
				'el' => 'Τούρκικα',
				'hi' => 'तुर्की',
				'hu' => 'Török',
				'id' => 'Turki',
				'it' => 'Turco',
				'he' => 'טורקית',
				'ja' => 'トルコ語',
				'ko' => '터키어',
				'nl' => 'Turks',
				'pl' => 'Turecki',
				'pt' => 'Turco',
				'ru' => 'Турецкий',
				'se' => 'Turkiska',
				'sk' => 'Turečtina',
				'zh' => '土耳其语',
				'tr' => 'Türkçe',
				'uk' => 'Турецька',
				'vi' => 'Thổ Nhĩ Kỳ',
			),
		),
		array(
			'name'             => __( 'Ukrainian', 'wplingua' ),
			'id'               => 'uk',
			'flag'             => 'ua',
			'flags'            => array(
				array(
					'name' => __( 'Ukraine', 'wplingua' ),
					'id'   => 'ua',
					'flag' => 'ua',
				),
			),
			'name_translation' => array(
				'ar' => 'الأوكرانية',
				'da' => 'Ukrainsk',
				'de' => 'Ukrainisch',
				'en' => 'Ukrainian',
				'es' => 'Ucraniano',
				'fi' => 'Ukrainan',
				'fr' => 'Ukrainien',
				'el' => 'Ουκρανικό',
				'hi' => 'यूक्रेनी',
				'hu' => 'Ukrán',
				'id' => 'Ukraina',
				'it' => 'Ucraino',
				'he' => 'אוקראינית',
				'ja' => 'ウクライナ語',
				'ko' => '우크라이나어',
				'nl' => 'Oekraïens',
				'pl' => 'Ukraiński',
				'pt' => 'Ucraniano',
				'ru' => 'Украинский',
				'se' => 'Ukrainska',
				'sk' => 'Ukrajinčina',
				'zh' => '乌克兰',
				'tr' => 'Ukraynaca',
				'uk' => 'Український',
				'vi' => 'U-crai-na',
			),
		),
		array(
			'name'             => __( 'Vietnamese', 'wplingua' ),
			'id'               => 'vi',
			'flag'             => 'vn',
			'flags'            => array(
				array(
					'name' => __( 'Vietnam', 'wplingua' ),
					'id'   => 'vn',
					'flag' => 'vn',
				),
			),
			'name_translation' => array(
				'ar' => 'فيتنامية',
				'da' => 'Vietnamesisk',
				'de' => 'Vietnamesisch',
				'en' => 'Vietnamese',
				'es' => 'Vietnamita',
				'fi' => 'Vietnamin',
				'fr' => 'Vietnamien',
				'el' => 'Βιετναμέζικα',
				'hi' => 'वियतनामी',
				'hu' => 'Vietnámi',
				'id' => 'Vietnam',
				'it' => 'Vietnamita',
				'he' => 'וייטנאמית',
				'ja' => 'ベトナム語',
				'ko' => '베트남어',
				'nl' => 'Vietnamees',
				'pl' => 'Wietnamski',
				'pt' => 'Vietnamita',
				'ru' => 'Вьетнамский',
				'se' => 'Vietnamesiska',
				'sk' => 'Vietnamský',
				'zh' => '越南语',
				'tr' => 'Vietnamca',
				'uk' => 'В\'єтнамська',
				'vi' => 'Việt',
			),
		),
	);
}
