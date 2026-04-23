<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Register wpLingua assets for option page : Settings
 *
 * @param string $hook
 * @return void
 */
function wplng_option_page_settings_assets( $hook ) {

	if ( ! is_admin()
		|| $hook !== 'toplevel_page_wplingua-settings'
		|| empty( wplng_get_api_key() )
	) {
		return;
	}

	/**
	 * Enqueue jQuery
	 */

	wp_enqueue_script( 'jquery' );

	/**
	 * Enqueue wpLingua JS scripts
	 */

	wp_enqueue_script(
		'wplingua-option',
		plugins_url() . '/wplingua/assets/js/admin/option-page.js',
		array( 'jquery' ),
		WPLNG_PLUGIN_VERSION
	);

	wp_enqueue_script(
		'wplingua-option-settings',
		plugins_url() . '/wplingua/assets/js/admin/option-page-settings.js',
		array( 'jquery' ),
		WPLNG_PLUGIN_VERSION
	);

	/**
	 * Enqueue wpLingua CSS styles
	 */

	wp_enqueue_style(
		'wplingua-option-settings',
		plugins_url() . '/wplingua/assets/css/admin/option-page-settings.css',
		array(),
		WPLNG_PLUGIN_VERSION
	);
}


/**
 * Register wpLingua assets for option page : Register
 *
 * @param string $hook
 * @return void
 */
function wplng_option_page_register_assets( $hook ) {

	if ( ! is_admin()
		|| $hook !== 'toplevel_page_wplingua-settings'
	) {
		return;
	}

	/**
	 * Enqueue jQuery
	 */

	wp_enqueue_script( 'jquery' );

	/**
	 * Enqueue wpLingua JS scripts
	 */

	wp_enqueue_script(
		'wplingua-option',
		plugins_url() . '/wplingua/assets/js/admin/option-page.js',
		array( 'jquery' ),
		WPLNG_PLUGIN_VERSION
	);

	wp_enqueue_script(
		'wplingua-option-register',
		plugins_url() . '/wplingua/assets/js/admin/option-page-register.js',
		array( 'jquery' ),
		WPLNG_PLUGIN_VERSION
	);

	/**
	 * Enqueue wpLingua CSS styles
	 */

	wp_enqueue_style(
		'wplingua-option-register',
		plugins_url() . '/wplingua/assets/css/admin/option-page-register.css',
		array(),
		WPLNG_PLUGIN_VERSION
	);
}


/**
 * Register wpLingua assets for option page : Switcher
 *
 * @param string $hook The current admin page hook.
 * @return void
 */
function wplng_option_page_switcher_assets( $hook ) {

	if ( ! is_admin()
		|| $hook !== 'wplingua_page_wplingua-switcher'
	) {
		return;
	}

	/**
	 * Enqueue jQuery
	 */

	wp_enqueue_script( 'jquery' );

	/**
	 * Enqueue wpLingua JS scripts
	 */

	wp_enqueue_script(
		'wplingua-option-switcher',
		plugins_url() . '/wplingua/assets/js/admin/option-page-switcher.js',
		array( 'jquery' ),
		WPLNG_PLUGIN_VERSION
	);

	wp_enqueue_script(
		'wplingua-option',
		plugins_url() . '/wplingua/assets/js/admin/option-page.js',
		array( 'jquery' ),
		WPLNG_PLUGIN_VERSION
	);

	wp_enqueue_script(
		'wplingua-script',
		plugins_url() . '/wplingua/assets/js/front.js',
		array(),
		WPLNG_PLUGIN_VERSION
	);

	/**
	 * Enqueue wpLingua CSS styles
	 */

	wp_enqueue_style(
		'wplingua-option-switcher',
		plugins_url() . '/wplingua/assets/css/admin/option-page-switcher.css',
		array(),
		WPLNG_PLUGIN_VERSION
	);

	wp_enqueue_style(
		'wplingua',
		plugins_url() . '/wplingua/assets/css/front.css',
		array(),
		WPLNG_PLUGIN_VERSION
	);

	/**
	 * Enqueue CSS and JS for the code editor
	 */

	if ( function_exists( 'wp_enqueue_code_editor' ) ) {
		$cm_settings               = array();
		$cm_settings['codeEditor'] = wp_enqueue_code_editor(
			array( 'type' => 'text/css' )
		);

		wp_localize_script( 'jquery', 'cm_settings', $cm_settings );
		wp_enqueue_script( 'wp-theme-plugin-editor' );
		wp_enqueue_style( 'wp-codemirror' );
	}

	/**
	 * Add inline style for wpLingua custom CSS
	 */

	$custom_css = get_option( 'wplng_custom_css' );

	if ( ! empty( $custom_css )
		&& is_string( $custom_css )
	) {
		$custom_css = wp_strip_all_tags( $custom_css );
		wp_add_inline_style( 'wplingua', $custom_css );
	}
}


