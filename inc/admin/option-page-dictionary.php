<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action(
	'update_option_wplng_dictionary_entries',
	'wplng_on_dictionary_entries_updated',
	10,
	2
);

/**
 * Fired when the 'wplng_dictionary_entries' option is saved.
 *
 * Compares the old and new dictionary entries to determine which source texts
 * and target languages are affected by the change. Builds $update_todo, a list
 * of items whose cached translations need to be regenerated.
 *
 * Each item in $update_todo has the shape:
 *   [ 'source' => string, 'impacted_languages' => 'all' | string[] ]
 *
 * 'impacted_languages' is the string 'all' when the entry has no per-language
 * rules (i.e., it is a "never translate" rule that applies to every language).
 *
 * @param string $old_value The previous JSON value of the option.
 * @param string $new_value The new JSON value of the option.
 */
function wplng_on_dictionary_entries_updated( $old_value, $new_value ) {

	// Entries are stored as a JSON string in the WordPress options table.
	// Decode them; fall back to an empty array if the value is absent or invalid.
	$old_entries = json_decode( $old_value, true );
	$new_entries = json_decode( $new_value, true );

	if ( ! is_array( $old_entries ) ) {
		$old_entries = array();
	}

	if ( ! is_array( $new_entries ) ) {
		$new_entries = array();
	}

	// Re-index entries by their source text for O(1) lookup in the loops below.
	// The dictionary is stored as a plain list; keying by source avoids nested loops.
	$old_by_source = array();
	foreach ( $old_entries as $entry ) {
		if ( isset( $entry['source'] ) ) {
			$old_by_source[ $entry['source'] ] = $entry;
		}
	}

	$new_by_source = array();
	foreach ( $new_entries as $entry ) {
		if ( isset( $entry['source'] ) ) {
			$new_by_source[ $entry['source'] ] = $entry;
		}
	}

	// Will hold the list of source/language pairs whose translations need refreshing.
	$update_todo = array();

	/**
	 * Detect removed entries.
	 *
	 * When a rule is deleted, cached translations built with that rule may be
	 * outdated (e.g., a forced translation that should revert to the default
	 * AI output). All languages covered by the old rule must be refreshed.
	 */

	foreach ( $old_by_source as $source => $old_entry ) {

		// Skip entries that still exist in the new value.
		if ( isset( $new_by_source[ $source ] ) ) {
			continue;
		}

		$impacted_languages = array();

		if ( empty( $old_entry['rules'] ) ) {
			// No per-language rules → this was a "never translate" rule affecting all languages.
			$impacted_languages = 'all';
		} else {
			// Collect every language that had a rule for this source.
			foreach ( $old_entry['rules'] as $language_id => $rule ) {
				$impacted_languages[] = $language_id;
			}
		}

		$update_todo[] = array(
			'source'             => $source,
			'impacted_languages' => $impacted_languages,
		);
	}

	/**
	 * Detect added and modified entries
	 */

	foreach ( $new_by_source as $source => $new_entry ) {
		if ( ! isset( $old_by_source[ $source ] ) ) {

			/**
			 * Added.
			 *
			 * Existing translations were produced without this rule, so they may
			 * not yet reflect the new forced translation or exclusion.
			 */

			$impacted_languages = array();

			if ( empty( $new_entry['rules'] ) ) {
				// No per-language rules → "never translate" rule affecting all languages.
				$impacted_languages = 'all';
			} else {
				// Collect every language that has a rule for this new source.
				foreach ( $new_entry['rules'] as $language_id => $rule ) {
					$impacted_languages[] = $language_id;
				}
			}

			$update_todo[] = array(
				'source'             => $source,
				'impacted_languages' => $impacted_languages,
			);

		} elseif ( $new_entry != $old_by_source[ $source ] ) {

			/**
			 * Modified.
			 *
			 * Loose comparison (!=) is intentional: strict (!==) would treat arrays
			 * with the same key/value pairs but different key order as unequal,
			 * causing false positives when WordPress re-encodes the JSON.
			 */

			$old_entry          = $old_by_source[ $source ];
			$impacted_languages = array();

			if ( empty( $new_entry['rules'] ) || empty( $old_entry['rules'] ) ) {
				// One side has no per-language rules, meaning the entry was or became a
				// "never translate" rule — all languages are affected by the change.
				$impacted_languages = 'all';
			} else {
				// Find every language whose rule value changed, was added, or was removed.
				// array_diff_assoc() catches rules that changed value or were newly added.
				// array_diff_key()  catches rules that existed in the old entry but were removed.
				// Merging both and taking the keys gives the full list of impacted languages.
				$impacted_languages = array_keys(
					array_merge(
						array_diff_assoc( $new_entry['rules'], $old_entry['rules'] ),
						array_diff_key( $old_entry['rules'], $new_entry['rules'] ),
					)
				);
			}

			if ( ! empty( $impacted_languages ) ) {
				$update_todo[] = array(
					'source'             => $source,
					'impacted_languages' => $impacted_languages,
				);
			}
		}
	}

	if ( empty( $update_todo ) ) {
		return;
	}

	// Normalize: if impacted_languages lists every active target language,
	// collapse it to 'all' to avoid unnecessary per-language filtering later.
	$all_target_ids = wplng_get_languages_target_ids();
	foreach ( $update_todo as &$item ) {
		if ( is_array( $item['impacted_languages'] )
			&& ! array_diff( $all_target_ids, $item['impacted_languages'] )
		) {
			$item['impacted_languages'] = 'all';
		}
	}
	unset( $item );

	/**
	 * Get all translations
	 */

	$translations           = wplng_get_translations();
	$translations_to_update = array();

	// Foreach translations
	foreach ( $translations as $index_strings => $translations_chunk ) {
		foreach ( $translations_chunk as $key => $translation ) {

			// Foreach $update_todo
			foreach ( $update_todo as $todo ) {
				$source_todo        = mb_strtolower( $todo['source'] );
				$source_translation = mb_strtolower( $translation['source'] );

				// Use a simple substring match for CJK sources (no word boundaries in CJK text).
				// For all other scripts, require word boundaries to avoid false matches (e.g. "men" in "women").
				$is_cjk  = (bool) preg_match( '#[\x{2E80}-\x{9FFF}\x{F900}-\x{FAFF}]#u', $source_todo );
				$pattern = $is_cjk
					? '#' . preg_quote( $source_todo, '#' ) . '#iu'
					: '#(*UCP)\b' . preg_quote( $source_todo, '#' ) . '\b#iu';

				if ( ! wplng_str_contains( $source_translation, $source_todo )
					|| ! preg_match( $pattern, $source_translation )
				) {
					continue;
				}

				$impacted_languages = 'all';

				if ( $todo['impacted_languages'] !== 'all' ) {

					$impacted_languages = array();

					$translation_review = $translation['review'] ?? array();

					foreach ( $translation['translations'] as $language_id => $value ) {
						if ( in_array( $language_id, $todo['impacted_languages'], true )
							&& ! in_array( $language_id, $translation_review, true )
						) {
							$impacted_languages[] = $language_id;
						}
					}
				}

				// If impacted_languages covers every language that has been translated,
				// collapse it to 'all' to avoid unnecessary per-language filtering later.
				if ( is_array( $impacted_languages ) ) {
					$temp_translations = $translation['translations'];
					foreach ( $impacted_languages as $language_id ) {
						unset( $temp_translations[ $language_id ] );
					}
					if ( empty( $temp_translations ) ) {
						$impacted_languages = 'all';
					}
				}

				$translations_to_update[] = array(
					'source'             => $translation['source'],
					'post_id'            => $translation['post_id'],
					'impacted_languages' => $impacted_languages,
				);

			}
		}
	}

	// Clear $translations_to_update when no loanguage impacted
	foreach ( $translations_to_update as $key => $value ) {
		if ( empty( $value['impacted_languages'] ) ) {
			unset( $translations_to_update[ $key ] );
		}
	}

	if ( ! empty( $translations_to_update ) ) {
		// Store the list in a short-lived transient so it survives the redirect
		// that options.php performs after saving. The page function reads and
		// immediately deletes it so it is only displayed once.
		set_transient( 'wplng_dictionary_updated_data', $translations_to_update, 60 );
	}
	// error_log( var_export( $translations_to_update, true ) );
}


