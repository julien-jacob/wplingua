<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



function wplng_exclude_woocommerce( $is_translatable ) {
	return $is_translatable && function_exists('is_woocommerce') && ! is_woocommerce();
}