<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Get ars from href for nav menu switcher
 *
 * @param string $href
 * @return array|false
 */
function wplng_switcher_nav_menu_args_from_href( $href ) {

	if ( empty( $href ) ) {
		return false;
	}

	// Get valid keys for nav menu switcher options

	$valid_name_format = wplng_data_switcher_nav_menu_valid_name_format();
	$valid_flags_style = wplng_data_switcher_nav_menu_valid_flags_style();
	$valid_layout      = wplng_data_switcher_nav_menu_valid_layout();

	// Create tht regex pattern

	$pattern  = '/#wplng';
	$pattern .= '-n(' . implode( '|', array_keys( $valid_name_format ) ) . ')';
	$pattern .= '-f(' . implode( '|', array_keys( $valid_flags_style ) ) . ')';
	$pattern .= '-l(' . implode( '|', array_keys( $valid_layout ) ) . ')';
	$pattern .= '/';

	// Apply and test the regex

	$match = array();

	if ( ! preg_match( $pattern, $href, $match )
		|| empty( $match[1] )
		|| ! array_key_exists( $match[1], $valid_name_format )
		|| empty( $match[2] )
		|| ! array_key_exists( $match[2], $valid_flags_style )
		|| empty( $match[3] )
		|| ! array_key_exists( $match[3], $valid_layout )
	) {
		return false;
	}

	// Check if names or fags are displayed
	if ( 'n' === $match[1] && 'n' === $match[2] ) {
		$match[1] = 'o';
	}

	// Return the $args array checked

	return array(
		'name_format' => array(
			'value' => $match[1],
			'label' => $valid_name_format[ $match[1] ],
		),
		'flags_style' => array(
			'value' => $match[2],
			'label' => $valid_flags_style[ $match[2] ],
		),
		'layout'      => array(
			'value' => $match[3],
			'label' => $valid_layout[ $match[3] ],
		),
	);
}


/**
 * Set switcher in  nav menu
 *
 * @param array $items
 * @return void
 */
