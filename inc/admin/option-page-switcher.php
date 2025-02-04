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

	$switcher_html = wplng_get_switcher_html(
		array( 'class' => 'switcher-preview' )
	);

	if ( empty( $custom_css ) || ! is_string( $custom_css ) ) {
		$custom_css = '';
	} else {
		$custom_css = wp_strip_all_tags( $custom_css );
	}

	?>

	<h1 class="wplng-option-page-title"><span class="dashicons dashicons-translation"></span> <?php esc_html_e( 'wpLingua - Switcher settings', 'wplingua' ); ?></h1>

	

	<div class="wrap">
		<hr class="wp-header-end">
		<form method="post" action="options.php">
			<?php
			settings_fields( 'wplng_switcher' );
			do_settings_sections( 'wplng_switcher' );
			?>
			<table class="form-table wplng-form-table">

				<?php if ( empty( $switcher_html ) ) : ?>
					<div class="wplng-notice notice notice-error">
						<p>
							<strong><?php esc_html_e( 'No target language selected.', 'wplingua' ); ?></strong>
						</p>
					</div>
				<?php else : ?>
					<tr>
					<th scope="row"><span class="dashicons dashicons-visibility"></span> <?php esc_html_e( 'Preview', 'wplingua' ); ?></th>
					<td id="wplng-switcher-preview-container">
						<div class="wplng-switcher-preview">
							<?php echo $switcher_html; ?>
						</div>
					</td>
				</tr>
				<?php endif; ?>

				<tr>
					<th scope="row"><span class="dashicons dashicons-admin-appearance"></span> <?php esc_html_e( 'Design', 'wplingua' ); ?></th>
					<td>
						<p>
							<fieldset>

								<label for="wplng_style" class="wplng-fe-50">
									<strong><?php esc_html_e( 'Layout: ', 'wplingua' ); ?></strong> 
									<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-layout"></span>
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
						</p>

						<div class="wplng-help-box" id="wplng-hb-layout">
							<p>
								<?php esc_html_e( 'This option allow you to define the layout of the languages ​​in the switcher.', 'wplingua' ); ?>
							</p>
							<hr>
							<ul>
								<li>
									<strong><?php esc_html_e( 'Inline list: ', 'wplingua' ); ?></strong> 
									<?php esc_html_e( 'the languages ​​are lined up next to each other.', 'wplingua' ); ?> 
								</li>
								<li>
									<strong><?php esc_html_e( 'Block: ', 'wplingua' ); ?></strong> 
									<?php esc_html_e( 'the languages ​​are placed in columns, one under the other.', 'wplingua' ); ?> 
								</li>
								<li>
									<strong><?php esc_html_e( 'Dropdown: ', 'wplingua' ); ?></strong> 
									<?php esc_html_e( 'the languages ​​appear in a drop-down menu; this is recommended when your website offers several languages.', 'wplingua' ); ?> 
								</li>
							</ul>
						</div>

						<hr>

						<p>
							<fieldset>
								<label for="wplng_name_format" class="wplng-fe-50">
									<strong><?php esc_html_e( 'Displayed names: ', 'wplingua' ); ?></strong> 
									<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-name-format"></span>
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
						</p>

						<div class="wplng-help-box" id="wplng-hb-name-format">
							<p>
								<?php esc_html_e( 'This option allow you to choose how the languages names should be written in the switcher.', 'wplingua' ); ?>
							</p>

							<hr>

							<ul>
								<li>
									<strong><?php esc_html_e( 'Translated names: ', 'wplingua' ); ?></strong>
									<?php esc_html_e( 'the name of the languages ​​is translated into the current language displayed.', 'wplingua' ); ?>
								</li>
								<li>
									<strong><?php esc_html_e( 'Original name: ', 'wplingua' ); ?></strong>
									<?php esc_html_e( 'each language retains its name in its own language (English, Français, 日本語, Português, Español, etc.).', 'wplingua' ); ?>
								</li>
								<li>
									<strong><?php esc_html_e( 'Language ID: ', 'wplingua' ); ?></strong>
									<?php esc_html_e( 'language names use language ID (FR, EN, DE, RU, etc.).', 'wplingua' ); ?>
								</li>
								<li>
									<strong><?php esc_html_e( 'No display: ', 'wplingua' ); ?></strong>
									<?php esc_html_e( 'no text, only flags are displayed.', 'wplingua' ); ?>
								</li>
							</ul>
						</div>

						<hr>

						<p>
							<fieldset>
								<label for="wplng_flags_style" class="wplng-fe-50">
									<strong><?php esc_html_e( 'Flags style: ', 'wplingua' ); ?></strong> 
									<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-flags-style"></span>
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
						</p>

						<div class="wplng-help-box" id="wplng-hb-flags-style">
							<p><?php esc_html_e( 'This option allow you to choose the appearance of the flags in the switcher. ', 'wplingua' ); ?></p>

							<hr>

							<ul>
								<li>
									<strong><?php esc_html_e( 'Circle: ', 'wplingua' ); ?></strong> 
									<?php esc_html_e( 'display round flags.', 'wplingua' ); ?>
								</li>
								<li>
									<strong><?php esc_html_e( 'Rectangular: ', 'wplingua' ); ?></strong> 
									<?php esc_html_e( 'display rectangular flags.', 'wplingua' ); ?>
								</li>
								<li>
									<strong><?php esc_html_e( 'Wave: ', 'wplingua' ); ?></strong> 
									<?php esc_html_e( 'display wavy flags.', 'wplingua' ); ?>
								</li>
								<li>
									<strong><?php esc_html_e( 'No display: ', 'wplingua' ); ?></strong>
									<?php esc_html_e( 'no display any flags, only the language name will be displayed.', 'wplingua' ); ?>
								</li>
							</ul>

							<hr>

							<p><?php esc_html_e( 'You can also change the country flag or set a custom flag from wpLingua ➔ General settings. For example, you can set the flag of Mexico for Spanish instead of the flag of Spain.', 'wplingua' ); ?></p>

						</div>

						<hr>

						<p>
							<fieldset>
								<label for="wplng_theme" class="wplng-fe-50">
									<strong><?php esc_html_e( 'Color theme: ', 'wplingua' ); ?></strong> 
									<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-theme"></span>
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
						</p>

						<div class="wplng-help-box" id="wplng-hb-theme">
							<p>
								<?php esc_html_e( 'This option allows you to choose the color and border styles of the languages switcher.', 'wplingua' ); ?>
							</p>

							<hr>

							<p>
								<?php esc_html_e( 'The color options offer 5 themes: ', 'wplingua' ); ?>
							</p>

							<ul>
								<li>
									<strong><?php esc_html_e( 'Light: ', 'wplingua' ); ?></strong>
									<?php esc_html_e( 'white color in the background of the switcher.', 'wplingua' ); ?>
								</li>
								<li>
									<strong><?php esc_html_e( 'Grey: ', 'wplingua' ); ?></strong>
									<?php esc_html_e( 'grey background color.', 'wplingua' ); ?>
								</li>
								<li>
									<strong><?php esc_html_e( 'Dark: ', 'wplingua' ); ?></strong>
									<?php esc_html_e( 'black background color.', 'wplingua' ); ?>
								</li>
								<li>
									<strong><?php esc_html_e( 'Blur Black: ', 'wplingua' ); ?></strong>
									<?php esc_html_e( 'transparent and blurred background color with black text and borders.', 'wplingua' ); ?>
								</li>
								<li>
									<strong><?php esc_html_e( 'Blur White: ', 'wplingua' ); ?></strong>
									<?php esc_html_e( 'transparent and blurred background color with white text and borders.', 'wplingua' ); ?>
								</li>
							</ul>

							<hr>

							<p>
								<?php esc_html_e( 'Each theme is then broken down by shape and border style:', 'wplingua' ); ?>
							</p>

							<ul>
								<li>
									<strong><?php esc_html_e( 'Double – Smooth: ', 'wplingua' ); ?></strong>
									<?php esc_html_e( 'the switcher will be framed by a double border with rounded corners.', 'wplingua' ); ?>
								</li>
								<li>
									<strong><?php esc_html_e( 'Double – Square: ', 'wplingua' ); ?></strong>
									<?php esc_html_e( 'double border with square corners.', 'wplingua' ); ?>
								</li>
								<li>
									<strong><?php esc_html_e( 'Simple – Smooth: ', 'wplingua' ); ?></strong>
									<?php esc_html_e( 'single border with rounded corners.', 'wplingua' ); ?>
								</li>
								<li>
									<strong><?php esc_html_e( 'Simple – Square: ', 'wplingua' ); ?></strong>
									<?php esc_html_e( 'single border with square corners.', 'wplingua' ); ?>
								</li>
							</ul>

						</div>

					</td>
				</tr>

				<tr>
					<th scope="row"><span class="dashicons dashicons-media-code"></span> <?php esc_html_e( 'Custom CSS', 'wplingua' ); ?></th>
					<td>
						<p><strong><?php esc_html_e( 'Set custom CSS: ', 'wplingua' ); ?></strong></p>
						<hr>
						<p>
							<?php esc_html_e( 'The field below allows you to add custom CSS code that will run on all pages.', 'wplingua' ); ?> 
							<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-css"></span>
						</p>
						<div class="wplng-help-box" id="wplng-hb-css">
							<p>
								<?php _e( 'First of all, note that the CSS class <code>.switcher-content</code> allows you to act on the entire block of the language switcher while the class <code>.wplng-langague</code> allows you to act on the languages ​​themselves.', 'wplingua' ); ?>
							</p>
							
							<?php
								echo '<pre>';
								echo '.wplng-switcher.theme-light-double-square .switcher-content {' . PHP_EOL;
								echo '    background-color: #5e33d9;' . PHP_EOL;
								echo '    border: 1px solid #5e33d9;' . PHP_EOL;
								echo '}';
								echo '</pre>';
							?>

							<hr>

							<p>
								<?php _e( 'To change the background and border colors of languages, here is the CSS:' ); ?>
							</p>

							<?php
								echo '<pre>';
								echo '.wplng-switcher.theme-light-double-square .switcher-content .wplng-language {' . PHP_EOL;
								echo '    border: 1px solid #5e33d9;' . PHP_EOL;
								echo '    color: #5e33d9;' . PHP_EOL;
								echo '}';
								echo '</pre>';
							?>

							<hr>

							<p>
								<a href="https://wplingua.com/documentation/user/how-to-customize-the-language-switcher/" target="_blank"><?php _e( 'More example on wplingua.com' ); ?></a>
							</p>
						</div>

						<fieldset>
							<textarea name="wplng_custom_css" id="wplng_custom_css" spellcheck="false"><?php echo esc_textarea( $custom_css ); ?></textarea>
						</fieldset>
					</td>
				</tr>

				<tr>
					<th scope="row"><span class="dashicons dashicons-admin-post"></span> <?php esc_html_e( 'Insertion', 'wplingua' ); ?></th>
					<td>
						
						<p>
							<fieldset>

								<label for="wplng_insert" class="wplng-fe-50">
									<strong><?php _e( 'Automatic insert: ', 'wplingua' ); ?></strong> 
									<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-insert-automatic"></span>
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
						</p>

						<div class="wplng-help-box" id="wplng-hb-insert-automatic">
							<p><?php esc_html_e( 'Choose a predefined location: ', 'wplingua' ); ?></p>

							<ul>
								<li>
									<strong><?php esc_html_e( 'Bottom right: ', 'wplingua' ); ?></strong>
									<?php esc_html_e( 'the switcher is placed at the bottom right of the screen.', 'wplingua' ); ?>
								</li>
								<li>
									<strong><?php esc_html_e( 'Bottom Center: ', 'wplingua' ); ?></strong>
									<?php esc_html_e( 'bottom center of the screen.', 'wplingua' ); ?>
								</li>
								<li>
									<strong><?php esc_html_e( 'Bottom left: ', 'wplingua' ); ?></strong>
									<?php esc_html_e( 'bottom left of the screen.', 'wplingua' ); ?>
								</li>
								<li>
									<strong><?php esc_html_e( 'None: ', 'wplingua' ); ?></strong>
									<?php esc_html_e( 'the switcher is not inserted automatically.', 'wplingua' ); ?>
								</li>
							</ul>

						</div>

						<hr>

						<p>
							<strong class="wplng-fe-50">
								<?php esc_html_e( 'Shortcode switcher: ', 'wplingua' ); ?> 
								<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-insert-shortcode"></span>
							</strong> 
							<code class="wplng-fe-50">[wplng_switcher]</code>
						</p>
						
						<div class="wplng-help-box" id="wplng-hb-insert-shortcode">
							<p><?php esc_html_e( 'If you want to insert the language switcher only in certain places on your website, you can use the shortcode provided for this purpose. Note that in this case, the previous option should be "None". This method is ideal for placing the switcher where you want it, whether you are using a Gutenberg block-based theme (FSE), a classic theme or even a page or theme builder like Divi, Elementor...', 'wplingua' ); ?></p>
						</div>

						<hr>

						<p>
							<strong class="wplng-fe-50">
								<?php esc_html_e( 'Gutenberg & FSE: ', 'wplingua' ); ?> 
								<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-insert-block"></span>
							</strong>
							<span class="wplng-fe-50"><?php esc_html_e( 'Block wpLingua Switcher', 'wplingua' ); ?> </span>
						</p>

						<div class="wplng-help-box" id="wplng-hb-insert-block">
							<p><?php esc_html_e( 'Block based themes, wpLingua offers a dedicated Gutenberg block. You can insert the switcher wherever you want, whether in the content or via the overall site editor.', 'wplingua' ); ?></p>
						</div>

						<hr>

						<p>
							<strong class="wplng-fe-50">
								<?php esc_html_e( 'Switcher in menu: ', 'wplingua' ); ?> 
								<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-insert-menu"></span>
							</strong>
							<span class="wplng-fe-50">
								<?php

								$support_menus = ! empty( get_nav_menu_locations() );

								if ( $support_menus ) {
									echo '<a ';
									echo 'href="' . esc_url( get_admin_url() . 'nav-menus.php' ) . '" ';
									echo 'target="_blank"';
									echo '>';
								}

								esc_html_e( 'Appearance ➔ Menus', 'wplingua' );

								if ( $support_menus ) {
									echo '</a>';
								}

								?>
							</span>
						</p>

						<div class="wplng-help-box" id="wplng-hb-insert-menu">
							<p><?php esc_html_e( 'If the active theme manages menus (classic theme), it is possible to add a language switcher. The design of these language switchers will be defined by the theme. Go to the Appearance ➔ Menu tab to add the wpLingua switcher as a menu item. It is also possible to add it as a sub-element.', 'wplingua' ); ?></p>
						</div>

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

	update_option(
		'wplng_target_languages',
		wp_json_encode(
			$target_languages,
			JSON_UNESCAPED_SLASHES
		)
	);
}
