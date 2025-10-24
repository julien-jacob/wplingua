<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Return true if a JSON string element is translatable
 *
 * @param string $element
 * @param array  $parents
 * @return bool
 */
function wplng_json_element_is_translatable( $element, $parents ) {

	$is_translatable   = false;
	$json_excluded     = wplng_data_excluded_json();
	$json_to_translate = wplng_data_json_to_translate();

	if ( in_array( $parents, $json_excluded ) ) {

		/**
		 * Is an excluded JSON
		 */

		$is_translatable = false;

	} elseif ( in_array( $parents, $json_to_translate ) ) {

		/**
		 * Is an included JSON
		 */

		$is_translatable = true;

	} else {

		if (
			! empty( $parents[0] )
			&& ( '@graph' === $parents[0] )
			&& ( count( $parents ) > 2 )
			&& (
				(
					( 'author' === $parents[ count( $parents ) - 2 ] )
					&& ( 'headline' === $parents[ count( $parents ) - 1 ] )
				)
				|| (
					( 'articleSection' === $parents[ count( $parents ) - 2 ] )
					&& ( is_int( $parents[ count( $parents ) - 1 ] ) )
				)
				|| ( 'caption' === $parents[ count( $parents ) - 1 ] )
				|| ( 'name' === $parents[ count( $parents ) - 1 ] )
				|| ( 'alternateName' === $parents[ count( $parents ) - 1 ] )
				|| ( 'description' === $parents[ count( $parents ) - 1 ] )
			)
		) {

			/**
			 * Is schema-graph
			 */

			$is_translatable = true;

		} elseif (
			! empty( $parents[0] )
			&& ( 'itemListElement' === $parents[0] )
			&& ! empty( $parents[2] )
			&& ( 'item' === $parents[2] )
			&& ! empty( $parents[3] )
			&& ( 'name' === $parents[3] )
		) {

			/**
			 * Is schema BreadcrumbList
			 */

			 $is_translatable = true;

		} elseif (
			count( $parents ) == 3
			&& ( 'elementorFrontendConfig' === $parents[0] )
			&& ( 'i18n' === $parents[1] )
		) {

			/**
			 * Plugin: Elementor - elementorFrontendConfig
			 */

			$is_translatable = true;

		} elseif (
			! empty( $parents[0] )
			&& ( 'wc_address_i18n_params' === $parents[0] )
			&& ( count( $parents ) > 1 )
			&& (
				( 'placeholder' === $parents[ count( $parents ) - 1 ] )
				|| ( 'label' === $parents[ count( $parents ) - 1 ] )
			)
		) {

			/**
			 * Is WooCommerce address params
			 */

			$is_translatable = true;

		} elseif (
			! empty( $parents[0] )
			&& ( 'EncodedAsURL' === $parents[0] )
			&& ! empty( $parents[1] )
			&& ( 'orderStatuses' === $parents[1] )
			&& ! empty( $parents[2] )
			&& is_string( $parents[2] )
		) {

			/**
			 * Is WooCommerce order Statuses
			 */

			$is_translatable = true;

		} elseif (
			! empty( $parents[0] )
			&& ( 'EncodedAsURL' === $parents[0] )
			&& ! empty( $parents[1] )
			&& ( 'countryData' === $parents[1] )
			&& ! empty( $parents[2] )
			&& is_string( $parents[2] )
			&& ! empty( $parents[3] )
			&& ( 'locale' === $parents[3] )
			&& ! empty( $parents[4] )
			&& (
				( 'state' === $parents[4] )
				|| ( 'postcode' === $parents[4] )
			)
			&& ! empty( $parents[5] )
			&& ( 'label' === $parents[5] )
		) {

			/**
			 * Is WooCommerce countries label
			 */

			$is_translatable = true;

		} elseif (
			! empty( $parents[0] )
			&& ( 'EncodedAsURL' === $parents[0] )
			&& ! empty( $parents[1] )
			&& ( 'locale' === $parents[1] )
			&& ! empty( $parents[2] )
			&& ( 'weekdaysShort' === $parents[2] )
			&& ! empty( $parents[3] )
			&& is_int( $parents[3] )
		) {

			/**
			 * Is WooCommerce weekdaysShort
			 */

			$is_translatable = true;

		} elseif (
			! empty( $parents[0] )
			&& ( 'EncodedAsURL' === $parents[0] )
			&& ! empty( $parents[1] )
			&& is_string( $parents[1] )
			&& ! empty( $parents[2] )
			&& ( 'body' === $parents[2] )
			&& ! empty( $parents[3] )
			&& ( 'items' === $parents[3] )
			&& ! empty( $parents[4] )
			&& is_int( $parents[4] )
			&& ! empty( $parents[5] )
			&& (
				( 'name' === $parents[5] )
				|| ( 'short_description' === $parents[5] )
				|| ( 'description' === $parents[5] )
				|| (
					( 'images' === $parents[5] )
					&& ! empty( $parents[6] )
					&& is_int( $parents[6] )
					&& ! empty( $parents[7] )
					&& (
						'alt' === $parents[7]
						|| 'name' === $parents[7]
					)
				)
			)
		) {

			/**
			 * Is WooCommerce product data (JSON in content, encoded as URL)
			 * Name, description, image alt, image name, etc
			 */

			$is_translatable = true;

		} elseif (
			! empty( $parents[0] )
			&& ( 'state' === $parents[0] )
			&& ! empty( $parents[1] )
			&& ( 'woocommerce' === $parents[1] )
			&& ! empty( $parents[2] )
			&& ( 'cart' === $parents[2] )
			&& ! empty( $parents[3] )
			&& ( 'items' === $parents[3] )
			&& ! empty( $parents[5] )
			&& ( 
				'name' === $parents[5] 
				|| 'short_description' === $parents[5] 
			)
		) {

			/**
			 * Is WooCommerce product data
			 * Name, description, image alt, image name, etc
			 */

			$is_translatable = true;

		} elseif (
			! empty( $parents[0] )
			&& ( 'EncodedAsURL' === $parents[0] )
			&& ! empty( $parents[1] )
			&& ( 'defaultFields' === $parents[1] )
			&& ! empty( $parents[2] )
			&& is_string( $parents[2] )
			&& ! empty( $parents[3] )
			&& (
				( 'label' === $parents[3] )
				|| ( 'optionalLabel' === $parents[3] )
			)
		) {

			/**
			 * Is WooCommerce form fields
			 */

			$is_translatable = true;

		} elseif (
			! empty( $parents[0] )
			&& ( 'EncodedAsURL' === $parents[0] )
			&& ! empty( $parents[1] )
			&& ( 'storePages' === $parents[1] )
			&& ! empty( $parents[2] )
			&& is_string( $parents[2] )
			&& ! empty( $parents[3] )
			&& ( 'title' === $parents[3] )
		) {

			/**
			 * Is WooCommerce Store pages title
			 */

			$is_translatable = true;

		} elseif (
			! empty( $parents[0] )
			&& is_string( $parents[0] )
			&& ! empty( $parents[1] )
			&& ( 'locale_data' === $parents[1] )
			&& ! empty( $parents[2] )
			&& ( 'messages' === $parents[2] )
			&& ! empty( $parents[3] )
			&& is_string( $parents[3] )
			&& isset( $parents[4] )
			&& is_int( $parents[4] )
		) {

			/**
			 * Is i18n scripts
			 */

			$is_translatable = true;

		} elseif (
			! empty( $parents[0] )
			&& ( 'wc_country_select_params' === $parents[0] )
			&& ! empty( $parents[1] )
			&& ( 'countries' === $parents[1] )
			&& ( count( $parents ) === 4 )
		) {

			/**
			 * Is WooCommerce country select
			 */

			$is_translatable = true;

		} elseif (
			! empty( $parents[0] )
			&& wplng_str_starts_with( $parents[0], 'CASE' )
			&& ! empty( $parents[1] )
			&& 'l10n' === $parents[1]
			&& ! empty( $parents[2] )
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

			/**
			 * Is 'My listing' theme - JSON in HTML
			 */

			$is_translatable = true;

		} elseif (
			! empty( $parents[0] )
			&& ( 'children' === $parents[0] )
			&& ! empty( $parents[1] )
			&& ( wplng_str_starts_with( $parents[1], 'term_' ) )
			&& ! empty( $parents[2] )
			&& (
				( 'name' === $parents[2] )
				|| ( 'description' === $parents[2] )
			)
		) {

			/**
			 * Is 'My listing' theme - JSON in AJAX
			 */

			$is_translatable = true;

		} elseif ( 'label' === $parents[ count( $parents ) - 1 ] ) {
			$is_translatable = true;
		}

		$element = wplng_text_esc( $element );

		if ( ! wplng_text_is_translatable( $element ) ) {
			$is_translatable = false;
		}
	}

	return apply_filters(
		'wplng_json_element_is_translatable',
		$is_translatable,
		$element,
		$parents
	);
}
