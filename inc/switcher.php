<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Get wpLingua switcher option : Insert
 *
 * @return string
 */
function wplng_get_switcher_insert() {

	$insert   = get_option( 'wplng_insert' );
	$is_valid = false;

	if ( ! empty( $insert ) ) {
		$valid_insert = wplng_data_switcher_valid_insert();
		foreach ( $valid_insert as $id => $name ) {
			if ( $insert === $id ) {
				$is_valid = true;
				break;
			}
		}
	}

	if ( ! $is_valid ) {
		$insert = 'bottom-left';
	}

	$insert = apply_filters(
		'wplng_switcher_insert',
		$insert
	);

	return $insert;
}


/**
 * Get wpLingua switcher option : Theme
 *
 * @return string
 */
function wplng_get_switcher_theme() {

	$theme    = get_option( 'wplng_theme' );
	$is_valid = false;

	if ( ! empty( $theme ) ) {

		$valid_theme = wplng_data_switcher_valid_theme();

		foreach ( $valid_theme as $id => $name ) {
			if ( $theme === $id ) {
				$is_valid = true;
				break;
			}
		}
	}

	if ( ! $is_valid ) {
		$theme = 'light-double-square';
	}

	$theme = apply_filters(
		'wplng_switcher_theme',
		$theme
	);

	return $theme;
}


/**
 * Get wpLingua switcher option : Style
 *
 * @return string
 */
function wplng_get_switcher_style() {

	$style    = get_option( 'wplng_style' );
	$is_valid = false;

	if ( ! empty( $style ) ) {

		$valid_style = wplng_data_switcher_valid_style();

		foreach ( $valid_style as $id => $name ) {
			if ( $style === $id ) {
				$is_valid = true;
				break;
			}
		}
	}

	if ( ! $is_valid ) {
		$style = 'list';
	}

	$style = apply_filters(
		'wplng_switcher_style',
		$style
	);

	return $style;
}


/**
 * Get wpLingua switcher option : Name format
 *
 * @return void
 */
function wplng_get_switcher_name_format() {

	$name_format = get_option( 'wplng_name_format' );
	$is_valid    = false;

	if ( ! empty( $name_format ) ) {

		$valid_name_format = wplng_data_switcher_valid_name_format();

		foreach ( $valid_name_format as $id => $name ) {
			if ( $name_format === $id ) {
				$is_valid = true;
				break;
			}
		}
	}

	if ( ! $is_valid ) {
		$name_format = 'name';
	}

	if ( wplng_get_switcher_flags_style() === 'none'
		&& 'none' === $name_format
	) {
		$name_format = 'name';
	}

	$name_format = apply_filters(
		'wplng_switcher_name_format',
		$name_format
	);

	return $name_format;
}


/**
 * Get wpLingua switcher option : Flags style
 *
 * @return string
 */
function wplng_get_switcher_flags_style() {

	$flags_style = get_option( 'wplng_flags_style' );
	$is_valid    = false;

	if ( ! empty( $flags_style ) ) {

		$valid_flags_style = wplng_data_switcher_valid_flags_style();

		foreach ( $valid_flags_style as $id => $name ) {
			if ( $flags_style === $id ) {
				$is_valid = true;
				break;
			}
		}
	}

	if ( ! $is_valid ) {
		$flags_style = 'rectangular';
	}

	return $flags_style;
}


/**
 * Get wpLingua switcher option : Class
 *
 * @param array $arg
 * @return string
 */
