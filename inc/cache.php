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

// ------------------------------------------------------------------------
// wpLingua file cache functions
// ------------------------------------------------------------------------

/**
 * Write data to a cache file in the wpLingua cache directory.
 *
 * Creates the cache directory structure and protection files if needed,
 * then writes the provided data to the specified file.
 *
 * @param string $file_name Relative path to the cache file (from WPLNG_CACHE_MAIN_PATH).
 * @param string $data      Data to write to the file.
 * @return int|false Number of bytes written, or false on failure.
 */
function wplng_put_cache_file( $file_name, $data ) {

	// Create main wpLingua cache folder if not exist
	if ( ! is_dir( WPLNG_CACHE_MAIN_PATH ) ) {
		if ( ! wp_mkdir_p( WPLNG_CACHE_MAIN_PATH ) ) {
			return false;
		}
	}

	// Create minimal protections in main folder (only once per request)
	static $main_protection_checked = false;

	if ( ! $main_protection_checked ) {

		// Create .htaccess if not exists
		if ( ! file_exists( WPLNG_CACHE_MAIN_PATH . '/.htaccess' ) ) {

			$htaccess  = '##############################' . PHP_EOL;
			$htaccess .= '# File generated by wpLingua #' . PHP_EOL;
			$htaccess .= '##############################' . PHP_EOL;
			$htaccess .= PHP_EOL;
			$htaccess .= '# Disable files listing' . PHP_EOL;
			$htaccess .= 'Options -Indexes' . PHP_EOL;
			$htaccess .= PHP_EOL;
			$htaccess .= '# Deny all HTTP access to this directory' . PHP_EOL;
			$htaccess .= '<IfModule mod_authz_core.c>' . PHP_EOL;
			$htaccess .= '	Require all denied' . PHP_EOL;
			$htaccess .= '</IfModule>' . PHP_EOL;
			$htaccess .= PHP_EOL;
			$htaccess .= '<IfModule !mod_authz_core.c>' . PHP_EOL;
			$htaccess .= '	Deny from all' . PHP_EOL;
			$htaccess .= '</IfModule>' . PHP_EOL;

			file_put_contents( WPLNG_CACHE_MAIN_PATH . '/.htaccess', $htaccess );
		}

		// Create an empty index.html file in main folder
		wplng_create_cache_index_html( WPLNG_CACHE_MAIN_PATH );

		$main_protection_checked = true;
	}

	// Create the subfolder if needed
	$dir = dirname( WPLNG_CACHE_MAIN_PATH . $file_name );
	if ( ! is_dir( $dir ) ) {
		if ( ! wp_mkdir_p( $dir ) ) {
			return false;
		}
	}

	// Create index.html in all directories between main path and target directory
	wplng_create_cache_index_html_recursive( $dir );

	// Write the cache file
	return file_put_contents( WPLNG_CACHE_MAIN_PATH . $file_name, $data );
}


/**
 * Create an empty index.html file in the specified directory.
 *
 * @param string $dir_path Absolute path to the directory.
 * @return bool True if file exists or was created, false on failure.
 */
function wplng_create_cache_index_html( $dir_path ) {

	$index_file = rtrim( $dir_path, '/\\' ) . '/index.html';

	if ( file_exists( $index_file ) ) {
		return true;
	}

	return file_put_contents( $index_file, '' ) !== false;
}


/**
 * Create index.html files in all directories from WPLNG_CACHE_MAIN_PATH to the target directory.
 *
 * @param string $target_dir Absolute path to the target directory.
 * @return void
 */
function wplng_create_cache_index_html_recursive( $target_dir ) {

	$target_dir = wp_normalize_path( $target_dir );
	$base_path  = wp_normalize_path( WPLNG_CACHE_MAIN_PATH );

	// Ensure target is within cache path
	if ( strpos( $target_dir, $base_path ) !== 0 ) {
		return;
	}

	// Get relative path from base
	$relative_path = substr( $target_dir, strlen( $base_path ) );
	$relative_path = trim( $relative_path, '/' );

	if ( empty( $relative_path ) ) {
		return;
	}

	// Build each subdirectory and create index.html
	$parts        = explode( '/', $relative_path );
	$current_path = $base_path;

	foreach ( $parts as $part ) {
		$current_path .= '/' . $part;
		if ( is_dir( $current_path ) ) {
			wplng_create_cache_index_html( $current_path );
		}
	}
}


/**
 * Read data from a cache file in the wpLingua cache directory.
 *
 * @param string $file_name Relative path to the cache file (from WPLNG_CACHE_MAIN_PATH).
 * @return string|false File contents, or false if file doesn't exist or isn't readable.
 */
function wplng_get_cache_file( $file_name ) {

	$file_path = WPLNG_CACHE_MAIN_PATH . $file_name;

	if ( ! is_readable( $file_path ) ) {
		return false;
	}

	return file_get_contents( $file_path );
}


/**
 * Clear wpLingua cache folder or a specific file/directory.
 *
 * @param string|false $file Relative path to delete, or false to delete entire cache folder.
 * @return bool True on success, false on failure.
 */
function wplng_clear_cache_folder( $file = false ) {

	if ( $file === false ) {
		$path = WPLNG_CACHE_MAIN_PATH;
	} else {
		$path = wp_normalize_path( WPLNG_CACHE_MAIN_PATH . $file );

		// Security: ensure path is within cache directory
		if ( strpos( $path, wp_normalize_path( WPLNG_CACHE_MAIN_PATH ) ) !== 0 ) {
			return false;
		}
	}

	if ( ! file_exists( $path ) ) {
		return true;
	}

	if ( is_file( $path ) ) {
		return @unlink( $path );
	}

	return wplng_delete_directory( $path );
}


/**
 * Recursively delete a directory and all its contents.
 *
 * Only deletes directories within WPLNG_CACHE_MAIN_PATH for security.
 *
 * @param string $dir_path Absolute path to the directory.
 * @return bool True on success, false on failure.
 */
function wplng_delete_directory( $dir_path ) {

	$dir_path  = wp_normalize_path( $dir_path );
	$base_path = wp_normalize_path( WPLNG_CACHE_MAIN_PATH );

	// Security: ensure path is within cache directory
	if ( strpos( $dir_path, $base_path ) !== 0 ) {
		return false;
	}

	if ( ! is_dir( $dir_path ) ) {
		return false;
	}

	$items = scandir( $dir_path );

	if ( $items === false ) {
		return false;
	}

	foreach ( $items as $item ) {

		if ( $item === '.' || $item === '..' ) {
			continue;
		}

		$item_path = $dir_path . '/' . $item;

		if ( is_dir( $item_path ) ) {
			wplng_delete_directory( $item_path );
		} else {
			@unlink( $item_path );
		}
	}

	return @rmdir( $dir_path );
}
