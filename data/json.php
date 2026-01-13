<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Retrieves an array of function calls that contain translatable JSON.
 *
 * This whitelist is used to identify JavaScript function calls where the
 * JSON argument should be parsed and translated (e.g., jQuery datepicker).
 *
 * @return array Array of function names with dot notation.
 */
function wplng_data_json_in_js_functions() {
    return apply_filters(
        'wplng_json_in_js_functions',
        array(
            'jQuery.datepicker.setDefaults',
            '$.datepicker.setDefaults',
        )
    );
}


/**
 * Retrieves an array of exclusion rules for JSON elements.
 *
 * Each rule is a callback function that determines whether a JSON element
 * should be excluded based on its value and its parent elements.
 *
 * @return array Array of callback functions defining exclusion rules.
 */
function wplng_data_json_rules_exclusion() {

	$logical_rules = array();

	// ------------------------------------------------------------------------
	// Plugin: wpLingua (Ajax edit modal)
	// ------------------------------------------------------------------------

	$logical_rules[] = function ( $element, $parents ) {
		return in_array(
			$parents,
			array(
				array( 'data', 'wplng_edit_html' ),
				array( 'wplngI18nTranslation' ),
				array( 'wplngI18nSlug' ),
				array( 'wplngI18nGutenberg' ),
			)
		);
	};

	// ------------------------------------------------------------------------
	// Plugin: Google Site Kit
	// ------------------------------------------------------------------------

	$logical_rules[] = function ( $element, $parents ) {
		return in_array(
			$parents,
			array(
				array( '_googlesitekitBaseData' ),
			)
		);
	};

	// ------------------------------------------------------------------------
	// Plugin: WooCommerce
	// ------------------------------------------------------------------------

	$logical_rules[] = function ( $element, $parents ) {
		return in_array(
			$parents,
			array(
				array( 'EncodedAsURL', 'wcBlocksConfig', 'pluginUrl' ),
				array( 'EncodedAsURL', 'wcBlocksConfig', 'restApiRoutes' ),
				array( 'EncodedAsURL', 'wcBlocksConfig', 'defaultAvatar' ),
			)
		);
	};
	return $logical_rules;
}


/**
 * Retrieves an array of inclusion rules for JSON elements.
 *
 * Each rule is a callback function that determines whether a JSON element
 * should be included for translation based on its value and its parent elements.
 *
 * @return array Array of callback functions defining inclusion rules.
 */
