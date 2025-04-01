<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * wpLingua translate : Get translated JSON
 *
 * @param string $json
 * @param array  $args
 * @return string
 */
function wplng_translate_json( $json, $args = array() ) {

	if ( empty( $json ) ) {
		return $json;
	}

	$json_translated = '';
	$json_decoded    = json_decode( $json, true );

	if ( empty( $json_decoded ) || ! is_array( $json_decoded ) ) {
		return $json;
	}

	/**
	 * Update args
	 */

	wplng_args_setup( $args );

	if ( empty( $args['translations'] ) ) {

		$texts = wplng_parse_json_array(
			$json_decoded,
			$args['parents']
		);

		wplng_args_update_from_texts(
			$args,
			$texts
		);
	}

	/**
	 * Translate JSON
	 */

	$json_translated = wp_json_encode(
		wplng_translate_json_array(
			$json_decoded,
			$args
		)
	);

	if ( empty( $json_translated ) ) {
		return $json;
	}

	return $json_translated;
}


/**
 * wpLingua translate : Get translated array from decoded JSON
 *
 * @param array $json_decoded
 * @param array $translations
 * @param array $parents
 * @return array
 */
function wplng_translate_json_array( $json_decoded, $args = array() ) {

	$array_translated = $json_decoded;
	$json_excluded    = wplng_data_excluded_json();

	/**
	 * Update args
	 */

	wplng_args_setup( $args );

	if ( empty( $args['translations'] ) ) {

		$texts = wplng_parse_json_array(
			$json_decoded,
			$args['parents']
		);

		wplng_args_update_from_texts(
			$args,
			$texts
		);
	}

	/**
	 * Don't parse JSON if it's exclude
	 */
	if ( in_array( $args['parents'], $json_excluded ) ) {

		if ( true === WPLNG_DEBUG_JSON ) {

			$debug = array(
				'title'   => 'wpLingua JSON debug - Excluded parent',
				'parents' => $args['parents'],
				'value'   => $json_decoded,
			);

			error_log(
				var_export(
					$debug,
					true
				)
			);
		}

		return $json_decoded;
	}

	/**
	 * Parse each JSON elements
	 */

	foreach ( $json_decoded as $key => $value ) {

		/**
		 * Don't parse element if it's exclude
		 */

		if ( in_array( array_merge( $args['parents'], array( $key ) ), $json_excluded ) ) {

			if ( true === WPLNG_DEBUG_JSON ) {

				$debug = array(
					'title'   => 'wpLingua JSON debug - Excluded element',
					'parents' => array_merge( $args['parents'], array( $key ) ),
					'value'   => $value,
				);

				error_log(
					var_export(
						$debug,
						true
					)
				);
			}

			continue;
		}

		if ( is_array( $value ) ) {

			/**
			 * If element is an array, parse it
			 */

			$args['parents'] = array_merge( $args['parents'], array( $key ) );

			$array_translated[ $key ] = wplng_translate_json_array(
				$value,
				$args
			);

			$args['parents'] = array_splice( $args['parents'], 0, -1 );

		} elseif ( is_string( $value ) ) {

			$debug_type = '';

			/**
			 * If element is a string
			 */

			if ( wplng_str_is_locale_id( $value ) ) {

				/**
				 * If is a local ID (fr_FR, fr, FR, ...), replace by current
				 */

				$debug_type               = 'String - Local ID';
				$array_translated[ $key ] = $args['language_target'];

			} elseif ( wplng_str_is_json( $value ) ) {

				/**
				 * If element is a JSON, parse and translate it
				 */

				$debug_type      = 'String - JSON';
				$args['parents'] = array_merge( $args['parents'], array( $key ) );

				$array_translated[ $key ] = wplng_translate_json(
					$value,
					$args
				);

			} elseif ( wplng_str_is_html( $value ) ) {

				/**
				 * If element is a HTML, parse and translate it
				 */

				$debug_type               = 'String - HTML';
				$array_translated[ $key ] = wplng_translate_html(
					$value,
					$args
				);

			} elseif ( wplng_str_is_url( $value ) ) {

				/**
				 * If element is an URL, replace by translated URL
				 */

				$debug_type               = 'String - URL';
				$array_translated[ $key ] = wplng_url_translate( $value );

			} else {

				/**
				 * Element is a unknow string, check if it's translatable
				 * - Check if is an excluded element
				 * - Check if is an included element
				 * - Check if is a translatable string
				 */

				$is_translatable = wplng_json_element_is_translatable(
					$value,
					array_merge( $args['parents'], array( $key ) )
				);

				if ( $is_translatable ) {
					$debug_type               = 'String - Translatale';
					$array_translated[ $key ] = wplng_get_translated_text_from_translations(
						$value,
						$args['translations']
					);
				} else {
					$debug_type = 'String - Untranslatale';
				}
			}

			/**
			 * Print debug data in debug.log file
			 */

			if ( true === WPLNG_DEBUG_JSON
				&& isset( $json_decoded[ $key ] )
				&& isset( $array_translated[ $key ] )
			) {

				$debug = array(
					'title'   => 'wpLingua JSON debug',
					'parents' => array_merge( $args['parents'], array( $key ) ),
					'type'    => $debug_type,
					'value'   => $json_decoded[ $key ],
				);

				if ( $json_decoded[ $key ] !== $array_translated[ $key ] ) {
					$debug['translated'] = $array_translated[ $key ];
				}

				error_log(
					var_export(
						$debug,
						true
					)
				);
			}
		}
	}

	return $array_translated;
}
