<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_option_page_exclusions() {
	?>
	<div class="wrap">
		
		<h1><span class="dashicons dashicons-translation"></span> <?php _e( 'wpLingua : Exclusion rules', 'wplingua' ); ?></h1>

		<br>

		<form method="post" action="options.php">
			<?php
			settings_fields( 'wplng_exclusions' );
			do_settings_sections( 'wplng_exclusions' );
			?>
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'Exclude elements', 'wplingua' ); ?></th>
					<td>
						<fieldset>
							<label for="wplng_excluded_selectors"><strong><?php _e( 'Exclude HTML elements:', 'wplingua' ); ?></strong></label>
							<p><?php echo __( 'You can leave some elements of your web pages untranslated. To do this, list the element selectors to be excluded below, one per line. For example, use ', 'wplingua' ) . '<code>#website-main-title</code>' . __( ' to exclude an element by ID attribute, or ', 'wplingua' ) . '<code>.author-name</code>' . __( ' to exclude an element by class.', 'wplingua' ); ?></p>
							<br>
							<textarea name="wplng_excluded_selectors" id="wplng_excluded_selectors" rows="6" style="width:100%;"><?php echo esc_textarea( get_option( 'wplng_excluded_selectors' ) ); ?></textarea>
						</fieldset>
						<br>
						<hr>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Exclude URL', 'wplingua' ); ?></th>
					<td>
						<fieldset>
							<label for="wplng_excluded_url"><strong><?php _e( 'Exclude URLs from translation:', 'wplingua' ); ?></strong></label>
							<p><?php echo __( 'You can exclude from translations pages you wish to offer only in the site\'s original language. To do this, list the URLs to be excluded below, one per line. For example  ', 'wplingua' ) . '<code>/my-page/</code>' . __( '. You can also use REGEX. For example, use ', 'wplingua' ) . '<code>/author/.*</code>' . __( ' to exclude author pages.', 'wplingua' ); ?></p>
							<br>
							<textarea name="wplng_excluded_url" id="wplng_excluded_url" rows="6" style="width:100%;"><?php echo esc_textarea( get_option( 'wplng_excluded_url' ) ); ?></textarea>
						</fieldset>
					</td>
				</tr>
			</table>
			
			<?php submit_button(); ?>
		
		</form>
	</div>
	<?php
}
