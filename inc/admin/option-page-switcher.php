<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Print HTML Option page : wpLingua Switcher
 *
 * @return void
 */
function wplng_option_page_switcher() {

	$insert      = wplng_get_switcher_insert();
	$theme       = wplng_get_switcher_theme();
	$style       = wplng_get_switcher_style();
	$name_format = wplng_get_switcher_name_format();
	$flags_style = wplng_get_switcher_flags_style();
	$custom_css  = get_option( 'wplng_custom_css' );

	if ( empty( $custom_css ) || ! is_string( $custom_css ) ) {
		$custom_css = '';
	} else {
		$custom_css = wp_strip_all_tags( $custom_css );
	}

	?>
	<div class="wrap">
		
		<h1 class="wp-heading-inline"><span class="dashicons dashicons-translation"></span> <?php esc_html_e( 'wpLingua - Switcher settings', 'wplingua' ); ?></h1>

		<hr class="wp-header-end">

		<form method="post" action="options.php">
			<?php
			settings_fields( 'wplng_switcher' );
			do_settings_sections( 'wplng_switcher' );
			?>
			<table class="form-table wplng-form-table">
			
				<tr>
					<th scope="row"><?php esc_html_e( 'Switcher preview', 'wplingua' ); ?></th>
					<td id="wplng-switcher-preview-container">
						<div class="wplng-switcher-preview">
							<?php
							echo wplng_get_switcher_html(
								array( 'class' => 'switcher-preview' )
							);
							?>
						</div>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php esc_html_e( 'Switcher design', 'wplingua' ); ?></th>
					<td>
						<fieldset>

							<label for="wplng_style" class="wplng-fe-50">
								<strong><?php esc_html_e( 'Switcher style: ', 'wplingua' ); ?></strong>
							</label>

							<select id="wplng_style" name="wplng_style" class="wplng-fe-50">
								<?php

								$style_options = wplng_data_switcher_valid_style();

								foreach ( $style_options as $option_value => $option_name ) {
									if ( $style === $option_value ) {
										echo '<option value="' . esc_attr( $option_value ) . '" selected>';
									} else {
										echo '<option value="' . esc_attr( $option_value ) . '">';
									}
									echo esc_html( $option_name );
									echo '</option>';
								}

								?>
							</select>

						</fieldset>

						<br>

						<fieldset>
							<label for="wplng_name_format" class="wplng-fe-50">
								<strong><?php esc_html_e( 'Displayed name: ', 'wplingua' ); ?></strong>
							</label>
							<select id="wplng_name_format" name="wplng_name_format" class="wplng-fe-50">
								<?php

								$name_format_options = wplng_data_switcher_valid_name_format();

								foreach ( $name_format_options as $option_value => $option_name ) {
									if ( $name_format === $option_value ) {
										echo '<option value="' . esc_attr( $option_value ) . '" selected>';
									} else {
										echo '<option value="' . esc_attr( $option_value ) . '">';
									}
									echo esc_html( $option_name );
									echo '</option>';
								}

								?>
							</select>
						</fieldset>

						<br>

						<fieldset>
							<label for="wplng_flags_style" class="wplng-fe-50">
								<strong><?php esc_html_e( 'Flag style: ', 'wplingua' ); ?></strong>
							</label>
							<select id="wplng_flags_style" name="wplng_flags_style" class="wplng-fe-50">
								<?php

								$flags_style_options = wplng_data_switcher_valid_flags_style();

								foreach ( $flags_style_options as $option_value => $option_name ) {
									if ( $flags_style === $option_value ) {
										echo '<option value="' . esc_attr( $option_value ) . '" selected>';
									} else {
										echo '<option value="' . esc_attr( $option_value ) . '">';
									}
									echo esc_html( $option_name );
									echo '</option>';
								}

								?>
							</select>
						</fieldset>

						<br>

						<fieldset>
							<label for="wplng_theme" class="wplng-fe-50">
								<strong><?php esc_html_e( 'Switcher theme: ', 'wplingua' ); ?></strong>
							</label>

							<select id="wplng_theme" name="wplng_theme" class="wplng-fe-50">
								<?php

								$theme_options = wplng_data_switcher_valid_theme();

								foreach ( $theme_options as $option_value => $option_name ) {
									if ( $theme === $option_value ) {
										echo '<option value="' . esc_attr( $option_value ) . '" selected>';
									} else {
										echo '<option value="' . esc_attr( $option_value ) . '">';
									}
									echo esc_html( $option_name );
									echo '</option>';
								}

								?>
							</select>
						</fieldset>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php esc_html_e( 'Custom CSS', 'wplingua' ); ?></th>
					<td>
						<fieldset>
							<label for="wplng_custom_css">
								<strong><?php esc_html_e( 'Set custom CSS:', 'wplingua' ); ?></strong>
							</label>
							<textarea name="wplng_custom_css" id="wplng_custom_css"><?php echo esc_textarea( $custom_css ); ?></textarea>
						</fieldset>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php esc_html_e( 'Switcher insertion', 'wplingua' ); ?></th>
					<td>
						<fieldset>

							<label for="wplng_insert" class="wplng-fe-50">
								<strong><?php _e( 'Automatic insert: ', 'wplingua' ); ?></strong>
							</label>
							
							<select id="wplng_insert" name="wplng_insert" class="wplng-fe-50">
								<?php

								$insert_options = wplng_data_switcher_valid_insert();

								foreach ( $insert_options as $option_value => $option_name ) {
									if ( $insert === $option_value ) {
										echo '<option value="' . esc_attr( $option_value ) . '" selected>';
									} else {
										echo '<option value="' . esc_attr( $option_value ) . '">';
									}
									echo esc_html( $option_name );
									echo '</option>';
								}

								?>
							</select>

						</fieldset>
						<br>
						<p>
							<strong class="wplng-fe-50"><?php esc_html_e( 'Shortcode switcher: ', 'wplingua' ); ?></strong>
							<code class="wplng-fe-50">[wplingua-switcher]</code>
						</p>
						
					</td>
				</tr>

			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}


