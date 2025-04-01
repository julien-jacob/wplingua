<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Print CSS and JS script for wpLingua nav menu option
 *
 * @param string $hook
 * @return void
 */
function wplng_switcher_nav_menu_inline_scripts( $hook ) {

	if ( ! is_admin()
		|| $hook !== 'nav-menus.php'
		|| empty( wplng_get_api_key() )
	) {
		return;
	}

	?>
	<style id="wplng-nav-menu-style">

		#tabs-panel-wplingua-endpoints.tabs-panel {
			max-height: unset;
		}

		#wplng_switcher_nav_menu .accordion-section-content .wplng-menu-header,
		#wplng_switcher_nav_menu .accordion-section-content .inside {
			margin-top: 0;
		}

		#tabs-panel-wplingua-endpoints label {
			font-size: 14px;
			margin-bottom: 6px;
			display: block;
		}

		#tabs-panel-wplingua-endpoints select {
			width: 100%;
			max-width: 100%;
		}

		.menu-item:has(.wplng-menu-item-settings-switcher) p.field-url,
		.menu-item:has(.wplng-menu-item-settings-switcher) p.description {
			display: none;
		}

		.menu-item:has(.wplng-menu-item-settings-switcher) span.item-type {
			display: none;
		}

		.menu-item:has(.wplng-menu-item-settings-switcher) span.item-controls::before {
			content: "wpLingua";
			display: inline-block;
			padding: 12px 16px;
			color: #646970;
			font-size: 12px;
			line-height: 1.5;
		}

		.wplng-menu-item-settings-switcher select {
			width: 100%;
			max-width: 100%;
			margin: 10px 0;
		}
	</style>
	<script id="wplng-nav-menu-script">
		window.onload = function() {

			/**
			 * wpLingua: Add new nav menu switcher
			 */

			// Infinite nav menu switcher
			// Update the edit fields event listen

			let wplngNewValidate = document.getElementById('submit-posttype-wplingua-endpoints');
			wplngNewValidate.addEventListener('click', wplngAlwaysValidate);

			function wplngAlwaysValidate() {
				wplngUpdateEditField();
				setTimeout(() => { 
					document.getElementById('wplng-validate-id').checked = true;
				}, 1500)
			}

			// Make link from option

			let wplngNewNameFormat = document.getElementById('wplng-menu-name-format')
			let wplngNewFlag = document.getElementById('wplng-menu-flag')
			let wplngNewLayout = document.getElementById('wplng-menu-layout')

			wplngNewNameFormat.addEventListener('change', wplngUpdateMenuSwitcherUrl);
			wplngNewFlag.addEventListener('change', wplngUpdateMenuSwitcherUrl);
			wplngNewLayout.addEventListener('change', wplngUpdateMenuSwitcherUrl);

			function wplngUpdateMenuSwitcherUrl() {

				let url = "#wplng";
				url += "-n" + wplngNewNameFormat.value;
				url += "-f" + wplngNewFlag.value;
				url += "-l" + wplngNewLayout.value;

				document.getElementById('wplng-menu-switcher-url').value = url;
			}

			wplngUpdateMenuSwitcherUrl();

			/**
			 * wpLingua: Edit nav menu switcher
			 */

			function wplngUpdateEditField() {

				let wplngEditNameFormat = document.getElementsByClassName('wplng-menu-name-format-edit')
				let wplngEditFlag = document.getElementsByClassName('wplng-menu-flag-edit')
				let wplngEditLayout = document.getElementsByClassName('wplng-menu-layout-edit')

				for (var i = 0; i < wplngEditNameFormat.length; i++) {
					wplngEditNameFormat[i].addEventListener('change', wplngEditMenuSwitcherUrl);
				}

				for (var i = 0; i < wplngEditFlag.length; i++) {
					wplngEditFlag[i].addEventListener('change', wplngEditMenuSwitcherUrl);
				}

				for (var i = 0; i < wplngEditLayout.length; i++) {
					wplngEditLayout[i].addEventListener('change', wplngEditMenuSwitcherUrl);
				}
			}

			function wplngEditMenuSwitcherUrl() {

				let item = this.getAttribute("item");

				let nameFormat = document.getElementById('wplng-menu-name-format-' + item).value;
				let flag = document.getElementById('wplng-menu-flag-' + item).value;
				let layout = document.getElementById('wplng-menu-layout-' + item).value;

				let url = "#wplng";
				url += "-n" + nameFormat;
				url += "-f" + flag;
				url += "-l" + layout;

				document.getElementById('edit-menu-item-url-' + item).value = url;
			}

			wplngUpdateEditField();
		}
	</script>
	<?php
}


