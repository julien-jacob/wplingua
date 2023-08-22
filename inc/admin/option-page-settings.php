<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Option page for the plugin
 *
 * @return void
 */
function wplng_option_page_settings() {

	delete_transient( 'wplng_api_key_data' );

	if ( empty( wplng_get_api_data() ) ) {
		wplng_option_page_register();
		return;
	}

	wplng_show_api_message();
	wplng_settings_part_first_use();

	?>
	<div class="wrap">
		
		<h1><span class="dashicons dashicons-translation"></span> <?php _e( 'wpLingua / Beta : Translation solution for multilingual website', 'wplingua' ); ?></h1>

		<br>

		<form method="post" action="options.php">
			<?php
			settings_fields( 'wplng_settings' );
			do_settings_sections( 'wplng_settings' );
			?>
			
			<table class="form-table wplng-form-table">
				<tr>
					<th scope="row"><?php _e( 'Website language', 'wplingua' ); ?></th>
					<td>
						<?php wplng_settings_part_language_website(); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Translated languages', 'wplingua' ); ?></th>
					<td>
						<?php wplng_settings_part_languages_target(); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Features', 'wplingua' ); ?></th>
					<td>
						<fieldset>
							<?php wplng_settings_part_features(); ?>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'API Key', 'wplingua' ); ?></th>
					<td>
						<?php wplng_settings_part_api_key(); ?>
					</td>
				</tr>
			</table>
			
			<?php submit_button(); ?>
		
		</form>
	</div>
	<?php
}


function wplng_settings_part_first_use() {

	if ( ! empty( get_option( 'wplng_website_language' ) ) ) {
		return;
	}

	// if ('all' === wplng_get_api_language_website()) {
	// 	return;
	// }

	update_option( 'wplng_website_language', wplng_get_api_language_website() );

	$data = wplng_get_api_data();

	if ( empty( $data['languages_target'][0] ) ) {
		return;
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
		return;
	}

	$url_front_page_translated = wplng_url_translate(
		get_site_url(),
		$language_target['id']
	);

	?>
	<div class="wplng-notice notice notice-info" id="wplng-notice-first-loading-loading">
		<iframe src="<?php echo esc_url( $url_front_page_translated ); ?>" frameborder="0" id="wplng-first-load-iframe" style="display: none;"></iframe>
		<h2><span class="dashicons dashicons-update wplng-spin"></span> <?php _e( 'Your site is being translated and will be ready soon.', 'wplingua' ); ?></h2>
	</div>

	<div class="wplng-notice notice notice-success" id="wplng-notice-first-loading-loaded" style="display: none;">
		<h2>🎉 <?php _e( 'Your website is now multilingual ! You can start visiting the translated version.', 'wplingua' ); ?></h2>
		<p>
			<a href="<?php echo esc_url( $url_front_page_translated ); ?>" target="_blank" class="button button-primary">
				<?php _e( 'Visit your multilingual website', 'wplingua' ); ?>
			</a>
		</p>
	</div>
	<?php
}


function wplng_settings_part_language_website() {

	$api_language_website   = wplng_get_api_language_website();
	$website_language_style = '';

	if ( 'all' !== $api_language_website ) {
		$website_language_style = ' style="display: none;"';
	}

	echo '<fieldset' . $website_language_style . '>';

	echo '<label for="wplng_website_language" class="wplng-fe-50">';
	echo '<strong>' . __( 'Original website language: ', 'wplingua' ) . ' </strong>';
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
		echo '<option value="">' . __( 'Please choose an option', 'wplingua' ) . '</option>';
	}
	echo '</select>';
	echo '<hr>';
	echo '</fieldset>';

	if ( 'all' !== $api_language_website ) {
		// $website_language_saved = ' disabled';
		echo '<p>';
		echo '<strong>';
		echo __( 'Original website language, defined by API key:', 'wplingua' );
		echo ' </strong>';
		echo wplng_get_language_name( $api_language_website );
		echo ' </p>';
		echo ' <hr>';
	}
	?>

	<div id="wplng-flags-radio-original-website-custom"><?php _e( 'Custom', 'wplingua' ); ?></div>

	<p>
		<strong><?php _e( 'Flag:', 'wplingua' ); ?></strong>
		<span id="wplng-flags-radio-original-website"></span>
	</p>

	<div id="wplng-website-flag-container">
		<hr>
		<strong class="wplng-fe-50"><?php _e( 'Custom flag URL : ', 'wplingua' ); ?></strong>
		<input type="url" name="wplng_website_flag" id="wplng_website_flag" value="<?php echo esc_url( wplng_get_language_website_flag() ); ?>" class="wplng-fe-50"/>
	</div>
	<?php
}


