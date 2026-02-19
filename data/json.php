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
				array( 'encoded_as_url','wcSettings', 'wcBlocksConfig', 'pluginUrl' ),
				array( 'encoded_as_url','wcSettings', 'wcBlocksConfig', 'restApiRoutes' ),
				array( 'encoded_as_url','wcSettings', 'wcBlocksConfig', 'defaultAvatar' ),
			)
		);
	};

	$logical_rules[] = function ( $element, $parents ) {
		return (
			isset( $parents[0] )
			&& $parents[0] === 'wc_order_attribution'
		);
	};

	return apply_filters(
		'wplng_json_rules_exclusion',
		$logical_rules
	);
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
			&& isset( $parents[4] )
			&& isset( $parents[5] )
			&& $parents[0] === 'i18n_script'
			&& is_string( $parents[1] )
			&& $parents[2] === 'locale_data'
			&& $parents[3] === 'messages'
			&& is_string( $parents[4] )
			&& is_int( $parents[5] )
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
		return (
			isset( $parents[0] )
			&& (
				$parents[0] === 'wc_add_to_cart_params'
				|| $parents[0] === 'wc_country_select_params'
				|| $parents[0] === 'wc_address_i18n_params'
				|| $parents[0] === 'woocommerce_params'
				|| $parents[0] === 'wc_single_product_params'
			)
			&& count( $parents ) >= 2
			&& wplng_str_starts_with( $parents[ array_key_last( $parents ) ], 'i18n_' )
		);
	};

	$logical_rules[] = function ( $element, $parents ) {
		return in_array(
			$parents,
			array(
				array( 'config', 'woocommerce', 'messages', 'addedToCartText' ),

				// JSON in attribute data-"wp-context"
				array( 'data-wp-context', 'addToCartText' ),
				array( 'data-wp-context', 'ariaLabel' ),
				array( 'data-wp-context', 'ariaLabelPrevious' ),
				array( 'data-wp-context', 'ariaLabelNext' ),
				array( 'data-wp-context', 'addToCartText' ),
				array( 'data-wp-context', 'inTheCartText' ),
				array( 'data-wp-context', 'inTheCartText' ),

				// JSON encoded as URL
				array( 'encoded_as_url', 'wcSettings', 'wcBlocksConfig', 'wordCountType' ),
				array( 'encoded_as_url', 'wcSettings', 'siteTitle' ),

				array( 'config', 'woocommerce/mini-cart-title-items-counter-block', 'itemsInCartTextTemplate' ),
				array( 'config', 'woocommerce/mini-cart-products-table-block', 'reduceQuantityLabel' ),
				array( 'config', 'woocommerce/mini-cart-products-table-block', 'increaseQuantityLabel' ),
				array( 'config', 'woocommerce/mini-cart-products-table-block', 'quantityDescriptionLabel' ),
				array( 'config', 'woocommerce/mini-cart-products-table-block', 'removeFromCartLabel' ),
				array( 'config', 'woocommerce/mini-cart-products-table-block', 'lowInStockLabel' ),
				array( 'config', 'woocommerce/mini-cart', 'buttonAriaLabelTemplate' ),
				array( 'state', 'woocommerce/mini-cart-title-items-counter-block', 'itemsInCartText' ),
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
	 * Plugin: WooCommerce - Order Statuses
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			isset(
				$parents[0],
				$parents[1],
				$parents[2],
				$parents[3],
			)
			&& $parents[0] === 'encoded_as_url'
			&& $parents[1] === 'wcSettings'
			&& $parents[2] === 'orderStatuses'
			&& is_string( $parents[3] )
		);
	};

	/**
	 * Plugin: WooCommerce - Item name
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			count( $parents ) >= 3
			&& isset(
				$parents[ count( $parents ) - 3 ],
				$parents[ count( $parents ) - 2 ],
				$parents[ count( $parents ) - 1 ],
			)
			&& $parents[ count( $parents ) - 3 ] === 'items'
			&& is_int( $parents[ count( $parents ) - 2 ] )
			&& $parents[ count( $parents ) - 1 ] === 'name'
		);
	};

	/**
	 * Plugin: WooCommerce - Item image name
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			count( $parents ) >= 5
			&& isset(
				$parents[ count( $parents ) - 5 ],
				$parents[ count( $parents ) - 4 ],
				$parents[ count( $parents ) - 3 ],
				$parents[ count( $parents ) - 2 ],
				$parents[ count( $parents ) - 1 ],
			)
			&& $parents[ count( $parents ) - 5 ] === 'items'
			&& is_int( $parents[ count( $parents ) - 4 ] )
			&& $parents[ count( $parents ) - 3 ] === 'images'
			&& is_int( $parents[ count( $parents ) - 2 ] )
			&& (
				$parents[ count( $parents ) - 1 ] === 'name'
				|| $parents[ count( $parents ) - 1 ] === 'alt'
			)
		);
	};

	/**
	 * Plugin: WooCommerce - Form fields
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			isset(
				$parents[0],
				$parents[1],
				$parents[2],
				$parents[3],
				$parents[4],
			)
			&& $parents[0] === 'encoded_as_url'
			&& $parents[1] === 'wcSettings'
			&& $parents[2] === 'defaultFields'
			&& is_string( $parents[3] )
			&& (
				$parents[4] === 'label'
				|| $parents[4] === 'optionalLabel'
			)
		);
	};

	/**
	 * Plugin: WooCommerce - Store pages title
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			isset(
				$parents[0],
				$parents[1],
				$parents[2],
				$parents[3],
				$parents[4],
			)
			&& $parents[0] === 'encoded_as_url'
			&& $parents[1] === 'wcSettings'
			&& $parents[2] === 'storePages'
			&& is_string( $parents[3] )
			&& $parents[4] === 'title'
		);
	};

	/**
	* Plugin: WooCommerce - Country select
	*/

	$logical_rules[] = function ( $element, $parents ) {
		return (
			isset(
				$parents[0], 
				$parents[1],
				$parents[2],
				$parents[3],
			)
			&& $parents[0] === 'wc_country_select_params'
			&& $parents[1] === 'countries'
			&& count($parents) === 4
		);
	};

	/**
	 * Plugin: WooCommerce - Country name
	 */

	$logical_rules[] = function ( $element, $parents ) {
		return (
			isset(
				$parents[0],
				$parents[1],
				$parents[2],
				$parents[3],
			)
			&& $parents[0] === 'encoded_as_url'
			&& $parents[1] === 'wcSettings'
			&& $parents[2] === 'countries'
			&& is_string( $parents[2] )
		);
	};

	/**
	* Plugin: WooCommerce - Countries label
	*/

	$logical_rules[] = function ( $element, $parents ) {
		return (
			isset(
				$parents[0],
				$parents[1],
				$parents[2],
				$parents[3],
				$parents[4],
				$parents[5],
			)
			&& $parents[0] === 'encoded_as_url'
			&& $parents[1] === 'wcSettings'
			&& $parents[2] === 'countryData'
			&& is_string( $parents[3] )
			&& $parents[4] === 'states'
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
	// Plugin: Contact Form 7
	// ------------------------------------------------------------------------

	$logical_rules[] = function ( $element, $parents ) {
		return (
			isset( $parents[0] )
			&& (
				$parents[0] === 'message'
				|| (
					isset( $parents[1] )
					&& isset( $parents[2] )
					&& $parents[0] === 'rules'
					&& is_int( $parents[1] )
					&& $parents[2] === 'error'
				)
				|| (
					isset( $parents[1] )
					&& isset( $parents[2] )
					&& $parents[0] === 'invalid_fields'
					&& is_int( $parents[1] )
					&& $parents[2] === 'message'
				)
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

	return apply_filters(
		'wplng_json_rules_inclusion',
		$logical_rules
	);
}
