<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * wpLingua translate : Get translated HTML
 *
 * @param string $html
 * @param array  $args
 * @return string
 */
function wplng_translate_html_loading( $html, $args = array() ) {

	/**
	 * Set JSON header
	 */

	header( 'Content-Type: application/json' );

	/**
	 * Check HTML
	 */

	if ( empty( $html ) ) {
		return wp_json_encode(
			array(
				'wplingua_error' => 'Empty HTML',
			)
		);
	}

	/**
	 * Create the dom element
	 */

	$dom = wplng_sdh_str_get_html( $html );

	if ( empty( $dom ) ) {
		return wp_json_encode(
			array(
				'wplingua_error' => 'Empty DOM',
			)
		);
	}

	/**
	 * Replace excluded HTML part by tag
	 */

	$excluded_elements = array();

	$dom = wplng_dom_exclusions_put_tags( $dom, $excluded_elements );

	/**
	 * Update args and get all texts in HTML if needed
	 */

	wplng_args_setup( $args );

	if ( empty( $args['translations'] ) ) {
		$texts = wplng_parse_html( $dom );
		wplng_args_update_from_texts( $args, $texts );
	}

	/**
	 * Create the html of message bar
	 */

	$number_of_texts       = $args['count_texts'] + 1;
	$numer_of_unknow_texts = $args['count_texts_unknow'] + 1;

	// Calculate percentage
	$percentage = (int) ( 100 - ( ( $numer_of_unknow_texts / $number_of_texts ) * 100 ) );

	if ( $percentage < 1 ) {
		$percentage = 1;
	}

	// Make the load and reload URL

	$url_load   = '';
	$url_reload = '';

	if ( $numer_of_unknow_texts > 20
		&& ! wplng_get_api_overloaded()
		&& wplng_api_feature_is_allow( 'detection' )
		&& ! empty( $args['translations'] )
	) {

		$url_load = add_query_arg(
			array(
				'wplng-load' => 'loading',
				'wplng-mode' => $args['mode'],
				'nocache'    => (string) time() . (string) rand( 100, 999 ),
			),
			$args['url_current']
		);

	} else {

		$url_reload = $args['url_current'];

		if ( $args['mode'] !== 'vanilla' ) {
			$url_reload = add_query_arg(
				'wplng-mode',
				$args['mode'],
				$url_reload
			);
		}
	}

	/**
	 * Update the dom
	 */

	return wp_json_encode(
		array(
			'wplingua_load' => 'loading',
			'url_reload'    => $url_reload,
			'url_load'      => $url_load,
			'percentage'    => $percentage,
			'translations'  => $args['translations'],
		)
	);
}
