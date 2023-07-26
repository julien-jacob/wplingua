<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function wplng_get_switcher_valid_insert() {
	return array(
		'bottom-right'  => __( 'Bottom right', 'wplingua' ),
		'bottom-center' => __( 'Bottom center', 'wplingua' ),
		'bottom-left'   => __( 'Bottom left', 'wplingua' ),
		'none'          => __( 'None', 'wplingua' ),
	);
}

function wplng_get_switcher_insert() {

	$insert   = get_option( 'wplng_insert' );
	$is_valid = false;

	if ( ! empty( $insert ) ) {

		$valid_insert = wplng_get_switcher_valid_insert();

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

function wplng_get_switcher_valid_theme() {
	return array(
		'smooth-light' => __( 'Smooth Light', 'wplingua' ),
		'smooth-grey'  => __( 'Smooth Grey', 'wplingua' ),
		'smooth-dark'  => __( 'Smooth Dark', 'wplingua' ),
		'square-light' => __( 'Square Light', 'wplingua' ),
		'square-grey'  => __( 'Square Grey', 'wplingua' ),
		'square-dark'  => __( 'Square Dark', 'wplingua' ),
	);
}

function wplng_get_switcher_theme() {

	$theme    = get_option( 'wplng_theme' );
	$is_valid = false;

	if ( ! empty( $theme ) ) {

		$valid_theme = wplng_get_switcher_valid_theme();

		foreach ( $valid_theme as $id => $name ) {
			if ( $theme === $id ) {
				$is_valid = true;
				break;
			}
		}
	}

	if ( ! $is_valid ) {
		$theme = 'square-light';
	}

	$theme = apply_filters(
		'wplng_switcher_theme',
		$theme
	);

	return $theme;
}

function wplng_get_switcher_valid_style() {
	return array(
		'list'     => __( 'List', 'wplingua' ),
		'block'    => __( 'Block', 'wplingua' ),
		'dropdown' => __( 'Dropdown', 'wplingua' ),
	);
}


function wplng_get_switcher_style() {

	$style    = get_option( 'wplng_style' );
	$is_valid = false;

	if ( ! empty( $style ) ) {

		$valid_style = wplng_get_switcher_valid_style();

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

function wplng_get_switcher_valid_name_format() {
	return array(
		'name' => __( 'Complete name', 'wplingua' ),
		'id'   => __( 'Language ID', 'wplingua' ),
		'none' => __( 'No display', 'wplingua' ),
	);
}


function wplng_get_switcher_name_format() {

	$name_format = get_option( 'wplng_name_format' );
	$is_valid    = false;

	if ( ! empty( $name_format ) ) {

		$valid_name_format = wplng_get_switcher_valid_name_format();

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

function wplng_get_switcher_valid_flags_style() {
	return array(
		'circle',
		'rectangular',
		'none',
	);
}


function wplng_get_switcher_flags_style() {

	$flags_style = get_option( 'wplng_flags_style' );
	$is_valid    = false;

	if ( ! empty( $flags_style ) ) {

		$valid_flags_style = wplng_get_switcher_valid_flags_style();

		foreach ( $valid_flags_style as $key => $valid ) {
			if ( $flags_style === $valid ) {
				$is_valid = true;
				break;
			}
		}
	}

	if ( ! $is_valid ) {
		$flags_style = 'rectangular';
	}

	$flags_style = apply_filters(
		'wplng_switcher_flags_style',
		$flags_style
	);

	return $flags_style;
}


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


function wplng_get_switcher_class( $arg = array() ) {

	$class = '';

	/**
	 * Define insert class (list, block)
	 */
	if ( ! empty( $arg['insert'] )
		&& in_array( $arg['insert'], wplng_get_switcher_valid_insert() )
	) {
		$class = 'insert-' . $arg['insert'];
	} else {
		$class = 'insert-' . wplng_get_switcher_insert();
	}

	/**
	 * Define style class (list, block)
	 */
	if ( ! empty( $arg['style'] )
		&& in_array( $arg['style'], wplng_get_switcher_valid_style() )
	) {
		$class .= ' style-' . $arg['style'];
	} else {
		$class .= ' style-' . wplng_get_switcher_style();
	}

	/**
	 * Define language name class (id, name, none)
	 */

	if ( ! empty( $arg['title'] )
		&& in_array( $arg['title'], wplng_get_switcher_valid_name_format() )
	) {
		$class .= ' title-' . $arg['title'];
	} else {
		$class .= ' title-' . wplng_get_switcher_name_format();
	}

	/**
	 * Define theme class (light, dark)
	 */
	if ( ! empty( $arg['theme'] )
		&& in_array( $arg['theme'], wplng_get_switcher_valid_theme() )
	) {
		$class .= ' theme-' . $arg['theme'];
	} else {
		$class .= ' theme-' . wplng_get_switcher_theme();
	}

	/**
	 * Define flags theme class (id, name, none)
	 */
	if ( ! empty( $arg['flags'] )
		&& in_array( $arg['flags'], wplng_get_switcher_valid_flags_style() )
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


// $class = '', $flags_show = true
function wplng_get_switcher_html( $arg = array() ) {

	$language_website    = wplng_get_language_website();
	$language_current_id = wplng_get_language_current_id();
	$languages_target    = wplng_get_languages_target();
	$class               = wplng_get_switcher_class( $arg );
	$flags_show          = true;

	if ( ! empty( $arg['flags'] ) && 'none' === $arg['flags'] ) {
		$flags_show = false;
	} elseif ('none' === wplng_get_switcher_flags_style()) {
		$flags_show = false;
	}
	
	if ( is_admin() ) {
		$flags_show = true;
	}

	if ( empty( $languages_target ) ) {
		return '';
	}

	$html  = '<div class="' . esc_attr( 'wplng-switcher ' . $class ) . '">';
	$html .= '<div class="switcher-content">';
	$html .= '<div class="wplng-languages">';

	// Create link for website language
	if (
		! empty( $language_website['id'] )
		&& ( $language_website['id'] === $language_current_id )
	) {
		$html .= '<a class="wplng-language website after current" href="' . esc_url( wplng_get_url_original() ) . '">';
	} else {
		$html .= '<a class="wplng-language website after" href="' . esc_url( wplng_get_url_original() ) . '">';
	}
	if ( ! empty( $language_website['flag'] && $flags_show ) ) {
		$html .= '<img src="' . esc_url( $language_website['flag'] ) . '" alt="' . esc_attr( $language_website['name'] ) . '">';
	}
	$html .= '<span class="language-id">' . esc_html( $language_website['id'] ) . '</span> ';
	$html .= '<span class="language-name">' . esc_html( $language_website['name'] ) . '</span>';
	$html .= '</a>';

	// Create link for each target languages
	foreach ( $languages_target as $key => $language_target ) {

		$class = '';
		if ( $language_target['id'] === $language_current_id ) {
			$class = ' current';
		}

		$url   = wplng_get_url_current_for_language( $language_target['id'] );
		$html .= '<a class="wplng-language' . $class . '" href="' . esc_url( $url ) . '">';
		if ( ! empty( $language_website['flag'] ) && $flags_show ) {
			$html .= '<img src="' . esc_url( $language_target['flag'] ) . '" alt="' . esc_attr( $language_target['name'] ) . '">';
		}
		$html .= '<span class="language-id">' . esc_html( $language_target['id'] ) . '</span> ';
		$html .= '<span class="language-name">' . esc_html( $language_target['name'] ) . '</span>';
		$html .= '</a>';
	}

	$html .= '</div>';

	// Create link for current language
	if ( $language_website['id'] === $language_current_id ) {

		$url   = wplng_get_url_current_for_language( $language_website['id'] );
		$html .= '<a class="wplng-language wplng-language-current" href="' . esc_url( $url ) . '">';
		if ( ! empty( $language_website['flag'] ) && $flags_show ) {
			$html .= '<img src="' . esc_url( $language_website['flag'] ) . '" alt="' . esc_attr( $language_website['name'] ) . '">';
		}
		$html .= '<span class="language-id">' . esc_html( $language_website['id'] ) . '</span> ';
		$html .= '<span class="language-name">' . esc_html( $language_website['name'] ) . '</span>';
		$html .= '</a>';

	} else {

		foreach ( $languages_target as $key => $language_target ) {

			if ( $language_target['id'] !== $language_current_id ) {
				continue;
			}

			$url   = wplng_get_url_current_for_language( $language_target['id'] );
			$html .= '<a class="wplng-language wplng-language-current" href="' . esc_url( $url ) . '">';
			if ( ! empty( $language_website['flag'] ) && $flags_show ) {
				$html .= '<img src="' . esc_url( $language_target['flag'] ) . '" alt="' . esc_attr( $language_target['name'] ) . '">';
			}
			$html .= '<span class="language-id">' . esc_html( $language_target['id'] ) . '</span> ';
			$html .= '<span class="language-name">' . esc_html( $language_target['name'] ) . '</span>';
			$html .= '</a>';
			break;
		}
	}

	$html .= '</div>';

	$html .= '</div>';

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
