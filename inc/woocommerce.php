<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * List woowommerce URL for exclusion
 *
 * @param array $url_exclude
 * @return array
 */
function wplng_exclude_woocommerce_url( $url_exclude ) {

	if ( ! function_exists( 'is_woocommerce' ) ) {
		return $url_exclude;
	}

	$url_woocommerce = array();

	// WooCommerce Cart URL
	$url_woocommerce[] = wc_get_cart_url();

	// WooCommerce Checkout URL
	$url_woocommerce[] = wc_get_checkout_url();

	// WooCommerce Shop URL
	$url_woocommerce[] = wc_get_page_permalink( 'shop' );

	foreach ( $url_woocommerce as $key => $url ) {
		$url_woocommerce[ $key ] = wp_make_link_relative( $url );
	}

	// My Account Page
	$url_woocommerce_account = wp_make_link_relative(
		wc_get_page_permalink( 'myaccount' )
	);

	$url_woocommerce_account .= '(.*)';

	$url_woocommerce[] = $url_woocommerce_account;

	$option_links = get_option( 'woocommerce_permalinks' );

	if ( ! empty( $option_links['product_base'] ) ) {
		$url_woocommerce[] = $option_links['product_base'] . '/(.*)';
	}

	if ( ! empty( $option_links['product-category'] ) ) {
		$url_woocommerce[] = '/' . $option_links['product-category'] . '/(.*)';
	}

	if ( ! empty( $option_links['product-tag'] ) ) {
		$url_woocommerce[] = '/' . $option_links['product-tag'] . '/(.*)';
	}

	foreach ( $url_woocommerce as $key => $value ) {
		$url_woocommerce[ $key ] = '#' . $value . '#';
	}

	return array_merge( $url_exclude, $url_woocommerce );
}
