<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Print HTML Option page : wpLingua Mail Translation
 *
 * @return void
 */
function wplng_option_page_mail() {

	$data = wplng_get_api_data();

	$checkbox_mail_enable_attr = 'value="1" disabled';

	if ( $data['status'] !== 'FREE' ) {
		$checkbox_mail_enable_attr = checked(
			1,
			get_option( 'wplng_mail_enable' ),
			false
		);
	}

	/**
	 * Get excluded mail addresses
	 *
	 * If the option was never saved by the user (get_option() returns
	 * false, its 3rd param, only when the option doesn't exist in DB),
	 * fallback on a default value excluding the site admin email
	 */

	$mail_exclude_address = get_option( 'wplng_mail_exclude_address', false );

	if ( false === $mail_exclude_address ) {

		$admin_email = get_option( 'admin_email' );

		$mail_exclude_address = '';

		if ( is_string( $admin_email ) && '' !== trim( $admin_email ) ) {
			$mail_exclude_address = '^' . preg_quote( $admin_email, '#' ) . '$';
		}

		$mail_exclude_address = apply_filters(
			'wplng_mail_exclude_address_default',
			$mail_exclude_address
		);
	}

	/**
	 * Get excluded URL for mail translation
	 *
	 * If the option was never saved by the user, fallback on a default
	 * value excluding the '/contact/' page, if it exists
	 */

	$mail_exclude_url = get_option( 'wplng_mail_exclude_url', false );

	if ( false === $mail_exclude_url ) {

		$mail_exclude_url = '';

		if ( get_page_by_path( 'contact', OBJECT, 'page' ) ) {
			$mail_exclude_url = '^/contact/$';
		}

		$mail_exclude_url = apply_filters(
			'wplng_mail_exclude_url_default',
			$mail_exclude_url
		);
	}
	?>

	<div class="wrap">

		<header id="wplng-option-page-header">
			<h1 class="wplng-option-page-title"><span class="dashicons dashicons-translation"></span> <?php esc_html_e( 'wpLingua - Emails translation', 'wplingua' ); ?></h1>
			<?php echo wplng_option_page_settings_menu(); ?>
		</header>

		<hr class="wp-header-end">
		
		<form method="post" action="options.php">
			<?php
			settings_fields( 'wplng_mail' );
			do_settings_sections( 'wplng_mail' );
			?>
			<table class="form-table wplng-form-table">
				<tr>
					<th scope="row"><span class="dashicons dashicons-email-alt"></span> <?php esc_html_e( 'Emails translation', 'wplingua' ); ?></th>
					<td>
						<p><strong><?php esc_html_e( 'Automatic Translation of Emails: ', 'wplingua' ); ?></strong></p>

						<hr>

						<fieldset>
							<input type="checkbox" id="wplng_mail_enable" name="wplng_mail_enable" value="1" <?php echo $checkbox_mail_enable_attr; ?>/>
							<label for="wplng_mail_enable">PRO - <?php esc_html_e( 'Translate emails automatically', 'wplingua' ); ?></label> 
							<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-feature-mail"></span>
						</fieldset>

						<div class="wplng-help-box wplng-spacing-bottom" id="wplng-hb-feature-mail">
							<p><?php esc_html_e( 'Translate emails automatically', 'wplingua' ); ?></p>
							<hr>
							<p><?php esc_html_e( 'You must have installed and activated the wpLingua PRO plugin for emails to be translated.', 'wplingua' ); ?> <a href="https://wplingua.com/download/#pro" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'wpLingua.com : Download PRO plugin', 'wplingua' ); ?></a></p>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row"><span class="dashicons dashicons-admin-users"></span> <?php esc_html_e( 'Exclude address', 'wplingua' ); ?></th>
					<td>
						<fieldset>
							<label for="wplng_mail_exclude_address"><strong><?php esc_html_e( 'Email addresses to exclude from translation: ', 'wplingua' ); ?></strong></label>

							<hr>

							<p><?php esc_html_e( 'Enter one regular expression per line. Emails sent to matching addresses will not be translated. Examples: ', 'wplingua' ); ?></p>
							
							<ul>
								<li><code>^admin@example\.com$</code> - <?php esc_html_e( 'Exclude mail address "admin@example.com"', 'wplingua' ); ?></li>
								<li><code>admin@example\.com</code> - <?php esc_html_e( 'Exclude mail address containing "admin@example.com"', 'wplingua' ); ?></li>
								<li><code>@example\.com$</code> - <?php esc_html_e( 'Exclude mail address ending with "@example.com"', 'wplingua' ); ?></li>
							</ul>
							<br>
							<textarea name="wplng_mail_exclude_address" id="wplng_mail_exclude_address" rows="6"><?php echo esc_textarea( $mail_exclude_address ); ?></textarea>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><span class="dashicons dashicons-admin-links"></span> <?php esc_html_e( 'Exclude URL', 'wplingua' ); ?></th>
					<td>
						<fieldset>
							<label for="wplng_mail_exclude_url"><strong><?php esc_html_e( 'URL to exclude from mail translation: ', 'wplingua' ); ?></strong></label>

							<hr>

							<p><?php esc_html_e( 'Enter one regular expression per line. Emails triggered from a matching page URL will not be translated. Examples: ', 'wplingua' ); ?></p>

							<ul>
								<li><code>^/contact/$</code> - <?php esc_html_e( 'Exclude URL "/contact/"', 'wplingua' ); ?></li>
								<li><code>^/contact/</code> - <?php esc_html_e( 'Exclude URL starting with "/contact/"', 'wplingua' ); ?></li>
								<li><code>/contact/$</code> - <?php esc_html_e( 'Exclude URL ending with "/contact/"', 'wplingua' ); ?></li>
								<li><code>/contact/</code> - <?php esc_html_e( 'Exclude URL containing "/contact/"', 'wplingua' ); ?></li>
							</ul>
							<br>
							<textarea name="wplng_mail_exclude_url" id="wplng_mail_exclude_url" rows="6"><?php echo esc_textarea( $mail_exclude_url ); ?></textarea>
						</fieldset>
					</td>
				</tr>
				<tr class="wplng-tr-submit">
					<th scope="row"><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Save', 'wplingua' ); ?></th>
					<td>
						<?php submit_button(); ?>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<?php
}
