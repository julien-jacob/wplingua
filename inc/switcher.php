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
		$insert = 'bottom-center';
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
		$theme = 'light-simple-smooth';
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

	if ( ! $is_valid
		|| (
			'none' === wplng_get_switcher_flags_style()
			&& 'none' === $name_format
		)
	) {
		$name_format = 'original';
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
 * Get HTML of wpLingua switcher
 *
 * @param array $arg
 * @return string
 */
function wplng_get_switcher_html( $arg = array() ) {

	$is_admin = is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST );

	if ( ! $is_admin && ! wplng_url_is_translatable() ) {
		return '';
	}

	$language_website    = wplng_get_language_website();
	$language_current_id = wplng_get_language_current_id();
	$languages_target    = wplng_get_languages_target();
	$class               = wplng_get_switcher_class( $arg );
	$flags_style         = wplng_get_switcher_flags_style();
	$url_website         = wplng_get_url_original();
	$flags_show          = true;

	$url_website = remove_query_arg(
		'wplng-mode',
		$url_website
	);

	if ( $is_admin ) {
		$flags_show  = true;
		$url_website = '#';
	} elseif ( ! empty( $arg['flags'] ) && 'none' === $arg['flags'] ) {
		$flags_show = false;
	} elseif ( 'none' === $flags_style ) {
		$flags_show = false;
	}

	if ( empty( $languages_target ) ) {
		return '';
	}

	/**
	 * Translate language names
	 */

	// Untranslated target language names
	foreach ( $languages_target as $key => $language_target ) {
		$languages_target[ $key ]['original'] = wplng_get_language_name_untranslated(
			$language_target
		);
	}

	// Untranslated website language name
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
	 * Change flags if necessary
	 */

	if ( ! empty( $arg['flags'] )
		&& ( 'none' !== $arg['flags'] )
		&& ( $flags_style !== $arg['flags'] )
	) {

		// Change flags on website languages
		if ( ! empty( $language_website['flag'] ) ) {
			$language_website['flag'] = str_replace(
				'/wplingua/assets/images/' . $flags_style . '/',
				'/wplingua/assets/images/' . $arg['flags'] . '/',
				$language_website['flag']
			);
		}

		// Change flags on target languages
		foreach ( $languages_target as $key => $language_target ) {

			if ( empty( $language_target['flag'] ) ) {
				continue;
			}

			$languages_target[ $key ]['flag'] = str_replace(
				'/wplingua/assets/images/' . $flags_style . '/',
				'/wplingua/assets/images/' . $arg['flags'] . '/',
				$language_target['flag']
			);
		}
	}

	/**
	 * Create the switcher HTML
	 */

	$html  = '<div class="' . esc_attr( 'wplng-switcher ' . $class ) . '">';
	$html .= '<div class="switcher-content">';

	// Create link for current language
	if ( $language_website['id'] === $language_current_id ) {

		$html .= '<a';
		$html .= ' class="wplng-language wplng-language-current"';
		$html .= ' href="' . esc_url( $url_website ) . '"';
		$html .= ' onclick="event.preventDefault();"';
		$html .= '>';

		if ( $flags_show && ! empty( $language_website['flag'] ) ) {
			$html .= '<img';
			$html .= ' src="' . esc_url( $language_website['flag'] ) . '"';
			$html .= ' alt="' . esc_attr( $language_website['name'] ) . '"';
			$html .= '>';
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

			$url = wplng_get_url_current_for_language( $language_target['id'] );

			$html .= '<a';
			$html .= ' class="wplng-language wplng-language-current"';
			$html .= ' href="' . esc_url( $url ) . '"';
			$html .= ' onclick="event.preventDefault();"';
			$html .= '>';

			if ( $flags_show && ! empty( $language_target['flag'] ) ) {
				$html .= '<img';
				$html .= ' src="' . esc_url( $language_target['flag'] ) . '"';
				$html .= ' alt="' . esc_attr( $language_target['name'] ) . '"';
				$html .= '>';
			}

			$html .= '<span class="language-id">' . esc_html( $language_target['id'] ) . '</span>';
			$html .= '<span class="language-name">' . esc_html( $language_target['name'] ) . '</span>';
			$html .= '<span class="language-original">' . esc_html( $language_target['original'] ) . '</span>';
			$html .= '</a>';
			break;
		}
	}

	/**
	 * Languages
	 */

	$html .= '<div class="wplng-languages">';

	// Create link for website language

	if ( $language_website['id'] === $language_current_id ) {

		$html .= '<a';
		$html .= ' class="wplng-language website after current"';
		$html .= ' href="' . esc_url( $url_website ) . '"';
		$html .= ' onclick="event.preventDefault();"';
		$html .= '>';

	} elseif ( $is_admin || wplng_str_contains( $url_website, '/?et_fb=1' ) ) {

		$html .= '<a';
		$html .= ' class="wplng-language website after"';
		$html .= ' href="' . esc_url( $url_website ) . '"';
		$html .= ' onclick="event.preventDefault();">';
		$html .= '>';

	} else {

		$html .= '<a';
		$html .= ' class="wplng-language website after" ';
		$html .= ' href="' . esc_url( $url_website ) . '"';
		$html .= '>';
	}

	if ( $flags_show && ! empty( $language_website['flag'] ) ) {
		$html .= '<img';
		$html .= ' src="' . esc_url( $language_website['flag'] ) . '"';
		$html .= ' alt="' . esc_attr( $language_website['name'] ) . '"';
		$html .= '>';
	}

	$html .= '<span class="language-id">' . esc_html( $language_website['id'] ) . '</span>';
	$html .= '<span class="language-name">' . esc_html( $language_website['name'] ) . '</span>';
	$html .= '<span class="language-original">' . esc_html( $language_website['original'] ) . '</span>';
	$html .= '</a>';

	// Create link for each target languages
	foreach ( $languages_target as $key => $language_target ) {

		$url = '#';

		if ( ! $is_admin ) {
			$url = wplng_get_url_current_for_language( $language_target['id'] );
		}

		if ( $language_target['id'] === $language_current_id ) {

			$html .= '<a';
			$html .= ' class="wplng-language current"';
			$html .= ' href="' . esc_url( $url ) . '"';
			$html .= ' onclick="event.preventDefault();"';
			$html .= '>';

		} elseif ( $is_admin || wplng_str_contains( $url, '/?et_fb=1' ) ) {

			$html .= '<a';
			$html .= ' class="wplng-language"';
			$html .= ' href="' . esc_url( $url ) . '"';
			$html .= ' onclick="event.preventDefault();"';
			$html .= '>';

		} else {

			$html .= '<a';
			$html .= ' class="wplng-language"';
			$html .= ' href="' . esc_url( $url ) . '"';
			$html .= '>';

		}

		if ( $flags_show && ! empty( $language_target['flag'] ) ) {
			$html .= '<img';
			$html .= ' src="' . esc_url( $language_target['flag'] ) . '"';
			$html .= ' alt="' . esc_attr( $language_target['name'] ) . '"';
			$html .= '>';
		}

		$html .= '<span class="language-id">' . esc_html( $language_target['id'] ) . '</span>';
		$html .= '<span class="language-name">' . esc_html( $language_target['name'] ) . '</span>';
		$html .= '<span class="language-original">' . esc_html( $language_target['original'] ) . '</span>';
		$html .= '</a>';
	}

	$html .= '</div>'; // End .wplng-languages

	$html .= '</div>'; // End .switcher-content
	$html .= '</div>'; // End .wplng-switcher

	$html = apply_filters(
		'wplng_switcher_html',
		$html,
		$language_website,
		$languages_target
	);

	return $html;
}
