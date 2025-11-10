<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Print HTML Option page : wpLingua Settings
 *
 * @return void
 */
function wplng_option_page_settings() {

	delete_option( 'wplng_api_key_data' );

	if ( empty( wplng_get_api_data() ) ) {
		wplng_option_page_register();
		return;
	}

	?>

	<h1 class="wplng-option-page-title"><span class="dashicons dashicons-translation"></span> <?php esc_html_e( 'wpLingua - General settings', 'wplingua' ); ?></h1>

	<div class="wrap">
		<hr class="wp-header-end">
		<?php

		$is_first = wplng_settings_part_first_use();
		$form_css = '';

		if ( $is_first ) {
			$form_css = 'display: none !important;';
		}

		?>
		<form 
			method="post" 
			action="options.php" 
			id="wplng-option-settings-form" 
			style="<?php echo esc_attr( $form_css ); ?>"
		>
			<?php
			settings_fields( 'wplng_settings' );
			do_settings_sections( 'wplng_settings' );
			?>

			<table class="form-table wplng-form-table">
				<tr>
					<th scope="row"><span class="dashicons dashicons-location"></span> <?php esc_html_e( 'Website language', 'wplingua' ); ?></th>
					<td>
						<?php wplng_settings_part_language_website(); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><span class="dashicons dashicons-location-alt"></span> <?php esc_html_e( 'Translated languages', 'wplingua' ); ?></th>
					<td>
						<?php wplng_settings_part_languages_target(); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><span class="dashicons dashicons-admin-settings"></span> <?php esc_html_e( 'Features', 'wplingua' ); ?></th>
					<td>
						<fieldset>
							<?php wplng_settings_part_features_api(); ?>
							<br><br><hr>
							<?php wplng_settings_part_features_seo(); ?>
							<br><br><hr>
							<?php wplng_settings_part_features_more(); ?>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><span class="dashicons dashicons-admin-network"></span> <?php _e( 'API Key', 'wplingua' ); ?></th>
					<td>
						<?php wplng_settings_part_api_key(); ?>
					</td>
				</tr>
				<tr class="wplng-tr-submit">
					<th scope="row"><span class="dashicons dashicons-yes-alt"></span> <?php _e( 'Save', 'wplingua' ); ?></th>
					<td>
						<?php submit_button(); ?>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<?php
}


/**
 * Print HTML subsection of Option page : wpLingua Settings - First use
 *
 * @return void
 */
