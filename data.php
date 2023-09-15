<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Data : Parce an translate
 */

function wplng_data_excluded_json() {

	return apply_filters(
		'wplng_excluded_json',
		array(
			array( '@context', '@graph', 0, '@type', '@id', 'url', 'name' ),
			array( '@context', '@graph', 0, '@type', '@id', 'url', 'name', 'thumbnailUrl', 'datePublished', 'dateModified', 'description' ),
			array( '@context', '@graph', 2, '@type', '@id', 'itemListElement', 0, '@type', 'name' ),
			array( '@context', '@graph', 2, '@type', '@id', 'itemListElement', 1, '@type', 'name' ),
			array( '@context', '@graph', 3, '@type', '@id', 'url', 'name' ),
			array( '@context', '@graph', 3, '@type', '@id', 'url', 'name', 'description' ),
			array( '@context', '@graph', 4, '@type', '@id', 'name' ),
			array( '@context', '@graph', 4, '@type', '@id', 'name', 'url', 'logo', '@type', 'inLanguage', '@id', 'url', 'contentUrl', 'caption' ),
		)
	);
}


function wplng_data_excluded_editor_link() {
	return apply_filters(
		'wplng_excluded_editor_link',
		array(
			'style',
			'svg',
			'script',
			'canvas',
			'link',
			'textarea',
		)
	);
}


function wplng_data_excluded_node_text() {
	return apply_filters(
		'wplng_excluded_node_text',
		array(
			'style',
			'svg',
			'script',
			'canvas',
			'link',
		)
	);
}


function wplng_data_attr_to_translate() {
	return apply_filters(
		'wplng_attr_to_translate',
		array(
			'alt',
			'title',
			'placeholder',
			'aria-label',
		)
	);
}


/**
 * Data : Switcher options
 */

function wplng_data_switcher_valid_insert() {
	return array(
		'bottom-right'  => __( 'Bottom right', 'wplingua' ),
		'bottom-center' => __( 'Bottom center', 'wplingua' ),
		'bottom-left'   => __( 'Bottom left', 'wplingua' ),
		'none'          => __( 'None', 'wplingua' ),
	);
}


function wplng_data_switcher_valid_theme() {
	// return array(
	// 	'smooth-light' => __( 'Smooth Light', 'wplingua' ),
	// 	'smooth-grey'  => __( 'Smooth Grey', 'wplingua' ),
	// 	'smooth-dark'  => __( 'Smooth Dark', 'wplingua' ),
	// 	'square-light' => __( 'Square Light', 'wplingua' ),
	// 	'square-grey'  => __( 'Square Grey', 'wplingua' ),
	// 	'square-dark'  => __( 'Square Dark', 'wplingua' ),
	// );

	return array(
		'light-double-smooth' => __( 'Light - Double - Smooth', 'wplingua' ),
		'light-double-square' => __( 'Light - Double - Square', 'wplingua' ),
		'grey-double-smooth'  => __( 'Grey - Double - Smooth', 'wplingua' ),
		'grey-double-square'  => __( 'Grey - Double - Square', 'wplingua' ),
		'dark-double-smooth'  => __( 'Dark - Double - Smooth', 'wplingua' ),
		'dark-double-square'  => __( 'Dark - Double - Square', 'wplingua' ),
	);
}


function wplng_data_switcher_valid_style() {
	return array(
		'list'     => __( 'List', 'wplingua' ),
		'block'    => __( 'Block', 'wplingua' ),
		'dropdown' => __( 'Dropdown', 'wplingua' ),
	);
}

function wplng_data_switcher_valid_name_format() {
	return array(
		'name' => __( 'Complete name', 'wplingua' ),
		'id'   => __( 'Language ID', 'wplingua' ),
		'none' => __( 'No display', 'wplingua' ),
	);
}


function wplng_data_switcher_valid_flags_style() {
	return array(
		'circle'      => __( 'Circle', 'wplingua' ),
		'rectangular' => __( 'Rectangular', 'wplingua' ),
		'none'        => __( 'No display', 'wplingua' ),
	);
}


/**
 * Data : Languages
 */

