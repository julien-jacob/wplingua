<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_dom_load_progress( $dom, $args ) {

	wplng_args_setup( $args );

	if ( 'disabled' === $args['load'] ) {

		return $dom;

	} elseif ( 'loading' === $args['load'] ) {

		$html  = '<!DOCTYPE html>';
		$html .= '<html lang="en">';
		$html .= '<head>';
		$html .= '<meta charset="UTF-8">';
		$html .= '<title>Translations load</title>';
		$html .= '</head>';
		$html .= '<body>';
		$html .= '<h1>Translations load</h1>';
		$html .= '</body>';
		$html .= '</html>';

		$dom = wplng_sdh_str_get_html( $html );

		return $dom;

	} elseif ( 'enabled' === $args['load'] ) {

		$redirect_query_arg = array();

		if ( $args['mode'] !== 'vanilla' ) {
			$redirect_query_arg['wplng-mode'] = $args['mode'];
		}

		$redirect_query_arg['wplng-load']    = 'progress';
		$redirect_query_arg['wplng-nocache'] = (string) time() . (string) rand( 100, 999 );

		wp_safe_redirect(
			add_query_arg(
				$redirect_query_arg,
				$args['url_current']
			),
			302
		);
		exit;

	}

	/**
	 * Add effect on unknow texts and translate know texts
	 */

	$edit_link_excluded = wplng_data_excluded_editor_link();
	$node_text_excluded = wplng_data_excluded_node_text();

	foreach ( $dom->find( 'body text' ) as $element ) {

		$text = wplng_text_esc( $element->innertext );

		if ( ! wplng_text_is_translatable( $text ) ) {
			continue;
		}

		$text_translated = '';

		foreach ( $args['translations'] as $translation ) {

			$source = wplng_text_esc( $translation['source'] );

			if ( $text === $source ) {
				$text_translated = $translation['translation'];
				break;
			}
		}

		if ( '' === $text_translated
			|| in_array( $element->parent->tag, $edit_link_excluded )
			|| in_array( $element->parent->tag, $node_text_excluded )
		) {

			$innertext  = '<span ';
			$innertext .= 'class="wplng-in-progress-text" ';
			$innertext .= 'title="' . esc_attr__( 'Translation in progress', 'wplingua' ) . '">';
			$innertext .= esc_html( $text );
			$innertext .= '</span>';

			$element->innertext = $innertext;

		} else {
			$element->innertext = esc_html( $text_translated );
		}
	}

	/**
	 * Create the html of message bar
	 */

	$number_of_texts           = (int) $args['count_texts'] + 1;
	$numer_of_translated_texts = count( $args['translations'] );
	$numer_of_unknow_texts     = (int) $number_of_texts - $numer_of_translated_texts;

	$percentage = (int) ( ( $numer_of_translated_texts / $number_of_texts ) * 100 );

	$html = '<div id="wplng-in-progress-container">';

	$html .= '<div id="wplng-in-progress-message">';
	$html .= '<span class="dashicons dashicons-update wplng-spin"></span> ';
	$html .= esc_html__( 'Translation in progress', 'wplingua' );
	$html .= ' - ';
	$html .= esc_html( $percentage );
	$html .= ' %';
	$html .= '</div>'; // End #wplng-translation-in-progress

	$html .= '<div id="wplng-progress-bar">';
	$html .= '<div id="wplng-progress-bar-value" ';
	$html .= 'style="width: ' . esc_attr( $percentage ) . '%">';
	$html .= '</div>'; // End #wplng-progress-bar-value
	$html .= '</div>'; // End #wplng-progress-bar

	$html .= '</div>'; // End #wplng-in-progress-container

	/**
	 * Create the html of iframe
	 */

	$url_reload = $args['url_current'];

	if ( $args['mode'] !== 'vanilla' ) {
		$url_reload = add_query_arg(
			'wplng-mode',
			$args['mode'],
			$url_reload
		);
	}

	if ( $numer_of_unknow_texts > 20 ) {

		$url_reload = add_query_arg(
			array(
				'wplng-load'    => 'progress',
				'wplng-nocache' => (string) time() . (string) rand( 100, 999 ),
			),
			$url_reload
		);

	}

	$url_iframe = add_query_arg(
		array(
			'wplng-load'    => 'loading',
			'wplng-nocache' => (string) time() . (string) rand( 100, 999 ),
		),
		$args['url_current']
	);

	$html .= '<iframe ';
	$html .= 'id="wplng-in-progress-iframe" ';
	$html .= 'src="' . esc_url( $url_iframe ) . '" ';
	$html .= 'wplng-reload="' . esc_url( $url_reload ) . '" ';
	$html .= 'style="display: none !important;">';
	$html .= '</iframe>'; // End #wplng-translation-in-progress

	/**
	 * Place the HTML in the end of body
	 */

	foreach ( $dom->find( 'body' ) as $body ) {
		$body->innertext = $body->innertext . $html;
	}

	return $dom;
}
