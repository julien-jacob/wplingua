<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Get JSON elements to translate
 *
 * @return array
 */
function wplng_data_included_json_element() {
	return apply_filters(
		'wplng_included_json_element',
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

			array( 'wc_country_select_params', 'countries', 'i18n_select_state_text' ),
			array( 'wc_country_select_params', 'countries', 'i18n_no_matches' ),
			array( 'wc_country_select_params', 'countries', 'i18n_ajax_error' ),
			array( 'wc_country_select_params', 'countries', 'i18n_input_too_short_1' ),
			array( 'wc_country_select_params', 'countries', 'i18n_input_too_short_n' ),
			array( 'wc_country_select_params', 'countries', 'i18n_input_too_long_1' ),
			array( 'wc_country_select_params', 'countries', 'i18n_input_too_long_n' ),
			array( 'wc_country_select_params', 'countries', 'i18n_selection_too_long_1' ),
			array( 'wc_country_select_params', 'countries', 'i18n_selection_too_long_n' ),
			array( 'wc_country_select_params', 'countries', 'i18n_load_more' ),
			array( 'wc_country_select_params', 'countries', 'i18n_searching' ),

			array( 'wc_address_i18n_params', 'locale', 'locale_fields', 'i18n_required_text' ),
			array( 'wc_address_i18n_params', 'locale', 'locale_fields', 'i18n_optional_text' ),

			array( 'woocommerce_params', 'i18n_password_show' ),
			array( 'woocommerce_params', 'i18n_password_hide' ),

			// Plugin: woocommerce-paypal-payments
			array( 'PayPalCommerceGateway', 'hosted_fields', 'labels', 'fields_empty' ),
			array( 'PayPalCommerceGateway', 'hosted_fields', 'labels', 'fields_not_valid' ),
			array( 'PayPalCommerceGateway', 'hosted_fields', 'labels', 'card_not_supported' ),
			array( 'PayPalCommerceGateway', 'hosted_fields', 'labels', 'cardholder_name_required' ),
			array( 'PayPalCommerceGateway', 'labels', 'error', 'generic' ),
			array( 'PayPalCommerceGateway', 'labels', 'error', 'required', 'generic' ),
			array( 'PayPalCommerceGateway', 'labels', 'error', 'required', 'field' ),
			array( 'PayPalCommerceGateway', 'labels', 'error', 'required', 'elements', 'terms' ),
			array( 'PayPalCommerceGateway', 'labels', 'billing_field' ),
			array( 'PayPalCommerceGateway', 'labels', 'shipping_field' ),
			array( 'PayPalCommerceGateway', 'labels', 'shipping_field' ),

			// Plugin: NM Gift Registry and Wishlist Lite (nm-wishlist)
			array( 'nm_wishlist_vars', 'wlButtonTitleAdd' ),
			array( 'nm_wishlist_vars', 'wlButtonTitleRemove' ),
			array( 'nm_wishlist_vars', 'wlButtonTitleRemove' ),

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
function wplng_data_excluded_json_element() {
	return apply_filters(
		'wplng_excluded_json_element',
		array(
			// wpLingua: Ajax edit modal
			array( 'data', 'wplng_edit_html' ),
			array( 'wplngI18nTranslation' ),
			array( 'wplngI18nSlug' ),
			array( 'wplngI18nGutenberg' ),

			// Plugin: Google Site Kit
			array( '_googlesitekitBaseData' ),
		)
	);
}