function wplng_data_languages() {
	return array(
		array(
			'name'             => __( 'English', 'wplingua' ),
			'id'               => 'en',
			'flag'             => 'en',
			'emoji'            => '🇬🇧',
			'flags'            => array(
				array(
					'name'  => __( 'UK', 'wplingua' ),
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
			'name_translation' => array(
				'en' => 'English',
				'fr' => 'Anglais',
				'de' => 'Englisch',
				'it' => 'Inglese',
				'pt' => 'Inglês',
				'es' => 'Inglés',
				'ja' => 'イングリッシュ',
				'ru' => 'Английский',
				'zh' => '英语',
			),
		),
		array(
			'name'             => __( 'French', 'wplingua' ),
			'id'               => 'fr',
			'flag'             => 'fr',
			'emoji'            => '🇫🇷',
			'flags'            => array(
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
			'name_translation' => array(
				'en' => 'French',
				'fr' => 'Français',
				'de' => 'Französisch',
				'it' => 'Francese',
				'pt' => 'Francês',
				'es' => 'Francés',
				'ja' => 'フレンチ',
				'ru' => 'Французский',
				'zh' => '法语',
			),
		),
		array(
			'name'             => __( 'German', 'wplingua' ),
			'id'               => 'de',
			'flag'             => 'de',
			'emoji'            => '🇩🇪',
			'flags'            => array(
				array(
					'name'  => __( 'Germany', 'wplingua' ),
					'id'    => 'de',
					'flag'  => 'de',
					'emoji' => '🇩🇪',
				),
			),
			'name_translation' => array(
				'en' => 'German',
				'fr' => 'Allemand',
				'de' => 'Deutsch',
				'it' => 'Tedesco',
				'pt' => 'Alemão',
				'es' => 'Alemán',
				'ja' => 'ドイツ',
				'ru' => 'Немецкий',
				'zh' => '德国',
			),
		),
		array(
			'name'             => __( 'Italian', 'wplingua' ),
			'id'               => 'it',
			'flag'             => 'it',
			'emoji'            => '🇮🇹',
			'flags'            => array(
				array(
					'name'  => __( 'Italy', 'wplingua' ),
					'id'    => 'it',
					'flag'  => 'it',
					'emoji' => '🇮🇹',
				),
			),
			'name_translation' => array(
				'en' => 'Italian',
				'fr' => 'Italien',
				'de' => 'Italienisch',
				'it' => 'Italiano',
				'pt' => 'Italiano',
				'es' => 'Italiano',
				'ja' => 'イタリア',
				'ru' => 'Итальянский',
				'zh' => '意大利',
			),
		),
		array(
			'name'             => __( 'Portuguese', 'wplingua' ),
			'id'               => 'pt',
			'flag'             => 'pt',
			'emoji'            => '🇵🇹',
			'flags'            => array(
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
			'name_translation' => array(
				'en' => 'Portuguese',
				'fr' => 'Portugais',
				'de' => 'Portugiesisch',
				'it' => 'Portoghese',
				'pt' => 'Português',
				'es' => 'Portugués',
				'ja' => 'ポルトガル',
				'ru' => 'Португальский',
				'zh' => '葡语',
			),
		),
		array(
			'name'             => __( 'Spanish', 'wplingua' ),
			'id'               => 'es',
			'flag'             => 'es',
			'emoji'            => '🇪🇸',
			'flags'            => array(
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
			'name_translation' => array(
				'en' => 'Spanish',
				'fr' => 'Espagnol',
				'de' => 'Spanisch',
				'it' => 'Spagnolo',
				'pt' => 'Espanhol',
				'es' => 'Español',
				'ja' => 'スペイン',
				'ru' => 'Испанский',
				'zh' => '英语',
			),
		),
		array(
			'name'             => __( 'Japanese', 'wplingua' ),
			'id'               => 'ja',
			'flag'             => 'ja',
			'emoji'            => '🇯🇵',
			'flags'            => array(
				array(
					'name'  => __( 'Japan', 'wplingua' ),
					'id'    => 'ja',
					'flag'  => 'ja',
					'emoji' => '🇯🇵',
				),
			),
			'name_translation' => array(
				'en' => 'Japanese',
				'fr' => 'Japonais',
				'de' => 'Japanisch',
				'it' => 'Giapponese',
				'pt' => 'Japonês',
				'es' => 'Japonés',
				'ja' => '日本',
				'ru' => 'Японский',
				'zh' => '日语',
			),
		),
		array(
			'name'             => __( 'Russian', 'wplingua' ),
			'id'               => 'ru',
			'flag'             => 'ru',
			'emoji'            => '🇷🇺',
			'flags'            => array(
				array(
					'name'  => __( 'Russia', 'wplingua' ),
					'id'    => 'ru',
					'flag'  => 'ru',
					'emoji' => '🇷🇺',
				),
			),
			'name_translation' => array(
				'en' => 'Russian',
				'fr' => 'Russe',
				'de' => 'Russisch',
				'it' => 'Russo',
				'pt' => 'Russo',
				'es' => 'Ruso',
				'ja' => 'ロシア',
				'ru' => 'Японский',
				'zh' => '日语',
			),
		),
		array(
			'name'             => __( 'Chinese', 'wplingua' ),
			'id'               => 'zh',
			'flag'             => 'zh',
			'emoji'            => '🇨🇳',
			'flags'            => array(
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
			'name_translation' => array(
				'en' => 'Chinese',
				'fr' => 'Chinois',
				'de' => 'Chinesisch',
				'it' => 'Cinese',
				'pt' => 'Chinês',
				'es' => 'Chino',
				'ja' => 'チャイニーズ',
				'ru' => 'Китайский',
				'zh' => '中文',
			),
		),
	);
}