function wplng_settings_part_languages_target() {

	$languages_target     = wplng_get_languages_target_simplified();
	$languages_target_ids = array();

	foreach ( $languages_target as $key => $language_target ) {
		if ( ! empty( $language_target['id'] ) ) {
			$languages_target_ids[] = $language_target['id'];
		}
	}

	$languages_target = wplng_get_language_by_ids( $languages_target_ids );

	?>
	<fieldset id="fieldset-add-target-language">
		<label for="wplng_add_new_target_language" class="wplng-fe-50">
			<strong><?php _e( 'Add new target Language: ', 'wplingua' ); ?></strong>
		</label>							
		
		<select id="wplng_add_new_target_language" name="wplng_add_new_target_language"></select>

		<a class="button button-primary wplng-icon-button" id="wplng-target-lang-add" title="<?php _e( 'Add language', 'wplingua' ); ?>" href="javascript:void(0);">
			<span class="dashicons dashicons-insert"></span>
		</a>

		<hr>
	</fieldset>

	<div id="wplng-target-language-template">
		<div class="wplng-target-language">

			<div class="wplng-target-language-displayed">
				<div class="wplng-target-language-left">
					[FLAG][NAME]
				</div>

				<div class="wplng-target-language-right">
					<a href="javascript:void(0);" class="wplng-target-lang-update-flag" wplng-target-lang="[LANG]"><?php _e( 'Edit flag', 'wplingua' ); ?></a>
					<a href="javascript:void(0);" class="wplng-target-lang-remove" wplng-target-lang="[LANG]"><?php _e( 'Remove', 'wplingua' ); ?></a>
				</div>
			</div>
			
			<div class="wplng-flag-target-container" wplng-target-lang="[LANG]">
				<p>
					<strong><?php _e( 'Flag: ', 'wplingua' ); ?></strong>
					<span class="wplng-subflags-radio-target-website">[FLAGS_OPTIONS]</span>
				</p>
				<div class="wplng-subflag-target-custom" wplng-target-lang="[LANG]">
					<strong><?php _e( 'Custom flag URL : ', 'wplingua' ); ?></strong>
					[INPUT]
				</div>
			</div>
		</div>
	</div>

	<div id="wplng-target-languages-container">
		<p><strong><?php _e( 'Current target languages: ', 'wplingua' ); ?></strong></p>
		<div id="wplng-target-languages-list"></div>
		<textarea name="wplng_target_languages" id="wplng_target_languages"><?php echo esc_textarea( json_encode( $languages_target, true ) ); ?></textarea>

	</div>
	<?php
}


function wplng_settings_part_features() {

	$api_features = wplng_get_api_feature();

	?>
	<p><strong><?php _e( 'Translation features:', 'wplingua' ); ?></strong></p>
	
	<fieldset>
		<label for="wplng_translate_mail">
			<input type="checkbox" id="wplng_translate_mail" name="wplng_translate_mail" value="1" <?php checked( 1, get_option( 'wplng_translate_mail' ) && in_array( 'mail', $api_features ), true ); ?> <?php disabled( false, in_array( 'mail', $api_features ), true ); ?>/> <?php _e( 'Premium / Beta : Translate mail sending from translated pages', 'wplingua' ); ?>
		</label>
	</fieldset>

	<fieldset>
		<label for="wplng_translate_search">
			<input type="checkbox" id="wplng_translate_search" name="wplng_translate_search" value="1" <?php checked( 1, get_option( 'wplng_translate_search' ) && in_array( 'search', $api_features ), true ); ?>  <?php disabled( false, in_array( 'search', $api_features ), true ); ?>/> <?php _e( 'Premium / Beta : Search from translated languages', 'wplingua' ); ?>
		</label>
	</fieldset>

	<fieldset>
		<label for="wplng_translate_woocommerce">
			<input type="checkbox" id="wplng_translate_woocommerce" name="wplng_translate_woocommerce" value="1" <?php checked( 1, get_option( 'wplng_translate_woocommerce' ) && in_array( 'woocommerce', $api_features ), true ); ?>  <?php disabled( false, in_array( 'woocommerce', $api_features ), true ); ?>/> <?php _e( 'Premium / Beta : Translate Woocommerce shop', 'wplingua' ); ?>
		</label>
	</fieldset>
	
	<?php
}



function wplng_settings_part_api_key() {
	?>
	<fieldset>
		<p><label for="wplng_api_key"><strong><?php _e( 'Website API key:', 'wplingua' ); ?></strong></label></p>
		
		<input type="text" id="wplng-api-key-fake" value="●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●" disabled></input>

		<input type="text" name="wplng_api_key" id="wplng_api_key" value="<?php echo esc_attr( wplng_get_api_key() ); ?>" style="display: none;"></input>
		
		<a class="button button-primary wplng-icon-button" id="wplng-api-key-show" href="javascript:void(0);" title="<?php _e( 'Show API key', 'wplingua' ); ?>"><span class="dashicons dashicons-visibility"></span></a>

		<a class="button button-primary wplng-icon-button" id="wplng-api-key-hide" href="javascript:void(0);" title="<?php _e( 'Hide API key', 'wplingua' ); ?>" style="display: none;"><span class="dashicons dashicons-hidden"></span></a>
	</fieldset>
	<?php
}
