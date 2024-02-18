<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Print HTML Option page : wpLingua Dictionary
 *
 * @return void
 */
function wplng_option_page_dictionary() {

	$entries_json = wplng_dictionary_get_entries_json();

	?>
	<div class="wrap">

		<h1 class="wp-heading-inline"><span class="dashicons dashicons-translation"></span> <?php esc_html_e( 'wpLingua - Dictionary', 'wplingua' ); ?></h1>

		<hr class="wp-header-end">

		<form method="post" action="options.php">
			<?php
			settings_fields( 'wplng_dictionary' );
			do_settings_sections( 'wplng_dictionary' );
			?>

			<textarea name="wplng_dictionary_entries" id="wplng_dictionary_entries" style="display: none;" type="hidden"><?php echo esc_textarea( $entries_json ); ?></textarea>

			<table class="form-table wplng-form-table">
				<tr id="wplng-section-all-entries">
					<th scope="row"><?php esc_html_e( 'Dictionary entries', 'wplingua' ); ?></th>
					<td>

						<fieldset>

							<p><strong><?php esc_html_e( 'All dictionary entries: ', 'wplingua' ); ?></strong></p>

							<p><?php esc_html_e( 'The dictionary allows you to define translation rules that apply when generating machine translations. You can specify words or sets of words that should never be translated, or define how they should be translated for each language.', 'wplingua' ); ?></p>

							<hr>

							<label for="wplng_dictionary_entries"><strong><?php esc_html_e( 'All dictionary entries: ', 'wplingua' ); ?></strong></label>
							<br>
							<div id="wplng-dictionary-entries">
								<?php wplng_option_page_dictionary_entries_html(); ?>
							</div>

							<br>
							<hr>

							<a href="javascript:void(0);" class="button button-primary" id="wplng-new-rule-button">
								<?php esc_html_e( 'Add a new rule', 'wplingua' ); ?>
							</a>
						</fieldset>

						
					</td>
				</tr>

				<tr id="wplng-section-new-entry" style="display: none;">
					<th scope="row"><?php esc_html_e( 'Add an entry', 'wplingua' ); ?></th>
					<td>
						<div id="wplng-dictionary-entry-new">
							<?php wplng_option_page_dictionary_new_entry_html(); ?>
						</div>
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>

		</form>
	</div>
	<?php
}


function wplng_option_page_dictionary_entries_html() {

	$language_website       = wplng_get_language_website();
	$language_website_html  = '<img ';
	$language_website_html .= 'src="' . esc_url( $language_website['flag'] ) . '" ';
	$language_website_html .= 'alt="' . esc_attr( $language_website['name'] ) . '" ';
	$language_website_html .= 'class="wplng-flag">';

	$dictionary_entries = wplng_dictionary_get_entries();
	$html               = '';

	foreach ( $dictionary_entries as $rule_number => $entry ) {

		$html .= '<div class="wplng-dictionary-entry" wplng-rule="' . esc_attr( $rule_number ) . '">';

		$html .= '<div class="wplng-rule-header">';

		$html .= '<div class="wplng-rule-name">';
		$html .= esc_html__( 'Rule N°', 'wplingua' );
		$html .= esc_html( $rule_number + 1 );
		$html .= '</div>';

		$html .= '<div class="wplng-rule-action">';
		// $html .= '<a ';
		// $html .= 'href="javascript:void(0);" ';
		// $html .= 'class="wplng-rule-link-edit" ';
		// $html .= 'wplng-target-lang="fr">';
		// $html .= esc_html__( 'Edit rule', 'wplingua' );
		// $html .= '</a> ';
		$html .= '<a ';
		$html .= 'href="javascript:void(0);" ';
		$html .= 'class="wplng-rule-link-remove" ';
		$html .= 'wplng-rule="' . esc_attr( $rule_number ) . '">';
		$html .= esc_html__( 'Remove', 'wplingua' );
		$html .= '</a>';
		$html .= '</div>';

		$html .= '</div>'; // End .wplng-rule-header

		$html .= '<hr>';

		if ( isset( $entry['rules'] ) ) {

			$html .= '<strong>';
			$html .= $language_website_html;
			$html .= esc_html__( 'Always translate: ', 'wplingua' );
			$html .= '</strong>';
			$html .= esc_html( $entry['source'] );

			foreach ( $entry['rules'] as $language_id => $rule ) {

				$language = wplng_get_language_by_id( $language_id );

				$html .= '<hr>';
				$html .= '<strong>';
				$html .= '<img ';
				$html .= 'src="' . esc_url( $language['flag'] ) . '" ';
				$html .= 'alt="' . esc_attr( $language['name'] ) . '" ';
				$html .= 'class="wplng-flag">';
				$html .= esc_html( $language['name'] );
				$html .= esc_html__( ' - By: ', 'wplingua' );
				$html .= '</strong>';
				$html .= esc_html( $rule );
			}
		} else {
			$html .= '<strong>';
			$html .= esc_html__( 'Never translate: ', 'wplingua' );
			$html .= '</strong>';
			$html .= esc_html( $entry['source'] );
		}

		$html .= '</div>'; // End .wplng-dictionary-entry
	}

	echo $html;
}




