<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_shortcode_notranslate( $atts, $content ) {

	$attributes = shortcode_atts(
		array(
			'tag' => 'span',
		), $atts
	);

	$html  = '<' . $attributes['tag'] . ' class="notranslate">';
	$html .= $content;
	$html .= '</' . $attributes['tag'] . '>';

	return $html;
}


function wplng_shortcode_switcher( $atts ) {

	$attributes = shortcode_atts(
		array(
			'insert' => false,
			'style'  => false,
			'title'  => false,
			'theme'  => false,
			'flags'  => false,
			'class'  => false,
		), $atts
	);

	if ( ! empty( $attributes['class'] ) ) {
		$attributes['class'] .= ' insert-shortcode';
	} else {
		$attributes['class'] = 'insert-shortcode';
	}

	return wplng_get_switcher_html( $attributes );
}
