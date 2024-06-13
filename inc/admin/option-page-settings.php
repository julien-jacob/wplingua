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

	delete_transient( 'wplng_api_key_data' );

	if ( empty( wplng_get_api_data() ) || is_multisite() ) {
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
					<th scope="row"><span class="dashicons dashicons-admin-settings"></span> <?php esc_html_e( 'API features', 'wplingua' ); ?></th>
					<td>
						<fieldset>
							<?php wplng_settings_part_features(); ?>
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

	update_option( 'wplng_website_language', wplng_get_api_language_website() );

	$data = wplng_get_api_data();

	if ( empty( $data['languages_target'][0] ) ) {
		return false;
	}

	// Set option for target language
	$language_target = wplng_get_language_by_id( $data['languages_target'][0] );
	if ( ! empty( $language_target ) ) {
		update_option(
			'wplng_target_languages',
			wp_json_encode(
				array( $language_target )
			)
		);
	} else {
		return false;
	}

	// Get URL for first registered language of front page
	$url_front_page_translated = wplng_url_translate(
		get_site_url(),
		$language_target['id']
	);

	$url_front_page_iframe = add_query_arg(
		'wplng-load',
		'disabled',
		$url_front_page_translated
	);

	?>
	<div class="wplng-notice notice notice-info" id="wplng-notice-first-loading-loading">
		<iframe src="<?php echo esc_url( $url_front_page_iframe ); ?>" frameborder="0" id="wplng-first-load-iframe" style="display: none;"></iframe>
		<h2><span class="dashicons dashicons-update wplng-spin"></span> <?php esc_html_e( 'Your website is being translated and will be ready soon.', 'wplingua' ); ?></h2>
		<p><?php esc_html_e( 'In just a few seconds, your site will be multilingual, and search engines will be able to index these new pages. wpLingua detects all the texts on your pages and offers you a first automatically generated translation. All translations are editable: open the visual editor from the administration bar and edit them simply by clicking on the texts on your site.', 'wplingua' ); ?></p>
	</div>

	<div class="wplng-notice notice notice-success is-dismissible" id="wplng-notice-first-loading-loaded" style="display: none;">
		<h2>🎉 <?php esc_html_e( 'Your website is now multilingual! You can start visiting the translated version.', 'wplingua' ); ?></h2>
		<p><?php esc_html_e( 'The first time a translated page is loaded, the translations are automatically generated and saved in the database. This may take some time (on first generation only) depending on the size of your content. This is why we advise you to browse your entire website for the first time in order to generate all the multilingual versions.', 'wplingua' ); ?></p>
		<p><?php esc_html_e( 'All translations are editable: open the visual editor from the administration bar and edit them simply by clicking on the texts on your site.', 'wplingua' ); ?></p>
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
		echo '<span ';
		echo 'title="' . esc_attr__( 'Click to expand', 'wplingua' ) . '" ';
		echo 'wplng-help-box="#wplng-hb-language-website" ';
		echo '></span>';
		echo ' </p>';

		echo '<div class="wplng-help-box" id="wplng-hb-language-website">';
		echo '<p>';
		echo esc_html__( 'This is the language of your website, defined by the associated API key. Make sure your website language is also correctly set in WordPress options (Settings ➔ General ➔ Site Language).', 'wplingua' );
		echo '<hr>';
		echo esc_html__( 'If you have mistakenly entered the wrong language, contact wpLingua support to request a correction.', 'wplingua' );
		echo ' ';
		echo '<a href="https://wplingua.com/support/" target="_blank">';
		echo esc_html__( 'Contact support on wplingua.com', 'wplingua' );
		echo '</a>';
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
				<a href="javascript:void(0);" id="wplng-website-lang-update-flag"><?php esc_html_e( 'Edit flag', 'wplingua' ); ?></a>
			</div>
		</div>

		<div id="wplng-flag-website-container">
			<p>
				<strong><?php esc_html_e( 'Flag: ', 'wplingua' ); ?></strong>
				<span id="wplng-flags-radio-original-website"></span>
			</p>

			<div id="wplng-website-flag-container">
				<strong><?php esc_html_e( 'Custom flag URL: ', 'wplingua' ); ?></strong>
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

	$languages_target     = wplng_get_languages_target_simplified();
	$languages_target_ids = array();

	foreach ( $languages_target as $language_target ) {
		if ( ! empty( $language_target['id'] ) ) {
			$languages_target_ids[] = $language_target['id'];
		}
	}

	$languages_target = wplng_get_languages_by_ids( $languages_target_ids );

	?>
	<fieldset id="fieldset-add-target-language">

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
					[FLAG][NAME]
				</div>

				<div class="wplng-target-language-right">
					<a href="javascript:void(0);" class="wplng-target-lang-update-flag" wplng-target-lang="[LANG]"><?php esc_html_e( 'Edit flag', 'wplingua' ); ?></a>
					<a href="javascript:void(0);" class="wplng-target-lang-remove" wplng-target-lang="[LANG]"><?php esc_html_e( 'Remove', 'wplingua' ); ?></a>
				</div>
			</div>

			<div class="wplng-flag-target-container" wplng-target-lang="[LANG]">
				<p>
					<strong><?php esc_html_e( 'Flag: ', 'wplingua' ); ?></strong>
					<span class="wplng-subflags-radio-target-website">[FLAGS_OPTIONS]</span>
				</p>
				<div class="wplng-subflag-target-custom" wplng-target-lang="[LANG]">
					<strong><?php esc_html_e( 'Custom flag URL: ', 'wplingua' ); ?></strong>
					[INPUT]
				</div>
			</div>
		</div>
	</div>

	<div id="wplng-target-languages-container">
		<p>
			<strong><?php esc_html_e( 'Target languages enabled: ', 'wplingua' ); ?></strong>
		</p>
		<div id="wplng-target-languages-list"></div>
		<textarea name="wplng_target_languages" id="wplng_target_languages"><?php echo esc_textarea( wp_json_encode( $languages_target, true ) ); ?></textarea>
	</div>

	<p>
		<?php esc_html_e( 'Access more target languages by upgrading your API key.', 'wplingua' ); ?></strong>
		<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-language-adding"></span>
	</p>

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
				<?php esc_attr_e( 'Upgrade your API key on wplingua.com', 'wplingua' ); ?>
			</a>
		</p>
	</div>
	<?php
}