/**
 * Print HTML Option page : wpLingua Dictionary
 *
 * @return void
 */
function wplng_option_page_dictionary() {

	// Read the transient set by wplng_on_dictionary_entries_updated() during the
	// previous POST request, then delete it immediately so it shows only once.
	$wplng_dictionary_updated_data = get_transient( 'wplng_dictionary_updated_data' );
	if ( false !== $wplng_dictionary_updated_data ) {
		delete_transient( 'wplng_dictionary_updated_data' );
	}

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
			<table class="form-table wplng-form-table">

				<?php

				$style_section_entries_all = '';

				if ( false !== $wplng_dictionary_updated_data ) {
					// $style_section_entries_all = 'display: none;';
					wplng_option_page_dictionary_update_translations_html( $wplng_dictionary_updated_data );
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


function wplng_option_page_dictionary_update_translations_html( $translations_to_update ) {

	$html = '<tr id="wplng-section-dictionnary-update-translations">';

	$html .= '<th scope="row">';
	$html .= '<span class="dashicons dashicons-update"></span> ';
	$html .= esc_html( 'Update translations', 'wplingua' );
	$html .= '</th>'; // End .row

	$html .= '<td>';
	$html .= '<fieldset>';
	$html .= '<label>';
	$html .= '<strong>' . esc_html( 'Translations Affected by Dictionary Changes', 'wplingua' ) . '</strong>';
	$html .= '</label>';
	$html .= '<p>';
	$html .= esc_html( 'Some translations have been identified as needing to be updated following the latest changes to the dictionary rules.', 'wplingua' );
	$html .= '</p>';
	$html .= '<p>';
	$html .= esc_html(
		sprintf(
			__( 'Number of translations to update: %d', 'wplingua' ),
			count( $translations_to_update )
		)
	);
	$html .= '</p>';
	$html .= '<hr>';
	$html .= '<label>';
	$html .= '<strong>' . esc_html( 'Translations to update: ', 'wplingua' ) . '</strong>';
	$html .= '</label>';

	foreach ( $translations_to_update as $key => $translation_to_update ) {

		if ( ! isset( $translation_to_update['post_id'] )
			|| ! isset( $translation_to_update['source'] )
		) {
			continue;
		}

		$html .= '<div class="wplng-dictionnary-text-to-update-entry">';
		$html .= '<strong>';
		$html .= esc_html( 'ID: ' ) . esc_html( $translation_to_update['post_id'] ) . ' | ';
		$html .= '</strong>';

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
		$html .= '<span class="wplng-dictionnary-text-to-update">';
		$html .= esc_html( $source_display );
		$html .= '</span>';

		$html .= '</div>';
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
