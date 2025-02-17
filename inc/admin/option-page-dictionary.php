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

	<h1 class="wplng-option-page-title"><span class="dashicons dashicons-translation"></span> <?php esc_html_e( 'wpLingua - Dictionary rules', 'wplingua' ); ?></h1>

	<div class="wrap">
		<hr class="wp-header-end">
		<form method="post" action="options.php">
			<?php
			settings_fields( 'wplng_dictionary' );
			do_settings_sections( 'wplng_dictionary' );
			?>

			<textarea name="wplng_dictionary_entries" id="wplng_dictionary_entries" style="display: none;" type="hidden"><?php echo esc_textarea( $entries_json ); ?></textarea>

			<table class="form-table wplng-form-table">
				<tr id="wplng-section-entries-all">
					<th scope="row"><span class="dashicons dashicons-book"></span> <?php esc_html_e( 'Dictionary entries', 'wplingua' ); ?></th>
					<td>
						<fieldset>

							<p><strong><?php esc_html_e( 'Translation rules by dictionary: ', 'wplingua' ); ?></strong></p>

							<p><?php esc_html_e( 'The dictionary allows you to define translation rules that apply when generating machine translations. You can specify words or sets of words that should never be translated, or define how they should be translated for each language.', 'wplingua' ); ?></p>

							<hr>

							<?php wplng_option_page_dictionary_entries_html(); ?>

							<a href="javascript:void(0);" class="button button-primary" id="wplng-new-rule-button">
								<?php esc_html_e( 'Add a dictionary entry', 'wplingua' ); ?>
							</a>

						</fieldset>
					</td>
				</tr>

				<tr id="wplng-section-entry-new" style="display: none;">
					<th scope="row"><span class="dashicons dashicons-welcome-add-page"></span> <?php esc_html_e( 'Add an entry', 'wplingua' ); ?></th>
					<td>
						<div id="wplng-dictionary-entry-new">
							<?php wplng_option_page_dictionary_new_entry_html(); ?>
						</div>
					</td>
				</tr>

				<tr id="wplng-section-entry-edit" style="display: none;">
					<th scope="row"><span class="dashicons dashicons-welcome-write-blog"></span> <?php esc_html_e( 'Edit the entry', 'wplingua' ); ?></th>
					<td>
						<div id="wplng-dictionary-entry-edit">
							<?php wplng_option_page_dictionary_edit_entry_html(); ?>
						</div>
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>

		</form>
	</div>
	<?php
}


/**
 * Print HTML subsection of Option page : wpLingua Dictionary - Entries
 *
 * @return void
 */