/**
 * Print HTML subsection of Option page : wpLingua Settings - Feature
 *
 * @return void
 */
function wplng_settings_part_features() {

	$api_features = wplng_get_api_feature();

	?>
	<p><strong><?php esc_html_e( 'API translation features: ', 'wplingua' ); ?></strong></p>
	<hr>

	<p><?php esc_html_e( 'The options below require extended access to the wpLingua API to be functional on your website.', 'wplingua' ); ?></p>

	<hr>

	<fieldset>
		<input type="checkbox" id="wplng_commercial_use" name="wplng_commercial_use" value="1" <?php checked( 1, in_array( 'commercial', $api_features ), true ); ?> disabled="disabled"/>
		<label for="wplng_commercial_use"> <?php esc_html_e( 'API feature: Use wpLingua on commercial website', 'wplingua' ); ?></label> 
		<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-feature-commercial"></span>
	</fieldset>

	<div class="wplng-help-box" id="wplng-hb-feature-commercial">
		<p>
			<?php esc_html_e( 'Use of the free wpLingua API keys is reserved for personal blogs and non-profit websites; paid subscriptions are available for companies and commercial websites.', 'wplingua' ); ?>
		</p>

		<hr>

		<p>
			<a href="https://wplingua.com/pricing/" target="_blank">
				<?php esc_attr_e( 'Upgrade your API key on wplingua.com', 'wplingua' ); ?>
			</a>
		</p>
	</div>

	<hr>

	<fieldset>
		<input type="checkbox" id="wplng_translate_search" name="wplng_translate_search" value="1" <?php checked( 1, get_option( 'wplng_translate_search' ) && in_array( 'search', $api_features ), true ); ?>  <?php disabled( false, in_array( 'search', $api_features ), true ); ?>/>
		<label for="wplng_translate_search"> <?php esc_html_e( 'API feature: Search from translated languages', 'wplingua' ); ?></label> 
		<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-feature-search"></span>
	</fieldset>

	<div class="wplng-help-box" id="wplng-hb-feature-search">
		<p>
			<?php esc_html_e( 'Enable visitors to search on your website in their own language.', 'wplingua' ); ?>
		</p>
		<hr>
		<p>
			<?php esc_html_e( 'Example: if your website is translated from English to French and you have a post named "Hello". When a French visitor searches for the term "Bonjour" on the website, this feature will translate it on the fly to "Hello" before launching the search. This will allow WordPress to find the post named "Hello" when your visitor has searched for "Bonjour".', 'wplingua' ); ?>
		</p>
		<hr>
		<p>
			<a href="https://wplingua.com/pricing/" target="_blank">
				<?php esc_attr_e( 'Upgrade your API key on wplingua.com', 'wplingua' ); ?>
			</a>
		</p>
		
	</div>
	<?php
}


/**
 * Print HTML subsection of Option page : wpLingua Settings - API Key
 *
 * @return void
 */
function wplng_settings_part_api_key() {
	?>
	<fieldset>
		<p><label for="wplng_api_key"><strong><?php esc_html_e( 'Website API key: ', 'wplingua' ); ?></strong></label></p>

		<hr>

		<p>
			<?php esc_html_e( 'The API key connects the website to wpLingua\'s online services and automatic translation generators.', 'wplingua' ); ?> 
			<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-api-key"></span>
		</p>

		<div class="wplng-help-box" id="wplng-hb-api-key">
			<p><?php esc_html_e( 'A wpLingua API key consists of 42 characters (uppercase, lowercase and numbers). It is emailed to you when you request it using the form provided when you install the plugin. You must keep this key secret and only communicate it to wplingua.com services', 'wplingua' ); ?></p>
		</div>

		<hr>

		<input type="text" id="wplng-api-key-fake" value="●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●" disabled></input>

		<input type="text" name="wplng_api_key" id="wplng_api_key" value="<?php echo esc_attr( wplng_get_api_key() ); ?>" style="display: none;"></input>

		<a class="button button-primary wplng-icon-button" id="wplng-api-key-show" href="javascript:void(0);" title="<?php esc_html_e( 'Show API key', 'wplingua' ); ?>"><span class="dashicons dashicons-visibility"></span></a>

		<a class="button button-primary wplng-icon-button" id="wplng-api-key-hide" href="javascript:void(0);" title="<?php esc_html_e( 'Hide API key', 'wplingua' ); ?>" style="display: none;"><span class="dashicons dashicons-hidden"></span></a>
	</fieldset>
	<?php
}
