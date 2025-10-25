<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Return true if a JSON string element is translatable
 *
 * @param string $element The string value
 * @param array  $parents Array of parents
 * @return bool True if element is translatable
 */
function wplng_json_element_is_translatable( $element, $parents ) {

	$is_translatable   = false;
	$json_excluded     = wplng_data_excluded_json_element();
	$json_included = wplng_data_included_json_element();

	if ( in_array( $parents, $json_excluded ) ) {

		/**
		 * Is an excluded JSON
		 */

		$is_translatable = false;

	} elseif ( in_array( $parents, $json_included ) ) {

		/**
		 * Is an included JSON
		 */

		$is_translatable = true;

	} else {

		$element = wplng_text_esc( $element );

		if ( ! wplng_text_is_translatable( $element ) ) {
			$is_translatable = false;
		} else {
			$is_translatable = wplng_json_element_is_translatable_logical_rules(
				$element,
				$parents
			);
		}
	}

	return apply_filters(
		'wplng_json_element_is_translatable',
		$is_translatable,
		$element,
		$parents
	);
}


/**
 * Return true if a JSON string element is translatable by using logical coditions.
 * This function allows you to identify translatable elements if this is not possible based on: excluded elements, included elements, or automatic identification of character string types (URLs, language identifiers, etc.).
 *
 * @param string $element The string value
 * @param array  $parents Array of parents
 * @return bool True if element checked as translatable by logical rules
 */
function wplng_json_element_is_translatable_logical_rules( $element, $parents ) {

	// ------------------------------------------------------------------------
	// schema-graph
	// ------------------------------------------------------------------------

	/**
	 * Schema-graph: Caption, name, description, etc
	 */

	if (
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
	) {
		return true;
	}

	/**
	 * Schema-graph: BreadcrumbList
	 */

	if (
		! empty( $parents[0] )
		&& ! empty( $parents[2] )
		&& ! empty( $parents[3] )
		&& $parents[0] === 'itemListElement'
		&& $parents[2] === 'item'
		&& $parents[3] === 'name'
	) {
		return true;
	}

	// ------------------------------------------------------------------------
	// Plugin: Elementor
	// ------------------------------------------------------------------------

	/**
	 * Plugin: Elementor - elementorFrontendConfig
	 */

	if (
		count( $parents ) === 3
		&& $parents[0] === 'elementorFrontendConfig'
		&& $parents[1] === 'i18n'
	) {
		return true;
	}

	// ------------------------------------------------------------------------
	// Plugin: WooCommerce
	// ------------------------------------------------------------------------

	/**
	 * Plugin: WooCommerce - Address params
	 */

	if (
		! empty( $parents[0] )
		&& $parents[0] === 'wc_address_i18n_params'
		&& count( $parents ) > 1
		&& (
			$parents[ count( $parents ) - 1 ] === 'placeholder'
			|| $parents[ count( $parents ) - 1 ] === 'label'
		)
	) {
		return true;
	}

	/**
	 * Plugin: WooCommerce - Order Statuses
	 */

	if (
		! empty( $parents[0] )
		&& ! empty( $parents[1] )
		&& ! empty( $parents[2] )
		&& $parents[0] === 'EncodedAsURL'
		&& $parents[1] === 'orderStatuses'
		&& is_string( $parents[2] )
	) {
		return true;
	}

	/**
	 * Plugin: WooCommerce - Countries label
	 */

	if (
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
	) {
		return true;
	}

	/**
	 * Plugin: WooCommerce - weekdaysShort
	 */

	if (
		! empty( $parents[0] )
		&& ! empty( $parents[1] )
		&& ! empty( $parents[2] )
		&& ! empty( $parents[3] )
		&& $parents[0] === 'EncodedAsURL'
		&& $parents[1] === 'locale'
		&& $parents[2] === 'weekdaysShort'
		&& is_int( $parents[3] )
	) {
		return true;
	}

	/**
	 * Plugin: WooCommerce
	 * Product data (JSON in content, encoded as URL)
	 * Name, description, image alt, image name, etc
	 */

	if (
		! empty( $parents[0] )
		&& ! empty( $parents[1] )
		&& ! empty( $parents[2] )
		&& ! empty( $parents[3] )
		&& ! empty( $parents[4] )
		&& ! empty( $parents[5] )
		&& $parents[0] === 'EncodedAsURL'
		&& is_string( $parents[1] )
		&& $parents[2] === 'body'
		&& $parents[3] === 'items'
		&& is_int( $parents[4] )
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
	) {
		return true;
	}

	// TODO: Review comment
	/**
	 * Plugin: WooCommerce - 
	 * Is WooCommerce product data
	 * Name, description, image alt, image name, etc
	 */

	if (
		! empty( $parents[0] )
		&& ! empty( $parents[1] )
		&& ! empty( $parents[2] )
		&& ! empty( $parents[3] )
		&& ! empty( $parents[5] )
		&& $parents[0] === 'state'
		&& $parents[1] === 'woocommerce'
		&& $parents[2] === 'cart'
		&& $parents[3] === 'items'
		&& (
			$parents[5] === 'name'
			|| $parents[5] === 'short_description'
		)
	) {
		return true;
	}

	/**
	 * Plugin: WooCommerce - Form fields
	 */

	if (
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
	) {
		return true;
	}

	/**
	 * Plugin: WooCommerce - Store pages title
	 */

	if (
		! empty( $parents[0] )
		&& ! empty( $parents[1] )
		&& ! empty( $parents[2] )
		&& ! empty( $parents[3] )
		&& $parents[0] === 'EncodedAsURL'
		&& $parents[1] === 'storePages'
		&& is_string( $parents[2] )
		&& $parents[3] === 'title'
	) {
		return true;
	}

	/**
	 * Plugin: WooCommerce - i18n scripts
	 */
	// TODO : Check si c'est Woo

	if (
		! empty( $parents[0] )
		&& ! empty( $parents[1] )
		&& ! empty( $parents[2] )
		&& ! empty( $parents[3] )
		&& is_string( $parents[0] )
		&& $parents[1] === 'locale_data'
		&& $parents[2] === 'messages'
		&& is_string( $parents[3] )
		&& isset( $parents[4] )
		&& is_int( $parents[4] )
	) {
		return true;
	}

	/**
	 * Plugin: WooCommerce - Country select
	 */

	if (
		! empty( $parents[0] )
		&& ! empty( $parents[1] )
		&& $parents[0] === 'wc_country_select_params'
		&& $parents[1] === 'countries'
		&& count( $parents ) === 4
	) {
		return true;
	}

	// ------------------------------------------------------------------------
	// Theme: My listing
	// ------------------------------------------------------------------------

	/**
	 * Theme: My listing - JSON in HTML
	 */

	if (
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
	) {
		return true;
	}

	/**
	 * Theme: My listing - JSON in AJAX
	 */

	if (
		! empty( $parents[0] )
		&& ! empty( $parents[1] )
		&& ! empty( $parents[2] )
		&& $parents[0] === 'children'
		&& wplng_str_starts_with( $parents[1], 'term_' )
		&& (
			$parents[2] === 'name'
			|| $parents[2] === 'description'
		)
	) {
		return true;
	}

	// TODO : Check
	// elseif ( $parents[ count( $parents ) - 1 ] === 'label' ) {
	// $is_translatable = true;
	// }

	return false;
}
