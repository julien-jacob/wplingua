<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



function wplng_exclude_woocommerce( $is_translatable ) {
	
	if ( ! function_exists('is_woocommerce') ) {
		return $is_translatable;
	}

	return $is_translatable && ! is_woocommerce();
}