function wplng_option_page_dictionary_new_entry_html() {

	$html = '';

	/**
	* Input : Source
	*/

	$html .= '<fieldset>';
	$html .= '<label for="wplng-new-source">';
	$html .= '<strong>';
	$html .= esc_html__( 'Source text: ', 'wplingua' );
	$html .= '</strong>';
	$html .= '</label>';
	$html .= '<br>';
	$html .= '<textarea ';
	$html .= 'name="wplng-new-source" ';
	$html .= 'id="wplng-new-source" ';
	$html .= 'class="wplng-adaptive-textarea">';
	$html .= '</textarea>';
	$html .= '</fieldset>';

	/**
	 * Input : Never translate
	 */

	$html .= '<fieldset>';
	$html .= '<input ';
	$html .= 'type="checkbox" ';
	$html .= 'id="wplng_new_never_translate" ';
	$html .= 'name="wplng_new_never_translate" ';
	$html .= '>';
	$html .= '<label for="wplng_new_never_translate"> ';
	$html .= esc_html__( 'Never translate', 'wplingua' );
	$html .= '</label>';
	$html .= '</fieldset>';

	$language_target = wplng_get_languages_target();

	$html .= '<div id="wplng-new-rules">';
	foreach ( $language_target as $key => $language ) {

		$name = 'wplng-new-always-translate-' . $language['id'];

		$html .= '<div class="wplng-new-rule" wplng-rule="' . esc_html( $language['id'] ) . '">';
		$html .= '<hr>';
		$html .= '<fieldset>';
		$html .= '<label for="' . esc_attr( $name ) . '">';
		$html .= '<strong>';
		$html .= '<img ';
		$html .= 'src="' . esc_url( $language['flag'] ) . '" ';
		$html .= 'alt="' . esc_attr( $language['name'] ) . '" ';
		$html .= 'class="wplng-flag">';
		$html .= esc_html( $language['name'] );
		$html .= esc_html__( ' - Always translate by: ', 'wplingua' );
		$html .= '</strong>';
		$html .= '</label>';

		$html .= '<br>';

		$html .= '<textarea ';
		$html .= 'name="' . esc_attr( $name ) . '" ';
		$html .= 'id="' . esc_attr( $name ) . '" ';
		$html .= 'class="wplng-adaptive-textarea">';

		$html .= '</textarea>';
		$html .= '</fieldset>';
		$html .= '</div>';

	}
	$html .= '</div>';

	$html .= '<div id="wplng-new-action-section">';

	$html .= '<a ';
	$html .= 'href="javascript:void(0);" ';
	$html .= 'id="wplng-new-cancel-button" ';
	$html .= 'class="button " ';
	$html .= '>';
	$html .= esc_html__( 'Cancel', 'wplingua' );
	$html .= '</a>';

	$html .= '<a ';
	$html .= 'href="javascript:void(0);" ';
	$html .= 'id="wplng-new-add-button" ';
	$html .= 'class="button button-primary" ';
	$html .= '>';
	$html .= esc_html__( 'Save new entry', 'wplingua' );
	$html .= '</a>';

	$html .= '</div>';

	echo $html;
}
