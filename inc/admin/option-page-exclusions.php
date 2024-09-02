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
	?>

	<h1 class="wplng-option-page-title"><span class="dashicons dashicons-translation"></span> <?php esc_html_e( 'wpLingua - Exclusion rules', 'wplingua' ); ?></h1>

	<div class="wrap">
		<hr class="wp-header-end">
		<form method="post" action="options.php">
			<?php
			settings_fields( 'wplng_exclusions' );
			do_settings_sections( 'wplng_exclusions' );
			?>
			<table class="form-table wplng-form-table">
			<tr>
					<th scope="row"><span class="dashicons dashicons-admin-links"></span> <?php esc_html_e( 'Exclude URL', 'wplingua' ); ?></th>
					<td>
						<fieldset>
							<label for="wplng_excluded_url"><strong><?php esc_html_e( 'Exclude URL from translation: ', 'wplingua' ); ?></strong></label>

							<hr>

							<p><?php esc_html_e( 'You can exclude from translations pages you wish to offer only in the website\'s original language. To do this, list the REGEXs that match the URL to be excluded, one per line. Examples: ', 'wplingua' ); ?></p>

							<ul>
								<li><code>^/my-page/$</code> - <?php esc_html_e( 'Exclude URL "/my-page/"', 'wplingua' ); ?></li>
								<li><code>^/my-page/</code> - <?php esc_html_e( 'Exclude URL starting with "/my-page/"', 'wplingua' ); ?></li>
								<li><code>/my-page/$</code> - <?php esc_html_e( 'Exclude URL ending with "/my-page/"', 'wplingua' ); ?></li>
								<li><code>/my-page/</code> - <?php esc_html_e( 'Exclude URL containing "/my-page/"', 'wplingua' ); ?></li>
							</ul>
							<br>
							<textarea name="wplng_excluded_url" id="wplng_excluded_url" rows="6"><?php echo esc_textarea( get_option( 'wplng_excluded_url' ) ); ?></textarea>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><span class="dashicons dashicons-welcome-widgets-menus"></span> <?php esc_html_e( 'Exclude elements', 'wplingua' ); ?></th>
					<td>
						<fieldset>
							<label for="wplng_excluded_selectors"><strong><?php esc_html_e( 'Exclude HTML elements: ', 'wplingua' ); ?></strong></label>

							<hr>

							<p><?php esc_html_e( 'You can leave some elements of your web pages untranslated. To do this, list below the CSS selectors for the elements to be excluded, one per line. Examples: ', 'wplingua' ); ?></p>
							
							<ul>
								<li><code>#website-main-title</code> - <?php esc_html_e( 'Exclude elements by ID attribute', 'wplingua' ); ?></li>
								<li><code>.author-name</code> - <?php esc_html_e( 'Exclude elements by class attribute', 'wplingua' ); ?></li>
								<li><code>.entry-content pre</code> - <?php esc_html_e( 'Exclude elements by CSS selector', 'wplingua' ); ?></li>
							</ul>
							<br>
							<textarea name="wplng_excluded_selectors" id="wplng_excluded_selectors" rows="6"><?php echo esc_textarea( get_option( 'wplng_excluded_selectors' ) ); ?></textarea>
						</fieldset>
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
