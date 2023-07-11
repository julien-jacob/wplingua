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
		'wplingua-settings',
		plugins_url() . '/wplingua/js/admin/option-page-settings.js',
		array( 'jquery' )
	);

	wp_enqueue_style(
		'wplingua-settings',
		plugins_url() . '/wplingua/css/admin/option-page-settings.css'
	);
}


function wplng_option_page_register_assets( $hook ) {

	if ( ! is_admin()
		|| $hook !== 'toplevel_page_wplng-settings'
		// || empty( wplng_get_api_key() )
	) {
		return;
	}

	wp_enqueue_script( 'jquery' );

	wp_enqueue_script(
		'wplingua-register',
		plugins_url() . '/wplingua/js/admin/option-page-register.js',
		array( 'jquery' )
	);

	wp_enqueue_style(
		'wplingua-register',
		plugins_url() . '/wplingua/css/admin/option-page-register.css'
	);
}



function wplng_option_page_exclusions_assets( $hook ) {

	if ( ! is_admin()
		|| $hook !== 'toplevel_page_wplingua/inc/admin/option-page'
	) {
		return;
	}

	wp_enqueue_script( 'jquery' );

	wp_enqueue_script(
		'wplingua-option',
		plugins_url() . '/wplingua/js/admin/option-page.js',
		array( 'jquery' )
	);

	wp_enqueue_style(
		'wplingua-option',
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

	// $languages_json = wplng_get_languages_all_json();

	// echo '<pre>' . var_export( wp_json_encode( $languages_allow ), true ) . '</pre>';
	// echo '<pre>' . var_export($languages_json, true) . '</pre>';
	// echo '<pre>' . var_export(! in_array( $language_website, $languages_allow, true ), true) . '</pre>';
	
	// die;

	?><script>var wplngAllLanguages = JSON.parse('<?php echo $languages_json; ?>');</script>
	<?php
}
