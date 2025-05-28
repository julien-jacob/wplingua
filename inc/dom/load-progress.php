<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Modify dom for the "in progress" load mode
 *
 * @param object $dom
 * @param array  $args
 * @return object
 */
function wplng_dom_load_progress( $dom, $args ) {

	wplng_args_setup( $args );

	if ( 'disabled' === $args['load'] ) {

		return $dom;

	} elseif ( 'loading' === $args['load'] ) {

		/**
		 * Current page identified as launching in hidden iframe in "in progress" mode
		 */

		$html  = '<!DOCTYPE html>' . PHP_EOL;
		$html .= '<html lang="en">' . PHP_EOL;
		$html .= '	<head>' . PHP_EOL;
		$html .= '		<meta charset="UTF-8">' . PHP_EOL;
		$html .= '		<title>Translations load</title>' . PHP_EOL;
		$html .= '	</head>' . PHP_EOL;
		$html .= '	<body>' . PHP_EOL;
		$html .= '		<h1>Translations load</h1>' . PHP_EOL;
		$html .= '	</body>' . PHP_EOL;
		$html .= '</html>';

		$dom = wplng_sdh_str_get_html( $html );

		return $dom;

	} elseif ( 'enabled' === $args['load'] ) {

		/**
		 * Current page identified as requiring “in progress” mode
		 */

		$redirect_query_arg = array();
		$redirect_needed    = false;

		if ( $args['mode'] !== 'vanilla' ) {
			$redirect_query_arg['wplng-mode'] = $args['mode'];
			$redirect_needed                  = true;
		}

		if ( ! wplng_get_api_overloaded() ) {

			$redirect_query_arg['wplng-load']    = 'progress';
			$redirect_query_arg['nocache'] = (string) time() . (string) rand( 100, 999 );
			$redirect_needed                     = true;
		}

		if ( $redirect_needed ) {
			wp_safe_redirect(
				add_query_arg(
					$redirect_query_arg,
					$args['url_current']
				),
				302
			);
			exit;
		}

		return $dom;

	}

	/**
	 * Add effect on unknow texts and translate know texts
	 */

	$edit_link_excluded = wplng_data_excluded_editor_link();
	$node_text_excluded = wplng_data_excluded_node_text();

	foreach ( $dom->find( 'body text' ) as $element ) {

		if ( in_array( $element->parent->tag, $edit_link_excluded )
			|| in_array( $element->parent->tag, $node_text_excluded )
		) {
			continue;
		}

		$text = $element->innertext;

		if ( empty( trim( $text ) ) ) {
			continue;
		}

		// Manage non breaking space
		$text = str_replace(
			array( '&nbsp;', html_entity_decode( '&nbsp;' ) ),
			array( ' ', ' ' ),
			$text
		);

		/**
		 * Get spaces before and after text
		 */

		$temp          = array();
		$spaces_before = '';
		$spaces_after  = '';

		preg_match( '/^(\s*).*/', $text, $temp );
		if ( ! empty( $temp[1] ) ) {
			$spaces_before = $temp[1];
		}

		preg_match( '/.*(\s*)$/U', $text, $temp );
		if ( ! empty( $temp[1] ) ) {
			$spaces_after = $temp[1];
		}

		$text       = wplng_text_esc( $text );
		$translated = '';

		if ( wplng_text_is_translatable( $text ) ) {

			foreach ( $args['translations'] as $translation ) {
				if ( $text === $translation['source'] ) {
					$translated = $translation['translation'];
					break;
				}
			}

			if ( '' === $translated ) {

				$innertext  = '<span';
				$innertext .= ' class="wplng-in-progress-text"';
				$innertext .= ' title="' . esc_attr__( 'Translation in progress', 'wplingua' ) . '"';
				$innertext .= '>';
				$innertext .= esc_html( $spaces_before . $text . $spaces_after );
				$innertext .= '</span>';

				$element->innertext = $innertext;

			} else {
				$element->innertext = esc_html( $spaces_before . $translated . $spaces_after );
			}
		}
	}

	/**
	 * Create the html of message bar
	 */

	$number_of_texts           = (int) $args['count_texts'] + 1;
	$numer_of_translated_texts = count( $args['translations'] );
	$numer_of_unknow_texts     = (int) $number_of_texts - $numer_of_translated_texts;

	// Calculate percentage

	$percentage = (int) ( ( $numer_of_translated_texts / $number_of_texts ) * 100 );

	if ( $percentage < 1 ) {
		$percentage = 1;
	}

	// Make the reload URL

	$url_reload = $args['url_current'];

	if ( $args['mode'] !== 'vanilla' ) {
		$url_reload = add_query_arg(
			'wplng-mode',
			$args['mode'],
			$url_reload
		);
	}

	if ( $numer_of_unknow_texts > 20
		&& ! wplng_get_api_overloaded()
		&& wplng_api_feature_is_allow( 'detection' )
	) {

		$url_reload = add_query_arg(
			array(
				'wplng-load'    => 'progress',
				'nocache' => (string) time() . (string) rand( 100, 999 ),
			),
			$url_reload
		);

	}

	$html  = '<div';
	$html .= ' id="wplng-in-progress-container"';
	$html .= ' wplng-reload="' . esc_url( $url_reload ) . '"';
	$html .= '>';

	$html .= '<div id="wplng-in-progress-message">';
	$html .= '<span class="dashicons dashicons-update wplng-spin"></span> ';

	$html .= '<span id="wplng-in-progress-text-mobile">';
	$html .= esc_html__( 'Translation in progress', 'wplingua' );
	$html .= '</span>';

	$html .= '<span id="wplng-in-progress-text-desktop">';
	$html .= esc_html__( 'In progress: Translation and saving of new texts', 'wplingua' );
	$html .= '</span>';

	$html .= ' - ';
	$html .= '<span id="wplng-in-progress-percent">';
	$html .= esc_html( $percentage );
	$html .= '</span>';
	$html .= ' %';
	$html .= '</div>'; // End #wplng-translation-in-progress

	$html .= '<div id="wplng-progress-bar">';
	$html .= '<div';
	$html .= ' id="wplng-progress-bar-value"';
	$html .= ' style="width: ' . esc_attr( $percentage ) . '%"';
	$html .= '>';
	$html .= '</div>'; // End #wplng-progress-bar-value
	$html .= '</div>'; // End #wplng-progress-bar

	$html .= '</div>'; // End #wplng-in-progress-container

	/**
	 * Create the html of iframe
	 */

	$url_iframe = add_query_arg(
		array(
			'wplng-load'    => 'loading',
			'nocache' => (string) time() . (string) rand( 100, 999 ),
		),
		$args['url_current']
	);

	$html .= '<iframe';
	$html .= ' id="wplng-in-progress-iframe"';
	$html .= ' src="' . esc_url( $url_iframe ) . '"';
	$html .= ' style="display: none !important;"';
	$html .= '>';
	$html .= '</iframe>'; // End #wplng-translation-in-progress
	
	/**
	 * Place the HTML in the end of body
	 */

	foreach ( $dom->find( 'body' ) as $body ) {
		$body->innertext = $body->innertext . $html;
	}

	return $dom;
}
