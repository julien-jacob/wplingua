<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_link_alternate_hreflang() {

	$html = '';

	// Create alternate link for website language
	$language_website = wplng_get_language_website();

	$html .= PHP_EOL;
	$html .= PHP_EOL . '<!-- This site is make multilingual with the wpLingua plugin -->';
	$html .= PHP_EOL . '<link rel="alternate" hreflang="' . esc_attr( $language_website['id'] ) . '" href="' . esc_url( wplng_get_url_original() ) . '">';

	// Create alternate link for each target languages
	$languages_target = wplng_get_languages_target();
	foreach ( $languages_target as $key => $language_target ) {
		$url   = wplng_get_url_current_for_language( $language_target['id'] );
		$html .= PHP_EOL . '<link rel="alternate" hreflang="' . esc_attr( $language_target['id'] ) . '" href="' . esc_url( $url ) . '">';
	}

	$html .= PHP_EOL . '<!-- / wpLingua plugin. -->' . PHP_EOL . PHP_EOL;

	echo $html;
}


function wplng_get_selector_exclude() {

	$selector_exclude = explode(
		PHP_EOL,
		get_option( 'wplng_excluded_selectors' )
	);

	// Remove empty
	$selector_exclude = array_values( array_filter( $selector_exclude ) );

	// Add default selectors
	$selector_exclude = array_merge(
		$selector_exclude,
		wplng_data_excluded_selector_default() // Make HTML to parse smaller
	);

	// Remove duplicate
	$selector_exclude = array_unique( $selector_exclude );

	// Sanitize selectors
	foreach ( $selector_exclude as $key => $selector ) {
		$selector_exclude[ $key ] = esc_attr( $selector );
	}

	$selector_exclude = apply_filters(
		'wplng_selector_exclude',
		$selector_exclude
	);

	return $selector_exclude;
}


function wplng_html_set_exclude_tag( $html, &$excluded_elements ) {

	$selector_exclude = wplng_get_selector_exclude();
	$dom              = str_get_html( $html );

	if ( false === $dom ) {
		return $html;
	}

	foreach ( $selector_exclude as $key => $selector ) {
		foreach ( $dom->find( $selector ) as $element ) {
			$excluded_elements[] = $element->outertext;
			$attr                = count( $excluded_elements ) - 1;
			$element->outertext  = '<div wplng-tag-exclude="' . esc_attr( $attr ) . '"></div>';
		}
	}

	$dom->save();

	return (string) str_get_html( $dom );
}


function wplng_html_replace_exclude_tag( $html, $excluded_elements ) {

	$dom = str_get_html( $html );

	if ( false === $dom ) {
		return $html;
	}

	foreach ( $dom->find( '[wplng-tag-exclude]' ) as $element ) {

		if ( isset( $element->attr['wplng-tag-exclude'] ) ) {
			$exclude_index = (int) $element->attr['wplng-tag-exclude'];

			if ( isset( $excluded_elements[ $exclude_index ] ) ) {
				$element->outertext = $excluded_elements[ $exclude_index ];
			}
		}
	}

	$dom->save();

	return (string) str_get_html( $dom );
}


function wplng_init() {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		ob_start( 'wplng_ob_callback_ajax' );
		return;
	}

	if ( wplng_get_language_website_id() === wplng_get_language_current_id() ) {
		return;
	}

	global $wplng_request_uri;
	$current_path = $wplng_request_uri;
	$origin_path  = '/' . substr( $current_path, 4, strlen( $current_path ) - 1 );

	if ( ! wplng_url_is_translatable( $origin_path ) ) {
		wp_redirect( $origin_path );
		exit;
	}

	$_SERVER['REQUEST_URI'] = $origin_path;

	if ( current_user_can( 'edit_posts' ) ) {
		if ( isset( $_GET['wplingua-editor'] ) ) {
			ob_start( 'wplng_ob_callback_editor' );
		} elseif ( isset( $_GET['wplingua-list'] ) ) {
			ob_start( 'wplng_ob_callback_list' );
		} else {
			ob_start( 'wplng_ob_callback_translate' );
		}
	} else {
		ob_start( 'wplng_ob_callback_translate' );
	}

}