function wplng_switcher_nav_menu_replace_items( $items ) {

	$new_items = array();

	foreach ( $items as $item ) {

		// Check if is on a translatable page

		if ( in_array( 'wplingua-menu-switcher-untreated', $item->classes )
			&& ! wplng_url_is_translatable()
		) {
			continue;
		}

		// Check if is a wpLingua switcher link

		if ( empty( $item->classes )
			|| ! in_array( 'wplingua-menu-switcher-untreated', $item->classes )
		) {
			$new_items[] = $item;
			continue;
		}

		// Check if option in URL is valid

		$args = wplng_switcher_nav_menu_args_from_href( $item->url );

		if ( false === $args ) {
			$new_items[] = $item;
			continue;
		}

		// Check if language target is empty, do not show the switcher

		$languages_target = wplng_get_languages_target();

		if ( empty( $languages_target ) ) {
			continue;
		}

		$offset              = $item->menu_order;
		$language_current_id = wplng_get_language_current_id();
		$language_current    = wplng_get_language_by_id( $language_current_id );
		$language_website    = wplng_get_language_website();
		$url_website         = wplng_get_url_original();

		$url_website = remove_query_arg(
			'wplng-mode',
			$url_website
		);

		// Create $item_template and prepare classes

		$item_template = clone $item;

		$item_template->classes = array_merge(
			array_diff(
				$item_template->classes,
				array(
					'wplingua-menu-switcher-untreated',
					'menu-item-type-custom',
					'menu-item-object-custom',
				)
			),
			array(
				'wplingua-menu',
			)
		);

		/**
		 * Add parent for sub-list
		 */

		if ( 's' === $args['layout']['value'] ) {

			$new_item = clone $item_template;

			$title = '';

			switch ( $args['name_format']['value'] ) {
				case 'o':
					$title = wplng_get_language_name_untranslated(
						$language_current_id
					);
					break;

				case 't':
					$title = wplng_get_language_name_translated(
						$language_current_id,
						$language_current_id
					);
					break;

				case 'i':
					$title = strtoupper( $language_current_id );
					break;

				default: // case 'n'
					$title = '';
					break;
			}

			$new_item->ID         = 'wplng-language-' . $language_current_id;
			$new_item->title      = $title;
			$new_item->attr_title = $title;
			$new_item->url        = wplng_get_url_current();
			$new_item->classes[]  = 'wplng-language-parent';
			$new_item->classes[]  = 'wplng-language-current';

			if ( 'y' === $args['flags_style']['value'] ) {

				$new_item->wplng_flag = $language_current['flag'];

				$new_item->wplng_alt = wplng_get_language_name_translated(
					$language_current_id,
					$language_current_id
				);
			}

			$new_items[] = $new_item;
		}

		/**
		 * Add website language
		 */

		if ( ! (
				$language_current_id === $language_website['id']
				&& 't' === $args['layout']['value']
			)
			&& (
				's' !== $args['layout']['value']
				|| $language_current_id !== $language_website['id']
			)
		) {

			$new_item = clone $item_template;

			$title = '';

			switch ( $args['name_format']['value'] ) {
				case 'o':
					$title = wplng_get_language_name_untranslated(
						$language_website['id']
					);
					break;

				case 't':
					$title = wplng_get_language_name_translated(
						$language_website['id'],
						$language_current_id
					);
					break;

				case 'i':
					$title = strtoupper( $language_website['id'] );
					break;

				default: // case 'n'
					$title = '';
					break;
			}

			$new_item->ID         = 'wplng-language-' . $language_website['id'];
			$new_item->menu_order = $item_template->menu_order + $offset;
			$new_item->title      = $title;
			$new_item->attr_title = $title;
			$new_item->url        = $url_website;
			$new_item->db_id      = 0;
			$new_item->classes[]  = 'wplng-language-website';

			if ( 's' === $args['layout']['value'] ) {
				$new_item->menu_item_parent = $item_template->ID;
			}

			if ( $language_current_id === $language_website['id'] ) {

				$new_item->classes[] = 'wplng-language-current';

				if ( 'a' === $args['layout']['value'] ) {
					$new_item->current   = true;
					$new_item->classes[] = 'current-menu-item';
				}
			}

			if ( 'y' === $args['flags_style']['value'] ) {

				$new_item->wplng_flag = $language_website['flag'];

				$new_item->wplng_alt = wplng_get_language_name_translated(
					$language_website['id'],
					$language_current_id
				);

			}

			$new_items[] = $new_item;

			++$offset;
		}

		/**
		 * Add target languages
		 */

		foreach ( $languages_target as $language_target ) {

			if ( $language_current_id === $language_target['id']
				&& (
					's' === $args['layout']['value']
					|| 't' === $args['layout']['value']
				)
			) {
				continue;
			}

			$new_item = clone $item_template;

			$title = '';

			switch ( $args['name_format']['value'] ) {
				case 'o':
					$title = wplng_get_language_name_untranslated(
						$language_target['id']
					);
					break;

				case 't':
					$title = wplng_get_language_name_translated(
						$language_target['id'],
						$language_current_id
					);
					break;

				case 'i': // case 'i'
					$title = strtoupper( $language_target['id'] );
					break;

				default: // case 'n'
					$title = '';
					break;
			}

			$new_item->ID         = 'wplng-language-' . $language_target['id'];
			$new_item->menu_order = $item_template->menu_order + $offset;
			$new_item->title      = $title;
			$new_item->attr_title = $title;
			$new_item->url        = wplng_get_url_current_for_language( $language_target['id'] );
			$new_item->db_id      = 0;
			$new_item->classes[]  = 'wplng-language-target';

			if ( 's' === $args['layout']['value'] ) {
				$new_item->menu_item_parent = $item_template->ID;
			}

			if ( $language_current_id === $language_target['id'] ) {

				$new_item->classes[] = 'wplng-language-current';

				if ( 'a' === $args['layout']['value'] ) {
					$new_item->current   = true;
					$new_item->classes[] = 'current-menu-item';
				}
			}

			if ( 'y' === $args['flags_style']['value'] ) {
				$new_item->wplng_flag = $language_target['flag'];

				$new_item->wplng_alt = wplng_get_language_name_translated(
					$language_target['id'],
					$language_current_id
				);
			}

			$new_items[] = $new_item;

			++$offset;
		}
	} // End foreach $items

	return $new_items;
}


/**
 * Add attribute in nav menu switcher
 *
 * @param array   $atts
 * @param WP_Post $menu_item
 * @return array
 */
function wplng_add_nav_menu_link_attributes_atts( $atts, $menu_item ) {

	if ( ! empty( $menu_item->wplng_flag ) ) {
		$atts['data-wplng-flag'] = $menu_item->wplng_flag;
	}

	if ( ! empty( $menu_item->wplng_alt ) ) {
		$atts['data-wplng-alt'] = $menu_item->wplng_alt;
	}

	if ( ! empty( $menu_item->classes )
		&& in_array( 'wplng-language-current', $menu_item->classes )
	) {
		$atts['onclick'] = 'event.preventDefault();';
	}

	return $atts;
}