/**
 * Add  wpLingua nav menu option meta ox
 *
 * @return void
 */
function wp_nav_menu_switcher_box_add_register() {
	add_meta_box(
		'wplng_switcher_nav_menu',
		__( 'wpLingua', 'wplingua' ),
		'wp_nav_menu_switcher_box_add',
		'nav-menus',
		'side',
		'default'
	);
}


/**
 * Print the HTML of the meta box used to add a language switcher in nav menu
 *
 * @return void
 */
function wp_nav_menu_switcher_box_add() {

	$valid_name_format = wplng_data_switcher_nav_menu_valid_name_format();
	$valid_flags_style = wplng_data_switcher_nav_menu_valid_flags_style();
	$valid_layout      = wplng_data_switcher_nav_menu_valid_layout();

	?>
	<div id="posttype-wplingua-endpoints" class="posttypediv">

		<p class="wplng-menu-header"><?php esc_html_e( 'These options are used to add a language switcher in a menu. This language switcher automatically uses the menu design.', 'wplingua' ); ?></p>

		<div id="tabs-panel-wplingua-endpoints" class="tabs-panel tabs-panel-active">
			<ul class="categorychecklist form-no-clear">

				<li>
					<label for="wplng-menu-name-format">
						<?php esc_html_e( 'Displayed names: ', 'wplingua' ); ?>
					</label>
					<select id="wplng-menu-name-format" name="wplng-menu-name-format">
						<?php
						foreach ( $valid_name_format as $key => $value ) {
							echo '<option value="' . esc_attr( $key ) . '">';
							echo esc_html( $value );
							echo '</option>';
						}
						?>
					</select>
					<hr>
				</li>

				<li>
					<label for="wplng-menu-flag">
						<?php esc_html_e( 'Displayed flag: ', 'wplingua' ); ?>
					</label>
					<select id="wplng-menu-flag" name="wplng-menu-flag">
						<?php
						foreach ( $valid_flags_style as $key => $value ) {
							echo '<option value="' . esc_attr( $key ) . '">';
							echo esc_html( $value );
							echo '</option>';
						}
						?>
					</select>
					<hr>
				</li>

				<li>
					<label for="wplng-menu-layout">
						<?php esc_html_e( 'Layout: ', 'wplingua' ); ?>
					</label>
					<select id="wplng-menu-layout" name="wplng-menu-layout">
						<?php
						foreach ( $valid_layout as $key => $value ) {
							echo '<option value="' . esc_attr( $key ) . '">';
							echo esc_html( $value );
							echo '</option>';
						}
						?>
					</select>
				</li>

				<li style="display: none;">
						
					<input type="checkbox" class="menu-item-checkbox" id="wplng-validate-id" name="menu-item[1][menu-item-validate]" value="1" onchange="alert('ok');" checked/>

					<input type="hidden" class="menu-item-object-id" name="menu-item[1][menu-item-object-id]" value="wplingua" />
					<input type="hidden" class="menu-item-object" name="menu-item[1][menu-item-object]" value="wplingua" />
					<input type="hidden" class="menu-item-type" name="menu-item[1][menu-item-type]" value="custom" />
					<input type="hidden" class="menu-item-title" name="menu-item[1][menu-item-title]" value="<?php esc_html_e( 'Language switcher', 'wplingua' ); ?>" />
					<input type="hidden" class="menu-item-classes" name="menu-item[1][menu-item-classes]" value="wplingua-menu-switcher-untreated"/>
					<input type="hidden" class="menu-item-url" name="menu-item[1][menu-item-url]" value="" id="wplng-menu-switcher-url"/>
				</li>

			</ul>
		</div>

		<p class="button-controls" data-items-type="posttype-wplingua-endpoints">
			<span class="add-to-menu">
				<input type="submit" class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to menu', 'wplingua' ); ?>" name="add-post-type-menu-item" id="submit-posttype-wplingua-endpoints">
				<span class="spinner"></span>
			</span>
		</p>

	</div>
	<?php
}


