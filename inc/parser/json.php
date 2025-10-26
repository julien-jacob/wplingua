<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * wpLingua Parser : Get texts in an JSON
 *
 * @param string $json
 * @param array  $parents
 * @return array Texts
 */
function wplng_parse_json( $json, $parents = array() ) {

	if ( empty( $json ) ) {
		return array();
	}

	$json_decoded = json_decode( $json, true );

	if ( empty( $json_decoded ) || ! is_array( $json_decoded ) ) {
		return array();
	}

	$texts = wplng_parse_json_array( $json_decoded, $parents );

	return $texts;
}


/**
 * wpLingua Parser : Get texts in an array decoded from a JSON
 *
 * @param array $json_decoded
 * @param array $parents
 * @return array Texts
 */
function wplng_parse_json_array( $json_decoded, $parents = array() ) {

	$texts = array();

	/**
	 * Don't parse JSON if it's exclude
	 */

	if ( wplng_json_element_is_excluded( $json_decoded, $parents ) ) {
		return array();
	}

	/**
	 * Parse each JSON elements
	 */

	foreach ( $json_decoded as $key => $value ) {

		$current_parents = array_merge( $parents, array( $key ) );

		/**
		 * Don't parse element if it's exclude
		 */

		if ( wplng_json_element_is_excluded( $value, $current_parents ) ) {
			continue;
		}

		/**
		 * Check the value
		 */

		if ( is_array( $value ) ) {

			/**
			 * If element is an array, parse it
			 */

			$texts = array_merge(
				$texts,
				wplng_parse_json_array( $value, $current_parents )
			);

		} elseif ( is_string( $value ) ) {

			/**
			 * If element is a string
			 */

			// Ignore if is an URL or a local ID (fr_FR, fr, FR, ...)
			if ( wplng_str_is_url( $value )
				|| wplng_str_is_locale_id( $value )
			) {
				continue;
			}

			if ( wplng_str_is_html( $value ) ) {

				/**
				 * If element is a HTML, parse it
				 */

				$texts = array_merge(
					$texts,
					wplng_parse_html( $value )
				);

			} elseif ( wplng_str_is_json( $value ) ) {

				/**
				 * If element is a JSON, parse it
				 */

				$texts = array_merge(
					$texts,
					wplng_parse_json( $value, $current_parents )
				);

			} else {

				/**
				 * Element is a unknow string, check if it's translatable
				 */

				if ( ! wplng_text_is_translatable( $value )
					|| ! wplng_json_element_is_included( $value, $current_parents )
				) {
					continue;
				}

				$texts[] = $value;
			}
		}
	}

	return $texts;
}
