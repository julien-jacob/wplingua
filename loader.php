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

// Require files in /inc/ folder
require_once WPLNG_PLUGIN_PATH . '/inc/api-key.php';
require_once WPLNG_PLUGIN_PATH . '/inc/args.php';
require_once WPLNG_PLUGIN_PATH . '/inc/assets.php';
require_once WPLNG_PLUGIN_PATH . '/inc/buffering.php';
require_once WPLNG_PLUGIN_PATH . '/inc/dictionary.php';
require_once WPLNG_PLUGIN_PATH . '/inc/heartbeat.php';
require_once WPLNG_PLUGIN_PATH . '/inc/hreflang.php';
require_once WPLNG_PLUGIN_PATH . '/inc/languages.php';
require_once WPLNG_PLUGIN_PATH . '/inc/link-media.php';
require_once WPLNG_PLUGIN_PATH . '/inc/search.php';
require_once WPLNG_PLUGIN_PATH . '/inc/shortcode.php';
require_once WPLNG_PLUGIN_PATH . '/inc/slug.php';
require_once WPLNG_PLUGIN_PATH . '/inc/switcher-block.php';
require_once WPLNG_PLUGIN_PATH . '/inc/switcher-nav-menu.php';
require_once WPLNG_PLUGIN_PATH . '/inc/switcher.php';
require_once WPLNG_PLUGIN_PATH . '/inc/translation.php';
require_once WPLNG_PLUGIN_PATH . '/inc/url.php';
require_once WPLNG_PLUGIN_PATH . '/inc/util.php';

// Require files in /inc/admin/ folder
require_once WPLNG_PLUGIN_PATH . '/inc/admin/admin-bar.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/admin.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/assets.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/switcher-nav-menu.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/option-page-link-media.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/option-page-dictionary.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/option-page-exclusions.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/option-page-register.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/option-page-settings.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/option-page-switcher.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/option-page.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/slug-cpt.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/slug-meta.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/translation-cpt.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/translation-edit-modal.php';
require_once WPLNG_PLUGIN_PATH . '/inc/admin/translation-meta.php';

// Require files in /inc/api-call/ folder
require_once WPLNG_PLUGIN_PATH . '/inc/api-call/request-api-key.php';
require_once WPLNG_PLUGIN_PATH . '/inc/api-call/translate.php';
require_once WPLNG_PLUGIN_PATH . '/inc/api-call/validate-api-key.php';

// Require files in /inc/dom/ folder
require_once WPLNG_PLUGIN_PATH . '/inc/dom/exclusion-put-tags.php';
require_once WPLNG_PLUGIN_PATH . '/inc/dom/exclusion-replace-tags.php';
require_once WPLNG_PLUGIN_PATH . '/inc/dom/load-overload.php';
require_once WPLNG_PLUGIN_PATH . '/inc/dom/load-progress.php';
require_once WPLNG_PLUGIN_PATH . '/inc/dom/mode-editor.php';
require_once WPLNG_PLUGIN_PATH . '/inc/dom/mode-list.php';
require_once WPLNG_PLUGIN_PATH . '/inc/dom/replace-attr-dir.php';
require_once WPLNG_PLUGIN_PATH . '/inc/dom/replace-attr-lang.php';
require_once WPLNG_PLUGIN_PATH . '/inc/dom/replace-body-class.php';
require_once WPLNG_PLUGIN_PATH . '/inc/dom/replace-links.php';
require_once WPLNG_PLUGIN_PATH . '/inc/dom/translate-attr-html.php';
require_once WPLNG_PLUGIN_PATH . '/inc/dom/translate-attr-texts.php';
require_once WPLNG_PLUGIN_PATH . '/inc/dom/translate-js.php';
require_once WPLNG_PLUGIN_PATH . '/inc/dom/translate-json.php';
require_once WPLNG_PLUGIN_PATH . '/inc/dom/translate-node-texts.php';

// Require files in /inc/lib/ folder
require_once WPLNG_PLUGIN_PATH . '/inc/lib/simple-html-dom.php';

// Require files in /inc/parser/ folder
require_once WPLNG_PLUGIN_PATH . '/inc/parser/html.php';
require_once WPLNG_PLUGIN_PATH . '/inc/parser/js.php';
require_once WPLNG_PLUGIN_PATH . '/inc/parser/json.php';

// Require files in /inc/translator/ folder
require_once WPLNG_PLUGIN_PATH . '/inc/translator/html.php';
require_once WPLNG_PLUGIN_PATH . '/inc/translator/js.php';
require_once WPLNG_PLUGIN_PATH . '/inc/translator/json.php';
