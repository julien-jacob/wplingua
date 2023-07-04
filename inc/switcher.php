<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function wplng_get_switcher_automatic_insertion() {

	$automatic_insertion = get_option( 'wplng_switcher', 'wplng_automatic_insertion' );
	$is_valid            = false;

	if ( ! empty( $automatic_insertion ) ) {

		$valid_automatic_insertion = array(
			'bottom-left',
			'bottom-right',
			'none',
		);

		foreach ( $valid_automatic_insertion as $key => $valid ) {
			if ( $automatic_insertion === $valid ) {
				$is_valid = true;
				break;
			}
		}
	}

	if ( ! $is_valid ) {
		$automatic_insertion = 'bottom-left';
	}

	$automatic_insertion = apply_filters(
		'wplng_switcher_automatic_insertion',
		$automatic_insertion
	);

	return $automatic_insertion;
}


function wplng_get_switcher_theme() {

	$theme    = get_option( 'wplng_switcher', 'wplng_theme' );
	$is_valid = false;

	if ( ! empty( $theme ) ) {

		$valid_theme = array(
			'light',
			'dark',
		);

		foreach ( $valid_theme as $key => $valid ) {
			if ( $theme === $valid ) {
				$is_valid = true;
				break;
			}
		}
	}

	if ( ! $is_valid ) {
		$theme = 'light';
	}

	$theme = apply_filters(
		'wplng_switcher_theme',
		$theme
	);

	return $theme;
}


function wplng_get_switcher_style() {

	$style    = get_option( 'wplng_switcher', 'wplng_style' );
	$is_valid = false;

	if ( ! empty( $style ) ) {

		$valid_style = array(
			'dropdown',
			'list',
			'block',
		);

		foreach ( $valid_style as $key => $valid ) {
			if ( $style === $valid ) {
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


function wplng_get_switcher_name_style() {

	$name_style = get_option( 'wplng_switcher', 'wplng_name_style' );
	$is_valid   = false;

	if ( ! empty( $name_style ) ) {

		$valid_name_style = array(
			'light',
			'dark',
		);

		foreach ( $valid_name_style as $key => $valid ) {
			if ( $name_style === $valid ) {
				$is_valid = true;
				break;
			}
		}
	}

	if ( ! $is_valid ) {
		$name_style = 'light';
	}

	$name_style = apply_filters(
		'wplng_switcher_name_style',
		$name_style
	);

	return $name_style;
}


function wplng_get_switcher_flags_show() {

	$flags_show = get_option( 'wplng_switcher', 'wplng_flags_show' );

	if ( 'hide' === $flags_show ) {
		$flags_show = false;
	} else {
		$flags_show = true;
	}

	$flags_show = apply_filters(
		'wplng_switcher_flags_show',
		$flags_show
	);

	return $flags_show;
}


function wplng_get_switcher_flags_style() {

	$flags_style = get_option( 'wplng_switcher', 'wplng_flags_style' );
	$is_valid    = false;

	if ( ! empty( $flags_style ) ) {

		$valid_flags_style = array(
			'light',
			'dark',
		);

		foreach ( $valid_flags_style as $key => $valid ) {
			if ( $flags_style === $valid ) {
				$is_valid = true;
				break;
			}
		}
	}

	if ( ! $is_valid ) {
		$flags_style = 'light';
	}

	$flags_style = apply_filters(
		'wplng_switcher_flags_style',
		$flags_style
	);

	return $flags_style;
}


function wplng_switcher_wp_footer() {

	if ( ! wplng_url_is_translatable() ) {
		return;
	}

	if ( ! wplng_get_switcher_automatic_insertion() ) {
		return;
	}

	echo wplng_get_switcher_html();
}


function wplng_get_switcher_html( $style = 'list', $theme = 'light', $name_style = 'name', $flags_show = true ) {

	$language_website = wplng_get_language_website();
	$languages_target = wplng_get_languages_target();
	$language_current_id = wplng_get_language_current_id();

	$switcher_class  = 'wplng-switcher ' . $style . ' ' . $theme;

	$html = '<div class="' . esc_attr( $switcher_class ) . '">';

	// TODO : Check if language original and target is OK, else return

	// Create link for current language
	if ($language_website['id'] === $language_current_id) {

		$url   = wplng_get_url_current_for_language( $language_website['id'] );
		$html .= '<a class="wplng-language wplng-language-current" href="' . esc_url( $url ) . '">';
		if ( ! empty( $language_website['flag'] ) && $flags_show ) {
			$html .= '<img src="' . esc_url( $language_website['flag'] ) . '" alt="' . esc_attr( $language_website['name'] ) . '">';
		}
		$html .= esc_html( $language_website['name'] );
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
			$html .= esc_html( $language_target['name'] );
			$html .= '</a>';
			break;
		}

	}


	$html .= '<div class="wplng-languages">';

	// Create link for website language
	$html .= '<a class="wplng-language website" href="' . esc_url( wplng_get_url_original() ) . '">';
	if ( ! empty( $language_website['flag'] && $flags_show) ) {
		$html .= '<img src="' . esc_url( $language_website['flag'] ) . '" alt="' . esc_attr( $language_website['name'] ) . '">';
	}
	$html .= esc_html( $language_website['name'] );
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
		$html .= esc_html( $language_target['name'] );
		$html .= '</a>';
	}

	$html .= '</div>';
	$html .= '</div>';

	$html = apply_filters(
		'wplng_switcher_html',
		$html,
		$language_website,
		$languages_target
	);

	return $html;
}
