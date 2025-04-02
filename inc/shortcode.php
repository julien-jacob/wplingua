<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * wpLingua Shortcode : [wplng_notranslate]
 *
 * @param array  $atts
 * @param string $content
 * @return string
 */
function wplng_shortcode_notranslate( $atts, $content ) {

	$html  = '<span class="notranslate">';
	$html .= wp_kses_post( $content );
	$html .= '</span>';

	return $html;
}


/**
 * wpLingua Shortcode : [wplng_only]
 *
 * @param array  $atts
 * @param string $content
 * @return string
 */
function wplng_shortcode_only( $atts, $content ) {

	$languages        = array();
	$language_current = wplng_get_language_current_id();

	$attributes = shortcode_atts(
		array(
			'lang' => false,
		),
		$atts
	);

	switch ( $attributes['lang'] ) {
		case false:
			return '';
			break;

		case 'translated':
			$languages = wplng_get_languages_target_ids();
			break;

		case 'original':
			$languages = array( wplng_get_language_website_id() );
			break;

		default:
			$languages_attr = explode( ',', $attributes['lang'] );

			foreach ( $languages_attr as $language ) {
				$language = trim( $language );
				if ( wplng_is_valid_language_id( $language ) ) {
					$languages[] = $language;
				}
			}
			break;
	}

	if ( empty( $languages )
		|| ! in_array( $language_current, $languages )
	) {
		return '';
	}

	$content = wp_kses_post( $content );
	$content = do_shortcode( $content );

	$html  = '<span class="notranslate">';
	$html .= $content;
	$html .= '</span>';

	return $html;
}


/**
 * wpLingua Shortcode : [wplng_switcher]
 *
 * @param array $atts
 * @return string
 */
function wplng_shortcode_switcher( $atts ) {

	$attributes = shortcode_atts(
		array(
			'insert' => false,
			'style'  => false,
			'title'  => false,
			'theme'  => false,
			'flags'  => false,
			'class'  => false,
		),
		$atts
	);

	if ( ! empty( $attributes['class'] ) ) {
		$attributes['class'] .= ' insert-shortcode';
	} else {
		$attributes['class'] = 'insert-shortcode';
	}

	return wplng_get_switcher_html( $attributes );
}
