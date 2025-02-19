<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Print HTML Option page - wpLingua : Links & Medias
 *
 * @return void
 */
function wplng_option_page_link_media() {

	$entries_json = wplng_link_media_get_entries_json();

	?>

	<h1 class="wplng-option-page-title"><span class="dashicons dashicons-translation"></span> <?php esc_html_e( 'wpLingua - Links & Medias', 'wplingua' ); ?></h1>

	<div class="wrap">
		<hr class="wp-header-end">
		<form method="post" action="options.php">
			<?php
			settings_fields( 'wplng_link_media' );
			do_settings_sections( 'wplng_link_media' );
			?>

			<textarea name="wplng_link_media_entries" id="wplng_link_media_entries" style="display: none;" type="hidden"><?php echo esc_textarea( $entries_json ); ?></textarea>

			<table class="form-table wplng-form-table">
				<tr id="wplng-section-entries-all">
					<th scope="row"><span class="dashicons dashicons-format-gallery"></span> <?php esc_html_e( 'Links & medias', 'wplingua' ); ?></th>
					<td>
						<fieldset>

							<p><strong><?php esc_html_e( 'Links and medias translations rules: ', 'wplingua' ); ?></strong></p>

							<p><?php esc_html_e( 'Translation rules on links and media allow different images to be displayed or link URLs to be changed according to the current language. These rules apply to elements such as: link target URLs, image source URLs, iframe source URLs, URLs in intercepted JSONs...', 'wplingua' ); ?></p>

							<hr>

							<?php wplng_option_page_link_media_entries_html(); ?>

							<a href="javascript:void(0);" class="button button-primary" id="wplng-new-rule-button">
								<?php esc_html_e( 'Add a rule', 'wplingua' ); ?>
							</a>

						</fieldset>
					</td>
				</tr>

				<tr id="wplng-section-entry-new" style="display: none;">
					<th scope="row"><span class="dashicons dashicons-welcome-add-page"></span> <?php esc_html_e( 'Add an entry', 'wplingua' ); ?></th>
					<td>
						<div id="wplng-link-media-entry-new">
							<?php wplng_option_page_link_media_new_entry_html(); ?>
						</div>
					</td>
				</tr>

				<tr id="wplng-section-entry-edit" style="display: none;">
					<th scope="row"><span class="dashicons dashicons-welcome-write-blog"></span> <?php esc_html_e( 'Edit the entry', 'wplingua' ); ?></th>
					<td>
						<div id="wplng-link-media-entry-edit">
							<?php wplng_option_page_link_media_edit_entry_html(); ?>
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
function wplng_option_page_link_media_entries_html() {

	$link_media_entries = wplng_link_media_get_entries();

	if ( empty( $link_media_entries ) ) {
		return '';
	}

	$language_website       = wplng_get_language_website();
	$language_website_html  = '<img';
	$language_website_html .= ' src="' . esc_url( $language_website['flag'] ) . '"';
	$language_website_html .= ' alt="' . esc_attr( $language_website['name'] ) . '"';
	$language_website_html .= ' class="wplng-flag"';
	$language_website_html .= '>';

	$html  = '<label><strong>';
	$html .= esc_html__( 'All links and medias rules: ', 'wplingua' );
	$html .= '</strong></label>';
	$html .= '<br>';
	$html .= '<div id="wplng-link-media-entries">';

	foreach ( $link_media_entries as $rule_number => $entry ) {

		$html .= '<div';
		$html .= ' class="wplng-link-media-entry"';
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

			/**
			 * Mode
			 */

			$html .= '<strong>';
			$html .= esc_html__( 'Mode: ', 'wplingua' );
			$html .= '</strong>';

			if ( empty( $entry['rules'] ) ) {
				$entry['rules'] = 'exactly';
			}

			switch ( $entry['mode'] ) {
				case 'exactly':
					$html .= esc_html__( 'Exactly equal', 'wplingua' );
					break;

				case 'partially':
					$html .= esc_html__( 'Partially equal', 'wplingua' );
					break;

				case 'regex':
					$html .= esc_html__( 'Regex', 'wplingua' );
					break;

				default:
					$html .= esc_html__( 'Error', 'wplingua' );
					break;
			}

			$html .= '<hr>';

			/**
			 * Source
			 */

			$html .= '<strong>';
			$html .= $language_website_html;
			$html .= esc_html__( 'Original: ', 'wplingua' );
			$html .= '</strong>';
			$html .= esc_html( $entry['source'] );

			/**
			 * Rules
			 */

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
				$html .= ' : ';
				$html .= '</strong>';
				$html .= esc_html( $rule );
			}
		}

		$html .= '</div>'; // End .wplng-dictionary-entry
	}

	$html .= '</div>'; // End #wplng-dictionary-entries

	echo $html;
}