function wplng_settings_part_first_use() {

	if ( ! empty( get_option( 'wplng_website_language' ) )
		|| 'all' === wplng_get_api_language_website()
	) {
		return false;
	}

	update_option(
		'wplng_website_language',
		wplng_get_api_language_website()
	);

	$data = wplng_get_api_data();

	// Set option for target language

	if ( empty( $data['languages_target'][0] )
		|| count( $data['languages_target'] ) !== 1
	) {
		return false;
	}

	$language_target = wplng_get_language_by_id(
		$data['languages_target'][0]
	);

	if ( ! empty( $language_target ) ) {

		global $wplng_languages_target_simplified;
		$wplng_languages_target_simplified = array( $language_target );

		update_option(
			'wplng_target_languages',
			wp_json_encode(
				array( $language_target )
			)
		);

		update_option( 'wplng_flags_style', 'rectangular' );

	} else {
		return false;
	}

	// Enable SEO feature
	update_option( 'wplng_sitemap_xml', 1 );
	update_option( 'wplng_hreflang', 1 );

	// Get URL for first registered language of front page
	$url_front_page_translated = wplng_url_translate(
		get_home_url(),
		$language_target['id']
	);

	$url_front_page_load = add_query_arg(
		array(
			'wplng-load' => 'disabled',
			'nocache'    => (string) time() . (string) rand( 100, 999 ),
		),
		$url_front_page_translated
	);

	?>
	<div class="wplng-notice notice notice-info" id="wplng-notice-first-loading-loading" data-wplng-url-first-load="<?php echo esc_url( $url_front_page_load ); ?>">
		<h2><span class="dashicons dashicons-update wplng-spin"></span> <?php esc_html_e( 'Your website is being translated and will be ready soon.', 'wplingua' ); ?></h2>
		<p><?php esc_html_e( 'In just a few seconds, your website will be multilingual, and search engines will be able to index these new pages. wpLingua detects all the texts on your pages and offers you a first automatically generated translation. All translations are editable: open the visual editor from the administration bar and edit them simply by clicking on the texts on your website.', 'wplingua' ); ?></p>
	</div>

	<div class="wplng-notice notice notice-success is-dismissible" id="wplng-notice-first-loading-loaded" style="display: none;">
		<h2>🎉 <?php esc_html_e( 'Your website is now multilingual! You can start visiting the translated version.', 'wplingua' ); ?></h2>
		<p><?php esc_html_e( 'The first time a translated page is loaded, the translations are automatically generated and saved in the database. This may take some time (on first generation only) depending on the size of your content. This is why we advise you to browse your entire website for the first time in order to generate all the multilingual versions.', 'wplingua' ); ?></p>
		<p><?php esc_html_e( 'All translations are editable: open the visual editor from the administration bar and edit them simply by clicking on the texts on your website.', 'wplingua' ); ?></p>
		<p>
			<a href="<?php echo esc_url( $url_front_page_translated ); ?>" target="_blank" class="button button-primary">
				<?php esc_html_e( 'Visit your multilingual website', 'wplingua' ); ?>
			</a>
		</p>
	</div>
	<?php

	return true;
}


/**
 * Print HTML subsection of Option page : wpLingua Settings - Website language
 *
 * @return void
 */
function wplng_settings_part_language_website() {

	$api_language_website = wplng_get_api_language_website();

	if ( 'all' !== $api_language_website ) {
		echo '<fieldset style="display: none;">';
	} else {
		echo '<fieldset>';
	}

	echo '<label for="wplng_website_language" class="wplng-fe-50">';
	echo '<strong>';
	esc_html_e( 'Original website language: ', 'wplingua' );
	echo ' </strong>';
	echo '</label>';

	echo '<select id="wplng_website_language" name="wplng_website_language" class="wplng-fe-50">';
	$website_language_saved = true;
	if ( empty( wplng_get_language_website_id() ) ) {
		$website_language_saved = false;
	} else {

		$website_language_id = wplng_get_language_website_id();
		$website_language    = wplng_get_language_by_id( $website_language_id );

		if ( ! empty( $website_language['id'] )
			&& ! empty( $website_language['name'] )
		) {
			echo '<option value="' . esc_attr( $website_language['id'] ) . '">';
			echo esc_html( $website_language['name'] );
			echo '</option>';
		} else {
			$website_language_saved = false;
		}
	}

	if ( ! $website_language_saved ) {
		echo '<option value="">';
		esc_html_e( 'Please choose an option', 'wplingua' );
		echo '</option>';
	}

	echo '</select>';
	echo '<hr>';
	echo '</fieldset>';

	if ( 'all' !== $api_language_website ) {
		echo '<p>';
		echo '<strong>';
		esc_html_e( 'Original website language, defined by API key: ', 'wplingua' );
		echo ' </strong>';
		echo '<span';
		echo ' title="' . esc_attr__( 'Click to expand', 'wplingua' ) . '"';
		echo ' wplng-help-box="#wplng-hb-language-website"';
		echo '></span>';
		echo ' </p>';

		echo '<div class="wplng-help-box" id="wplng-hb-language-website">';
		echo '<p>';
		echo esc_html__( 'This is the language of your website, defined by the associated API key. Make sure your website language is also correctly set in WordPress options (Settings ➔ General ➔ Website Language).', 'wplingua' );
		echo '<hr>';
		echo esc_html__( 'If you have mistakenly selected the wrong language, you can delete the API key in the site options below and request a new API key.', 'wplingua' );
		echo '</p>';
		echo '</div>';
	}
	?>

	<div id="wplng-flags-radio-original-website-custom"><?php _e( 'Custom', 'wplingua' ); ?></div>

	<div id="wplng-website-language-box">

		<div class="wplng-website-language-displayed">
			<div id="wplng-website-language" class="wplng-website-language-left">
				<img src="<?php echo esc_url( wplng_get_language_website_flag() ); ?>" id="wplng-website-flag"><?php echo esc_html( $website_language['name'] ); ?>
			</div>
			<div class="wplng-target-language-right">
				<a href="javascript:void(0);" id="wplng-website-lang-update-flag"><?php esc_html_e( 'Edit', 'wplingua' ); ?></a>
			</div>
		</div>

		<div id="wplng-flag-website-container">
			<p><strong><?php esc_html_e( 'Flag to use for this language: ', 'wplingua' ); ?></strong></p>
			<p><span id="wplng-flags-radio-original-website"></span></p>

			<div id="wplng-website-flag-container">
				<hr>
				<p>
					<strong><?php esc_html_e( 'Custom flag URL for this language: ', 'wplingua' ); ?></strong>
				</p>
				<input type="url" name="wplng_website_flag" id="wplng_website_flag" value="<?php echo esc_url( wplng_get_language_website_flag() ); ?>"/>
			</div>
		</div>

	</div>
	<?php
}


