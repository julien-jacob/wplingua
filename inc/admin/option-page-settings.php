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
	set_transient('wplng_api_key_data', false);
	wplng_get_api_data();
	// echo '<pre>' . var_export(wplng_get_api_data(), true) . '</pre>';
	?>
	<div class="wrap">
		
		<h1><?php _e( 'wpLingua : Translation solution for multilingual website', 'wplingua' ); ?></h1>

		<br>

		<form method="post" action="options.php">
			<?php
			settings_fields( 'wplng_settings' );
			do_settings_sections( 'wplng_settings' );
			?>
			
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'Website language', 'wplingua' ); ?></th>
					<td>
						<?php wplng_settings_part_language_website(); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Translated languages', 'wplingua' ); ?></th>
					<td>
						<fieldset>
							<?php wplng_settings_part_languages_target(); ?>
						</fieldset>
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
						<fieldset>
							<?php wplng_settings_part_api_key(); ?>
						</fieldset>
					</td>
				</tr>
			</table>
			
			<?php submit_button(); ?>
		
		</form>
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

	echo '<label for="wplng_website_language">';
	echo '<strong>' . __( 'The original website language: ', 'wplingua' ) . ' </strong>';
	echo '</label>';

	echo '<select id="wplng_website_language" name="wplng_website_language">';
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
	echo '</fieldset>';
	

	if ( 'all' !== $api_language_website ) {
		// $website_language_saved = ' disabled';
		echo '<strong>';
		echo __( 'The original website language, defined by API key:', 'wplingua' );
		echo ' </strong>';
		echo wplng_get_language_name( $api_language_website );
	}
	?>
	
	<br>
	<br>

	<div id="wplng-flags-radio-original-website-custom"><?php _e( 'Custom', 'wplingua' ); ?></div>

	<strong><?php _e( 'The original website flag: ', 'wplingua' ); ?></strong>
	<span id="wplng-flags-radio-original-website"></span>

	<br>
	<br>

	<div id="wplng-website-flag-container">
		<?php _e( 'Custom flag URL (64px*64px recommended) : ', 'wplingua' ); ?>
		<input type="url" name="wplng_website_flag" id="wplng_website_flag" value="<?php echo esc_url( wplng_get_language_website_flag() ); ?>" />
	</div>

	<hr>
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

	<label for="wplng_target_language">
		<strong><?php _e( 'Add new target Language: ', 'wplingua' ); ?></strong>
	</label>							

	<select id="wplng_add_new_target_language" name="wplng_add_new_target_language"></select>

	<a class="button button-primary" id="wplng-target-lang-add" href="javascript:void(0);"><?php _e( 'Add', 'wplingua' ); ?></a>

	<hr>
	<br>
	
	<p><strong><?php _e( 'Current target languages: ', 'wplingua' ); ?></strong></p>

	<hr>

	<div id="wplng-target-language-template">
		<div class="wplng-target-language">
			[FLAG][NAME] - <a href="javascript:void(0);" class="wplng-target-lang-update-flag" wplng-target-lang="[LANG]"><?php _e( 'Edit flag', 'wplingua' ); ?></a> - <a href="javascript:void(0);" class="wplng-target-lang-remove" wplng-target-lang="[LANG]"><?php _e( 'Remove', 'wplingua' ); ?></a>
			<div class="wplng-flag-target-container" wplng-target-lang="[LANG]">
				<br>
				<span><?php _e( 'Flag: ', 'wplingua' ); ?></span>
				<span class="wplng-subflags-radio-target-website">[FLAGS_OPTIONS]</span>
				<div class="wplng-subflag-target-custom" wplng-target-lang="[LANG]">
					<?php _e( 'Custom flag URL (64px*64px recommended) : ', 'wplingua' ); ?>
					[INPUT]
				</div>
			</div>
			<hr>
		</div>
	</div>

	<div id="wplng-target-languages-list"></div>
	
	<textarea name="wplng_target_languages" id="wplng_target_languages"><?php echo esc_textarea( json_encode( $languages_target, true ) ); ?></textarea>
	<?php
}


function wplng_settings_part_features() {
	?>
	<p><strong><?php _e( 'Translation features:', 'wplingua' ); ?></strong></p>
	<br>
	<fieldset>
		<label for="wplng_translate_mail">
			<input type="checkbox" id="wplng_translate_mail" name="wplng_translate_mail" value="1" <?php checked( 1, get_option( 'wplng_translate_mail' ), true ); ?> /> <?php _e( 'Premium: Translate sending mail', 'wplingua' ); ?>
		</label>
	</fieldset>

	<fieldset>
		<label for="wplng_translate_search">
			<input type="checkbox" id="wplng_translate_search" name="wplng_translate_search" value="1" <?php checked( 1, get_option( 'wplng_translate_search' ), true ); ?> /> <?php _e( 'Premium: Search from translated languages', 'wplingua' ); ?>
		</label>
	</fieldset>

	<fieldset>
		<label for="wplng_translate_woocommerce">
			<input type="checkbox" id="wplng_translate_woocommerce" name="wplng_translate_woocommerce" value="1" <?php checked( 1, get_option( 'wplng_translate_woocommerce' ), true ); ?> /> <?php _e( 'Premium: Translate Woocommerce shop', 'wplingua' ); ?>
		</label>
	</fieldset>
	<?php
}



function wplng_settings_part_api_key() {
	?>
	<fieldset>
		<label for="wplng_api_key"><strong><?php _e( 'Website API key:', 'wplingua' ); ?></strong></label>
		<br>
		<input type="text" name="wplng_api_key" id="wplng_api_key" value="<?php echo esc_attr( get_option( 'wplng_api_key' ) ); ?>" style="max-width: 100%; width: 32em;"></input>
	</fieldset>
	<?php
}
