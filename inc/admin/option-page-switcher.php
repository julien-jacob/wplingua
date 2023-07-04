<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_option_page_switcher() {
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

							<label for="wplng_automatic_insertion">
								<strong><?php _e( 'Position for automatic switcher insertion: ', 'wplingua' ); ?></strong>
							</label>

							<select id="wplng_automatic_insertion" name="wplng_automatic_insertion">
								<option value="bottom-left"><?php _e( 'Bottom left', 'wplingua' ); ?></option>
								<option value="bottom-right"><?php _e( 'Bottom right', 'wplingua' ); ?></option>
								<option value="none"><?php _e( 'None', 'wplingua' ); ?></option>
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
								<strong><?php _e( 'Theme color: ', 'wplingua' ); ?></strong>
							</label>

							<select id="wplng_theme" name="wplng_theme">
								<option value="light"><?php _e( 'light', 'wplingua' ); ?></option>
								<option value="dark"><?php _e( 'dark', 'wplingua' ); ?></option>
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
								<option value="dropdown"><?php _e( 'Dropdown', 'wplingua' ); ?></option>
								<option value="list"><?php _e( 'List', 'wplingua' ); ?></option>
								<option value="block"><?php _e( 'Block', 'wplingua' ); ?></option>
							</select>

						</fieldset>

						<br>

						<fieldset>
							<label for="wplng_name_style">
								<strong><?php _e( 'Displayed name: ', 'wplingua' ); ?></strong>
							</label>
							<select id="wplng_name_style" name="wplng_name_style">
								<option value="bottom-left"><?php _e( 'Complete name', 'wplingua' ); ?></option>
								<option value="bottom-right"><?php _e( 'Language ID', 'wplingua' ); ?></option>
								<option value="bottom-right"><?php _e( 'No display', 'wplingua' ); ?></option>
							</select>
						</fieldset>

						<br>

						<fieldset>
							<label for="wplng_flags_show">
								<strong><?php _e( 'Displayed Flags: ', 'wplingua' ); ?></strong>
							</label>
							<select id="wplng_flags_show" name="wplng_flags_show">
								<option value="show"><?php _e( 'Show', 'wplingua' ); ?></option>
								<option value="hide"><?php _e( 'Hide', 'wplingua' ); ?></option>
							</select>
						</fieldset>

						<br>

						<fieldset>
							<label for="wplng_flags_style">
								<strong><?php _e( 'Flag style: ', 'wplingua' ); ?></strong>
							</label>
							<select id="wplng_flags_style" name="wplng_flags_style">
								<option value="circle"><?php _e( 'Circle', 'wplingua' ); ?></option>
								<option value="rounded"><?php _e( 'Rounded', 'wplingua' ); ?></option>
								<option value="rectangular"><?php _e( 'Rectangular', 'wplingua' ); ?></option>
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