/**
 * Print HTML subsection of Option page : wpLingua Settings - Languages target
 *
 * @return void
 */
function wplng_settings_part_languages_target() {

	$languages_target = wplng_get_languages_target_simplified();

	?>
	<fieldset id="fieldset-add-target-language">

		<div id="wplng_add_new_target_language_message" style="display: none !important;"><?php esc_html_e( 'Important: space out the addition of new languages ​​by at least ten days and browse your site for the first time in the added languages. This promotes better indexing by search engines, reduces the load on your server and minimizes the risk of slowdowns on your site.', 'wplingua' ); ?></div>

		<p class="wplng-fe-50">
			<label for="wplng_add_new_target_language">
				<strong><?php esc_html_e( 'Add new target Language: ', 'wplingua' ); ?></strong>
			</label>
		</p>

		<p class="wplng-fe-50">
			<select id="wplng_add_new_target_language" name="wplng_add_new_target_language"></select>
			<a class="button button-primary wplng-icon-button" id="wplng-target-lang-add" title="<?php esc_html_e( 'Add language', 'wplingua' ); ?>" href="javascript:void(0);">
				<span class="dashicons dashicons-insert"></span>
			</a>
		</p>
	</fieldset>

	<hr id="wplng-languages-target-separator">

	<div id="wplng-target-language-template">
		<div class="wplng-target-language">

			<div class="wplng-target-language-displayed">
				<div class="wplng-target-language-left">
					[FLAG][NAME]<span class="wplng-private-label"> <?php esc_html_e( '(Private)', 'wplingua' ); ?></span>
				</div>

				<div class="wplng-target-language-right">
					<a href="javascript:void(0);" class="wplng-target-lang-move-up" wplng-target-lang="[LANG]" title="<?php esc_attr_e( 'Move up', 'wplingua' ); ?>"><span class="dashicons dashicons-arrow-up-alt2"></span></a>
					<a href="javascript:void(0);" class="wplng-target-lang-move-down" wplng-target-lang="[LANG]" title="<?php esc_attr_e( 'Move down', 'wplingua' ); ?>"><span class="dashicons dashicons-arrow-down-alt2"></span></a>
					<a href="javascript:void(0);" class="wplng-target-lang-update-flag" wplng-target-lang="[LANG]"><?php esc_html_e( 'Edit', 'wplingua' ); ?></a>
					<a href="javascript:void(0);" class="wplng-target-lang-remove" wplng-target-lang="[LANG]"><?php esc_html_e( 'Remove', 'wplingua' ); ?></a>
				</div>
			</div>

			<div class="wplng-flag-target-container" wplng-target-lang="[LANG]">
				<p><strong><?php esc_html_e( 'Flag to use for this language: ', 'wplingua' ); ?></strong></p>
				<p><span class="wplng-subflags-radio-target-website">[FLAGS_OPTIONS]</span></p>
				<div class="wplng-subflag-target-custom" wplng-target-lang="[LANG]">
					<hr>
					<p><strong><?php esc_html_e( 'Custom flag URL for this language: ', 'wplingua' ); ?></strong></p>
					[INPUT]
				</div>
				<hr>
				<fieldset>
					[PRIVATE_INPUT]<label for="wplng-language-private-[LANG]"><strong><?php _e( 'Make this language private', 'wplingua' ); ?></strong></label>
					<p><?php _e( 'Private languages will only be visible to logged-in users with editing rights. This option allows administrators to pre-generate and correct translations before they are accessible to the public.', 'wplingua' ); ?></p>
				</fieldset>
			</div>
		</div>
	</div>

	<div id="wplng-target-languages-container">
		<p><strong><?php esc_html_e( 'Target languages enabled: ', 'wplingua' ); ?></strong></p>
		<div id="wplng-target-languages-list"></div>
		<textarea name="wplng_target_languages" id="wplng_target_languages"><?php echo esc_textarea( wp_json_encode( $languages_target, true ) ); ?></textarea>
	</div>

	<hr>

	<p><?php esc_html_e( 'Access more target languages by upgrading your API key.', 'wplingua' ); ?></strong>
		<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-language-adding"></span></p>

	<div class="wplng-help-box" id="wplng-hb-language-adding">
		<p>
		<?php

		echo '<strong>';
		echo esc_html__( 'Available languages: ', 'wplingua' );
		echo '</strong>';
		echo '<br>';

		$languages_all     = wplng_get_languages_all();
		$last_language_key = count( $languages_all ) - 1;

		foreach ( $languages_all as $key => $language ) {
			echo $language['name'];
			if ( $key !== $last_language_key ) {
				echo '&nbsp;- ';
			}
		}
		?>
		</p>

		<hr>

		<p>
			<a href="https://wplingua.com/pricing/" target="_blank">
				<?php esc_attr_e( 'wplingua.com : Upgrade your API key', 'wplingua' ); ?>
			</a>
		</p>
	</div>
	<?php
}


