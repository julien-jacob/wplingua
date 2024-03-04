<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Print HTML Option page : wpLingua Help & Support
 *
 * @return void
 */
function wplng_option_page_help() {

	?>

	<h1 class="wplin-option-page-title"><span class="dashicons dashicons-translation"></span> <?php esc_html_e( 'wpLingua - Help & Support', 'wplingua' ); ?></h1>

	<div class="wrap">
		<hr class="wp-header-end">

		<table class="form-table wplng-form-table">
		<tr>
				<th scope="row"><span class="dashicons dashicons-editor-ul"></span> <?php esc_html_e( 'Summary', 'wplingua' ); ?></th>
				<td>
					<fieldset>

						<p><strong><?php esc_html_e( 'Summary of the documentation: ', 'wplingua' ); ?></strong></p>

						<ul>
							<li><a href="#wplng-help-how">Lorem ipsum dolor sit amet</a></li>
							<li><a href="#wplng-help-how">Lorem ipsum dolor sit amet</a></li>
							<li><a href="#wplng-help-how">Lorem ipsum dolor sit amet</a></li>
						</ul>

					</fieldset>
				</td>
			</tr>

			<tr id="wplng-help-how">
				<th scope="row"><span class="dashicons dashicons-editor-help"></span> <?php esc_html_e( 'How wpLingua works', 'wplingua' ); ?></th>
				<td>
					<fieldset>

						<p><strong><?php esc_html_e( 'Automatic texts finding: ', 'wplingua' ); ?></strong></p>
						
						<p><?php esc_html_e( 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Magni ullam dolorem deserunt neque vero aut veniam consequuntur ab. Labore voluptatem esse delectus deserunt consequatur autem itaque suscipit aperiam porro unde!', 'wplingua' ); ?></p>

					</fieldset>
					<hr>
					<fieldset>

						<p><strong><?php esc_html_e( 'Automatic texts translation: ', 'wplingua' ); ?></strong></p>

						<p><?php esc_html_e( 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Magni ullam dolorem deserunt neque vero aut veniam consequuntur ab. Labore voluptatem esse delectus deserunt consequatur autem itaque suscipit aperiam porro unde!', 'wplingua' ); ?></p>

					</fieldset>
				</td>
			</tr>

			<tr>
				<th scope="row"><span class="dashicons dashicons-edit"></span> <?php esc_html_e( 'Edit translations', 'wplingua' ); ?></th>
				<td>
					<fieldset>

						<p><strong><?php esc_html_e( 'Lorem ipsum dolor sit amet: ', 'wplingua' ); ?></strong></p>

						<p><?php esc_html_e( 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Magni ullam dolorem deserunt neque vero aut veniam consequuntur ab. Labore voluptatem esse delectus deserunt consequatur autem itaque suscipit aperiam porro unde!', 'wplingua' ); ?></p>

					</fieldset>
					<hr>
					<fieldset>

						<p><strong><?php esc_html_e( 'Visual editor: ', 'wplingua' ); ?></strong></p>

						<p><?php esc_html_e( 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Magni ullam dolorem deserunt neque vero aut veniam consequuntur ab. Labore voluptatem esse delectus deserunt consequatur autem itaque suscipit aperiam porro unde!', 'wplingua' ); ?></p>

					</fieldset>
					<hr>
					<fieldset>

						<p><strong><?php esc_html_e( 'All translation on page: ', 'wplingua' ); ?></strong></p>

						<p><?php esc_html_e( 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Magni ullam dolorem deserunt neque vero aut veniam consequuntur ab. Labore voluptatem esse delectus deserunt consequatur autem itaque suscipit aperiam porro unde!', 'wplingua' ); ?></p>

					</fieldset>
					<hr>
					<fieldset>

						<p><strong><?php esc_html_e( 'All translation on website: ', 'wplingua' ); ?></strong></p>

						<p><?php esc_html_e( 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Magni ullam dolorem deserunt neque vero aut veniam consequuntur ab. Labore voluptatem esse delectus deserunt consequatur autem itaque suscipit aperiam porro unde!', 'wplingua' ); ?></p>

					</fieldset>
				</td>
			</tr>

			<tr>
				<th scope="row"><span class="dashicons dashicons-book"></span> <?php esc_html_e( 'Dictionary', 'wplingua' ); ?></th>
				<td>
					<fieldset>

						<p><strong><?php esc_html_e( 'Translation rules by dictionary: ', 'wplingua' ); ?></strong></p>

						<p><?php esc_html_e( 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Magni ullam dolorem deserunt neque vero aut veniam consequuntur ab. Labore voluptatem esse delectus deserunt consequatur autem itaque suscipit aperiam porro unde!', 'wplingua' ); ?></p>

					</fieldset>
				</td>
			</tr>

			<tr>
				<th scope="row"><span class="dashicons dashicons-no"></span> <?php esc_html_e( 'Exclusion', 'wplingua' ); ?></th>
				<td>
					<fieldset>

						<p><strong><?php esc_html_e( 'Exclude HTML element by selector: ', 'wplingua' ); ?></strong></p>

						<p><?php esc_html_e( 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Magni ullam dolorem deserunt neque vero aut veniam consequuntur ab. Labore voluptatem esse delectus deserunt consequatur autem itaque suscipit aperiam porro unde!', 'wplingua' ); ?></p>

					</fieldset>
					<hr>
					<fieldset>

						<p><strong><?php esc_html_e( 'Exclude pages by URL: ', 'wplingua' ); ?></strong></p>

						<p><?php esc_html_e( 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Magni ullam dolorem deserunt neque vero aut veniam consequuntur ab. Labore voluptatem esse delectus deserunt consequatur autem itaque suscipit aperiam porro unde!', 'wplingua' ); ?></p>

					</fieldset>
				</td>
			</tr>

			<tr>
				<th scope="row"><span class="dashicons dashicons-sos"></span> <?php esc_html_e( 'Support', 'wplingua' ); ?></th>
				<td>
					<fieldset>

						<p><strong><?php esc_html_e( 'Contact wpLingua team: ', 'wplingua' ); ?></strong></p>

						<p><?php esc_html_e( 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Magni ullam dolorem deserunt neque vero aut veniam consequuntur ab. Labore voluptatem esse delectus deserunt consequatur autem itaque suscipit aperiam porro unde!', 'wplingua' ); ?></p>

					</fieldset>
					<hr>
					<fieldset>

						<p><strong><?php esc_html_e( 'wpLingua plugin forum: ', 'wplingua' ); ?></strong></p>

						<p><?php esc_html_e( 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Magni ullam dolorem deserunt neque vero aut veniam consequuntur ab. Labore voluptatem esse delectus deserunt consequatur autem itaque suscipit aperiam porro unde!', 'wplingua' ); ?></p>

					</fieldset>
					<hr>
					<fieldset>

						<p><strong><?php esc_html_e( 'Open an issue on GitHub: ', 'wplingua' ); ?></strong></p>

						<p><?php esc_html_e( 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Magni ullam dolorem deserunt neque vero aut veniam consequuntur ab. Labore voluptatem esse delectus deserunt consequatur autem itaque suscipit aperiam porro unde!', 'wplingua' ); ?></p>

					</fieldset>
				</td>
			</tr>

		</table>

	</div>
	<?php
}
