<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



/**
 * Create a menu link for the plugin settings in the admin area
 *
 * @return void
 */
function wplng_create_menu() {

	add_menu_page(
		__( 'wpLingua : Settings', 'wplingua' ),
		__( 'wpLingua', 'wplingua' ),
		'administrator',
		__FILE__,
		'wplng_settings',
		'dashicons-admin-site'
		// plugins_url( '/images/icon.png', __FILE__ )
	);

	add_submenu_page(
		__FILE__,
		__( 'wplingua : Exclusion', 'wplingua' ),
		__( 'Exclusion', 'wplingua' ),
		'administrator',
		'wplng-exclude',
		'wplng_exclude'
	);

}




/**
 * register settings
 *
 * @return void
 */
function wplng_register_settings() {

	register_setting( 'wplng_settings', 'wplng_website_language' );
	register_setting( 'wplng_settings', 'wplng_website_flag' );
	register_setting( 'wplng_settings', 'wplng_target_languages' );
	register_setting( 'wplng_settings', 'wplng_translate_mail' );
	register_setting( 'wplng_settings', 'wplng_translate_search' );

}


/**
 * Add 'Settings' link on the plugin list
 *
 * @param array $settings
 * @return array
 */
function wplng_settings_link( $settings ) {

	$url = esc_url(
		add_query_arg(
			'page',
			'wplingua/inc/option-page.php',
			get_admin_url() . 'admin.php'
		)
	);

	$settings[] = '<a href="' . $url . '">' . __( 'Settings', 'wplingua' ) . '</a>';

	return $settings;
}


/**
 * Option page for the plugin
 *
 * @return void
 */
function wplng_settings() {
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
						<fieldset>
							<?php wplng_settings_part_language_website(); ?>
						</fieldset>
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
			</table>
			
			<?php submit_button(); ?>
		
		</form>
	</div>
	<?php
}


function wplng_settings_part_language_website() {
	?>
	<legend class="screen-reader-text">
		<span><?php _e( 'Website language', 'wplingua' ); ?></span>
	</legend>

	<label for="wplng_website_language">
		<?php _e( 'The original website language: ', 'wplingua' ); ?>
	</label>

	<select id="wplng_website_language" name="wplng_website_language">
		<?php
		$website_language_saved = true;
		if ( empty( wplng_get_language_website_id() ) ) {
			$website_language_saved = false;
		} else {

			$website_language_id = wplng_get_language_website_id();
			$website_language    = wplng_get_language_by_id( $website_language_id );

			if ( ! empty( $website_language['id'] )
				&& ! empty( $website_language['name'] )
			) {
				echo '<option value="' . esc_attr( $website_language['id'] ) . '">' . esc_html( $website_language['name'] ) . '</option>';
			} else {
				$website_language_saved = false;
			}
		}

		if ( ! $website_language_saved ) {
			echo '<option value="">' . __( 'Please choose an option', 'wplingua' ) . '</option>';
		}
		?>
	</select>

	<br>
	<br>

	<legend class="screen-reader-text">
		<span><?php _e( 'Website flag', 'wplingua' ); ?></span>
	</legend>

	<div id="wplng-flags-radio-original-website-custom"><?php _e( 'Custom', 'wplingua' ); ?></div>

	<span><?php _e( 'The original website flag: ', 'wplingua' ); ?></span>
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
	<legend class="screen-reader-text">
		<span><?php _e( 'Translated languages', 'wplingua' ); ?></span>
	</legend>

	<label for="wplng_target_language">
		<?php _e( 'Add new target Language: ', 'wplingua' ); ?>
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
	<fieldset>
		<legend class="screen-reader-text">
			<span><?php _e( 'Translate sending mail', 'wplingua' ); ?></span>
		</legend>
		<label for="wplng_translate_mail">
			<input type="checkbox" id="wplng_translate_mail" name="wplng_translate_mail" value="1" <?php checked( 1, get_option( 'wplng_translate_mail' ), true ); ?> /> <?php _e( 'Translate sending mail', 'wplingua' ); ?>
		</label>
	</fieldset>
	<fieldset>
		<legend class="screen-reader-text">
			<span><?php _e( 'Search from translated languages', 'wplingua' ); ?></span>
		</legend>
		<label for="wplng_translate_search">
			<input type="checkbox" id="wplng_translate_search" name="wplng_translate_search" value="1" <?php checked( 1, get_option( 'wplng_translate_search' ), true ); ?> /> <?php _e( 'Search from translated languages', 'wplingua' ); ?>
		</label>
	</fieldset>
	<?php
}


function wplng_exclude() {
	?>
	<div class="wrap">
		
		<h1><?php _e( 'wpLingua : Exclusion rules', 'wplingua' ); ?></h1>

		<br>

		<form method="post" action="options.php">
			<?php
			settings_fields( 'wplng_settings' );
			do_settings_sections( 'wplng_settings' );
			?>
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'Exclude elements', 'wplingua' ); ?></th>
					<td>
						<fieldset>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Exclude URL', 'wplingua' ); ?></th>
					<td>
						<fieldset>
						</fieldset>
					</td>
				</tr>
			</table>
			
			<?php submit_button(); ?>
		
		</form>
	</div>
	<?php
}
