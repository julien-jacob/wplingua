<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Require all wpLingua PHP files
 */

 // Require data functions
require_once WPLNG_PLUGIN_PATH . '/data.php';

// Require files in /inc/admin/ folder
require_once WPLNG_PLUGIN_PATH . '/inc/admin/admin-bar.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/assets.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/option-page-dictionary.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/option-page-exclusions.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/option-page-register.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/option-page-settings.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/option-page-switcher.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/option-page.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/translation-cpt.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/translation-meta.php';

// Require files in /inc/api-call/ folder
require_once WPLNG_PLUGIN_PATH . '/inc/api-call/request-api-key.php';
require_once WPLNG_PLUGIN_PATH . '/inc/api-call/translate.php';
require_once WPLNG_PLUGIN_PATH . '/inc/api-call/validate-api-key.php';

// Require files in /inc/lib/ folder
require_once WPLNG_PLUGIN_PATH . '/inc/lib/simple-html-dom.php';

// Require files in /inc/ob-callback/ folder
require_once WPLNG_PLUGIN_PATH . '/inc/ob-callback/ajax.php';
require_once WPLNG_PLUGIN_PATH . '/inc/ob-callback/editor.php';
require_once WPLNG_PLUGIN_PATH . '/inc/ob-callback/list.php';
require_once WPLNG_PLUGIN_PATH . '/inc/ob-callback/translate.php';

// Require files in /inc/parser/ folder
require_once WPLNG_PLUGIN_PATH . '/inc/parser/html.php';
require_once WPLNG_PLUGIN_PATH . '/inc/parser/js.php';
require_once WPLNG_PLUGIN_PATH . '/inc/parser/json.php';

// Require files in /inc/translator/ folder
require_once WPLNG_PLUGIN_PATH . '/inc/translator/html.php';
require_once WPLNG_PLUGIN_PATH . '/inc/translator/js.php';
require_once WPLNG_PLUGIN_PATH . '/inc/translator/json.php';

// Require files in /inc/ folder
require_once WPLNG_PLUGIN_PATH . '/inc/api-key.php';
require_once WPLNG_PLUGIN_PATH . '/inc/assets.php';
require_once WPLNG_PLUGIN_PATH . '/inc/dictionary.php';
require_once WPLNG_PLUGIN_PATH . '/inc/hreflang.php';
require_once WPLNG_PLUGIN_PATH . '/inc/html-updater.php';
require_once WPLNG_PLUGIN_PATH . '/inc/languages.php';
require_once WPLNG_PLUGIN_PATH . '/inc/ob-start.php';
require_once WPLNG_PLUGIN_PATH . '/inc/search.php';
require_once WPLNG_PLUGIN_PATH . '/inc/shortcode.php';
require_once WPLNG_PLUGIN_PATH . '/inc/switcher.php';
require_once WPLNG_PLUGIN_PATH . '/inc/translation.php';
require_once WPLNG_PLUGIN_PATH . '/inc/url.php';
require_once WPLNG_PLUGIN_PATH . '/inc/util.php';
require_once WPLNG_PLUGIN_PATH . '/inc/woocommerce.php';
