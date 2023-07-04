<?php

// If this file is called directly, abort.

use function Code_Snippets\Settings\update_setting;

if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_option_page_register() {
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
						<fieldset>
							<label for="wplng_api_key"><strong><?php _e( 'Set API key:', 'wplingua' ); ?></strong></label>
							<p><?php _e( 'If you already have an API key, enter it below. If you\'ve forgotten your site\'s API key, visit ', 'wplingua' ); ?><a href="#"><?php _e( 'the API key retrieval page', 'wplingua' ); ?></a>.</p>
							<br>
							<input type="text" name="wplng_api_key" id="wplng_api_key" value="<?php echo esc_attr( get_option( 'wplng_api_key' ) ); ?>"></input>

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
					</td>
				</tr>


				<tr>
					<th scope="row"><?php _e( 'Get premium API Key', 'wplingua' ); ?></th>
					<td>
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
					</td>
				</tr>


				<tr>
					<th scope="row"><?php _e( 'Get free API key', 'wplingua' ); ?></th>
					<td>
						<p for="wplng_api_key"><strong><?php _e( 'The API key:', 'wplingua' ); ?></strong></p>
						<p><?php _e( 'API key premium text...', 'wplingua' ); ?></p>
						<br>

						<a class="button button-primary" href="#"><?php _e( 'Get a free API key', 'wplingua' ); ?></a>

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


function km_hook_into_options_page_after_save( $old_value, $new_value ) {

	if ( $old_value !== $new_value ) {

		delete_transient( 'wplng_api_key_data' );

		if ( strlen( $new_value ) != 32 ) {
			// set_transient( 'wplng_notice_api_key_bad_format', true, 5 );
			return;
		}

		$api_key_data_json = wplng_validate_api_key( $new_value );

		if ( empty( $api_key_data_json ) ) {
			return;
		}

		$api_key_data = json_decode( $api_key_data_json, true );

		if ( empty( $api_key_data ) ) {
			return;
		}

		if ( ! empty( $api_key_data['error'] ) ) {
			// TODO : Gérer
			return;
		}

		set_transient( 'wplng_api_key_data', $api_key_data_json, 5 );
		// update_setting('wplng_api', 'wplng_api', $api_key_data_json );
	}

}
// add_action( 'update_option_wplng_api_key', 'km_hook_into_options_page_after_save', 10, 2 );

function sample_admin_notice__success() {
	?>
	<div class="notice notice-success is-dismissible">
		<p><?php _e( 'Done!', 'sample-text-domain' ); ?></p>
	</div>
	<?php
}