/**
 * Print HTML subsection of Option page : wpLingua Switcher - Flags style
 *
 * @param string $old_flags_style Flag URL
 * @param string $new_flags_style Flag URL
 * @return void
 */
function wplng_options_switcher_update_flags_style( $old_flags_style, $new_flags_style ) {

	if ( $old_flags_style === $new_flags_style ) {
		return;
	}

	if ( 'none' === $new_flags_style ) {
		$new_flags_style = 'rectangular';
	}

	if ( 'none' === $old_flags_style ) {
		$old_flags_style = 'rectangular';
	}

	/**
	 * Replace flag URL for website language
	 */

	$website_flag = wplng_get_language_website_flag();

	$website_flag = str_replace(
		'/wplingua/assets/images/' . $old_flags_style . '/',
		'/wplingua/assets/images/' . $new_flags_style . '/',
		$website_flag
	);

	$website_flag = sanitize_url( $website_flag );

	update_option( 'wplng_website_flag', $website_flag );

	/**
	 * Replace flag URL for target languages
	 */

	$target_languages_json = get_option( 'wplng_target_languages' );

	if ( empty( $target_languages_json ) ) {
		return;
	}

	$target_languages = json_decode(
		$target_languages_json,
		true
	);

	if ( empty( $target_languages ) || ! is_array( $target_languages ) ) {
		return;
	}

	foreach ( $target_languages as $key => $target_language ) {
		if ( ! empty( $target_language['flag'] )
			&& is_string( $target_language['flag'] )
		) {

			$old_src = sanitize_url( $target_language['flag'] );

			$new_src = str_replace(
				'/' . $old_flags_style . '/',
				'/' . $new_flags_style . '/',
				$old_src
			);

			$new_src = sanitize_url( $new_src );

			$target_languages[ $key ]['flag'] = $new_src;
		}
	}

	$target_languages_json = wp_json_encode(
		$target_languages,
		JSON_UNESCAPED_SLASHES
	);

	update_option( 'wplng_target_languages', $target_languages_json );
}
