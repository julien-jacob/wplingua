<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Print HTML Option page : wpLingua Exclusions
 *
 * @return void
 */
function wplng_option_page_exclusions() {

	wplng_show_api_message();

	?>
	<div class="wrap">
		
		<h1><span class="dashicons dashicons-translation"></span> <?php _e( 'wpLingua / Beta : Exclusion rules', 'wplingua' ); ?></h1>

		<br>

		<form method="post" action="options.php">
			<?php
			settings_fields( 'wplng_exclusions' );
			do_settings_sections( 'wplng_exclusions' );
			?>
			<table class="form-table wplng-form-table">
				<tr>
					<th scope="row"><?php _e( 'Exclude elements', 'wplingua' ); ?></th>
					<td>
						<fieldset>
							<label for="wplng_excluded_selectors"><strong><?php _e( 'Exclude HTML elements:', 'wplingua' ); ?></strong></label>
							<p><?php echo __( 'You can leave some elements of your web pages untranslated. To do this, list the element selectors to be excluded below, one per line. Examples:', 'wplingua' ); ?></p>
							<ul>
								<li><code>#website-main-title</code> - <?php _e( 'Exclude elements by ID attribute', 'wplingua' ); ?></li>
								<li><code>.author-name</code> - <?php _e( 'Exclude elements by class attribute', 'wplingua' ); ?></li>
								<li><code>.entry-content pre</code> - <?php _e( 'Exclude elements by CSS selector', 'wplingua' ); ?></li>
							</ul>
							<br>
							<textarea name="wplng_excluded_selectors" id="wplng_excluded_selectors" rows="6"><?php echo esc_textarea( get_option( 'wplng_excluded_selectors' ) ); ?></textarea>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Exclude URL', 'wplingua' ); ?></th>
					<td>
						<fieldset>
							<label for="wplng_excluded_url"><strong><?php _e( 'Exclude URLs from translation:', 'wplingua' ); ?></strong></label>
							<p><?php echo __( 'You can exclude from translations pages you wish to offer only in the site\'s original language. To do this, List the REGEXs that match the URLs to be excluded. Examples:', 'wplingua' ); ?></p>
							<ul>
								<li><code>^/my-page/$</code> - <?php _e( 'Exclude URL "/my-page/"', 'wplingua' ); ?></li>
								<li><code>^/my-page/</code> - <?php _e( 'Exclude URLs starting with "/my-page/"', 'wplingua' ); ?></li>
								<li><code>/my-page/$</code> - <?php _e( 'Exclude URLs ending with "/my-page/"', 'wplingua' ); ?></li>
								<li><code>/my-page/</code> - <?php _e( 'Exclude URL containing "/my-page/"', 'wplingua' ); ?></li>
							</ul>
							<br>
							<textarea name="wplng_excluded_url" id="wplng_excluded_url" rows="6"><?php echo esc_textarea( get_option( 'wplng_excluded_url' ) ); ?></textarea>
						</fieldset>
					</td>
				</tr>
			</table>
			
			<?php submit_button(); ?>
		
		</form>
	</div>
	<?php
}
