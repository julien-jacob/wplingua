<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_option_page_register() {

	$api_key          = wplng_get_api_key();
	$json_request_key = get_option( 'wplng_request_free_key' );

	delete_transient( 'wplng_api_key_data' );

	if ( get_option( 'wplng_api_key' ) !== $api_key
		&& empty( wplng_get_api_data() )
	) :
		update_option( 'wplng_api_key', '' );
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php _e( 'Invalid API key.', 'wplingua' ); ?></p>
		</div>
		<?php
	elseif ( ! empty( $json_request_key ) ) :

		delete_option( 'wplng_request_free_key' );
		$data_request_key = json_decode( $json_request_key, true );
		$response         = wplng_api_request_free_api_key( $data_request_key );

		if ( ! empty( $response['error'] ) ) {
			$message = '';
			if ( ! empty( $response['message'] ) ) {
				$message .= '<p>';
				$message .= __( 'Message :', 'wplingua' );
				$message .= ' ' . esc_html( $response['message'] );
				$message .= '</p>';
			}
			?>
			<div class="notice notice-error is-dismissible">
				<p><?php _e( 'An error occurred while creating the API key..', 'wplingua' ); ?></p>
				<?php echo $message; ?>
			</div>
			<?php
		} elseif ( ! empty( $response['register'] ) ) {
			$mail = '';
			if ( ! empty( $data_request_key['mail_address'] ) ) {
				$mail = ' ' . esc_html( $data_request_key['mail_address'] );
			}
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php _e( 'The API key has been correctly created and sent to the following e-mail address:', 'wplingua' ); echo $mail; ?> </p>
			</div>
			<?php
		}
	endif;
	?>
	

	<div class="wrap">
		
		<h1><?php _e( 'wpLingua : Register API key', 'wplingua' ); ?></h1>

		<br>

		<form method="post" action="options.php">
			<?php
			settings_fields( 'wplng_settings' );
			do_settings_sections( 'wplng_settings' );
			?>
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'API Key', 'wplingua' ); ?></th>
					<td>
						<?php wplng_register_part_api_key( $api_key ); ?>
					</td>
				</tr>


				<tr>
					<th scope="row"><?php _e( 'Get premium API Key', 'wplingua' ); ?></th>
					<td>
						<?php wplng_register_part_premium(); ?>
					</td>
				</tr>


				<tr>
					<th scope="row"><?php _e( 'Get free API key', 'wplingua' ); ?></th>
					<td>
						<?php wplng_register_part_free_api_key(); ?>
					</td>
				</tr>
				
			</table>
			
		
		</form>
	</div>
	<?php
}


function wplng_register_part_api_key( $api_key ) {
	?>
	<fieldset>
		<label for="wplng_api_key"><strong><?php _e( 'Set API key:', 'wplingua' ); ?></strong></label>
		<p><?php _e( 'If you already have an API key, enter it below. If you\'ve forgotten your site\'s API key, visit ', 'wplingua' ); ?><a href="#"><?php _e( 'the API key retrieval page', 'wplingua' ); ?></a>.</p>
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
	<br>
	<hr>
	<?php
}



function wplng_register_part_premium() {
	?>
	<p><strong><?php _e( 'Get more target languages and premium features', 'wplingua' ); ?></strong></p>
						
	<p><?php _e( 'To translate your site into more languages and access premium features, visit wpLingua plans.', 'wplingua' ); ?></p>
	<ul style="list-style: inside; padding: 0 0 0 15px;">
		<li><?php _e( 'Multilingual Woocommerce store', 'wplingua' ); ?></li>
		<li><?php _e( 'Automatic e-mail translation', 'wplingua' ); ?></li>
		<li><?php _e( 'Allow search from all languages', 'wplingua' ); ?></li>
		<li><?php _e( 'Get more target languages', 'wplingua' ); ?></li>
	</ul>
	<br>

	<a class="button button-primary" href="#"><?php _e( 'Visit wpLingua plans', 'wplingua' ); ?></a>

	<br>
	<br>
	<hr>
	<?php
}


function wplng_register_part_free_api_key() {

	$website_locale = substr( get_locale(), 0, 2 );

	?>
	<p for="wplng_api_key"><strong><?php _e( 'The API key:', 'wplingua' ); ?></strong></p>
	<p><?php _e( 'API key premium text...', 'wplingua' ); ?></p>
	<br>
	<br>

	<fieldset>
		<label for="wplng-website-url">
			<strong><?php _e( 'Website URL:', 'wplingua' ); ?> </strong>
		</label>
		<input type="url" name="wplng-website-url" id="wplng-website-url" value="<?php echo esc_url( get_site_url() ); ?>">
	</fieldset>
	<br>
	<fieldset>
		<label for="wplng-email">
			<strong><?php _e( 'Mail address:', 'wplingua' ); ?> </strong>
		</label>
		<input type="email" name="wplng-email" id="wplng-email" value="<?php echo esc_attr( get_bloginfo( 'admin_email' ) ); ?>">
	</fieldset>
	<br>
	<fieldset>
		<label for="wplng-language-website">
			<strong><?php _e( 'Website language:', 'wplingua' ); ?> </strong>
		</label>
		<select name="wplng-language-website" id="wplng-language-website"></select>
	</fieldset>
	<br>
	<fieldset>
		<label for="wplng-language-target">
			<strong><?php _e( 'Target language:', 'wplingua' ); ?> </strong>
		</label>
		<select name="wplng-language-target" id="wplng-language-target"></select>
	</fieldset>
	<br>
	<fieldset>
		<input type="checkbox" name="wplng-accept-eula" id="wplng-accept-eula">
		<label for="wplng-accept-eula">
			<strong><?php _e( 'I have read and accept the', 'wplingua' ); ?> <a href="#"><?php _e( 'general conditions of use', 'wplingua' ); ?></a> </strong>
		</label>
	</fieldset>
	<fieldset style="display: none;">
		<p><?php _e( 'Website Locale:', 'wplingua' ); ?> <span id="wplng-website-locale"><?php echo esc_html( $website_locale ); ?></span></p>
		<textarea name="wplng_request_free_key" id="wplng_request_free_key"></textarea>
	</fieldset>
	<br>


	<button id="wplng-get-free-api-submit" class="button button-primary">
		<?php _e( 'Get a free API key', 'wplingua' ); ?>
	</button>
	<?php
	// submit_button(
	// 	__( 'Get a free API key', 'wplingua' ),
	// 	'primary',
	// 	'submit',
	// 	false,
	// 	array(
	// 		'get-free-api' => '1'
	// 	)
	// );
	?>
	<br>
	<hr>
	<?php
}
