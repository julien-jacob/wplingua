<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_option_page_switcher() {

	$insert      = wplng_get_switcher_insert();
	$theme       = wplng_get_switcher_theme();
	$style       = wplng_get_switcher_style();
	$name_format = wplng_get_switcher_name_format();
	$flags_show  = wplng_get_switcher_flags_show();
	$flags_style = wplng_get_switcher_flags_style();

	?>
	<div class="wrap">
		
		<h1><?php _e( 'wpLingua : Switcher settings', 'wplingua' ); ?></h1>

		<br>

		<form method="post" action="options.php">
			<?php
			settings_fields( 'wplng_switcher' );
			do_settings_sections( 'wplng_switcher' );
			?>
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'Automatic insertion', 'wplingua' ); ?></th>
					<td>
						<fieldset>

							<label for="wplng_insert">
								<strong><?php _e( 'Position for automatic switcher insertion: ', 'wplingua' ); ?></strong>
							</label>
							
							<select id="wplng_insert" name="wplng_insert">
								<?php

								$insert_options = array(
									'bottom-right'  => __( 'Bottom right', 'wplingua' ),
									'bottom-center' => __( 'Bottom center', 'wplingua' ),
									'bottom-left'   => __( 'Bottom left', 'wplingua' ),
									'none'          => __( 'None', 'wplingua' ),
								);

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
						<p><?php _e( 'Shortcode switcher: ', 'wplingua' ); ?><code>[wplng-switcher]</code></p>
						<hr>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php _e( 'Theme', 'wplingua' ); ?></th>
					<td>
						<fieldset>
							<label for="wplng_theme">
								<strong><?php _e( 'Theme: ', 'wplingua' ); ?></strong>
							</label>

							<select id="wplng_theme" name="wplng_theme">
								<?php

								$theme_options = array(
									'light' => __( 'Light', 'wplingua' ),
									'dark'  => __( 'Dark', 'wplingua' ),
								);

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
						<hr>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php _e( 'Elements', 'wplingua' ); ?></th>
					<td>
						<fieldset>

							<label for="wplng_style">
								<strong><?php _e( 'Switcher style: ', 'wplingua' ); ?></strong>
							</label>

							<select id="wplng_style" name="wplng_style">
								<?php

								$style_options = array(
									'list'  => __( 'List', 'wplingua' ),
									'block' => __( 'Block', 'wplingua' ),
								);

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
							<label for="wplng_name_format">
								<strong><?php _e( 'Displayed name: ', 'wplingua' ); ?></strong>
							</label>
							<select id="wplng_name_format" name="wplng_name_format">
								<?php

								$name_format_options = array(
									'name' => __( 'Complete name', 'wplingua' ),
									'id'   => __( 'Language ID', 'wplingua' ),
									'none' => __( 'No display', 'wplingua' ),
								);

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
							<label for="wplng_flags_show">
								<strong><?php _e( 'Displayed Flags: ', 'wplingua' ); ?></strong>
							</label>
							<select id="wplng_flags_show" name="wplng_flags_show">
								<?php

								if ( $flags_show ) {
									echo '<option value="show" selected>';
									echo __( 'Show', 'wplingua' );
									echo '</option>';
									echo '<option value="hide">';
									echo __( 'Hide', 'wplingua' );
									echo '</option>';
								} else {
									echo '<option value="show">';
									echo __( 'Show', 'wplingua' );
									echo '</option>';
									echo '<option value="hide" selected>';
									echo __( 'Hide', 'wplingua' );
									echo '</option>';
								}

								?>
							</select>
						</fieldset>

						<br>

						<fieldset>
							<label for="wplng_flags_style">
								<strong><?php _e( 'Flag style: ', 'wplingua' ); ?></strong>
							</label>
							<select id="wplng_flags_style" name="wplng_flags_style">
								<?php

								$flags_style_options = array(
									'circle'      => __( 'Circle', 'wplingua' ),
									'rectangular' => __( 'Rectangular', 'wplingua' ),
								);

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
						
					</td>
				</tr>

				
				
			</table>
			
			<?php submit_button(); ?>
		
		</form>
	</div>
	<?php
}

function wplng_options_switcher_update_flags_style( $old_flags_style, $new_flags_style ) {

	error_log( $old_flags_style );
	error_log( $new_flags_style );
	error_log( '-------' );

	if ( $old_flags_style !== $new_flags_style ) {

		$website_flag = wplng_get_language_website_flag();
		$website_flag = str_replace(
			'/wplingua/images/' . $old_flags_style . '/',
			'/wplingua/images/' . $new_flags_style . '/',
			$website_flag
		);
		update_option( 'wplng_website_flag', $website_flag );

		$target_languages = get_option( 'wplng_target_languages' );
		$target_languages = str_replace(
			'/' . $old_flags_style . '/',
			'/' . $new_flags_style . '/',
			$target_languages
		);
		update_option( 'wplng_target_languages', $target_languages );

	}

}
add_action( 'update_option_wplng_flags_style', 'wplng_options_switcher_update_flags_style', 10, 2 );