/**
 * Register wpLingua assets for option page : Exclusions
 *
 * @param string $hook
 * @return void
 */
function wplng_option_page_exclusions_assets( $hook ) {

	if ( ! is_admin()
		|| $hook !== 'wplingua_page_wplingua-exclusions'
	) {
		return;
	}

	/**
	 * Enqueue jQuery
	 */

	wp_enqueue_script( 'jquery' );

	/**
	 * Enqueue wpLingua CSS styles
	 */

	wp_enqueue_style(
		'wplingua-option-exclusions',
		plugins_url() . '/wplingua/assets/css/admin/option-page-exclusions.css',
		array(),
		WPLNG_PLUGIN_VERSION
	);
}


/**
 * Register wpLingua assets for option page : Links & Medias
 *
 * @param string $hook
 * @return void
 */
function wplng_option_page_link_media_assets( $hook ) {

	if ( ! is_admin()
		|| $hook !== 'wplingua_page_wplingua-link-media'
	) {
		return;
	}

	/**
	 * Enqueue jQuery
	 */

	wp_enqueue_script( 'jquery' );

	/**
	 * Enqueue wpLingua JS scripts
	 */

	wp_enqueue_script(
		'wplingua-option',
		plugins_url() . '/wplingua/assets/js/admin/option-page.js',
		array( 'jquery' ),
		WPLNG_PLUGIN_VERSION
	);

	wp_enqueue_script(
		'wplingua-option-link-media',
		plugins_url() . '/wplingua/assets/js/admin/option-page-link-media.js',
		array( 'jquery' ),
		WPLNG_PLUGIN_VERSION
	);

	/**
	 * Enqueue wpLingua CSS styles
	 */

	wp_enqueue_style(
		'wplingua-option-link-media',
		plugins_url() . '/wplingua/assets/css/admin/option-page-link-media.css',
		array(),
		WPLNG_PLUGIN_VERSION
	);
}


/**
 * Register wpLingua assets for option page : Dictionary
 *
 * @param string $hook
 * @return void
 */
function wplng_option_page_dictionary_assets( $hook ) {

	if ( ! is_admin()
		|| $hook !== 'wplingua_page_wplingua-dictionary'
	) {
		return;
	}

	/**
	 * Enqueue jQuery
	 */

	wp_enqueue_script( 'jquery' );

	/**
	 * Enqueue wpLingua JS scripts
	 */

	wp_enqueue_script(
		'wplingua-option',
		plugins_url() . '/wplingua/assets/js/admin/option-page.js',
		array( 'jquery' ),
		WPLNG_PLUGIN_VERSION
	);

	wp_enqueue_script(
		'wplingua-option-dictionary',
		plugins_url() . '/wplingua/assets/js/admin/option-page-dictionary.js',
		array( 'jquery' ),
		WPLNG_PLUGIN_VERSION
	);

	wp_localize_script(
		'wplingua-option-dictionary',
		'wplngDictionaryData',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'wplng_dictionary_ajax' ),
			'i18n'    => array(
				'loadingTitle'    => esc_html__( 'Analyzing changes…', 'wplingua' ),
				'confirmTitle'    => esc_html__( 'Confirm dictionary changes', 'wplingua' ),
				'confirmMessage'  => esc_html__( '%d translation(s) will be affected by this change. Non-reviewed translations will be deleted so they can be re-generated with the new rules.', 'wplingua' ),
				'confirmButton'   => esc_html__( 'Validate changes', 'wplingua' ),
				'cancelButton'    => esc_html__( 'Cancel', 'wplingua' ),
				'progressTitle'   => esc_html__( 'Processing translations…', 'wplingua' ),
				'progressText'    => esc_html__( '%1$d / %2$d translations processed', 'wplingua' ),
				'successTitle'    => esc_html__( 'Changes saved', 'wplingua' ),
				'successMessage'  => esc_html__( 'Dictionary rules saved. Affected translations will be re-generated automatically.', 'wplingua' ),
				'noImpactMessage' => esc_html__( 'No existing translations are affected. Saving…', 'wplingua' ),
				'errorTitle'      => esc_html__( 'An error occurred', 'wplingua' ),
				'errorMessage'    => esc_html__( 'An error occurred. Please reload the page and try again.', 'wplingua' ),
				'errorCloseBtn'   => esc_html__( 'Close', 'wplingua' ),
			),
		)
	);

	/**
	 * Enqueue wpLingua CSS styles
	 */

	wp_enqueue_style(
		'wplingua-option-dictionary',
		plugins_url() . '/wplingua/assets/css/admin/option-page-dictionary.css',
		array(),
		WPLNG_PLUGIN_VERSION
	);

	wp_add_inline_style(
		'wplingua-option-dictionary',
		'
		#wplng-dictionary-confirm-section .button { margin-right: 8px; }
		#wplng-dictionary-progress-wrap {
			background: #f0f0f1;
			border-radius: 3px;
			height: 18px;
			overflow: hidden;
			margin: 8px 0;
		}
		#wplng-dictionary-progress-bar {
			height: 100%;
			background: #2271b1;
			transition: width .25s ease;
			width: 0%;
		}
		#wplng-dictionary-success-section .dashicons { vertical-align: middle; }
		'
	);
}


