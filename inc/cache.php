<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


// ------------------------------------------------------------------------
// Manage cache on "init" hoot
// ------------------------------------------------------------------------

/**
 * Do not cache page for connected editor
 *
 * @return void
 */
function wplng_init_manage_cache() {
	if ( current_user_can( 'edit_posts' ) ) {
		wplng_do_not_cache_page();
	}
}


// ------------------------------------------------------------------------
// Global cache functions
// ------------------------------------------------------------------------

/**
 * Prevent the current page from being cached
 *
 * Sets HTTP no-cache headers and defines constants used by popular
 * WordPress caching plugins to bypass caching for the current request.
 *
 * @return void
 */
function wplng_do_not_cache_page() {

	// Set HTTP no-cache header
	nocache_headers();

	// DONOTCACHEPAGE - Standard WordPress
	// - WP Super Cache
	// - W3 Total Cache
	// - WP Rocket
	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}

	// LiteSpeed Cache
	if ( ! defined( 'LSCACHE_NO_CACHE' ) ) {
		define( 'LSCACHE_NO_CACHE', true );
	}

	// Comet Cache
	if ( ! defined( 'COMET_CACHE_ALLOWED' ) ) {
		define( 'COMET_CACHE_ALLOWED', false );
	}

	// Cache Enabler
	if ( ! defined( 'CE_CACHE_BYPASS' ) ) {
		define( 'CE_CACHE_BYPASS', true );
	}

	// WP Fastest Cache
	if ( ! defined( 'WPFC_NO_CACHE' ) ) {
		define( 'WPFC_NO_CACHE', true );
	}

	// Breeze (Cloudways)
	if ( ! defined( 'DONOTCACHEDB' ) ) {
		define( 'DONOTCACHEDB', true );
	}

	// Object cache
	if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
		define( 'DONOTCACHEOBJECT', true );
	}
}


/**
 * Clear all website caches
 *
 * Flushes the WordPress object cache and triggers cache clearing
 * for all detected caching plugins and services.
 *
 * @return void
 */
function wplng_clear_website_cache() {

	// WordPress Cache
	wp_cache_flush();

	// LiteSpeed Cache
	if ( has_action( 'litespeed_purge_all' ) ) {
		do_action( 'litespeed_purge_all' );
	}

	// WP Super Cache
	if ( function_exists( 'wp_cache_clear_cache' ) ) {
		wp_cache_clear_cache();
	}

	// W3 Total Cache
	if ( function_exists( 'w3tc_flush_all' ) ) {
		w3tc_flush_all();
	}

	// WP Fastest Cache
	if ( function_exists( 'wpfc_clear_all_cache' ) ) {
		wpfc_clear_all_cache();
	}

	// WP Rocket
	if ( function_exists( 'rocket_clean_domain' ) ) {
		rocket_clean_domain();
	}

	// Autoptimize
	if ( class_exists( 'autoptimizeCache' )
		&& method_exists( 'autoptimizeCache', 'clearall' )
	) {
		autoptimizeCache::clearall();
	}

	// Comet Cache
	if ( class_exists( 'comet_cache' )
		&& method_exists( 'comet_cache', 'clear' )
	) {
		comet_cache::clear();
	}

	// Hummingbird
	if ( has_action( 'wphb_clear_page_cache' ) ) {
		do_action( 'wphb_clear_page_cache' );
	}

	// SG Optimizer (SiteGround)
	if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
		sg_cachepress_purge_cache();
	}

	// Breeze (Cloudways)
	if ( has_action( 'breeze_clear_all_cache' ) ) {
		do_action( 'breeze_clear_all_cache' );
	}

	// Cache Enabler
	if ( class_exists( 'Cache_Enabler' )
		&& method_exists( 'Cache_Enabler', 'clear_total_cache' )
	) {
		Cache_Enabler::clear_total_cache();
	}

	// Swift Performance
	if ( class_exists( 'Swift_Performance_Cache' )
		&& method_exists( 'Swift_Performance_Cache', 'clear_all_cache' )
	) {
		Swift_Performance_Cache::clear_all_cache();
	}

	// Nginx Helper
	if ( has_action( 'rt_nginx_helper_purge_all' ) ) {
		do_action( 'rt_nginx_helper_purge_all' );
	}

	// WP-Optimize
	if ( class_exists( 'WP_Optimize' )
		&& function_exists( 'wpo_cache_flush' )
	) {
		wpo_cache_flush();
	}

	// Kinsta Cache
	if ( class_exists( 'Kinsta\Cache' )
		&& wp_get_environment_type() === 'production'
	) {
		wp_remote_get(
			home_url( '/?kinsta-clear-cache-all' ),
			array( 'blocking' => false )
		);
	}

	// Cloudflare
	if ( has_action( 'cloudflare_purge_everything' ) ) {
		do_action( 'cloudflare_purge_everything' );
	}
}


// ------------------------------------------------------------------------
// Translations cache functions
// ------------------------------------------------------------------------

/**
 * Clear cached translations
 *
 * Deletes the translations transient and clears the website cache.
 *
 * @return void
 */
function wplng_clear_translations_cache() {
	delete_transient( 'wplng_cached_translations' );
	wplng_clear_website_cache();
}


/**
 * Clear cached translations when a translation post is trashed or untrashed
 *
 * @param int $post_id The ID of the post being trashed or untrashed.
 * @return void
 */
function wplng_clear_translations_cache_trash_untrash( $post_id ) {

	if ( 'wplng_translation' !== get_post_type( $post_id ) ) {
		return;
	}

	wplng_clear_translations_cache();
}


// ------------------------------------------------------------------------
// Slug cache functions
// ------------------------------------------------------------------------

/**
 * Clear cached slugs
 *
 * Deletes the slugs transient and clears the website cache.
 *
 * @return void
 */
function wplng_clear_slugs_cache() {
	delete_transient( 'wplng_cached_slugs' );
	wplng_clear_website_cache();
}


/**
 * Clear cached slugs when a slug post is trashed or untrashed
 *
 * @param int $post_id The ID of the post being trashed or untrashed.
 * @return void
 */
function wplng_clear_slugs_cache_trash_untrash( $post_id ) {

	if ( 'wplng_slug' !== get_post_type( $post_id ) ) {
		return;
	}

	wplng_clear_slugs_cache();
}