function wplng_get_switcher_class( $arg = array() ) {

	$class = '';

	/**
	 * Define insert class (list, block)
	 */
	if ( ! empty( $arg['insert'] )
		&& array_key_exists( $arg['insert'], wplng_data_switcher_valid_insert() )
	) {
		$class = 'insert-' . $arg['insert'];
	} else {
		$class = 'insert-' . wplng_get_switcher_insert();
	}

	/**
	 * Define style class (list, block)
	 */
	if ( ! empty( $arg['style'] )
		&& array_key_exists( $arg['style'], wplng_data_switcher_valid_style() )
	) {
		$class .= ' style-' . $arg['style'];
	} else {
		$class .= ' style-' . wplng_get_switcher_style();
	}

	/**
	 * Define language name class (id, name, none)
	 */

	if ( ! empty( $arg['title'] )
		&& array_key_exists( $arg['title'], wplng_data_switcher_valid_name_format() )
	) {
		$class .= ' title-' . $arg['title'];
	} else {
		$class .= ' title-' . wplng_get_switcher_name_format();
	}

	/**
	 * Define theme class (light, dark)
	 */
	if ( ! empty( $arg['theme'] )
		&& array_key_exists( $arg['theme'], wplng_data_switcher_valid_theme() )
	) {
		$class .= ' theme-' . $arg['theme'];
	} else {
		$class .= ' theme-' . wplng_get_switcher_theme();
	}

	/**
	 * Define flags theme class (id, name, none)
	 */
	if ( ! empty( $arg['flags'] )
		&& array_key_exists( $arg['flags'], wplng_data_switcher_valid_flags_style() )
	) {
		$class .= ' flags-' . $arg['flags'];
	} else {
		$class .= ' flags-' . wplng_get_switcher_flags_style();
	}

	/**
	 * Define additional class (id, name, none)
	 */
	if ( ! empty( $arg['class'] ) ) {
		$class .= ' ' . $arg['class'];
	}

	return esc_attr( $class );
}


/**
 * Print HTML of wpLingua switcher if it's inserted automaticaly
 *
 * @return string
 */
function wplng_switcher_wp_footer() {

	if ( ! wplng_url_is_translatable()
		|| 'none' === wplng_get_switcher_insert()
	) {
		return;
	}

	echo wplng_get_switcher_html(
		array(
			'class' => 'insert-auto',
		)
	);
}


/**
 * Print HTML of wpLingua switcher
 *
 * @param array $arg
 * @return string
 */
