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

	$entries_json                  = wplng_dictionary_get_entries_json();
	$wplng_dictionary_updated_data = get_transient( 'wplng_dictionary_updated_data' );

	delete_transient( 'wplng_dictionary_updated_data' );

	?>

	<h1 class="wplng-option-page-title"><span class="dashicons dashicons-translation"></span> <?php esc_html_e( 'wpLingua - Dictionary rules', 'wplingua' ); ?></h1>

	<div class="wrap">
		<hr class="wp-header-end">
		<form method="post" action="options.php">
			<?php
			settings_fields( 'wplng_dictionary' );
			do_settings_sections( 'wplng_dictionary' );
			?>
			<table class="form-table wplng-form-table">

				<?php

				$style_section_entries_all = '';

				if ( false !== $wplng_dictionary_updated_data ) {
					$style_section_entries_all = 'display: none;';
					wplng_option_page_dictionary_update_translationss_html(
						$wplng_dictionary_updated_data
					);
				}
				?>

				<tr class="wplng-beta-hidden" style="display: none;">
					<th scope="row"><span class="dashicons dashicons-printer"></span> <?php esc_html_e( 'Debug', 'wplingua' ); ?></th>
					<td>
						<fieldset>
							<textarea name="wplng_dictionary_entries" id="wplng_dictionary_entries"><?php echo esc_textarea( $entries_json ); ?></textarea>
						</fieldset>
					</td>
				</tr>
					
				<tr id="wplng-section-entries-all" style="<?php echo esc_attr( $style_section_entries_all ); ?>">
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
 * Print HTML subsection of Option page : wpLingua Dictionary - Update translations
 *
 * Displays the list of translations impacted by the latest dictionary rule
 * changes, along with the controls used to launch, ignore, or track the
 * progress of the AJAX update queue.
 *
 * @param array $translations_to_update List of items with 'post_id', 'source' and 'impacted_languages' ('all' or an array of language IDs).
 * @return void
 */