/**
 * Print HTML subsection of Option page : wpLingua Settings - API feature
 *
 * @return void
 */
function wplng_settings_part_features_api() {

	$api_features = wplng_get_api_feature();

	?>
	<p><strong><?php esc_html_e( 'API translation features: ', 'wplingua' ); ?></strong></p>
	<hr>
	<p><?php esc_html_e( 'The options below require extended access to the wpLingua API to be functional on your website.', 'wplingua' ); ?></p>
	<hr>
	<fieldset>
		<input type="checkbox" id="wplng_commercial_use" name="wplng_commercial_use" value="1" <?php checked( 1, in_array( 'commercial', $api_features ), true ); ?> disabled="disabled"/>
		<label for="wplng_commercial_use">PREMIUM - <?php esc_html_e( 'Use wpLingua on commercial website', 'wplingua' ); ?></label> 
		<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-feature-commercial"></span>
	</fieldset>

	<div class="wplng-help-box" id="wplng-hb-feature-commercial">
		<p><?php esc_html_e( 'Use of the free wpLingua API keys is reserved for personal blogs and non-profit websites; paid subscriptions are available for companies and commercial websites.', 'wplingua' ); ?></p>
		<hr>
		<p>
			<a href="https://wplingua.com/pricing/" target="_blank">
				<?php esc_attr_e( 'wplingua.com : Upgrade your API key', 'wplingua' ); ?>
			</a>
		</p>
	</div>

	<fieldset>
		<input type="checkbox" id="wplng_translate_search" name="wplng_translate_search" value="1" <?php checked( 1, get_option( 'wplng_translate_search' ) && in_array( 'search', $api_features ), true ); ?>  <?php disabled( false, in_array( 'search', $api_features ), true ); ?>/>
		<label for="wplng_translate_search">PREMIUM - <?php esc_html_e( 'Search from translated languages', 'wplingua' ); ?></label> 
		<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-feature-search"></span>
	</fieldset>

	<div class="wplng-help-box" id="wplng-hb-feature-search">
		<p><?php esc_html_e( 'Enable visitors to search on your website in their own language.', 'wplingua' ); ?></p>
		<hr>
		<p><?php esc_html_e( 'Example: if your website is translated from English to French and you have a post named "Hello". When a French visitor searches for the term "Bonjour" on the website, this feature will translate it on the fly to "Hello" before launching the search. This will allow WordPress to find the post named "Hello" when your visitor has searched for "Bonjour".', 'wplingua' ); ?></p>
		<hr>
		<p>
			<a href="https://wplingua.com/pricing/" target="_blank">
				<?php esc_attr_e( 'wplingua.com : Upgrade your API key', 'wplingua' ); ?>
			</a>
		</p>
		
	</div>
	<?php
}