function wplng_get_switcher_html( $arg = array() ) {

	if ( ! wplng_url_is_translatable() && ! is_admin() ) {
		return '';
	}

	$language_website    = wplng_get_language_website();
	$language_current_id = wplng_get_language_current_id();
	$languages_target    = wplng_get_languages_target();
	$class               = wplng_get_switcher_class( $arg );
	$flags_show          = true;

	if ( is_admin() ) {
		$flags_show = true;
	} elseif ( ! empty( $arg['flags'] ) && 'none' === $arg['flags'] ) {
		$flags_show = false;
	} elseif ( 'none' === wplng_get_switcher_flags_style() ) {
		$flags_show = false;
	}

	if ( empty( $languages_target ) ) {
		return '';
	}

	/**
	 * Translate language names
	 */

	// Unranslated target language names
	foreach ( $languages_target as $key => $language_target ) {
		$languages_target[ $key ]['original'] = wplng_get_language_name_untranslated(
			$language_target
		);
	}

	// Unranslated website language name
	$language_website['original'] = wplng_get_language_name_untranslated(
		$language_website
	);

	// Translate target language names
	foreach ( $languages_target as $key => $language_target ) {
		$languages_target[ $key ]['name'] = wplng_get_language_name_translated(
			$language_target,
			$language_current_id
		);
	}

	// Translate website language name
	$language_website['name'] = wplng_get_language_name_translated(
		$language_website,
		$language_current_id
	);

	/**
	 * Create the switcher HTML
	 */

	$html  = '<div class="' . esc_attr( 'wplng-switcher ' . $class ) . '">';
	$html .= '<div class="switcher-content">';
	$html .= '<div class="wplng-languages">';

	// Create link for website language
	if (
		! empty( $language_website['id'] )
		&& ( $language_website['id'] === $language_current_id )
	) {
		$html .= '<a class="wplng-language website after current" href="javascript:void(0);">';
	} else {
		$url = '';
		if ( is_admin() || 0 > strpos( $url, '/?et_fb=1' ) ) {
			$url = 'javascript:void(0);';
		} else {
			$url = esc_url( wplng_get_url_original() );
		}
		$html .= '<a class="wplng-language website after" href="' . $url . '">';
	}

	if ( ! empty( $language_website['flag'] && $flags_show ) ) {

		$alt = __( 'Flag for language: ', 'wplingua' ) . $language_website['name'];

		$html .= '<img ';
		$html .= 'src="' . esc_url( $language_website['flag'] ) . '" ';
		$html .= 'alt="' . esc_attr( $alt ) . '">';
	}
	$html .= '<span class="language-id">' . esc_html( $language_website['id'] ) . '</span>';
	$html .= '<span class="language-name">' . esc_html( $language_website['name'] ) . '</span>';
	$html .= '<span class="language-original">' . esc_html( $language_website['original'] ) . '</span>';
	$html .= '</a>';

	// Create link for each target languages
	foreach ( $languages_target as $key => $language_target ) {

		$class = '';
		$url   = 'javascript:void(0);';
		if ( $language_target['id'] === $language_current_id ) {
			$class = ' current';
		} elseif ( ! is_admin() && 0 <= strpos( $url, '/?et_fb=1' ) ) {
			$url = wplng_get_url_current_for_language( $language_target['id'] );
		}

		$html .= '<a class="wplng-language' . $class . '" href="' . $url . '">';
		if ( ! empty( $language_target['flag'] ) && $flags_show ) {

			$alt = __( 'Flag for language: ', 'wplingua' ) . $language_target['name'];

			$html .= '<img ';
			$html .= 'src="' . esc_url( $language_target['flag'] ) . '" ';
			$html .= 'alt="' . esc_attr( $alt ) . '">';
		}
		$html .= '<span class="language-id">' . esc_html( $language_target['id'] ) . '</span>';
		$html .= '<span class="language-name">' . esc_html( $language_target['name'] ) . '</span>';
		$html .= '<span class="language-original">' . esc_html( $language_target['original'] ) . '</span>';
		$html .= '</a>';
	}

	$html .= '</div>'; // End .wplng-languages

	// Create link for current language
	if ( $language_website['id'] === $language_current_id ) {

		$html .= '<a class="wplng-language wplng-language-current" href="javascript:void(0);">';
		if ( ! empty( $language_website['flag'] ) && $flags_show ) {

			$alt = __( 'Flag for language: ', 'wplingua' ) . $language_website['name'];

			$html .= '<img ';
			$html .= 'src="' . esc_url( $language_website['flag'] ) . '" ';
			$html .= 'alt="' . esc_attr( $alt ) . '">';
		}
		$html .= '<span class="language-id">' . esc_html( $language_website['id'] ) . '</span>';
		$html .= '<span class="language-name">' . esc_html( $language_website['name'] ) . '</span>';
		$html .= '<span class="language-original">' . esc_html( $language_website['original'] ) . '</span>';
		$html .= '</a>';

	} else {

		foreach ( $languages_target as $key => $language_target ) {

			if ( $language_target['id'] !== $language_current_id ) {
				continue;
			}

			$html .= '<a class="wplng-language wplng-language-current" href="javascript:void(0);">';
			if ( ! empty( $language_target['flag'] ) && $flags_show ) {

				$alt = __( 'Flag for language: ', 'wplingua' ) . $language_target['name'];

				if ( ! empty( $arg['flags'] )
					&& 'none' !== $arg['flags']
				) {
					$flags_style = wplng_get_switcher_flags_style();
					if ( $flags_style !== $arg['flags'] ) {
						$html = str_replace(
							'/wplingua/assets/images/' . $flags_style . '/',
							'/wplingua/assets/images/' . $arg['flags'] . '/',
							$html
						);
					}
				}

				$html .= '<img ';
				$html .= 'src="' . esc_url( $language_target['flag'] ) . '" ';
				$html .= 'alt="' . esc_attr( $alt ) . '">';
			}
			$html .= '<span class="language-id">' . esc_html( $language_target['id'] ) . '</span>';
			$html .= '<span class="language-name">' . esc_html( $language_target['name'] ) . '</span>';
			$html .= '<span class="language-original">' . esc_html( $language_target['original'] ) . '</span>';
			$html .= '</a>';
			break;
		}
	}

	$html .= '</div>'; // End .switcher-content
	$html .= '</div>'; // End .wplng-switcher

	if ( ! empty( $arg['flags'] )
		&& 'none' !== $arg['flags']
	) {
		$flags_style = wplng_get_switcher_flags_style();
		if ( $flags_style !== $arg['flags'] ) {
			$html = str_replace(
				'/wplingua/assets/images/' . $flags_style . '/',
				'/wplingua/assets/images/' . $arg['flags'] . '/',
				$html
			);
		}
	}

	$html = apply_filters(
		'wplng_switcher_html',
		$html,
		$language_website,
		$languages_target
	);

	return $html;
}
