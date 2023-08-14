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
			'name_translation' => array(
				'en' => 'French',
				'fr' => 'Français',
				'de' => 'Französisch',
				'it' => 'Francese',
				'pt' => 'francês',
				'es' => 'Francés',
				'ja' => 'フレンチ',
				'ru' => 'Французский',
				'zh' => '法语',
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
			'name_translation' => array(
				'en' => 'Italian',
				'fr' => 'Italien',
				'de' => 'Italienisch',
				'it' => 'Italiano',
				'pt' => 'italiano',
				'es' => 'Italiano',
				'ja' => 'イタリア',
				'ru' => 'Итальянский',
				'zh' => '意大利',
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
			'name_translation' => array(
				'en' => 'Spanish',
				'fr' => 'Espagnol',
				'de' => 'Spanisch',
				'it' => 'Spagnolo',
				'pt' => 'espanhol',
				'es' => 'Español',
				'ja' => 'スペイン',
				'ru' => 'Испанский',
				'zh' => '英语',
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