/**
 * Register wpLingua assets on translations edit pages
 *
 * @return void
 */
function wplng_translation_edit_assets() {

	global $post_type;

	if ( 'wplng_translation' === $post_type ) {

		/**
		 * Enqueue jQuery
		 */

		wp_enqueue_script( 'jquery' );

		/**
		 * Enqueue wpLingua JS scripts
		 */

		wp_enqueue_script(
			'wplingua-edit-translation',
			plugins_url() . '/wplingua/assets/js/admin/edit-translation.js',
			array( 'jquery' ),
			WPLNG_PLUGIN_VERSION
		);

		/**
		 * Localize script
		 */

		wp_localize_script(
			'wplingua-edit-translation',
			'wplngI18nTranslation',
			array(
				'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
				'currentLanguage' => false,
				'message'         => array(
					'exitPage'             => esc_html__(
						'You are about to leave the page without saving your changes. They will be lost if you continue. Would you like to leave the page anyway?',
						'wplingua'
					),
					'exitEditorModal'      => '',
					'buttonSave'           => '',
					'buttonSaveInProgress' => '',
				),
			)
		);

		/**
		 * Enqueue wpLingua CSS styles
		 */

		wp_enqueue_style(
			'wplingua-edit-translation',
			plugins_url() . '/wplingua/assets/css/admin/edit-translation.css',
			array(),
			WPLNG_PLUGIN_VERSION
		);

	}
}



/**
 * Register wpLingua assets on translations admin list
 *
 * @return void
 */
function wplng_translation_list_assets() {

	global $post_type;

	if ( 'wplng_translation' === $post_type ) {

		/**
		 * Enqueue wpLingua CSS styles
		 */

		wp_enqueue_style(
			'wplingua-list-translation',
			plugins_url() . '/wplingua/assets/css/admin/list-translation.css',
			array(),
			WPLNG_PLUGIN_VERSION
		);

	}
}


/**
 * Register wpLingua assets on slugs edit pages
 *
 * @return void
 */
function wplng_slug_edit_assets() {

	global $post_type;

	if ( 'wplng_slug' === $post_type ) {

		/**
		 * Enqueue jQuery
		 */

		wp_enqueue_script( 'jquery' );

		/**
		 * Enqueue wpLingua JS scripts
		 */

		wp_enqueue_script(
			'wplingua-edit-slug',
			plugins_url() . '/wplingua/assets/js/admin/edit-slug.js',
			array( 'jquery' ),
			WPLNG_PLUGIN_VERSION
		);

		/**
		 * Localize script
		 */

		wp_localize_script(
			'wplingua-edit-slug',
			'wplngI18nSlug',
			array(
				'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
				'currentLanguage' => false,
				'message'         => array(
					'exitPage'             => esc_html__(
						'You are about to leave the page without saving your changes. They will be lost if you continue. Would you like to leave the page anyway?',
						'wplingua'
					),
					'exitEditorModal'      => '',
					'buttonSave'           => '',
					'buttonSaveInProgress' => '',
				),
			)
		);

		/**
		 * Enqueue wpLingua CSS styles
		 */

		wp_enqueue_style(
			'wplingua-edit-slug',
			plugins_url() . '/wplingua/assets/css/admin/edit-slug.css',
			array(),
			WPLNG_PLUGIN_VERSION
		);

	}
}


/**
 * Register wpLingua assets on slugs admin list
 *
 * @return void
 */
function wplng_slug_list_assets() {

	global $post_type;

	if ( 'wplng_slug' === $post_type ) {

		/**
		 * Enqueue wpLingua CSS styles
		 */

		wp_enqueue_style(
			'wplingua-list-slug',
			plugins_url() . '/wplingua/assets/css/admin/list-slug.css',
			array(),
			WPLNG_PLUGIN_VERSION
		);

	}
}


/**
 * Print wpLingua head script (JSON with all languages informations)
 *
 * @return void
 */
function wplng_inline_script_languages() {

	$languages       = array();
	$languages_allow = wplng_get_languages_allow();

	if ( ! empty( $languages_allow ) && is_array( $languages_allow ) ) {

		$language_website = wplng_get_language_website();

		if ( ! in_array( $language_website, $languages_allow, true ) ) {
			$languages_allow[] = $language_website;
		}

		$languages = $languages_allow;

	} else {
		$languages = wplng_get_languages_all();
	}

	?><script>var wplngAllLanguages = <?php echo wp_json_encode( $languages ); ?>;</script>
	<?php
}
