<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_replace_og_local( $html ) {

	if ( ! wplng_url_is_translatable()
		|| wplng_get_language_website_id() === wplng_get_language_current_id()
	) {
		return $html;
	}

	$html = preg_replace(
		'#<meta (.*?)?property=(\"|\')og:locale(\"|\') (.*?)?>#',
		'<meta property=$2og:locale$2 content=$2' . wplng_get_language_current_id() . '$2>',
		$html
	);

	return $html;
}


function wplng_language_attributes( $attr ) {

	$language_current_id = wplng_get_language_current_id();

	// TODO : Check if untranslatable page ?
	if ( is_admin() || empty( $language_current_id ) ) {
		return $attr;
	}

	$attr = preg_replace(
		'#lang=(\"|\')(..)-(..)(\"|\')#i',
		'lang=$1' . esc_attr( $language_current_id ) . '$4',
		$attr
	);

	// Remove dir attr
	$attr = preg_replace(
		'#dir=(\"|\')(...)(\"|\')#i',
		'',
		$attr
	);

	$language_current = wplng_get_language_by_id(
		wplng_get_language_current_id()
	);

	// Add dir attribute if necessary
	if ( ! empty( $language_current['dir'] )
		&& 'rtl' === $language_current['dir']
	) {
		$attr .= ' dir="rtl"';
	}

	return $attr;
}


function wplng_link_alternate_hreflang() {

	$html = '';

	// Create alternate link for website language
	$language_website = wplng_get_language_website();
	$html            .= '<link rel="alternate" hreflang="' . esc_attr( $language_website['id'] ) . '" href="' . esc_url( wplng_get_url_original() ) . '">';

	// Create alternate link for each target languages
	$languages_target = wplng_get_languages_target();
	foreach ( $languages_target as $key => $language_target ) {
		$url   = wplng_get_url_current_for_language( $language_target['id'] );
		$html .= '<link rel="alternate" hreflang="' . esc_attr( $language_target['id'] ) . '" href="' . esc_url( $url ) . '">';
	}

	echo $html;
}


function wplng_html_translate_links( $html, $language_target ) {
	$dom = str_get_html( $html );

	if ( empty( $dom ) ) {
		// Return empty string if $html is not valid
		return '';
	}

	foreach ( $dom->find( 'a' ) as $element ) {
		$link          = $element->href;
		$element->href = wplng_url_translate( $link, $language_target );
	}
	foreach ( $dom->find( 'form' ) as $element ) {
		$link            = $element->action;
		$element->action = wplng_url_translate( $link, $language_target );
	}

	$dom->save();
	return (string) str_get_html( $dom );
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
		array(
			'#wpadminbar',
			'.no-translate',
			'.notranslate',
			'.wplng-switcher',
			'address',
		)
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


function wplng_get_selector_clear() {

	$selector_clear = array(
		'style',
		'svg',
		'script',
		'canvas',
		'link',
	);

	$selector_clear = apply_filters(
		'wplng_selector_clear',
		$selector_clear
	);

	return $selector_clear;
}


function wplng_html_set_exclude_tag( $html, &$excluded_elements ) {

	$selector_exclude = wplng_get_selector_exclude();
	$dom              = str_get_html( $html );

	if ( $dom === false ) {
		return $html;
	}

	foreach ( $selector_exclude as $key => $selector ) {
		foreach ( $dom->find( $selector ) as $element ) {
			$excluded_elements[] = $element->outertext;
			$attr                = count( $excluded_elements ) - 1;
			$element->outertext  = '<div wplng-tag-exclude="' . esc_attr( $attr ) . '"></div>';
		}
	}

	$dom->load( $dom->save() );

	return (string) str_get_html( $dom );
}


function wplng_html_replace_exclude_tag( $html, $excluded_elements ) {

	foreach ( $excluded_elements as $key => $element ) {
		$s    = '<div wplng-tag-exclude="' . esc_attr( $key ) . '"></div>';
		$html = str_replace( $s, $element, $html );
	}

	return $html;
}


// function wplng_clear_intercepted_html( $html ) {

// 	return str_replace( 'sspan', 'span', $html );

// 	$search = array(
// 		'#\s\s+#s',
// 		'#\s+>#',
// 		'#\s+/>#',
// 		'#(\n|^)(\x20+|\t)#',
// 		'#(\n|^)\/\/(.*?)(\n|$)#',
// 		'#\n+#', // Multiple end of line
// 		'#(\x20+|\t)#', // Delete multispace (Without \n)
// 		'#\>\s+\<#', // strip whitespaces between tags
// 		'#(\"|\')\s+\>#', // strip whitespaces between quotation ("') and end tags
// 		'#=\s+(\"|\')#', // strip whitespaces between = "'
// 		'#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s', // Remove HTML comment(s) except IE comment(s)
// 	);

// 	$replace = array(
// 		' ',
// 		'>',
// 		'/>',
// 		"\n",
// 		"\n",
// 		"\n",
// 		' ',
// 		'><',
// 		'$1>',
// 		'=$1',
// 		'',
// 	);

// 	$html = preg_replace(
// 		$search,
// 		$replace,
// 		trim( $html )
// 	);

// 	// while ( str_contains( $html, '  ' ) ) {
// 	// 	$html = str_replace( '  ', ' ', $html );
// 	// }

// 	$html = str_replace( 'sspan', 'span', $html );

// 	return $html;
// }


// function wplng_ob_callback_ajax( $output ) {

// 	error_log( $output );

// 	return $output;
// }


function wplng_init() {

	// error_log( var_export( defined( 'DOING_AJAX' ) && DOING_AJAX, true ) );
	// if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
	// 	ob_start( 'wplng_ob_callback_ajax' );
	// 	return;
	// }

	if ( wplng_get_language_website_id() === wplng_get_language_current_id() ) {
		return;
	}

	global $wplng_request_uri;

	$current_path = $wplng_request_uri;
	$origin_path  = '/' . substr( $current_path, 4, strlen( $current_path ) - 1 );

	if ( ! wplng_url_is_translatable() ) {
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
