<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Require all wpLingua PHP files
 */

// Require files in /data/ folder
require_once WPLNG_PLUGIN_DIR . '/data/ajax.php';
require_once WPLNG_PLUGIN_DIR . '/data/attribute.php';
require_once WPLNG_PLUGIN_DIR . '/data/json.php';
require_once WPLNG_PLUGIN_DIR . '/data/language.php';
require_once WPLNG_PLUGIN_DIR . '/data/node.php';
require_once WPLNG_PLUGIN_DIR . '/data/switcher.php';

// Require files in /inc/ folder
require_once WPLNG_PLUGIN_DIR . '/inc/api-key.php';
require_once WPLNG_PLUGIN_DIR . '/inc/args.php';
require_once WPLNG_PLUGIN_DIR . '/inc/assets.php';
require_once WPLNG_PLUGIN_DIR . '/inc/browser-language.php';
require_once WPLNG_PLUGIN_DIR . '/inc/buffering.php';
require_once WPLNG_PLUGIN_DIR . '/inc/cache.php';
require_once WPLNG_PLUGIN_DIR . '/inc/dictionary.php';
require_once WPLNG_PLUGIN_DIR . '/inc/encryption.php';
require_once WPLNG_PLUGIN_DIR . '/inc/heartbeat.php';
require_once WPLNG_PLUGIN_DIR . '/inc/hreflang.php';
require_once WPLNG_PLUGIN_DIR . '/inc/i18n-script.php';
require_once WPLNG_PLUGIN_DIR . '/inc/languages.php';
require_once WPLNG_PLUGIN_DIR . '/inc/link-media.php';
require_once WPLNG_PLUGIN_DIR . '/inc/search.php';
require_once WPLNG_PLUGIN_DIR . '/inc/shortcode.php';
require_once WPLNG_PLUGIN_DIR . '/inc/sitemap.php';
require_once WPLNG_PLUGIN_DIR . '/inc/slug.php';
require_once WPLNG_PLUGIN_DIR . '/inc/switcher-block.php';
require_once WPLNG_PLUGIN_DIR . '/inc/switcher-nav-menu.php';
require_once WPLNG_PLUGIN_DIR . '/inc/switcher.php';
require_once WPLNG_PLUGIN_DIR . '/inc/translation.php';
require_once WPLNG_PLUGIN_DIR . '/inc/url.php';
require_once WPLNG_PLUGIN_DIR . '/inc/util.php';

// Require files in /inc/admin/ folder
require_once WPLNG_PLUGIN_DIR . '/inc/admin/admin-bar.php';
require_once WPLNG_PLUGIN_DIR . '/inc/admin/admin.php';
require_once WPLNG_PLUGIN_DIR . '/inc/admin/assets.php';
require_once WPLNG_PLUGIN_DIR . '/inc/admin/switcher-nav-menu.php';
require_once WPLNG_PLUGIN_DIR . '/inc/admin/option-page-link-media.php';
require_once WPLNG_PLUGIN_DIR . '/inc/admin/option-page-dictionary.php';
require_once WPLNG_PLUGIN_DIR . '/inc/admin/option-page-exclusions.php';
require_once WPLNG_PLUGIN_DIR . '/inc/admin/option-page-register.php';
require_once WPLNG_PLUGIN_DIR . '/inc/admin/option-page-settings.php';
require_once WPLNG_PLUGIN_DIR . '/inc/admin/option-page-switcher.php';
require_once WPLNG_PLUGIN_DIR . '/inc/admin/option-page.php';
require_once WPLNG_PLUGIN_DIR . '/inc/admin/slug-cpt.php';
require_once WPLNG_PLUGIN_DIR . '/inc/admin/slug-meta.php';
require_once WPLNG_PLUGIN_DIR . '/inc/admin/translation-cpt.php';
require_once WPLNG_PLUGIN_DIR . '/inc/admin/translation-edit-modal.php';
require_once WPLNG_PLUGIN_DIR . '/inc/admin/translation-meta.php';

// Require files in /inc/api-call/ folder
require_once WPLNG_PLUGIN_DIR . '/inc/api/request-api-key.php';
require_once WPLNG_PLUGIN_DIR . '/inc/api/translate.php';
require_once WPLNG_PLUGIN_DIR . '/inc/api/validate-api-key.php';

// Require files in /inc/dom/ folder
require_once WPLNG_PLUGIN_DIR . '/inc/dom/exclusion-put-tags.php';
require_once WPLNG_PLUGIN_DIR . '/inc/dom/exclusion-replace-tags.php';
require_once WPLNG_PLUGIN_DIR . '/inc/dom/load-overload.php';
require_once WPLNG_PLUGIN_DIR . '/inc/dom/load-progress.php';
require_once WPLNG_PLUGIN_DIR . '/inc/dom/mode-editor.php';
require_once WPLNG_PLUGIN_DIR . '/inc/dom/mode-list.php';
require_once WPLNG_PLUGIN_DIR . '/inc/dom/replace-attr-dir.php';
require_once WPLNG_PLUGIN_DIR . '/inc/dom/replace-attr-lang.php';
require_once WPLNG_PLUGIN_DIR . '/inc/dom/replace-body-class.php';
require_once WPLNG_PLUGIN_DIR . '/inc/dom/replace-links.php';
require_once WPLNG_PLUGIN_DIR . '/inc/dom/translate-attr-html.php';
require_once WPLNG_PLUGIN_DIR . '/inc/dom/translate-attr-json.php';
require_once WPLNG_PLUGIN_DIR . '/inc/dom/translate-attr-texts.php';
require_once WPLNG_PLUGIN_DIR . '/inc/dom/translate-node-texts.php';
require_once WPLNG_PLUGIN_DIR . '/inc/dom/translate-script.php';

// Require files in /inc/lib/ folder
require_once WPLNG_PLUGIN_DIR . '/inc/lib/simple-html-dom.php';

// Require files in /inc/parser/ folder
require_once WPLNG_PLUGIN_DIR . '/inc/parser/html.php';
require_once WPLNG_PLUGIN_DIR . '/inc/parser/js.php';
require_once WPLNG_PLUGIN_DIR . '/inc/parser/json.php';

// Require files in /inc/translator/ folder
require_once WPLNG_PLUGIN_DIR . '/inc/translator/html.php';
require_once WPLNG_PLUGIN_DIR . '/inc/translator/js.php';
require_once WPLNG_PLUGIN_DIR . '/inc/translator/json.php';
