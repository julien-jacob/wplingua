<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Disable caching for editors/administrators across many cache plugins.
 */


function wplng_no_cache_for_editor() {

    // If the current user can edit posts, disable caching.
    if ( ! is_user_logged_in() || ! current_user_can( 'edit_posts' ) ) {
        return;
    }

    /* -------------------------
     * GENERAL HTTP HEADERS
     * ------------------------- */
    nocache_headers(); // WordPress native no-cache

    header( 'Cache-Control: no-cache, no-store, must-revalidate, max-age=0' );
    header( 'Pragma: no-cache' );
    header( 'Expires: 0' );

    /* -------------------------
     * LITESPEED CACHE
     * ------------------------- */
    if ( class_exists( 'LiteSpeed_Cache_Control' ) ) {
        LiteSpeed_Cache_Control::set_nocache();
    }

    /* -------------------------
     * WP SUPER CACHE
     * ------------------------- */
    if ( defined( 'WPSC_DISABLE' ) === false ) {
        define( 'WPSC_DISABLE', true );
    }
    if ( function_exists( 'wp_cache_disable' ) ) {
        wp_cache_disable();
    }

    /* -------------------------
     * WP ROCKET
     * ------------------------- */
    if ( function_exists( 'rocket_has_constant' ) ) {
        add_filter( 'do_rocket_generate_caching_files', '__return_false', 999 );
    }
    if ( function_exists( 'rocket_clean_domain' ) ) {
        // No direct "nocache", but prevent generation
        add_filter( 'rocket_override_donotcachepage', '__return_true' );
    }

    /* -------------------------
     * W3 TOTAL CACHE
     * ------------------------- */
    if ( defined( 'W3TC' ) ) {
        define( 'DONOTCACHEPAGE', true );
        define( 'DONOTMINIFY', true );
        define( 'DONOTCDN', true );
    }

    /* -------------------------
     * AUTOPTIMIZE
     * ------------------------- */
    if ( class_exists( 'autoptimizeCache' ) ) {
        define( 'AUTOPTIMIZE_NOCACHE', true );
    }

    /* -------------------------
     * CLOUDFLARE APO
     * ------------------------- */
    if ( ! headers_sent() ) {
        header( 'Cf-Cache-Status: BYPASS' );
    }

    /* -------------------------
     * GENERIC WORDPRESS CONSTANTS
     * ------------------------- */
    if ( ! defined( 'DONOTCACHEPAGE' ) ) {
        define( 'DONOTCACHEPAGE', true );
    }
    if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
        define( 'DONOTCACHEOBJECT', true );
    }
    if ( ! defined( 'DONOTCACHEDB' ) ) {
        define( 'DONOTCACHEDB', true );
    }
}
