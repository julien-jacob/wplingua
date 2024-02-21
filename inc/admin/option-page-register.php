<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Print HTML Option page : wpLingua Register
 *
 * @return void
 */
function wplng_option_page_register() {

	if ( is_multisite() ) {
		?>
		<div class="wplng-notice notice notice-info">
			<p><?php esc_html_e( 'wpLingua is not compatible with Multisite.', 'wplingua' ); ?></p>
		</div>
		<?php
		return;
	}

	$mail             = '';
	$api_key          = wplng_get_api_key();
	$json_request_key = get_option( 'wplng_request_free_key' );
	$error_validation = get_transient( 'wplng_api_key_error' );
	$error_validation = sanitize_text_field( $error_validation );

	delete_transient( 'wplng_api_key_data' );

	if ( ! empty( $error_validation )
		&& is_string( $error_validation )
	) :

		delete_transient( 'wplng_api_key_error' );

		$message  = '';
		$message .= __( 'Message: ', 'wplingua' );
		$message .= $error_validation;

		?>
		<div class="wplng-notice notice notice-error is-dismissible">
			<p>
				<strong><?php esc_html_e( 'An error occurred with API key.', 'wplingua' ); ?></strong>
				<br>
				<?php echo esc_html( $message ); ?>
			</p>
		</div>
		<?php

	elseif ( get_option( 'wplng_api_key' ) !== $api_key
		&& empty( wplng_get_api_data() )
	) :

		update_option( 'wplng_api_key', '' );

		if ( ! empty( get_option( 'wplng_api_key' ) ) ) :
			?>
			<div class="wplng-notice notice notice-error is-dismissible">
				<p><strong><?php esc_html_e( 'Invalid API key.', 'wplingua' ); ?></strong></p>
			</div>
			<?php
		endif;

	elseif ( ! empty( $json_request_key ) ) :

		delete_option( 'wplng_request_free_key' );

		$data_request_key = json_decode( $json_request_key, true );
		$response         = wplng_api_call_request_api_key( $data_request_key );

		if ( ! empty( $response['error'] ) ) {

			$message = '';

			if ( ! empty( $response['message'] ) ) {
				$message .= __( 'Message: ', 'wplingua' );
				$message .= $response['message'];
			}

			?>
			<div class="wplng-notice notice notice-error is-dismissible">
				<p>
					<strong><?php esc_html_e( 'An error occurred while creating the API key.', 'wplingua' ); ?></strong>
					<br>
					<?php echo esc_html( $message ); ?>
				</p>
			</div>
			<?php
		} elseif ( ! empty( $response['register'] ) ) {
			if ( ! empty( $data_request_key['mail_address'] )
				&& is_email( $data_request_key['mail_address'] )
			) {
				$mail = sanitize_email( $data_request_key['mail_address'] );
			}
		}
	endif;
	?>

	<h1 class="wplin-option-page-title"><span class="dashicons dashicons-translation"></span> <?php esc_html_e( 'wpLingua - Register API key', 'wplingua' ); ?></h1>

	<div class="wrap">
		<hr class="wp-header-end">
		<form method="post" action="options.php">
			<?php
			settings_fields( 'wplng_settings' );
			do_settings_sections( 'wplng_settings' );
			?>
			<table class="form-table wplng-form-table">

				<?php
				/**
				 * After register a new API key without error
				 */

				if ( ! empty( $mail ) ) :
					?>

				<tr>
					<th scope="row"><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'API key created', 'wplingua' ); ?></th>
					<td id="wplng-register-success-message">
						<p id="wplng-register-success-title"><span class="dashicons dashicons-email-alt"></span> <?php esc_html_e( 'API key created and sent by email', 'wplingua' ); ?></p>

						<p><strong><?php esc_html_e( 'The API key has been correctly created and sent to the following e-mail address: ', 'wplingua' ); ?><span id="wplng-register-success-mail"><?php esc_html_e( $mail ); ?><span></strong></p>

						<hr>

						<p><?php esc_html_e( 'Go to your mailbox and copy the API key sent to you (don\'t forget to check the spam section of your mailbox). Then paste it in the section below, and click "Set API key" to make your site multilingual.', 'wplingua' ); ?></p>

					</td>
				</tr>

				<tr>
					<th scope="row"><span class="dashicons dashicons-admin-network"></span> <?php esc_html_e( 'Set API Key', 'wplingua' ); ?></th>
					<td>
						<?php wplng_register_part_api_key( $api_key ); ?>
					</td>
				</tr>

				<?php else : ?>

				<tr>
					<th scope="row"><span class="dashicons dashicons-info"></span> <?php esc_html_e( 'Start with wpLingua', 'wplingua' ); ?></th>
					<td>
						<p><strong><?php esc_html_e( 'You are just a few clicks away from making your site multilingual!', 'wplingua' ); ?></strong></p>

						<p><?php esc_html_e( 'For wpLingua to work, an API key is required so that your website has access to the automatic translation service. On this page you can enter your API key if you already have one, or create one for free.', 'wplingua' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><span class="dashicons dashicons-admin-network"></span> <?php esc_html_e( 'Set API Key', 'wplingua' ); ?></th>
					<td>
						<?php wplng_register_part_api_key( $api_key ); ?>
					</td>
				</tr>

				<tr>
					<th scope="row"><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Get free API key', 'wplingua' ); ?></th>
					<td>
						<?php wplng_register_part_free_api_key(); ?>
					</td>
				</tr>

				<tr>
					<th scope="row"><span class="dashicons dashicons-admin-settings"></span> <?php esc_html_e( 'Advanced API features', 'wplingua' ); ?></th>
					<td>
						<?php wplng_register_part_premium(); ?>
					</td>
				</tr>

				<?php endif; ?>

			</table>
		</form>
	</div>
	<?php
}