function wplng_option_page_dictionary_entries_html() {

	$dictionary_entries = wplng_dictionary_get_entries();

	if ( empty( $dictionary_entries ) ) {
		return '';
	}

	$language_website       = wplng_get_language_website();
	$language_website_html  = '<img';
	$language_website_html .= ' src="' . esc_url( $language_website['flag'] ) . '"';
	$language_website_html .= ' alt="' . esc_attr( $language_website['name'] ) . '"';
	$language_website_html .= ' class="wplng-flag"';
	$language_website_html .= '>';

	$html  = '<label><strong>';
	$html .= esc_html__( 'All dictionary entries: ', 'wplingua' );
	$html .= '</strong></label>';
	$html .= '<br>';
	$html .= '<div id="wplng-dictionary-entries">';

	foreach ( $dictionary_entries as $rule_number => $entry ) {

		$html .= '<div';
		$html .= ' class="wplng-dictionary-entry"';
		$html .= ' wplng-rule="' . esc_attr( $rule_number ) . '"';
		$html .= '>';

		$html .= '<div class="wplng-rule-header">';

		$html .= '<div class="wplng-rule-name">';
		$html .= esc_html__( 'Rule N°', 'wplingua' );
		$html .= esc_html( $rule_number + 1 );
		$html .= '</div>'; // ENd .wplng-rule-name

		$html .= '<div class="wplng-rule-action">';
		$html .= '<a';
		$html .= ' href="javascript:void(0);"';
		$html .= ' class="wplng-rule-link-edit"';
		$html .= ' wplng-rule="' . esc_attr( $rule_number ) . '"';
		$html .= '>';
		$html .= esc_html__( 'Edit', 'wplingua' );
		$html .= '</a> ';
		$html .= '<a';
		$html .= ' href="javascript:void(0);"';
		$html .= ' class="wplng-rule-link-remove"';
		$html .= ' wplng-rule="' . esc_attr( $rule_number ) . '"';
		$html .= '>';
		$html .= esc_html__( 'Remove', 'wplingua' );
		$html .= '</a>';
		$html .= '</div>'; // .wplng-rule-action

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
				$html .= '<img';
				$html .= ' src="' . esc_url( $language['flag'] ) . '"';
				$html .= ' alt="' . esc_attr( $language['name'] ) . '"';
				$html .= ' class="wplng-flag"';
				$html .= '>';
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

	$html .= '</div>'; // End #wplng-dictionary-entries

	echo $html;
}


/**
 * Print HTML subsection of Option page : wpLingua Dictionary - New entry
 *
 * @return void
 */
function wplng_option_page_dictionary_new_entry_html() {

	$html = '';

	/**
	* Input : Source
	*/

	$language_website       = wplng_get_language_website();
	$language_website_html  = '<img';
	$language_website_html .= ' src="' . esc_url( $language_website['flag'] ) . '"';
	$language_website_html .= ' alt="' . esc_attr( $language_website['name'] ) . '"';
	$language_website_html .= ' class="wplng-flag"';
	$language_website_html .= '>';

	$html .= '<fieldset>';
	$html .= '<label for="wplng-new-source">';
	$html .= '<strong>';
	$html .= $language_website_html;
	$html .= esc_html__( 'Source text: ', 'wplingua' );
	$html .= '</strong>';
	$html .= '</label>';
	$html .= '<br>';
	$html .= '<textarea';
	$html .= ' name="wplng-new-source"';
	$html .= ' id="wplng-new-source"';
	$html .= ' class="wplng-adaptive-textarea"';
	$html .= ' maxlength="256"';
	$html .= '>';
	$html .= '</textarea>';
	$html .= '</fieldset>';

	/**
	 * Input : Never translate
	 */

	$html .= '<fieldset>';
	$html .= '<input';
	$html .= ' type="checkbox"';
	$html .= ' id="wplng-new-never-translate"';
	$html .= ' name="wplng-new-never-translate"';
	$html .= '>';
	$html .= '<label for="wplng-new-never-translate"> ';
	$html .= esc_html__( 'Never translate', 'wplingua' );
	$html .= '</label>';
	$html .= '</fieldset>';

	$language_target = wplng_get_languages_target();

	$html .= '<div id="wplng-new-rules">';
	foreach ( $language_target as $language ) {

		$name = 'wplng-new-always-translate-' . $language['id'];

		$html .= '<div class="wplng-new-rule" wplng-rule="' . esc_html( $language['id'] ) . '">';
		$html .= '<hr>';
		$html .= '<fieldset>';
		$html .= '<label for="' . esc_attr( $name ) . '">';
		$html .= '<strong>';
		$html .= '<img';
		$html .= ' src="' . esc_url( $language['flag'] ) . '"';
		$html .= ' alt="' . esc_attr( $language['name'] ) . '"';
		$html .= ' class="wplng-flag"';
		$html .= '>';
		$html .= esc_html( $language['name'] );
		$html .= esc_html__( ' - Always translate by: ', 'wplingua' );
		$html .= '</strong>';
		$html .= '</label>';

		$html .= '<br>';

		$html .= '<textarea';
		$html .= ' name="' . esc_attr( $name ) . '"';
		$html .= ' id="' . esc_attr( $name ) . '"';
		$html .= ' class="wplng-adaptive-textarea"';
		$html .= ' maxlength="256"';
		$html .= '>';
		$html .= '</textarea>';

		$html .= '</fieldset>';
		$html .= '</div>';

	}
	$html .= '</div>';

	$html .= '<div id="wplng-new-action-section">';

	$html .= '<a';
	$html .= ' href="javascript:void(0);"';
	$html .= ' id="wplng-new-cancel-button"';
	$html .= ' class="button "';
	$html .= '>';
	$html .= esc_html__( 'Cancel', 'wplingua' );
	$html .= '</a>';

	$html .= '<a';
	$html .= ' href="javascript:void(0);"';
	$html .= ' id="wplng-new-add-button"';
	$html .= ' class="button button-primary"';
	$html .= '>';
	$html .= esc_html__( 'Save new entry', 'wplingua' );
	$html .= '</a>';

	$html .= '</div>';

	echo $html;
}


/**
 * Print HTML subsection of Option page : wpLingua Dictionary - Edit entry
 *
 * @return void
 */
function wplng_option_page_dictionary_edit_entry_html() {

	$html = '';

	/**
	* Input : Source
	*/

	$language_website       = wplng_get_language_website();
	$language_website_html  = '<img';
	$language_website_html .= ' src="' . esc_url( $language_website['flag'] ) . '"';
	$language_website_html .= ' alt="' . esc_attr( $language_website['name'] ) . '"';
	$language_website_html .= ' class="wplng-flag"';
	$language_website_html .= '>';

	$html .= '<fieldset>';
	$html .= '<label for="wplng-edit-source">';
	$html .= '<strong>';
	$html .= $language_website_html;
	$html .= esc_html__( 'Source text: ', 'wplingua' );
	$html .= '</strong>';
	$html .= '</label>';
	$html .= '<br>';
	$html .= '<textarea';
	$html .= ' name="wplng-edit-source"';
	$html .= ' id="wplng-edit-source"';
	$html .= ' class="wplng-adaptive-textarea"';
	$html .= ' maxlength="256"';
	$html .= '>';
	$html .= '</textarea>';
	$html .= '</fieldset>';

	/**
	 * Input : Never translate
	 */

	$html .= '<fieldset>';
	$html .= '<input';
	$html .= ' type="checkbox"';
	$html .= ' id="wplng-edit-never-translate"';
	$html .= ' name="wplng-edit-never-translate"';
	$html .= '>';
	$html .= '<label for="wplng-edit-never-translate"> ';
	$html .= esc_html__( 'Never translate', 'wplingua' );
	$html .= '</label>';
	$html .= '</fieldset>';

	$language_target = wplng_get_languages_target();

	$html .= '<div id="wplng-edit-rules">';
	foreach ( $language_target as $language ) {

		$name = 'wplng-edit-always-translate-' . $language['id'];

		$html .= '<div class="wplng-edit-rule" wplng-rule="' . esc_html( $language['id'] ) . '">';
		$html .= '<hr>';
		$html .= '<fieldset>';
		$html .= '<label for="' . esc_attr( $name ) . '">';
		$html .= '<strong>';
		$html .= '<img';
		$html .= ' src="' . esc_url( $language['flag'] ) . '"';
		$html .= ' alt="' . esc_attr( $language['name'] ) . '"';
		$html .= ' class="wplng-flag"';
		$html .= '>';
		$html .= esc_html( $language['name'] );
		$html .= esc_html__( ' - Always translate by: ', 'wplingua' );
		$html .= '</strong>';
		$html .= '</label>';

		$html .= '<br>';

		$html .= '<textarea';
		$html .= ' name="' . esc_attr( $name ) . '"';
		$html .= ' id="' . esc_attr( $name ) . '"';
		$html .= ' class="wplng-adaptive-textarea"';
		$html .= ' maxlength="256"';
		$html .= '>';
		$html .= '</textarea>';

		$html .= '</fieldset>';
		$html .= '</div>';

	}
	$html .= '</div>';

	$html .= '<div id="wplng-edit-action-section">';

	$html .= '<a';
	$html .= ' href="javascript:void(0);"';
	$html .= ' id="wplng-edit-cancel-button"';
	$html .= ' class="button "';
	$html .= '>';
	$html .= esc_html__( 'Cancel', 'wplingua' );
	$html .= '</a>';

	$html .= '<a';
	$html .= ' href="javascript:void(0);"';
	$html .= ' id="wplng-edit-save-button"';
	$html .= ' class="button button-primary"';
	$html .= '>';
	$html .= esc_html__( 'Save edited entry', 'wplingua' );
	$html .= '</a>';

	$html .= '</div>';

	echo $html;
}