/**
 * Print HTML subsection of Option page : wpLingua Settings - Plugin feature
 *
 * @return void
 */
function wplng_settings_part_features_seo() {

	?>
	<p><strong><?php esc_html_e( 'SEO features: ', 'wplingua' ); ?></strong></p>

	<hr>

	<fieldset>
		<input type="checkbox" id="wplng_sitemap_xml" name="wplng_sitemap_xml" value="1" <?php checked( 1, get_option( 'wplng_sitemap_xml', 1 ), true ); ?>/>
		<label for="wplng_sitemap_xml"><?php esc_html_e( 'Enable multilingual XML Sitemap', 'wplingua' ); ?></label> 
		<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-feature-sitemap-xml"></span>
	</fieldset>

	<div class="wplng-help-box" id="wplng-hb-feature-sitemap-xml">
		<p><?php esc_html_e( 'This option automatically adds your wpLingua translated pages into your XML Sitemap files.', 'wplingua' ); ?></p>
		<hr>
		<p><?php esc_html_e( 'An XML Sitemap is used by search engines (Google, Bing, etc.) to better discover and index the pages of your website. By default, only the default language pages are included. With this option enabled, all translated versions of your content will also be listed.', 'wplingua' ); ?></p>
		<p><?php esc_html_e( 'This greatly improves SEO visibility, ensuring that search engines can easily find and index your content in every language.', 'wplingua' ); ?></p>
		<hr>
		<p><?php esc_html_e( 'wpLingua uses a universal method that intercepts and extends sitemap. It works with the native WordPress sitemap and with popular SEO plugins such as Yoast SEO, Rank Math, All in One SEO, SEOPress, etc.', 'wplingua' ); ?></p>
	</div>

	<fieldset>
		<input type="checkbox" id="wplng_hreflang" name="wplng_hreflang" value="1" <?php checked( 1, get_option( 'wplng_hreflang', 1 ), true ); ?>/>
		<label for="wplng_hreflang"><?php esc_html_e( 'Add hreflang tags to translated pages', 'wplingua' ); ?></label> 
		<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-feature-hreflang"></span>
	</fieldset>

	<div class="wplng-help-box" id="wplng-hb-feature-hreflang">
		<p><?php esc_html_e( 'This option automatically adds hreflang tags to your pages that are available in multiple languages.', 'wplingua' ); ?></p>
		<hr>
		<p><?php esc_html_e( 'Hreflang tags are HTML metadata used to indicate the URLs of each language version of a page. They are not visible to visitors, but they allow search engines (Google, Bing, etc.) to identify the multilingual structure of the site and display the correct version according to the user\'s language.', 'wplingua' ); ?></p>
	</div>
	
	<?php
}