function wplng_data_json_rules_inclusion() {

	$logical_rules = array();

	// ------------------------------------------------------------------------
	// Global methods
	// ------------------------------------------------------------------------

	/**
	 * Some case tagged as label
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			! empty( $parents[ count( $parents ) - 1 ] )
			&& $parents[ count( $parents ) - 1 ] === 'label'
		);
	};

	$logical_rules[] = function ( $element, $parents ) {
		return in_array(
			$parents,
			array(
				array( 'i18n', 'loading' ),
				array( 'i18n', 'loaded' ),
			)
		);
	};

	/**
	 * i18n scripts
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			isset( $parents[0] )
			&& isset( $parents[1] )
			&& isset( $parents[2] )
			&& isset( $parents[3] )
			&& is_string( $parents[0] )
			&& $parents[1] === 'locale_data'
			&& $parents[2] === 'messages'
			&& is_string( $parents[3] )
			&& isset( $parents[4] )
			&& is_int( $parents[4] )
		);
	};

	// ------------------------------------------------------------------------
	// schema-graph
	// ------------------------------------------------------------------------

	/**
	 * Schema-graph: Caption, name, description, etc
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			! empty( $parents[0] )
			&& $parents[0] === '@graph'
			&& count( $parents ) > 2
			&& (
				(
					$parents[ count( $parents ) - 2 ] === 'author'
					&& $parents[ count( $parents ) - 1 ] === 'headline'
				)
				|| (
					$parents[ count( $parents ) - 2 ] === 'articleSection'
					&& is_int( $parents[ count( $parents ) - 1 ] )
				)
				|| $parents[ count( $parents ) - 1 ] === 'caption'
				|| $parents[ count( $parents ) - 1 ] === 'name'
				|| $parents[ count( $parents ) - 1 ] === 'alternateName'
				|| $parents[ count( $parents ) - 1 ] === 'description'
			)
		);
	};

	/**
	 * Schema-graph: BreadcrumbList
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			! empty( $parents[0] )
			&& ! empty( $parents[2] )
			&& ! empty( $parents[3] )
			&& $parents[0] === 'itemListElement'
			&& $parents[2] === 'item'
			&& $parents[3] === 'name'
		);
	};

	// ------------------------------------------------------------------------
	// Plugin: Elementor
	// ------------------------------------------------------------------------

	/**
	 * Plugin: Elementor - elementorFrontendConfig
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			count( $parents ) === 3
			&& $parents[0] === 'elementorFrontendConfig'
			&& $parents[1] === 'i18n'
		);
	};

	// ------------------------------------------------------------------------
	// Plugin: Elementor Essential Addons
	// ------------------------------------------------------------------------

	/**
	 * Plugin: Elementor - Event Calendar
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return in_array(
			$parents,
			array(
				array( 'data-translate', 'today' ),
				array( 'data-translate', 'tomorrow' ),
			)
		);
	};

	$logical_rules[] = function ( $element, $parents ) {
		return (
			isset( $parents[0] )
			&& isset( $parents[1] )
			&& isset( $parents[2] )
			&& $parents[0] === 'data-events'
			&& is_int( $parents[1] )
			&& (
				$parents[2] === 'title'
				|| $parents[2] === 'description'
			)
		);
	};

	$logical_rules[] = function ( $element, $parents ) {
		return (
			! empty( $parents[0] )
			&& ! empty( $parents[1] )
			&& $parents[0] === 'jQuery.datepicker.setDefaults'
			&& $parents[1] !== 'dateFormat'
		);
	};

	// ------------------------------------------------------------------------
	// Plugin: WooCommerce
	// ------------------------------------------------------------------------

	$logical_rules[] = function ( $element, $parents ) {
		return in_array(
			$parents,
			array(
				array( 'config', 'woocommerce', 'messages', 'addedToCartText' ),
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
				array( 'wc_single_product_params', 'i18n_required_rating_text' ),

				// JSON in attribute data-"wp-context"
				array( 'data-wp-context', 'addToCartText' ),
				array( 'data-wp-context', 'ariaLabel' ),
				array( 'data-wp-context', 'ariaLabelPrevious' ),
				array( 'data-wp-context', 'ariaLabelNext' ),
				array( 'data-wp-context', 'addToCartText' ),
				array( 'data-wp-context', 'inTheCartText' ),
				array( 'data-wp-context', 'inTheCartText' ),

				// JSON encoded as URL
				array( 'EncodedAsURL', 'wcBlocksConfig', 'wordCountType' ),
				array( 'EncodedAsURL', 'siteTitle' ),
			)
		);
	};

	/**
	 * Plugin: WooCommerce - Product rating
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			isset( $parents[0] )
			&& isset( $parents[1] )
			&& isset( $parents[2] )
			&& $parents[0] === 'wc_single_product_params'
			&& $parents[1] === 'i18n_rating_options'
			&& is_int( $parents[2] )
		);
	};

	/**
	 * Plugin: WooCommerce - Address params
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			! empty( $parents[0] )
			&& $parents[0] === 'wc_address_i18n_params'
			&& count( $parents ) > 1
			&& (
				$parents[ count( $parents ) - 1 ] === 'placeholder'
				|| $parents[ count( $parents ) - 1 ] === 'label'
			)
		);
	};

	/**
	 * Plugin: WooCommerce - Order Statuses
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			! empty( $parents[0] )
			&& ! empty( $parents[1] )
			&& ! empty( $parents[2] )
			&& $parents[0] === 'EncodedAsURL'
			&& $parents[1] === 'orderStatuses'
			&& is_string( $parents[2] )
		);
	};

	/**
	 * Plugin: WooCommerce - weekdaysShort
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			isset( $parents[0] )
			&& isset( $parents[1] )
			&& isset( $parents[2] )
			&& isset( $parents[3] )
			&& $parents[0] === 'EncodedAsURL'
			&& $parents[1] === 'locale'
			&& $parents[2] === 'weekdaysShort'
			&& is_int( $parents[3] )
		);
	};

	/**
	 * Plugin: WooCommerce
	 * Product data (JSON in content, encoded as URL)
	 * Name, description, image alt, image name, etc
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			isset( $parents[0] )
			&& isset( $parents[2] )
			&& isset( $parents[3] )
			&& isset( $parents[5] )
			&& (
				$parents[0] === 'EncodedAsURL'
				|| $parents[0] === 'responses'
			)
			&& $parents[2] === 'body'
			&& $parents[3] === 'items'
			&& (
				$parents[5] === 'name'
				|| $parents[5] === 'short_description'
				|| $parents[5] === 'description'
				|| (
					$parents[5] === 'images'
					&& ! empty( $parents[6] )
					&& is_int( $parents[6] )
					&& ! empty( $parents[7] )
					&& (
						$parents[7] === 'alt'
						|| $parents[7] === 'name'
					)
				)
			)
		);
	};

	/**
	 * Plugin: WooCommerce
	 * Is WooCommerce Cart product data
	 * Name, description, image alt, image name, etc
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			! empty( $parents[0] )
			&& ! empty( $parents[1] )
			&& ! empty( $parents[2] )
			&& ! empty( $parents[3] )
			&& ! empty( $parents[5] )
			&& $parents[0] === 'state'
			&& $parents[1] === 'woocommerce'
			&& $parents[2] === 'cart'
			&& (
				(
					$parents[3] === 'items'
					&& (
						$parents[5] === 'name'
						|| $parents[5] === 'short_description'
						|| (
							$parents[5] === 'images'
							&& ! empty( $parents[7] )
							&& (
								$parents[7] === 'name'
								|| $parents[7] === 'alt'
							)
						)
					)
				)
				|| (
					$parents[3] === 'shipping_rates'
					&& (
						$parents[5] === 'name'
						|| (
							$parents[5] === 'items'
							&& ! empty( $parents[7] )
							&& $parents[7] === 'name'
						)
						|| (
							$parents[5] === 'shipping_rates'
							&& ! empty( $parents[7] )
							&& $parents[7] === 'meta_data'
							&& ! empty( $parents[9] )
							&& (
								$parents[9] === 'key' // TODO : Check
								|| $parents[9] === 'value'
							)
						)
						|| (
							$parents[5] === 'shipping_rates'
							&& ! empty( $parents[7] )
							&& $parents[7] === 'name'
						)
					)
				)
			)
		);
	};

	/**
	 * Plugin: WooCommerce
	 * Is WooCommerce Cart product data in REST API
	 * Name, description, image alt, image name, etc
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			! empty( $parents[0] )
			&& ! empty( $parents[2] )
			&& $parents[0] === 'shipping_rates'
			&& (
				$parents[2] === 'name'
				|| (
					$parents[2] === 'shipping_rates'
					&& ! empty( $parents[4] )
					&& (
						$parents[4] === 'name'
						|| $parents[4] === 'description'
						|| (
							$parents[4] === 'meta_data'
							&& ! empty( $parents[6] )
							&& (
								$parents[6] === 'key'
								|| $parents[6] === 'value'
							)
						)
					)
				)
				|| (
					$parents[2] === 'items'
					&& ! empty( $parents[4] )
					&& $parents[4] === 'name'
				)
			)
		);
	};

	$logical_rules[] = function ( $element, $parents ) {
		return (
			! empty( $parents[0] )
			&& ! empty( $parents[2] )
			&& ! empty( $parents[4] )
			&& $parents[0] === 'items'
			&& $parents[2] === 'images'
			&& (
				$parents[4] === 'name'
				|| $parents[4] === 'alt'
			)
		);
	};

	$logical_rules[] = function ( $element, $parents ) {
		return (
			! empty( $parents[0] )
			&& ! empty( $parents[2] )
			&& $parents[0] === 'items'
			&& (
				$parents[2] === 'short_description'
				|| $parents[2] === 'name'
			)
		);
	};

	/**
	 * Plugin: WooCommerce - Form fields
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			! empty( $parents[0] )
			&& ! empty( $parents[1] )
			&& ! empty( $parents[2] )
			&& ! empty( $parents[3] )
			&& $parents[0] === 'EncodedAsURL'
			&& $parents[1] === 'defaultFields'
			&& is_string( $parents[2] )
			&& (
				$parents[3] === 'label'
				|| $parents[3] === 'optionalLabel'
			)
		);
	};

	/**
	 * Plugin: WooCommerce - Store pages title
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			! empty( $parents[0] )
			&& ! empty( $parents[1] )
			&& ! empty( $parents[2] )
			&& ! empty( $parents[3] )
			&& $parents[0] === 'EncodedAsURL'
			&& $parents[1] === 'storePages'
			&& is_string( $parents[2] )
			&& $parents[3] === 'title'
		);
	};

	/**
	 * Plugin: WooCommerce - Country select
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			! empty( $parents[0] )
			&& ! empty( $parents[1] )
			&& $parents[0] === 'wc_country_select_params'
			&& $parents[1] === 'countries'
			&& count( $parents ) === 4
		);
	};

	/**
	 * Plugin: WooCommerce - Country name
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			! empty( $parents[0] )
			&& ! empty( $parents[1] )
			&& ! empty( $parents[2] )
			&& $parents[0] === 'EncodedAsURL'
			&& $parents[1] === 'countries'
			&& is_string( $parents[2] )
			&& ( preg_match( '#^[A-Z]{2}$#', $parents[2] ) === 1 )
		);
	};

	/**
	 * Plugin: WooCommerce - Countries label
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			! empty( $parents[0] )
			&& ! empty( $parents[1] )
			&& ! empty( $parents[2] )
			&& ! empty( $parents[3] )
			&& ! empty( $parents[4] )
			&& $parents[0] === 'EncodedAsURL'
			&& $parents[1] === 'countryData'
			&& is_string( $parents[2] )
			&& $parents[3] === 'locale'
			&& (
				$parents[4] === 'state'
				|| $parents[4] === 'postcode'
			)
			&& ! empty( $parents[5] )
			&& $parents[5] === 'label'
		);
	};

	// ------------------------------------------------------------------------
	// Plugin: Plugin: woocommerce-paypal-payments
	// ------------------------------------------------------------------------

	$logical_rules[] = function ( $element, $parents ) {
		return in_array(
			$parents,
			array(
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
			)
		);
	};

	// ------------------------------------------------------------------------
	// Plugin: NM Gift Registry and Wishlist Lite (nm-wishlist)
	// ------------------------------------------------------------------------

	$logical_rules[] = function ( $element, $parents ) {
		return in_array(
			$parents,
			array(
				array( 'nm_wishlist_vars', 'wlButtonTitleAdd' ),
				array( 'nm_wishlist_vars', 'wlButtonTitleRemove' ),
				array( 'nm_wishlist_vars', 'wlButtonTitleRemove' ),
			)
		);
	};

	// ------------------------------------------------------------------------
	// Plugin: YITH
	// ------------------------------------------------------------------------

	$logical_rules[] = function ( $element, $parents ) {
		return in_array(
			$parents,
			array(
				array( 'yith_wcwl_l10n', 'labels', 'cookie_disabled' ),
			)
		);
	};

	// ------------------------------------------------------------------------
	// Plugin: WF Cookie Consent
	// ------------------------------------------------------------------------

	$logical_rules[] = function ( $element, $parents ) {
		return in_array(
			$parents,
			array(
				array( 'wfCookieConsentSettings', 'wf_cookietext' ),
				array( 'wfCookieConsentSettings', 'wf_dismisstext' ),
				array( 'wfCookieConsentSettings', 'wf_linktext' ),
			)
		);
	};

	// ------------------------------------------------------------------------
	// Plugin: complianz
	// ------------------------------------------------------------------------

	$logical_rules[] = function ( $element, $parents ) {
		return in_array(
			$parents,
			array(
				array( 'complianz', 'categories', 'statistics' ),
				array( 'complianz', 'categories', 'marketing' ),
				array( 'complianz', 'placeholdertext' ),
				array( 'complianz', 'page_links', 'eu', 'privacy-statement', 'title' ),
				array( 'complianz', 'aria_label' ),
			)
		);
	};

	// ------------------------------------------------------------------------
	// Plugin: ultimate-post-kit
	// ------------------------------------------------------------------------

	$logical_rules[] = function ( $element, $parents ) {
		return in_array(
			$parents,
			array(
				array( 'UltimatePostKitConfig', 'mailchimp', 'subscribing' ),
			)
		);
	};

	// ------------------------------------------------------------------------
	// Plugin: royal-elementor-addons
	// ------------------------------------------------------------------------

	$logical_rules[] = function ( $element, $parents ) {
		return in_array(
			$parents,
			array(
				array( 'WprConfig', 'addedToCartText' ),
				array( 'WprConfig', 'viewCart' ),
				array( 'WprConfig', 'chooseQuantityText' ),
				array( 'WprConfig', 'input_empty' ),
				array( 'WprConfig', 'select_empty' ),
				array( 'WprConfig', 'file_empty' ),
				array( 'WprConfig', 'recaptcha_error' ),
			)
		);
	};

	// ------------------------------------------------------------------------
	// Plugin: WP Grid Builder
	// ------------------------------------------------------------------------

	$logical_rules[] = function ( $element, $parents ) {
		return in_array(
			$parents,
			array(
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
	};

	// ------------------------------------------------------------------------
	// Plugin: WP Amelia
	// ------------------------------------------------------------------------

	$logical_rules[] = function ( $element, $parents ) {
		return (
			// -> wpAmeliaSettings
			isset( $parents[0] )
			&& $parents[0] === 'wpAmeliaSettings'
			&& (
				// -> appointments
				(
					isset( $parents[1] )
					&& $parents[1] === 'appointments'
				)
				// -> roles -> providerBadges -> badges -> content
				|| (
					isset( $parents[1] )
					&& isset( $parents[2] )
					&& isset( $parents[3] )
					&& isset( $parents[4] )
					&& isset( $parents[5] )
					&& $parents[1] === 'roles'
					&& $parents[2] === 'providerBadges'
					&& $parents[3] === 'badges'
					&& is_int( $parents[4] )
					&& $parents[5] === 'content'
				)
				// -> customizedData -> sbsNew -> ... -> ... -> ... -> name
				|| (
					isset( $parents[1] )
					&& isset( $parents[2] )
					&& isset( $parents[3] )
					&& isset( $parents[4] )
					&& isset( $parents[5] )
					&& isset( $parents[6] )
					&& $parents[1] === 'customizedData'
					&& $parents[2] === 'sbsNew'
					&& $parents[6] === 'name'
				)
			)
		);
	};

	$logical_rules[] = function ( $element, $parents ) {
		return (
			// wpAmeliaLabels -> welcome_back
			isset( $parents[0] )
			&& $parents[0] === 'wpAmeliaLabels'
			&& isset( $parents[1] )
			&& is_string( $parents[1] )
		);
	};

	// ------------------------------------------------------------------------
	// Theme: Divi
	// ------------------------------------------------------------------------

	$logical_rules[] = function ( $element, $parents ) {
		return in_array(
			$parents,
			array(
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

				array( 'data-et-multi-view', 'schema', 'content', 'desktop' ),
				array( 'data-et-multi-view', 'schema', 'content', 'tablet' ),
				array( 'data-et-multi-view', 'schema', 'content', 'phone' ),
			)
		);
	};

	// ------------------------------------------------------------------------
	// Theme: My listing
	// ------------------------------------------------------------------------

	/**
	 * Theme: My listing - JSON in HTML
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			! empty( $parents[0] )
			&& ! empty( $parents[1] )
			&& ! empty( $parents[2] )
			&& wplng_str_starts_with( $parents[0], 'CASE' )
			&& $parents[1] === 'l10n'
			&& (
				$parents[2] === 'selectOption'
				|| $parents[2] === 'errorLoading'
				|| $parents[2] === 'removeAllItems'
				|| $parents[2] === 'loadingMore'
				|| $parents[2] === 'noResults'
				|| $parents[2] === 'searching'
				|| $parents[2] === 'irreversible_action'
				|| $parents[2] === 'delete_listing_confirm'
				|| $parents[2] === 'copied_to_clipboard'
				|| $parents[2] === 'nearby_listings_location_required'
				|| $parents[2] === 'nearby_listings_retrieving_location'
				|| $parents[2] === 'nearby_listings_searching'
				|| $parents[2] === 'geolocation_failed'
				|| $parents[2] === 'something_went_wrong'
				|| $parents[2] === 'all_in_category'
				|| $parents[2] === 'invalid_file_type'
				|| $parents[2] === 'file_limit_exceeded'
				|| $parents[2] === 'file_size_limit'
				|| (
					$parents[2] === 'datepicker'
					&& ! empty( $parents[3] )
					&& (
						$parents[3] === 'applyLabel'
						|| $parents[3] === 'cancelLabel'
						|| $parents[3] === 'customRangeLabel'
						|| $parents[3] === 'daysOfWeek'
						|| $parents[3] === 'monthNames'
					)
				)
			)
		);
	};

	/**
	 * Theme: My listing - JSON in AJAX
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			isset( $parents[0] )
			&& isset( $parents[1] )
			&& isset( $parents[2] )
			&& $parents[0] === 'children'
			&& wplng_str_starts_with( $parents[1], 'term_' )
			&& (
				$parents[2] === 'name'
				|| $parents[2] === 'description'
			)
		);
	};

	return $logical_rules;
}