/**
 * Print HTML subsection of Option page : wpLingua Register - API Key
 *
 * @param string $api_key
 * @return void
 */
function wplng_register_part_api_key( $api_key ) {
	?>
	<fieldset>
		<label for="wplng_api_key"><strong><?php esc_html_e( 'Set the wpLingua API key:', 'wplingua' ); ?></strong></label>
		<p><?php esc_html_e( 'If you already have an API key, enter it below. If you\'ve forgotten your site\'s API key, visit', 'wplingua' ); ?> <a href="https://wplingua.com/recovery/" target="_blank"><?php esc_html_e( 'the API key retrieval page', 'wplingua' ); ?></a>.</p>
		<br>
		<input type="text" name="wplng_api_key" id="wplng_api_key" value="<?php echo esc_attr( $api_key ); ?>"></input>

		<?php
		submit_button(
			__( 'Set API key', 'wplingua' ),
			'primary',
			'submit',
			false
		);
		?>
	</fieldset>
	<?php
}


/**
 * Print HTML subsection of Option page : wpLingua Register - Register free API Key
 *
 * @return void
 */
function wplng_register_part_free_api_key() {

	$email  = sanitize_email( get_bloginfo( 'admin_email' ) );
	$locale = strtolower( substr( get_locale(), 0, 2 ) );

	if ( ! wplng_is_valid_language_id( $locale ) ) {
		$locale = 'en';
	}

	?>
	<p for="wplng_api_key"><strong><?php esc_html_e( 'Register free wpLingua API key:', 'wplingua' ); ?></strong></p>
	<p><?php esc_html_e( 'Get a free wpLingua API key and make your website bilingual in a minute!', 'wplingua' ); ?></p>
	<hr>
	<br>
	<fieldset>
		<label for="wplng-website-url" class="wplng-fe-50">
			<strong><?php esc_html_e( 'Website URL:', 'wplingua' ); ?> </strong>
		</label>
		<input type="url" name="wplng-website-url" id="wplng-website-url" class="wplng-fe-50" value="<?php echo esc_url( get_site_url() ); ?>">
	</fieldset>
	<br>
	<fieldset>
		<label for="wplng-email" class="wplng-fe-50">
			<strong><?php esc_html_e( 'Mail address:', 'wplingua' ); ?> </strong>
		</label>
		<input type="email" name="wplng-email" id="wplng-email" class="wplng-fe-50" value="<?php echo esc_attr( $email ); ?>">
	</fieldset>
	<br>
	<fieldset>
		<label for="wplng-language-website" class="wplng-fe-50">
			<strong><?php esc_html_e( 'Website language:', 'wplingua' ); ?> </strong>
		</label>
		<select name="wplng-language-website" id="wplng-language-website" class="wplng-fe-50"></select>
	</fieldset>
	<br>
	<fieldset>
		<label for="wplng-language-target" class="wplng-fe-50">
			<strong><?php esc_html_e( 'Target language:', 'wplingua' ); ?> </strong>
		</label>
		<select name="wplng-language-target" id="wplng-language-target" class="wplng-fe-50"></select>
	</fieldset>
	<br>
	<fieldset>
		<input type="checkbox" name="wplng-accept-eula" id="wplng-accept-eula">
		<label for="wplng-accept-eula">
			<strong><?php esc_html_e( 'I have read and accept the', 'wplingua' ); ?> <a href="https://wplingua.com/terms/" target="_blank"><?php esc_html_e( 'API terms of use', 'wplingua' ); ?></a> </strong>
		</label>
	</fieldset>
	<fieldset style="display: none;">
		<p><?php esc_html_e( 'Website Locale:', 'wplingua' ); ?> <span id="wplng-website-locale"><?php echo esc_html( $locale ); ?></span></p>
		<textarea name="wplng_request_free_key" id="wplng_request_free_key"></textarea>
	</fieldset>
	<button id="wplng-get-free-api-submit" class="button button-primary">
		<?php esc_html_e( 'Get a free API key', 'wplingua' ); ?>
	</button>
	<?php
}


/**
 * Print HTML subsection of Option page : wpLingua Register - Premium informations
 *
 * @return void
 */
function wplng_register_part_premium() {
	?>
	<p><strong><?php esc_html_e( 'Get more target languages and advanced features:', 'wplingua' ); ?></strong></p>		
	<p><?php esc_html_e( 'To translate your site into more languages and access advanced features, visit wpLingua website.', 'wplingua' ); ?></p>
	<ul style="list-style: inside; padding: 0 0 0 15px;">
		<li><?php esc_html_e( 'Multilingual WooCommerce store', 'wplingua' ); ?></li>
		<li><?php esc_html_e( 'Allow search from all languages', 'wplingua' ); ?></li>
		<li><?php esc_html_e( 'Get more target languages', 'wplingua' ); ?></li>
	</ul>
	<br>
	<a class="button button-primary" href="https://wplingua.com/" target="_blank"><?php esc_html_e( 'Visit wpLingua website', 'wplingua' ); ?></a>
	<?php
}