/**
 * Print option in wpLingua nav menu
 *
 * @param string  $item_id
 * @param WP_Post $menu_item
 * @return void
 */
function wp_nav_menu_switcher_box_edit( $item_id, $menu_item ) {

	if ( ! is_array( $menu_item->classes )
		|| ! in_array( 'wplingua-menu-switcher-untreated', $menu_item->classes )
		|| empty( $menu_item->url )
	) {
		return;
	}

	$args = wplng_switcher_nav_menu_args_from_href( $menu_item->url );

	if ( false === $args ) {
		return;
	}

	$valid_name_format = wplng_data_switcher_nav_menu_valid_name_format();
	$valid_flags_style = wplng_data_switcher_nav_menu_valid_flags_style();
	$valid_layout      = wplng_data_switcher_nav_menu_valid_layout();

	?>
	<div class="wplng-menu-item-settings-switcher">
		<label for="wplng-menu-name-format-<?php esc_attr_e( $item_id ); ?>">
			<?php esc_html_e( 'Displayed names: ', 'wplingua' ); ?>
		</label>
		<select 
			class="wplng-menu-name-format-edit" 
			id="wplng-menu-name-format-<?php esc_attr_e( $item_id ); ?>" 
			name="wplng-menu-name-format-<?php esc_attr_e( $item_id ); ?>"
			item="<?php esc_attr_e( $item_id ); ?>" 
		>
			<?php
			foreach ( $valid_name_format as $key => $value ) {
				if ( $key === $args['name_format']['value'] ) {
					echo '<option value="' . esc_attr( $key ) . '" selected>';
				} else {
					echo '<option value="' . esc_attr( $key ) . '">';
				}
				echo esc_html( $value );
				echo '</option>';
			}
			?>
		</select>
		<hr>
		<label for="wplng-menu-flag-<?php esc_attr_e( $item_id ); ?>">
			<?php esc_html_e( 'Displayed flag: ', 'wplingua' ); ?>
		</label>
		<select 
			class="wplng-menu-flag-edit" 
			id="wplng-menu-flag-<?php esc_attr_e( $item_id ); ?>" 
			name="wplng-menu-flag-<?php esc_attr_e( $item_id ); ?>"
			item="<?php esc_attr_e( $item_id ); ?>" 
		>
			<?php
			foreach ( $valid_flags_style as $key => $value ) {
				if ( $key === $args['flags_style']['value'] ) {
					echo '<option value="' . esc_attr( $key ) . '" selected>';
				} else {
					echo '<option value="' . esc_attr( $key ) . '">';
				}
				echo esc_html( $value );
				echo '</option>';
			}
			?>
		</select>
		<hr>
		<label for="wplng-menu-layout-<?php esc_attr_e( $item_id ); ?>">
			<?php esc_html_e( 'Layout: ', 'wplingua' ); ?>
		</label>
		<select 
			class="wplng-menu-layout-edit" 
			id="wplng-menu-layout-<?php esc_attr_e( $item_id ); ?>" 
			name="wplng-menu-layout-<?php esc_attr_e( $item_id ); ?>"
			item="<?php esc_attr_e( $item_id ); ?>" 
		>
			<?php
			foreach ( $valid_layout as $key => $value ) {
				if ( $key === $args['layout']['value'] ) {
					echo '<option value="' . esc_attr( $key ) . '" selected>';
				} else {
					echo '<option value="' . esc_attr( $key ) . '">';
				}
				echo esc_html( $value );
				echo '</option>';
			}
			?>
		</select>
		<hr>
	</div>
	<?php
}