/**
 * Print HTML subsection of Option page : wpLingua Links & Medias - New entry
 *
 * @return void
 */
function wplng_option_page_link_media_new_entry_html() {

	$html = '';

	/**
	* Input: Source
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
	$html .= esc_html__( 'Original URL: ', 'wplingua' );
	$html .= '</strong>';
	$html .= '</label>';
	$html .= '<br>';
	$html .= '<input';
	$html .= ' type="text"';
	$html .= ' name="wplng-new-source"';
	$html .= ' id="wplng-new-source"';
	$html .= ' maxlength="256"';
	$html .= '/>';
	$html .= '</fieldset>';

	/**
	 * Input: Mode
	 */

	$html .= '<fieldset class="wplng-link-media-mode">';
	$html .= '<input';
	$html .= ' type="radio"';
	$html .= ' name="wplng_new_mode"';
	$html .= ' value="exactly"';
	$html .= ' id="wplng_new_mode_exactly"';
	$html .= ' checked';
	$html .= '>';
	$html .= '<label for="wplng_new_mode_exactly"> ';
	$html .= esc_html__( 'Exactly equal', 'wplingua' );
	$html .= '</label> ';
	$html .= '<input';
	$html .= ' type="radio"';
	$html .= ' name="wplng_new_mode"';
	$html .= ' value="partially"';
	$html .= ' id="wplng_new_mode_partially"';
	$html .= '>';
	$html .= '<label for="wplng_new_mode_partially"> ';
	$html .= esc_html__( 'Partially equal', 'wplingua' );
	$html .= '</label> ';
	$html .= '<input';
	$html .= ' type="radio"';
	$html .= ' name="wplng_new_mode"';
	$html .= ' value="wplng_new_mode_regex"';
	$html .= ' id="wplng_new_mode_regex"';
	$html .= '>';
	$html .= '<label for="wplng_new_mode_regex"> ';
	$html .= esc_html__( 'REGEX', 'wplingua' );
	$html .= '</label>';
	$html .= '</fieldset>';

	/**
	 * Input: Rules
	 */

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
		$html .= esc_html__( ' - URL: ', 'wplingua' );
		$html .= '</strong>';
		$html .= '</label>';

		$html .= '<br>';

		$html .= '<input';
		$html .= ' type="text"';
		$html .= ' name="' . esc_attr( $name ) . '"';
		$html .= ' id="' . esc_attr( $name ) . '"';
		$html .= ' maxlength="256"';
		$html .= '/>';

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
 * Print HTML subsection of Option page : wpLingua Links & Medias - Edit entry
 *
 * @return void
 */
function wplng_option_page_link_media_edit_entry_html() {

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
	$html .= esc_html__( 'Original URL: ', 'wplingua' );
	$html .= '</strong>';
	$html .= '</label>';
	$html .= '<br>';
	$html .= '<input';
	$html .= ' type="text"';
	$html .= ' name="wplng-edit-source"';
	$html .= ' id="wplng-edit-source"';
	$html .= ' maxlength="256"';
	$html .= '/>';
	$html .= '</fieldset>';

	/**
	 * Input: Mode
	 */

	$html .= '<fieldset class="wplng-link-media-mode">';
	$html .= '<input';
	$html .= ' type="radio"';
	$html .= ' name="wplng_edit_mode"';
	$html .= ' value="exactly"';
	$html .= ' id="wplng_edit_mode_exactly"';
	$html .= ' checked';
	$html .= '>';
	$html .= '<label for="wplng_edit_mode_exactly"> ';
	$html .= esc_html__( 'Exactly equal', 'wplingua' );
	$html .= '</label> ';
	$html .= '<input';
	$html .= ' type="radio"';
	$html .= ' name="wplng_edit_mode"';
	$html .= ' value="partially"';
	$html .= ' id="wplng_edit_mode_partially"';
	$html .= '>';
	$html .= '<label for="wplng_edit_mode_partially"> ';
	$html .= esc_html__( 'Partially equal', 'wplingua' );
	$html .= '</label> ';
	$html .= '<input';
	$html .= ' type="radio"';
	$html .= ' name="wplng_edit_mode"';
	$html .= ' value="regex"';
	$html .= ' id="wplng_edit_mode_regex"';
	$html .= '>';
	$html .= '<label for="wplng_edit_mode_regex"> ';
	$html .= esc_html__( 'REGEX', 'wplingua' );
	$html .= '</label>';
	$html .= '</fieldset>';

	/**
	 * Input: Rules
	 */

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

		$html .= '<input';
		$html .= ' type="text"';
		$html .= ' name="' . esc_attr( $name ) . '"';
		$html .= ' id="' . esc_attr( $name ) . '"';
		$html .= ' maxlength="256"';
		$html .= '/>';

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
