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
	register_setting( 'mcv_settings', 'mcv_target_languages' );
	// register_setting( 'mcv_settings', 'mcv_disable_files_editor' );
	// register_setting( 'mcv_settings', 'mcv_show_reusable_blocks' );
	// register_setting( 'mcv_settings', 'mcv_show_navigation_menu' );
	// register_setting( 'mcv_settings', 'mcv_show_templates' );
	// register_setting( 'mcv_settings', 'mcv_show_template_parts' );

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

	$all_languages   = mcv_get_all_languages();
	$language_source = mcv_get_language_source();

	$languages_target_json = get_option( 'mcv_target_languages' );
	$languages_target = json_decode($languages_target_json);
	$languages_target = mcv_get_language_by_ids($languages_target);



	// var_dump($languages_target); die;



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
								if ( empty( get_option( 'mcv_website_language' ) ) ) {
									$website_language_saved = false;
								} else {

									$website_language_id = get_option( 'mcv_website_language' );
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

								/*

								foreach ( $all_languages as $language ) {
									if ( ! empty( $language['id'] )
										&& ! empty( $language['name'] )
									) {

										if ( $website_language_saved
											&& $website_language_id === $language['id']
										) {
											continue;
										}

										echo '<option  value="' . esc_attr( $language['id'] ) . '">' . esc_html( $language['name'] ) . '</option>';
									}
								}
								*/ ?>
							</select>

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

							<select id="mcv_add_new_target_language" name="mcv_add_new_target_language">

								<?php

								// if ( ! empty( get_option( 'mcv_target_language' ) ) {

								// }
								// TODO : Retirer langues déjà sélectionnées + langue courante du site
								/*
								foreach ( $all_languages as $language ) {
									if ( ! empty( $language['id'] )
										&& ! empty( $language['name'] )
									) {

										echo '<option value="' . esc_attr( $language['id'] ) . '">' . esc_html( $language['name'] ) . '</option>';
									}
								}
								*/
								?>
							</select>

							<button class="button button-primary" id="mcv-target-lang-add">
								<?php _e( 'Add', 'machiavel' ); ?>
							</button>

							<hr>
							<br>
							<p><strong><?php _e( 'Current target languages: ', 'machiavel' ); ?></strong></p>
							<hr>

							<?php
							echo '<div id="mcv-target-language-template"><div class="mcv-target-language">[NAME] - <a href="javascript:void(0);" class="mcv-target-lang-remove" mcv-target-lang="[LANG]">' . __( 'Remove', 'machiavel' ) . '</a><hr></div></div>';
							?>
						

							<div id="mcv-target-languages-list">
								<?php
								// foreach ($languages_target as $language_target) {
								// 	$remove_link = '<a href="javascript:void(0);" class="mcv-target-lang-remove" mcv-target-lang="' . esc_attr($language_target['id']) . '">' . __( 'Remove', 'machiavel' ) . '</a>';

								// 	echo '<div class="mcv-target-language">';
								// 	echo esc_html($language_target['name']) . ' - ' . $remove_link . '<hr>';
								// 	echo '</div>';
								// }
								?>
							</div>

							<input type="text" name="mcv_target_languages" id="mcv_target_languages" value="<?php echo esc_attr( $languages_target_json ); ?>" />

						</fieldset>
					</td>
				</tr>


			</table>
			
			<?php submit_button(); ?>
		
		</form>
	</div>
	<?php
}
