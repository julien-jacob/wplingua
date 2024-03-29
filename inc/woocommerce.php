<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * List of regex to exclude woowommerce URL
 *
 * @param array $url_exclude
 * @return array
 */
function wplng_exclude_woocommerce_url( $url_exclude ) {

	/**
	 * Return if WooCommerce look not load
	 */

	if ( ! function_exists( 'is_woocommerce' ) ) {
		return $url_exclude;
	}

	/**
	 * List woocommerce URL and slug
	 */

	$url_woocommerce = array();

	// The following functions not working with the last woocommerce version
	// Get WooCommerce Cart URL : wc_get_cart_url()
	// Get WooCommerce Checkout URL : wc_get_checkout_url()
	// Get WooCommerce My Account Page : wc_get_page_permalink( 'myaccount' )

	// Get Woocommerce permalink option
	$option_links = get_option( 'woocommerce_permalinks' );

	// Get WooCommerce product base slug
	if ( ! empty( $option_links['product_base'] ) ) {
		$url_woocommerce[] = '/' . $option_links['product_base'] . '/';
	}

	// Get WooCommerce product category slug
	if ( ! empty( $option_links['category_base'] ) ) {
		$url_woocommerce[] = '/' . $option_links['category_base'] . '/';
	}

	// Get WooCommerce product tag slug
	if ( ! empty( $option_links['tag_base'] ) ) {
		$url_woocommerce[] = '/' . $option_links['tag_base'] . '/';
	}

	/**
	 * Check, sanitize and transform URL to REGEX
	 */

	$regex_woocommerce = array();

	foreach ( $url_woocommerce as $url ) {

		// URL : Check, sanitize and make relative

		if ( ! is_string( $url ) || '' === $url ) {
			continue;
		}

		$url = sanitize_url( $url );
		$url = wp_make_link_relative( $url );

		// Transform the URL to Regex

		$regex = preg_quote( $url );

		if ( '' === $regex ) {
			continue;
		}

		$regex = '#^' . $regex . '(.*)#';

		$regex_woocommerce[] = $regex;

	}

	return array_merge( $url_exclude, $regex_woocommerce );
}
