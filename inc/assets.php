<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Register asset for wpLingua in front (CSS, JS)
 *
 * @return void
 */
function wplng_register_assets() {

	if ( is_admin() || ! wplng_url_is_translatable() ) {
		return;
	}

	/**
	 * Enqueue jQuery
	 */

	wp_enqueue_script( 'jquery' );

	/**
	 * Enqueue wpLingua JS script
	 */

	wp_enqueue_script(
		'wplingua-script',
		plugins_url() . '/wplingua/assets/js/front.js',
		array( 'jquery' ),
		WPLNG_PLUGIN_VERSION
	);

	/**
	 * Enqueue wpLingua CSS style
	 */

	wp_enqueue_style(
		'wplingua',
		plugins_url() . '/wplingua/assets/css/front.css',
		array(),
		WPLNG_PLUGIN_VERSION
	);

	/**
	 * Add scripts and CSS for translation editor
	 */

	if ( ! empty( $_GET['wplng-mode'] )
		&& empty( $_GET['wplng-load'] )
		&& (
			'editor' === $_GET['wplng-mode']
			|| 'list' === $_GET['wplng-mode']
		)
	) {

		wp_enqueue_script(
			'wplingua-translation',
			plugins_url() . '/wplingua/assets/js/admin/edit-translation.js',
			array( 'jquery' ),
			WPLNG_PLUGIN_VERSION
		);

		wp_localize_script(
			'wplingua-translation',
			'wplngI18nTranslation',
			array(
				'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
				'currentLanguage' => wplng_get_language_current_id(),
				'message'         => array(
					'exitPage'             => esc_html__(
						'You are about to leave the page without saving your changes. They will be lost if you continue. Would you like to leave the page anyway?',
						'wplingua'
					),
					'exitEditorModal'      => esc_html__(
						'You are about to exit without saving your changes. They will be lost if you continue. Would you like to leave anyway?',
						'wplingua'
					),
					'buttonSave'           => esc_html__( 'Save', 'wplingua' ),
					'buttonSaveInProgress' => esc_html__( 'Save in progress...', 'wplingua' ),
				),
			)
		);

	}

}


/**
 * Add custom inline styles for wpLingua.
 *
 * This function retrieves custom CSS from the database (stored in the 'wplng_custom_css' option),
 * sanitizes it, and outputs it directly into the <head> section of the page.
 *
 * @return void
 */
function wplng_add_custom_inline_styles() {

	if ( ! empty( $_GET['wplng-mode'] )
		&& 'list' === $_GET['wplng-mode']
	) {
		return;
	}

	$custom_css = get_option( 'wplng_custom_css' );

	if ( ! empty( $custom_css ) && is_string( $custom_css ) ) {
		$custom_css = wp_strip_all_tags( $custom_css );
		echo '<style id="wplingua-inline-styles">' . $custom_css . '</style>';
	}
}


/**
 * Print the on page scrip
 *
 * @return void
 */
function wplng_on_page_script() {

	if ( ! empty( $_GET['wplng-load'] ) ) {
		return;
	}

	$script = file_get_contents( WPLNG_PLUGIN_PATH . '/assets/js/on-page.js' );

	if ( empty( $script ) ) {
		return;
	}

	$script = str_replace(
		array( '[admin-ajax-php]', '//# sourceMappingURL=on-page.js.map' ),
		array( admin_url( 'admin-ajax.php' ), '' ),
		$script
	);

	echo '<script id="wplingua-js-on-page">' . rtrim( $script ) . '</script>';
}
