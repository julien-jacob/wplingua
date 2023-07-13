<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_option_page_settings_assets( $hook ) {

	if ( ! is_admin()
		|| $hook !== 'toplevel_page_wplng-settings'
		|| empty( wplng_get_api_key() )
	) {
		return;
	}

	wp_enqueue_script( 'jquery' );

	wp_enqueue_script(
		'wplingua-option-settings',
		plugins_url() . '/wplingua/js/admin/option-page-settings.js',
		array( 'jquery' )
	);

	wp_enqueue_style(
		'wplingua-option-settings',
		plugins_url() . '/wplingua/css/admin/option-page-settings.css'
	);

	wp_enqueue_style(
		'wplingua-option-pages',
		plugins_url() . '/wplingua/css/admin/option-page.css'
	);
}


function wplng_option_page_register_assets( $hook ) {

	if ( ! is_admin()
		|| $hook !== 'toplevel_page_wplng-settings'
	) {
		return;
	}

	wp_enqueue_script( 'jquery' );

	wp_enqueue_script(
		'wplingua-option-register',
		plugins_url() . '/wplingua/js/admin/option-page-register.js',
		array( 'jquery' )
	);

	wp_enqueue_style(
		'wplingua-option-register',
		plugins_url() . '/wplingua/css/admin/option-page-register.css'
	);

	wp_enqueue_style(
		'wplingua-option-pages',
		plugins_url() . '/wplingua/css/admin/option-page.css'
	);
}


function wplng_option_page_switcher_assets( $hook ) {
	
	if ( ! is_admin()
		|| $hook !== 'wplingua_page_wplng-switcher'
	) {
		return;
	}

	wp_enqueue_script( 'jquery' );

	wp_enqueue_script(
		'wplingua-option-switcher',
		plugins_url() . '/wplingua/js/admin/option-page-switcher.js',
		array( 'jquery' )
	);

	wp_enqueue_style(
		'wplingua-option-switcher',
		plugins_url() . '/wplingua/css/admin/option-page-switcher.css'
	);

	wp_enqueue_style(
		'wplingua-option-pages',
		plugins_url() . '/wplingua/css/admin/option-page.css'
	);

	if ( function_exists( 'wp_enqueue_code_editor' ) ) {
		$cm_settings['codeEditor'] = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
		wp_localize_script( 'jquery', 'cm_settings', $cm_settings );
		wp_enqueue_script( 'wp-theme-plugin-editor' );
		wp_enqueue_style( 'wp-codemirror' );
	}
}


function wplng_option_page_exclusions_assets( $hook ) {

	if ( ! is_admin()
		|| $hook !== 'wplingua_page_wplng-exclusions'
	) {
		return;
	}

	wp_enqueue_script( 'jquery' );

	wp_enqueue_script(
		'wplingua-option-exclusions',
		plugins_url() . '/wplingua/js/admin/option-page-exclusions.js',
		array( 'jquery' )
	);

	wp_enqueue_style(
		'wplingua-option-exclusions',
		plugins_url() . '/wplingua/css/admin/option-page-exclusions.css'
	);

	wp_enqueue_style(
		'wplingua-option-pages',
		plugins_url() . '/wplingua/css/admin/option-page.css'
	);

}


function wplng_translation_assets() {
	global $post_type;
	if ( 'wplng_translation' === $post_type ) {

		wp_enqueue_script(
			'wplingua-translation',
			plugins_url() . '/wplingua/js/admin/translation.js',
			array( 'jquery' )
		);

		wp_enqueue_style(
			'wplingua-translation',
			plugins_url() . '/wplingua/css/admin/translation.css'
		);

	}

}


function wplng_inline_script_languages() {

	$languages_json  = array();
	$languages_allow = wplng_get_languages_allow();

	if ( ! empty($languages_allow) && is_array( $languages_allow ) ) {
		$language_website = wplng_get_language_website();
		if ( ! in_array( $language_website, $languages_allow, true ) ) {
			$languages_allow[] = $language_website;
		}
		$languages_json = wp_json_encode( $languages_allow );
	} else {
		$languages_json = wplng_get_languages_all_json();
	}

	?><script>var wplngAllLanguages = JSON.parse('<?php echo $languages_json; ?>');</script>
	<?php
}
