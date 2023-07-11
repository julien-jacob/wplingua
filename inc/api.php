<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_api_request_free_api_key( $data ) {

	if ( empty( $data['r'] )
		|| $data['r'] !== 'register'
		|| empty( $data['mail_address'] )
		|| empty( $data['website'] )
		|| empty( $data['language_original'] )
		|| ! wplng_is_valid_language_id( $data['language_original'] )
		|| empty( $data['languages_target'] )
		|| ! wplng_is_valid_language_id( $data['languages_target'] )
		|| empty( $data['accept_eula'] )
		|| $data['accept_eula'] !== true
		|| $data['language_original'] === $data['languages_target']
	) {
		return '1';
	}

	/**
	 * Set default options
	 */
	// Set option for website language
	// $language_website = wplng_get_language_by_id( $data['language_original'] );
	// if ( ! empty( $language_website['id'] ) ) {
	// 	update_option( 'wplng_website_language', $language_website['id'] );
	// }
	// if ( ! empty( $language_website['flag'] ) ) {
	// 	update_option( 'wplng_website_flag', $language_website['flag'] );
	// }

	// Set option for target language
	$language_target = wplng_get_language_by_id( $data['languages_target'] );
	if ( ! empty( $language_target['id'] ) && ! empty( $language_target['flag'] ) ) {
		update_option(
			'wplng_website_language',
			wp_json_encode(
				array(
					'id'   => $language_target['id'],
					'flag' => $language_target['flag'],
				)
			)
		);
	}

	$body = array(
		'r'                 => 'register',
		'website'           => $data['website'],
		'mail_address'      => $data['mail_address'],
		'language_original' => $data['language_original'],
		'languages_target'  => $data['languages_target'],
		'accept_eula'       => true,
	);
	$args = array(
		'method'    => 'POST',
		'timeout'   => 5,
		'sslverify' => false,
		'body'      => $body,
	);

	// error_log( var_export( $body, true ) );

	$request = wp_remote_post( WPLNG_API, $args );

	if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		error_log( print_r( $request, true ) );
		return false;
	}

	$response = json_decode( wp_remote_retrieve_body( $request ), true );

	// if ( ! empty( $response['error'] ) ) {
	// 	// TODO : Check for remove or update
	// 	return '2';
	// }

	return (string) wp_remote_retrieve_body( $request );
}


function wplng_validate_api_key( $api_key = '' ) {

	if ( empty( $api_key ) ) {
		$api_key = wplng_get_api_key();
	}

	// TODO : Check API key format

	// if (empty($api_key)) {
	// 	$api_key = wplng_get_api_key();
	// }

	$body = array(
		'r'       => 'api_key',
		'api_key' => $api_key,
	);
	$args = array(
		'method'    => 'POST',
		'timeout'   => 5,
		'sslverify' => false,
		'body'      => $body,
	);

	// error_log( var_export( $body, true ) );

	$request = wp_remote_post( WPLNG_API, $args );

	if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		error_log( print_r( $request, true ) );
		return false;
	}

	$response = json_decode( wp_remote_retrieve_body( $request ), true );

	if ( ! empty( $response['error'] ) ) {
		// TODO : Check for remove or update
		return false;
	}

	return (string) wp_remote_retrieve_body( $request );
}


function wplng_translate( $text, $language_source_id = '', $language_target_id = '' ) {

	// TODO : Replacer par ternaire
	if ( empty( $language_target_id ) ) {
		$language_target_id = wplng_get_language_current_id();
	}

	// TODO : Replacer par ternaire
	if ( empty( $language_source_id ) ) {
		$language_source_id = wplng_get_language_website_id();
	}

	$body = array(
		'api-key' => '1111111111111111',
		'r'       => 'translate',
		'source'  => $language_source_id,
		'target'  => $language_target_id,
		'text'    => $text,
	);
	$args = array(
		'method'    => 'POST',
		'timeout'   => 5,
		'sslverify' => false,
		'body'      => $body,
	);

	// error_log( var_export( $body, true ) );

	$request = wp_remote_post( WPLNG_API, $args );

	if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		error_log( print_r( $request, true ) );
		return $text;
	}

	$response = json_decode( wp_remote_retrieve_body( $request ), true );

	if ( ! isset( $response['translation'] ) ) {
		// TODO : Check for remove or update
		return $text;
	}

	return (string) $response['translation'];
}


function wplng_parser_clear_html( $html, $translations = array() ) {

	$selector_clear   = wplng_get_selector_clear();
	$selector_exclude = wplng_get_selector_exclude();

	/**
	 * Clear HTML for API call
	 */

	// Remove comments from HTML Clear
	$html_clear = preg_replace( '#<!--.*-->#Uis', '', $html );

	// Remove useless and excluded elements from HTML clear
	$dom = str_get_html( $html_clear );

	if ( $dom === false ) {
		return $html;
	}

	$selector_to_remove = array_merge( $selector_exclude, $selector_clear );

	foreach ( $selector_to_remove as $key => $selector ) {
		foreach ( $dom->find( $selector ) as $element ) {
			$element->outertext = '';
		}
	}

	$dom->save();
	$html_clear = (string) str_get_html( $dom );

	// Clear HTML from multiple space and tab
	$html_clear = preg_replace( '#\s+#', ' ', $html_clear );

	// Clear HTML from useless attributes
	$html_clear = preg_replace( '# (src|srcset|rel|class|href|target|itemscope|style|name|media|loading|decoding|role|height|width|itemprop|type|itemtype|sizes|onchange|onclick|datetime|selected|value)=(\"|\').*(\"|\')#Uis', '', $html_clear );

	/**
	 * Remove saved translation from HTML clear
	 */
	foreach ( $translations as $translation ) {

		// Check if translaton data is valid
		if (
			! isset( $translation['source'] ) // Original text
			|| ! isset( $translation['translation'] ) // Translater text
			|| ! isset( $translation['sr'] ) // Search Replace
		) {
			continue;
		}

		foreach ( $translation['sr'] as $key => $sr ) {

			$regex = str_replace(
				'WPLNG',
				preg_quote( $translation['source'] ),
				$sr['search']
			);

			$replace = str_replace(
				'WPLNG',
				'',
				$sr['replace']
			);

			// Replace knowing translation by empty string
			$html_clear = preg_replace(
				$regex,
				$replace,
				$html_clear
			);

		}
	}
	$html_clear = preg_replace( '#>\s*<#Uis', '><', $html_clear );

	return $html_clear;
}


function wplng_parser( $html, $language_source_id = '', $language_target_id = '', $translations = array() ) {

	$html = wplng_parser_clear_html( $html, $translations );

	if ( empty( $html ) ) {
		return array();
	}

	// TODO : Replacer par ternaire
	if ( empty( $language_target_id ) ) {
		$language_target_id = wplng_get_language_current_id();
	}

	// TODO : Replacer par ternaire
	if ( empty( $language_source_id ) ) {
		$language_source_id = wplng_get_language_website_id();
	}

	$body = array(
		'api-key' => '1111111111111111',
		'r'       => 'parser',
		'source'  => $language_source_id,
		'target'  => $language_target_id,
		'text'    => $html,
	);
	$args = array(
		'method'    => 'POST',
		'timeout'   => 120,
		'sslverify' => false,
		'body'      => $body,
	);

	$request = wp_remote_post( WPLNG_API, $args );

	if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		error_log( print_r( $request, true ) );
		return array();
	}

	$response = json_decode( wp_remote_retrieve_body( $request ), true );

	if ( null == $response ) {
		// TODO : Bad Json - Error log
		return array();
	}

	return $response;
}