/**
 * Print HTML subsection of Option page : wpLingua Settings - Plugin feature
 *
 * @return void
 */
function wplng_settings_part_features_more() {

	?>
	<p><strong><?php esc_html_e( 'More features: ', 'wplingua' ); ?></strong></p>

	<hr>

	<input type="checkbox" id="wplng_browser_language_redirect_checkbox" name="wplng_browser_language_redirect_checkbox" value="1"/> 
	<label for="wplng_browser_language_redirect_checkbox"><?php esc_html_e( 'Enable language browser redirection', 'wplingua' ); ?></label> 
	<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-feature-browser-language-redirect"></span>

	<div class="wplng-help-box" id="wplng-hb-feature-browser-language-redirect">
		<p><?php esc_html_e( 'This option automatically redirects visitors to the translated version of your site that matches their browser language when they land on the main homepage. It can improve user experience by showing content in the right language immediately, but depending on your setup, it may introduce side effects.', 'wplingua' ); ?></p>
		<hr>
		<p><strong><?php esc_html_e( 'Disabled (recommended):', 'wplingua' ); ?></strong></p>
		<p><?php esc_html_e( 'No redirection is applied. Every visitor always sees the default homepage, regardless of their browser language. This is the safest option and ensures maximum compatibility with caching systems, SEO, and shared links. Recommended if you are unsure which option to choose.', 'wplingua' ); ?></p>
		<hr>
		<p><strong><?php esc_html_e( 'JS only:', 'wplingua' ); ?></strong></p>
		<p><?php esc_html_e( 'The redirect is handled with JavaScript, after the default homepage has loaded. This method works in almost all cases and avoids issues with caching plugins. However, it may cause a short flicker effect: users will briefly see the default page before being redirected to the translated version.', 'wplingua' ); ?></p>
		<hr>
		<p><strong><?php esc_html_e( 'PHP and JS:', 'wplingua' ); ?></strong></p>
		<p><?php esc_html_e( 'The redirect is performed server-side with PHP, which makes it faster and more seamless for visitors. In addition, a cookie is set via JavaScript to remember the user’s preferred language for future visits. While this provides the smoothest experience, it can sometimes conflict with caching systems (static pages, CDN, aggressive cache settings) and may cause incorrect redirects in certain setups.', 'wplingua' ); ?></p>
	</div>

	<fieldset id="wplng-browser-language-fieldset">
		<?php

		$wbrowser_language_redirect = get_option( 'wplng_browser_language_redirect', 'disable' );

		if ( $wbrowser_language_redirect !== 'disable'
			&& $wbrowser_language_redirect !== 'js_only'
			&& $wbrowser_language_redirect !== 'php_js'
		) {
			$wbrowser_language_redirect = 'disable';
		}

		?>
		<label>
			<input type="radio" name="wplng_browser_language_redirect" value="disable" <?php checked( $wbrowser_language_redirect, 'disable' ); ?> />
			<?php esc_html_e( 'Disable (recommended)', 'wplingua' ); ?> 
		</label>

		<br>

		<label>
			<input type="radio" name="wplng_browser_language_redirect" value="js_only" <?php checked( $wbrowser_language_redirect, 'js_only' ); ?> />
			<?php esc_html_e( 'Enable with JS only', 'wplingua' ); ?> 
		</label>

		<br>

		<label>
			<input type="radio" name="wplng_browser_language_redirect" value="php_js" <?php checked( $wbrowser_language_redirect, 'php_js' ); ?> />
			<?php esc_html_e( 'Enable with PHP and JS', 'wplingua' ); ?> 
		</label>
	</fieldset>

	<div class="wplng-beta-hidden" style="display: none;">
		<hr>

		<fieldset>
			<input type="checkbox" id="wplng_load_in_progress" name="wplng_load_in_progress" value="1" <?php checked( 1, get_option( 'wplng_load_in_progress' ), true ); ?>/>
			<label for="wplng_load_in_progress">BETA - <?php esc_html_e( 'Progress bar for editors', 'wplingua' ); ?></label> 
			<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-feature-load-in-progress"></span>
		</fieldset>

		<div class="wplng-help-box" id="wplng-hb-feature-load-in-progress">
			<p><?php esc_html_e( 'Enable progress bar: Smooth translations loading for editors', 'wplingua' ); ?></p>
			<hr>
			<p><?php esc_html_e( 'Activate a progress bar when a page requires the generation of more than 20 new string translations. This feature only applies to connected editors.', 'wplingua' ); ?></p>
		</div>

	</div><!-- End .wplng-beta-hidden -->
	
	<?php
}