function wplng_option_page_dictionary_update_translationss_html( $translations_to_update ) {

	/**
	 * HTML Buttons
	 */

	$html_buttons = '<div class="wplng-flex-row wplng-dictionary-update-subsection-launch">';

	$html_buttons .= '<div class="wplng-flex-item">';
	$html_buttons .= '<a';
	$html_buttons .= ' href="javascript:void(0);"';
	$html_buttons .= ' class="button wplng-dictionary-update-button-ignore"';
	$html_buttons .= '>';
	$html_buttons .= esc_html__( 'Ignore', 'wplingua' );
	$html_buttons .= '</a>';
	$html_buttons .= '</div>'; // End .wplng-flex-item

	$html_buttons .= '<div class="wplng-flex-item">';
	$html_buttons .= '<a';
	$html_buttons .= ' href="javascript:void(0);"';
	$html_buttons .= ' class="button button-primary wplng-dictionary-update-button-start"';
	$html_buttons .= '>';
	$html_buttons .= esc_html__( 'Update translations', 'wplingua' );
	$html_buttons .= '</a>';
	$html_buttons .= '</div>'; // End .wplng-flex-item

	$html_buttons .= '</div>'; // End .wplng-flex-row

	/**
	 * HTML Section
	 */

	$html = '<tr id="wplng-section-dictionary-update-translations">';

	$html .= '<th scope="row">';
	$html .= '<span class="dashicons dashicons-update"></span> ';
	$html .= esc_html( 'Update translations', 'wplingua' );
	$html .= '</th>'; // End .row

	$html .= '<td class="wplng-flex-container">';
	$html .= '<fieldset>';
	$html .= '<label>';
	$html .= '<strong>' . esc_html( 'Translations Affected by Dictionary Changes', 'wplingua' ) . '</strong>';
	$html .= '</label>';
	$html .= '<p>';
	$html .= esc_html( 'Some translations have been identified as needing to be updated following the latest changes to the dictionary rules.', 'wplingua' );
	$html .= '</p>';

	$html .= '<hr>';

	/**
	 * Subsection info before process
	 */

	$html .= '<div class="wplng-dictionary-update-subsection-info">';
	$html .= '<p>';
	$html .= esc_html(
		sprintf(
			__( 'Number of translations to update: %d', 'wplingua' ),
			count( $translations_to_update )
		)
	);
	$html .= '</p>';
	$html .= '</div>'; // End .wplng-dictionary-update-subsection-info

	/**
	 * Subsection info in process
	 */

	$progression_info  = '<span class="wplng-count-processed">0</span>';
	$progression_info .= ' / ' . count( $translations_to_update );
	// $progression_info .= ' - <span class="wplng-count-percent">0 %</span>';

	$html .= '<div class="wplng-dictionary-update-subsection-info-progress" style="display: none;">';
	$html .= '<p>';
	$html .= esc_html__( 'Translations are currently being updated.', 'wplingua' );
	$html .= '</p>';
	$html .= '<p>';
	$html .= sprintf(
		__( 'Number of translations updated: %1$s', 'wplingua' ),
		$progression_info
	);
	$html .= '</p>';
	$html .= '</div>'; // End .wplng-dictionary-update-subsection-info

	/**
	 * Subsection info before process
	 */

	$html .= '<div class="wplng-dictionary-update-subsection-info-end" style="display: none;">';
	$html .= '<p>';
	$html .= esc_html__( 'End of translations update.', 'wplingua' );
	$html .= '</p>';
	$html .= '<a';
	$html .= ' href="javascript:void(0);"';
	$html .= ' class="button button-primary wplng-dictionary-update-button-end"';
	$html .= '>';
	$html .= esc_html__( 'Back to the dictionary', 'wplingua' );
	$html .= '</a>';
	$html .= '</div>'; // End .wplng-dictionary-update-subsection-info

	$html .= $html_buttons;

	$html .= '<hr>';
	$html .= '<p>';
	$html .= '<strong>' . esc_html( 'Translations to update: ', 'wplingua' ) . '</strong>';
	$html .= '</p>';

	foreach ( $translations_to_update as $key => $translation_to_update ) {

		if ( ! isset( $translation_to_update['post_id'] )
			|| ! isset( $translation_to_update['source'] )
			|| ! isset( $translation_to_update['impacted_languages'] )
			|| ( $translation_to_update['impacted_languages'] !== 'all'
				&& ! is_array( $translation_to_update['impacted_languages'] )
			)
		) {
			continue;
		}

		// Write 'all' as a plain string so jQuery's .data() returns the string 'all'
		// (jQuery only auto-parses values starting with '{' or '[').
		// Arrays are JSON-encoded so jQuery parses them back into JS arrays.
		$impacted_languages_attr = 'all';
		if ( $translation_to_update['impacted_languages'] !== 'all' ) {
			$impacted_languages_attr = wp_json_encode( $translation_to_update['impacted_languages'] );
		}

		$html .= '<div';
		$html .= ' class="wplng-dictionary-text-to-update-entry"';
		$html .= ' data-post-id="' . esc_attr( $translation_to_update['post_id'] ) . '"';
		$html .= ' data-impacted-languages="' . esc_attr( $impacted_languages_attr ) . '"';
		$html .= '>';

		// $html .= '<span class="wplng-dictionary-update-state dashicons dashicons-yes-alt"></span>';
		$html .= '<span class="wplng-dictionary-update-state dashicons"></span>';

		$html .= '<strong>';
		$html .= ' | ' . esc_html__( 'ID: ', 'wplingua' ) . esc_html( $translation_to_update['post_id'] );
		$html .= '</strong>';

		$html .= '<hr>';

		// Truncate source at the last word boundary before 200 characters.
		$source_display = $translation_to_update['source'];
		if ( mb_strlen( $source_display ) > 200 ) {
			$source_display = mb_substr( $source_display, 0, 200 );
			$last_space     = mb_strrpos( $source_display, ' ' );
			if ( false !== $last_space ) {
				$source_display = mb_substr( $source_display, 0, $last_space );
			}
			$source_display .= '…';
		}
		$html .= '<p class="wplng-dictionary-text-to-update">';
		$html .= esc_html( $source_display );
		$html .= '</p>';

		$html .= '</div>'; // End .wplng-dictionary-text-to-update-entry
	}

	$html .= '</fieldset>';
	$html .= '</td>';
	$html .= '</tr>';

	echo $html;
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
