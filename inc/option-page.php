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
function mcv_create_menu() {

	add_menu_page(
		__( 'Machiavel : Settings', 'machiavel' ),
		__( 'Machiavel Settings', 'machiavel' ),
		'administrator',
		__FILE__,
		'mcv_settings',
		'dashicons-admin-site'
		// plugins_url( '/images/icon.png', __FILE__ )
	);

	// add_submenu_page(
	// 	'options-general.php',
	// 	__( 'Machiavel : Settings', 'machiavel' ),
	// 	__( 'Machiavel Settings', 'machiavel' ),
	// 	'administrator',
	// 	'mcv-settings',
	// 	'mcv_settings'
	// );

}


/**
 * register settings
 *
 * @return void
 */
function mcv_register_settings() {

	register_setting( 'mcv_settings', 'mcv_website_language' );
	register_setting( 'mcv_settings', 'mcv_website_flag' );
	register_setting( 'mcv_settings', 'mcv_target_languages' );

}


/**
 * Add 'Settings' link on the plugin list
 *
 * @param array $settings
 * @return array
 */
function mcv_settings_link( $settings ) {

	$url = esc_url(
		add_query_arg(
			'page',
			'mcv-settings',
			get_admin_url() . 'options-general.php'
		)
	);

	$settings[] = '<a href="' . $url . '">' . __( 'Settings', 'machiavel' ) . '</a>';

	return $settings;
}


/**
 * Option page for the plugin
 *
 * @return void
 */
function mcv_settings() {

	$languages_target     = mcv_get_languages_target();
	$languages_target_ids = array();

	foreach ( $languages_target as $key => $language_target ) {
		if ( ! empty( $language_target['id'] ) ) {
			$languages_target_ids[] = $language_target['id'];
		}
	}

	$languages_target = mcv_get_language_by_ids( $languages_target_ids );

	?>
	<div class="wrap">
		<h1><?php _e( 'Machiavel Translate : Multilingual solution', 'machiavel' ); ?></h1>
		
		<form method="post" action="options.php">
			<?php
			settings_fields( 'mcv_settings' );
			do_settings_sections( 'mcv_settings' );
			?>

			<table class="form-table">
				
				<tr>
					<th scope="row"><?php _e( 'Website language', 'machiavel' ); ?></th>
					<td>
						<fieldset>

							<legend class="screen-reader-text">
								<span><?php _e( 'Website language', 'machiavel' ); ?></span>
							</legend>

							<label for="mcv_website_language">
								<?php _e( 'The original website language: ', 'machiavel' ); ?>
							</label>

							<select id="mcv_website_language" name="mcv_website_language">
								<?php
								$website_language_saved = true;
								if ( empty( mcv_get_language_website_id() ) ) {
									$website_language_saved = false;
								} else {

									$website_language_id = mcv_get_language_website_id();
									$website_language    = mcv_get_language_by_id( $website_language_id );

									if ( ! empty( $website_language['id'] )
										&& ! empty( $website_language['name'] )
									) {
										echo '<option value="' . esc_attr( $website_language['id'] ) . '">' . esc_html( $website_language['name'] ) . '</option>';
									} else {
										$website_language_saved = false;
									}
								}

								if ( ! $website_language_saved ) {
									echo '<option value="">' . __( 'Please choose an option', 'machiavel' ) . '</option>';
								}
								?>
							</select>

							<br>
							<br>

							<legend class="screen-reader-text">
								<span><?php _e( 'Website flag', 'machiavel' ); ?></span>
							</legend>

							<div id="mcv-flags-radio-original-website-custom"><?php _e( 'Custom', 'machiavel' ); ?></div>

							<span><?php _e( 'The original website flag: ', 'machiavel' ); ?></span>
							<span id="mcv-flags-radio-original-website"></span>

							<br>
							<br>

							<div id="mcv-website-flag-container">
								<?php _e( 'Custom flag URL (64px*64px recommended) : ', 'machiavel' ); ?>
								<input type="url" name="mcv_website_flag" id="mcv_website_flag" value="<?php echo esc_url( mcv_get_language_website_flag() ); ?>" />
							</div>

							<hr>

						</fieldset>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php _e( 'Translated languages', 'machiavel' ); ?></th>
					<td>
						<fieldset>

							<legend class="screen-reader-text">
								<span><?php _e( 'Translated languages', 'machiavel' ); ?></span>
							</legend>

							<label for="mcv_target_language">
								<?php _e( 'Add new target Language: ', 'machiavel' ); ?>
							</label>							

							<select id="mcv_add_new_target_language" name="mcv_add_new_target_language"></select>

							<a class="button button-primary" id="mcv-target-lang-add" href="javascript:void(0);"><?php _e( 'Add', 'machiavel' ); ?></a>

							<hr>
							<br>
							
							<p><strong><?php _e( 'Current target languages: ', 'machiavel' ); ?></strong></p>

							<hr>

							<div id="mcv-target-language-template">
								<div class="mcv-target-language">
									[FLAG][NAME] - <a href="javascript:void(0);" class="mcv-target-lang-update-flag" mcv-target-lang="[LANG]"><?php _e( 'Edit flag', 'machiavel' ); ?></a> - <a href="javascript:void(0);" class="mcv-target-lang-remove" mcv-target-lang="[LANG]"><?php _e( 'Remove', 'machiavel' ); ?></a>
									<div class="mcv-flag-target-container" mcv-target-lang="[LANG]">
										<br>
										<span><?php _e( 'Flag: ', 'machiavel' ); ?></span>
										<span class="mcv-subflags-radio-target-website">[FLAGS_OPTIONS]</span>
										<div class="mcv-subflag-target-custom" mcv-target-lang="[LANG]">
											<?php _e( 'Custom flag URL (64px*64px recommended) : ', 'machiavel' ); ?>
											[INPUT]
										</div>
									</div>
									<hr>
								</div>
							</div>

							<div id="mcv-target-languages-list"></div>
							
							<textarea name="mcv_target_languages" id="mcv_target_languages"><?php echo esc_textarea( json_encode( $languages_target, true ) ); ?></textarea>

						</fieldset>
					</td>
				</tr>
			</table>
			
			<?php submit_button(); ?>
		
		</form>
	</div>
	<?php
}