/**
 * Print HTML subsection of Option page : wpLingua Settings - API Key
 *
 * @return void
 */
function wplng_settings_part_api_key() {

	$data = wplng_get_api_data();

	?>
	<fieldset>
		<p><label for="wplng_api_key"><strong><?php esc_html_e( 'Website API key: ', 'wplingua' ); ?></strong></label></p>

		<hr>

		<p>
			<?php esc_html_e( 'The API key connects the website to wpLingua\'s online services and automatic translation generators.', 'wplingua' ); ?> 
			<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-api-key"></span>
		</p>

		<div class="wplng-help-box" id="wplng-hb-api-key">
			<p>
				<?php esc_html_e( 'A wpLingua API key consists of 42 characters (uppercase, lowercase and numbers). It is emailed to you when you request it using the form provided when you install the plugin. You must keep this key secret and only communicate it to wplingua.com services.', 'wplingua' ); ?>
			</p>
			<hr>
			<p>
				<a href="https://wplingua.com/pricing/" target="_blank">
					<?php esc_attr_e( 'wplingua.com : Upgrade your API key', 'wplingua' ); ?>
				</a>
			</p>
		</div>

		<hr>

		<input type="text" id="wplng-api-key-fake" value="●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●" disabled></input>

		<input type="text" name="wplng_api_key" id="wplng_api_key" value="<?php echo esc_attr( wplng_get_api_key() ); ?>" style="display: none;"></input>

		<a class="button button-primary wplng-icon-button" id="wplng-api-key-show" href="javascript:void(0);" title="<?php esc_html_e( 'Show API key', 'wplingua' ); ?>"><span class="dashicons dashicons-visibility"></span></a>

		<a class="button button-primary wplng-icon-button" id="wplng-api-key-hide" href="javascript:void(0);" title="<?php esc_html_e( 'Hide API key', 'wplingua' ); ?>" style="display: none;"><span class="dashicons dashicons-hidden"></span></a>
	</fieldset>

	<?php if ( ! empty( $data['status'] ) ) : ?>
		
		<hr>
		<p class="wplng-fe-50"><?php esc_html_e( 'API key status: ', 'wplingua' ); ?></p>

		<?php if ( 'FREE' === $data['status'] ) : ?>
			<p class="wplng-fe-50" style="text-align: right;"><span class="dashicons dashicons-saved"></span> FREE</p>
		<?php elseif ( 'PREMIUM' === $data['status'] ) : ?>
			<p class="wplng-fe-50" style="text-align: right;"><span class="dashicons dashicons-superhero-alt"></span> PREMIUM</p>
		<?php elseif ( 'VIP' === $data['status'] ) : ?>
			<p class="wplng-fe-50" style="text-align: right;"><span class="dashicons dashicons-star-empty"></span> VIP</p>
		<?php endif; ?>


	<?php endif; ?>

	<?php if ( ! empty( $data['expiration'] ) ) : ?>

		<p class="wplng-fe-50"><?php esc_html_e( 'Premium expiration: ', 'wplingua' ); ?></p>
		<p class="wplng-fe-50" style="text-align: right;"><?php echo esc_html( $data['expiration'] ); ?></p>
		
	<?php endif; ?>

	<?php
}
