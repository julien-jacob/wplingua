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

	delete_option( 'wplng_api_key_data' );

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

	<h1 class="wplng-option-page-title"><span class="dashicons dashicons-translation"></span> <?php esc_html_e( 'wpLingua - Register API key', 'wplingua' ); ?></h1>

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

						<p id="wplng-register-success-title">
							<span class="dashicons dashicons-email-alt"></span> <?php esc_html_e( 'API key created and sent by email', 'wplingua' ); ?>
						</p>

						<p>
							<strong><?php esc_html_e( 'The API key has been correctly created and sent to the following e-mail address: ', 'wplingua' ); ?><span id="wplng-register-success-mail"><?php esc_html_e( $mail ); ?><span></strong>
						</p>

						<hr>

						<p>
							<?php esc_html_e( 'Go to your mailbox and copy the API key sent to you (don\'t forget to check the spam section of your mailbox). Then paste it in the section below, and click "Set API key" to make your website multilingual.', 'wplingua' ); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row"><span class="dashicons dashicons-admin-network"></span> <?php esc_html_e( 'Set API Key', 'wplingua' ); ?></th>
					<td class="wplng-td-last">
						<?php wplng_register_part_api_key( $api_key ); ?>
					</td>
				</tr>

				<?php else : ?>

				<tr id="wplng-get-api-key">
					<th scope="row"><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Get API key', 'wplingua' ); ?></th>
					<td>
						<?php wplng_register_part_free_api_key(); ?>
					</td>
				</tr>

				<tr id="wplng-set-api-key">
					<th scope="row"><span class="dashicons dashicons-admin-network"></span> <?php esc_html_e( 'Set API Key', 'wplingua' ); ?></th>
					<td>
						<?php wplng_register_part_api_key( $api_key ); ?>
					</td>
				</tr>

				<tr>
					<th scope="row"><span class="dashicons dashicons-info"></span> <?php esc_html_e( 'Start with wpLingua', 'wplingua' ); ?></th>
					<td>
						<p>
							<strong><?php esc_html_e( 'You are just a few clicks away from making your website multilingual!', 'wplingua' ); ?></strong>
						</p>

						<hr>

						<p>
							<?php esc_html_e( 'For wpLingua to work, an API key is required so that your website has access to the automatic translation service. On this page you can enter your API key if you already have one, or create one for free.', 'wplingua' ); ?>
						</p>

						<hr>

						<p><span class="dashicons dashicons-star-filled"></span> <?php _e( 'An <strong>instantly multilingual website</strong> thanks to automatic text detection and translation. Easy to use, no knowledge required.', 'wplingua' ); ?></p>

						<hr>

						<p><span class="dashicons dashicons-admin-site"></span> <?php _e( '<strong>SEO friendly</strong> to allow search engines to index translated pages. Open up your website and your business to the whole world.', 'wplingua' ); ?></p>

						<hr>

						<p><span class="dashicons dashicons-edit"></span> <?php _e( 'All <strong>translations are editable</strong>. Discover the visual editor and edit translations simply by clicking on them. With a fully customizable language switcher.', 'wplingua' ); ?></p>

						<hr>

						<p><span class="dashicons dashicons-heart"></span> <?php _e( 'One <strong>free and unlimited</strong> language for personal blogs and non-commercial websites. And many more features!', 'wplingua' ); ?></p>

						<hr>

						<div class="wplng-fe-50">
							<a href="#wplng-get-api-key" class="button button-primary"><?php esc_html_e( 'Get API key', 'wplingua' ); ?></a>
						</div>

						<div class="wplng-fe-50">
							<a href="#wplng-set-api-key" class="button button-primary"><?php esc_html_e( 'Set API key', 'wplingua' ); ?></a>
						</div>
					</td>
				</tr>

				<tr>
					<th scope="row"><span class="dashicons dashicons-admin-settings"></span> <?php esc_html_e( 'Advanced API features', 'wplingua' ); ?></th>
					<td class="wplng-td-last">
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
		<p>
			<label for="wplng_api_key"><strong><?php esc_html_e( 'Set the wpLingua API key: ', 'wplingua' ); ?></strong></label>
		</p>

		<hr>

		<p>
			<?php esc_html_e( 'If you already have an API key, enter it below. If you\'ve forgotten your website\'s API key, visit', 'wplingua' ); ?> <a href="https://wplingua.com/recovery/" target="_blank"><?php esc_html_e( 'the API key retrieval page', 'wplingua' ); ?></a>. 
			<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-api-key"></span>
		</p>

		<div class="wplng-help-box" id="wplng-hb-api-key">
			<p><?php esc_html_e( 'A wpLingua API key consists of 42 characters (uppercase, lowercase and numbers). It is emailed to you when you request it using the form provided when you install the plugin. You must keep this key secret and only communicate it to wplingua.com services.', 'wplingua' ); ?></p>
		</div>

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
	<p for="wplng_api_key">
		<strong><?php esc_html_e( 'Register wpLingua API key: ', 'wplingua' ); ?></strong>
	</p>

	<hr>

	<p>
		<?php esc_html_e( 'Claim a wpLingua API key and make your website multilingual in a minute! For a personal blog or a non-commercial website, get one unlimited free language.', 'wplingua' ); ?>
	</p>

	<br>
	<hr>

	<p>
		<fieldset>
			<label for="wplng-email" class="wplng-fe-50">
				<strong><?php esc_html_e( 'Email address: ', 'wplingua' ); ?> </strong> 
				<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-register-email"></span>
			</label>
			<input type="email" name="wplng-email" id="wplng-email" class="wplng-fe-50" value="<?php echo esc_attr( $email ); ?>">
		</fieldset>
	</p>

	<div class="wplng-help-box" id="wplng-hb-register-email">
		<p><?php esc_html_e( 'The email address is detected and pre-filled. This is the administrative email address entered in the Settings ➔ General tab. You can use another email address used to receive your API key.', 'wplingua' ); ?></p>
	</div>

	<hr>

	<p>
		<fieldset>
			<label for="wplng-website-url" class="wplng-fe-50">
				<strong><?php esc_html_e( 'Website URL: ', 'wplingua' ); ?> </strong> 
				<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-register-url"></span>
			</label>
			<input type="url" name="wplng-website-url" id="wplng-website-url" class="wplng-fe-50" value="<?php echo esc_url( get_home_url() ); ?>">
		</fieldset>
	</p>

	<div class="wplng-help-box" id="wplng-hb-register-url">
		<p><?php esc_html_e( 'This is the URL of your website, it is detected and pre-filled automatically. Please check it and only enter the address of your production website (no test website or development environment).', 'wplingua' ); ?></p>
	</div>

	<hr>

	<p>
		<fieldset>
			<label for="wplng-language-website" class="wplng-fe-50">
				<strong><?php esc_html_e( 'Website language: ', 'wplingua' ); ?> </strong> 
				<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-register-language-website"></span>
			</label>
			<select name="wplng-language-website" id="wplng-language-website" class="wplng-fe-50"></select>
		</fieldset>
	</p>

	<div class="wplng-help-box" id="wplng-hb-register-language-website">
		<p><?php esc_html_e( 'This is the language of your website, defined by the associated API key. Make sure your website language is also correctly set in WordPress options (Settings ➔ General ➔ Website Language).', 'wplingua' ); ?></p>
	</div>

	<hr>

	<p>
		<fieldset>
			<label for="wplng-language-target" class="wplng-fe-50">
				<strong><?php esc_html_e( 'Target language: ', 'wplingua' ); ?> </strong> 
				<span title="<?php esc_attr_e( 'Click to expand', 'wplingua' ); ?>" wplng-help-box="#wplng-hb-register-language-target"></span>
			</label>
			<select name="wplng-language-target" id="wplng-language-target" class="wplng-fe-50"></select>
		</fieldset>
	</p>

	<div class="wplng-help-box" id="wplng-hb-register-language-target">
		<p><?php esc_html_e( 'This is the language you want to translate your website into. Be careful not to make a mistake because you will not be able to change the language afterwards.', 'wplingua' ); ?></p>
	</div>

	<hr>

	<p>
		<fieldset>
			<input type="checkbox" name="wplng-accept-eula" id="wplng-accept-eula">
			<label for="wplng-accept-eula">
				<strong><?php esc_html_e( 'I have read and accept the', 'wplingua' ); ?> <a href="https://wplingua.com/terms/" target="_blank"><?php esc_html_e( 'API terms of use', 'wplingua' ); ?></a> </strong>
			</label>
		</fieldset>
	</p>

	<hr>

	<fieldset style="display: none;">
		<p><?php esc_html_e( 'Website Locale: ', 'wplingua' ); ?> <span id="wplng-website-locale"><?php echo esc_html( $locale ); ?></span></p>
		<textarea name="wplng_request_free_key" id="wplng_request_free_key"></textarea>
	</fieldset>

	<br>

	<button id="wplng-get-free-api-submit" class="button button-primary">
		<?php esc_html_e( 'Get API key', 'wplingua' ); ?>
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
	<p>
		<strong><?php esc_html_e( 'Get more target languages and advanced features: ', 'wplingua' ); ?></strong>
	</p>

	<hr>

	<p>
		<?php esc_html_e( 'To translate your website into more languages and access advanced features, visit wpLingua website.', 'wplingua' ); ?>
	</p>

	<ul style="list-style: inside; padding: 0 0 0 15px;">
		<li><?php esc_html_e( 'Use wpLingua on commercial website', 'wplingua' ); ?></li>
		<li><?php esc_html_e( 'Allow search from all languages', 'wplingua' ); ?></li>
		<li><?php esc_html_e( 'Get more target languages', 'wplingua' ); ?></li>
	</ul>

	<br>

	<a class="button button-primary" href="https://wplingua.com/" target="_blank"><?php esc_html_e( 'Visit wpLingua website', 'wplingua' ); ?></a>
	<?php
}
