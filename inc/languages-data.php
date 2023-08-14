<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function wplng_get_languages_data() {
	return array(
		array(
			'name'  => __( 'English', 'wplingua' ),
			'id'    => 'en',
			'flag'  => 'en',
			'emoji' => '🇬🇧',
			'flags' => array(
				array(
					'name'  => __( 'United Kingdom', 'wplingua' ),
					'id'    => 'en',
					'flag'  => 'en',
					'emoji' => '🇬🇧',
				),
				array(
					'name'  => __( 'USA', 'wplingua' ),
					'id'    => 'us',
					'flag'  => 'us',
					'emoji' => '🇺🇸',
				),
			),
		),
		array(
			'name'  => __( 'French', 'wplingua' ),
			'id'    => 'fr',
			'flag'  => 'fr',
			'emoji' => '🇫🇷',
			'flags' => array(
				array(
					'name'  => __( 'France', 'wplingua' ),
					'id'    => 'fr',
					'flag'  => 'fr',
					'emoji' => '🇫🇷',
				),
				array(
					'name'  => __( 'Belgium', 'wplingua' ),
					'id'    => 'be',
					'flag'  => 'be',
					'emoji' => '🇧🇪',
				),
			),
		),
		array(
			'name'  => __( 'German', 'wplingua' ),
			'id'    => 'de',
			'flag'  => 'de',
			'emoji' => '🇩🇪',
			'flags' => array(
				array(
					'name'  => __( 'Germany', 'wplingua' ),
					'id'    => 'de',
					'flag'  => 'de',
					'emoji' => '🇩🇪',
				),
			),
		),
		array(
			'name'  => __( 'Italian', 'wplingua' ),
			'id'    => 'it',
			'flag'  => 'it',
			'emoji' => '🇮🇹',
			'flags' => array(
				array(
					'name'  => __( 'Italy', 'wplingua' ),
					'id'    => 'it',
					'flag'  => 'it',
					'emoji' => '🇮🇹',
				),
			),
		),
		array(
			'name'  => __( 'Portuguese', 'wplingua' ),
			'id'    => 'pt',
			'flag'  => 'pt',
			'emoji' => '🇵🇹',
			'flags' => array(
				array(
					'name'  => __( 'Portugal', 'wplingua' ),
					'id'    => 'pt',
					'flag'  => 'pt',
					'emoji' => '🇵🇹',
				),
				array(
					'name'  => __( 'Brazil', 'wplingua' ),
					'id'    => 'br',
					'flag'  => 'br',
					'emoji' => '🇧🇷',
				),
			),
		),
		array(
			'name'  => __( 'Spanish', 'wplingua' ),
			'id'    => 'es',
			'flag'  => 'es',
			'emoji' => '🇪🇸',
			'flags' => array(
				array(
					'name'  => __( 'Spain', 'wplingua' ),
					'id'    => 'es',
					'flag'  => 'es',
					'emoji' => '🇪🇸',
				),
				array(
					'name'  => __( 'Mexico', 'wplingua' ),
					'id'    => 'mx',
					'flag'  => 'mx',
					'emoji' => '🇲🇽',
				),
			),
		),
		array(
			'name'  => __( 'Japanese', 'wplingua' ),
			'id'    => 'ja',
			'flag'  => 'ja',
			'emoji' => '🇯🇵',
			'flags' => array(
				array(
					'name'  => __( 'Japan', 'wplingua' ),
					'id'    => 'ja',
					'flag'  => 'ja',
					'emoji' => '🇯🇵',
				),
			),
		),
		array(
			'name'  => __( 'Russian', 'wplingua' ),
			'id'    => 'ru',
			'flag'  => 'ru',
			'emoji' => '🇷🇺',
			'flags' => array(
				array(
					'name'  => __( 'Russia', 'wplingua' ),
					'id'    => 'ru',
					'flag'  => 'ru',
					'emoji' => '🇷🇺',
				),
			),
		),
		array(
			'name'  => __( 'Chinese', 'wplingua' ),
			'id'    => 'zh',
			'flag'  => 'zh',
			'emoji' => '🇨🇳',
			'flags' => array(
				array(
					'name'  => __( 'China', 'wplingua' ),
					'id'    => 'zh',
					'flag'  => 'zh',
					'emoji' => '🇨🇳',
				),
				array(
					'name'  => __( 'Hong Kong', 'wplingua' ),
					'id'    => 'hk',
					'flag'  => 'hk',
					'emoji' => '🇭🇰',
				),
			),
		),
	);
}
