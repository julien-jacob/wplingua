<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_exclude() {
	?>
	<div class="wrap">
		
		<h1><?php _e( 'wpLingua : Exclusion rules', 'wplingua' ); ?></h1>

		<br>

		<form method="post" action="options.php">
			<?php
			settings_fields( 'wplng_exclusions' );
			do_settings_sections( 'wplng_exclusions' );
			?>
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'Exclude texts', 'wplingua' ); ?></th>
					<td>
						<fieldset>
							<label for=""><?php _e( 'Exclude texts:', 'wplingua' ); ?></label>
							<textarea name="" id="" rows="6" style="width:100%;"></textarea>
						</fieldset>
						<br>
						<hr>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Exclude elements', 'wplingua' ); ?></th>
					<td>
						<fieldset>
							<label for=""><?php _e( 'Exclude elements with selector:', 'wplingua' ); ?></label>
							<textarea name="" id="" rows="6" style="width:100%;"></textarea>
						</fieldset>
						<br>
						<hr>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Exclude URL', 'wplingua' ); ?></th>
					<td>
						<fieldset>
							<label for=""><?php _e( 'Exclude URL with regex:', 'wplingua' ); ?></label>
							<textarea name="" id="" rows="6" style="width:100%;"></textarea>
						</fieldset>
						<br>
						<hr>
					</td>
				</tr>
			</table>
			
			<?php submit_button(); ?>
		
		</form>
	</div>
	<?php
}
