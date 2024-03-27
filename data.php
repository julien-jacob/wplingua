<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * ------ Data : Parse and translate ------
 */

 /**
  * Get JSON elements to translate
  *
  * @return array
  */
function wplng_data_json_to_translate() {
	return apply_filters(
		'wplng_json_to_translate',
		array(
			// Plugin : WooCommerce
			array( 'wc_add_to_cart_params', 'i18n_view_cart' ),
			array( 'wc_country_select_params', 'i18n_select_state_text' ),
			array( 'wc_country_select_params', 'i18n_no_matches' ),
			array( 'wc_country_select_params', 'i18n_ajax_error' ),
			array( 'wc_country_select_params', 'i18n_input_too_short_1' ),
			array( 'wc_country_select_params', 'i18n_input_too_short_n' ),
			array( 'wc_country_select_params', 'i18n_input_too_long_1' ),
			array( 'wc_country_select_params', 'i18n_input_too_long_n' ),
			array( 'wc_country_select_params', 'i18n_selection_too_long_1' ),
			array( 'wc_country_select_params', 'i18n_selection_too_long_n' ),
			array( 'wc_country_select_params', 'i18n_load_more' ),
			array( 'wc_country_select_params', 'i18n_searching' ),
			array( 'wc_address_i18n_params', 'i18n_required_text' ),
			array( 'wc_address_i18n_params', 'i18n_optional_text' ),
			// Plugin : YITH
			array( 'yith_wcwl_l10n', 'labels', 'cookie_disabled' ),
			// Plugin : Yoast SEO
			array( '@graph', 0, 'description' ),
		)
	);
}


/**
 * Get JSON to exclude from translation
 *
 * @return array
 */
function wplng_data_excluded_json() {
	return apply_filters(
		'wplng_excluded_json',
		array(
			array( 'wc_country_select_params', 'countries' ),
		)
	);
}


/**
 * Get selectors of excluded elements in visual editor
 *
 * @return array
 */
function wplng_data_excluded_editor_link() {
	return apply_filters(
		'wplng_excluded_editor_link',
		array(
			'textarea',
			'pre',
			'option',
		)
	);
}


/**
 * Get selectors of excluded elements
 *
 * @return array
 */
function wplng_data_excluded_selector_default() {
	return array(
		'style',
		'svg',
		'canvas',
		'address',
		'iframe',
		'code',
		'#wpadminbar',
		'.no-translate',
		'.notranslate',
		'.wplng-switcher',
		'link[hreflang]',
	);
}


/**
 * Get selectors of excluded nodes text
 *
 * @return array
 */
function wplng_data_excluded_node_text() {
	return apply_filters(
		'wplng_excluded_node_text',
		array(
			'style',
			'svg',
			'canvas',
			'link',
			'script',
			'code',
		)
	);
}


/**
 * Get attributes and selectors to translate
 *
 * @return array
 */
function wplng_data_attr_text_to_translate() {
	return apply_filters(
		'wplng_attr_text_to_translate',
		array(
			array(
				'attr'     => 'alt',
				'selector' => '[alt]',
			),
			array(
				'attr'     => 'title',
				'selector' => '[title]',
			),
			array(
				'attr'     => 'placeholder',
				'selector' => '[placeholder]',
			),
			array(
				'attr'     => 'aria-label',
				'selector' => '[aria-label]',
			),
			array(
				'attr'     => 'value',
				'selector' => 'input[type="submit"][value]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[property="og:title"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[property="og:description"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[property="og:site_name"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="twitter:title"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="twitter:description"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="twitter:label1"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="twitter:data1"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="twitter:label2"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="twitter:data2"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="dc.title"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="description"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="dc.description"]',
			),
		)
	);
}


/**
 * Get attributes and selectors of URL to translate
 *
 * @return array
 */
function wplng_data_attr_url_to_translate() {
	return apply_filters(
		'wplng_attr_url_to_translate',
		array(
			array(
				'attr'     => 'href',
				'selector' => 'a[href]',
			),
			array(
				'attr'     => 'action',
				'selector' => 'form[action]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[property="og:url"]',
			),
			array(
				'attr'     => 'href',
				'selector' => 'link[rel="canonical"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="dc.relation"]',
			),
		)
	);
}


/**
 * GEt attributes and selector of elements where translate language ID
 *
 * @return array
 */
function wplng_data_attr_lang_id_to_replace() {
	return apply_filters(
		'wplng_attr_lang_id_to_replace',
		array(
			array(
				'attr'     => 'lang',
				'selector' => 'html',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[property="og:locale"]',
			),
			array(
				'attr'     => 'content',
				'selector' => 'meta[name="dc.language"]',
			),
		)
	);
}


/**
 * ------ Data : Switcher options ------
 */

 /**
  * Get options for switcher insertion
  *
  * @return array
  */
function wplng_data_switcher_valid_insert() {
	return array(
		'bottom-right'  => __( 'Bottom right', 'wplingua' ),
		'bottom-center' => __( 'Bottom center', 'wplingua' ),
		'bottom-left'   => __( 'Bottom left', 'wplingua' ),
		'none'          => __( 'None', 'wplingua' ),
	);
}


/**
 * Get options for switcher themes
 *
 * @return array
 */
function wplng_data_switcher_valid_theme() {
	return array(
		'light-double-smooth'     => __( 'Light - Double - Smooth', 'wplingua' ),
		'light-double-square'     => __( 'Light - Double - Square', 'wplingua' ),
		'light-simple-smooth'     => __( 'Light - Simple - Smooth', 'wplingua' ),
		'light-simple-square'     => __( 'Light - Simple - Square', 'wplingua' ),
		'grey-double-smooth'      => __( 'Grey - Double - Smooth', 'wplingua' ),
		'grey-double-square'      => __( 'Grey - Double - Square', 'wplingua' ),
		'grey-simple-smooth'      => __( 'Grey - Simple - Smooth', 'wplingua' ),
		'grey-simple-square'      => __( 'Grey - Simple - Square', 'wplingua' ),
		'dark-double-smooth'      => __( 'Dark - Double - Smooth', 'wplingua' ),
		'dark-double-square'      => __( 'Dark - Double - Square', 'wplingua' ),
		'dark-simple-smooth'      => __( 'Dark - Simple - Smooth', 'wplingua' ),
		'dark-simple-square'      => __( 'Dark - Simple - Square', 'wplingua' ),
		'blurblack-double-smooth' => __( 'Blur Black - Double - Smooth', 'wplingua' ),
		'blurblack-double-square' => __( 'Blur Black - Double - Square', 'wplingua' ),
		'blurblack-simple-smooth' => __( 'Blur Black - Simple - Smooth', 'wplingua' ),
		'blurblack-simple-square' => __( 'Blur Black - Simple - Square', 'wplingua' ),
		'blurwhite-double-smooth' => __( 'Blur White - Double - Smooth', 'wplingua' ),
		'blurwhite-double-square' => __( 'Blur White - Double - Square', 'wplingua' ),
		'blurwhite-simple-smooth' => __( 'Blur White - Simple - Smooth', 'wplingua' ),
		'blurwhite-simple-square' => __( 'Blur White - Simple - Square', 'wplingua' ),
	);
}


/**
 * Get options for switcher style
 *
 * @return array
 */
function wplng_data_switcher_valid_style() {
	return array(
		'list'     => __( 'Inline list', 'wplingua' ),
		'block'    => __( 'Block', 'wplingua' ),
		'dropdown' => __( 'Dropdown', 'wplingua' ),
	);
}


/**
 * Get options for switcher name format
 *
 * @return array
 */
function wplng_data_switcher_valid_name_format() {
	return array(
		'name'     => __( 'Translated name', 'wplingua' ),
		'original' => __( 'Original name', 'wplingua' ),
		'id'       => __( 'Language ID', 'wplingua' ),
		'none'     => __( 'No display', 'wplingua' ),
	);
}


/**
 * Get options for switcher flags style
 *
 * @return array
 */
function wplng_data_switcher_valid_flags_style() {
	return array(
		'circle'      => __( 'Circle', 'wplingua' ),
		'rectangular' => __( 'Rectangular', 'wplingua' ),
		'wave'        => __( 'Wave', 'wplingua' ),
		'none'        => __( 'No display', 'wplingua' ),
	);
}


/**
 * ------ Data : Languages ------
 */

 /**
  * Get all languages data
  *
  * @return array
  */
function wplng_data_languages() {
	return array(
		array(
			'name'             => __( 'Arabic', 'wplingua' ),
			'id'               => 'ar',
			'dir'              => 'rtl',
			'flag'             => 'eg',
			'flags'            => array(
				array(
					'name' => __( 'Egypt', 'wplingua' ),
					'id'   => 'eg',
					'flag' => 'eg',
				),
				array(
					'name' => __( 'Saudi Arabia', 'wplingua' ),
					'id'   => 'sa',
					'flag' => 'sa',
				),
				array(
					'name' => __( 'Algeria', 'wplingua' ),
					'id'   => 'dz',
					'flag' => 'dz',
				),
				array(
					'name' => __( 'Bahrain', 'wplingua' ),
					'id'   => 'bh',
					'flag' => 'bh',
				),
				array(
					'name' => __( 'Chad', 'wplingua' ),
					'id'   => 'td',
					'flag' => 'td',
				),
				array(
					'name' => __( 'Comoros', 'wplingua' ),
					'id'   => 'km',
					'flag' => 'km',
				),
				array(
					'name' => __( 'Djibouti', 'wplingua' ),
					'id'   => 'dj',
					'flag' => 'dj',
				),
				array(
					'name' => __( 'Iraq', 'wplingua' ),
					'id'   => 'iq',
					'flag' => 'iq',
				),
				array(
					'name' => __( 'Jordan', 'wplingua' ),
					'id'   => 'jo',
					'flag' => 'jo',
				),
				array(
					'name' => __( 'Kuwait', 'wplingua' ),
					'id'   => 'kw',
					'flag' => 'kw',
				),
				array(
					'name' => __( 'Lebanon', 'wplingua' ),
					'id'   => 'lb',
					'flag' => 'lb',
				),
				array(
					'name' => __( 'Libya', 'wplingua' ),
					'id'   => 'ly',
					'flag' => 'ly',
				),
				array(
					'name' => __( 'Mauritania', 'wplingua' ),
					'id'   => 'mr',
					'flag' => 'mr',
				),
				array(
					'name' => __( 'Morocco', 'wplingua' ),
					'id'   => 'ma',
					'flag' => 'ma',
				),
				array(
					'name' => __( 'Oman', 'wplingua' ),
					'id'   => 'om',
					'flag' => 'om',
				),
				array(
					'name' => __( 'Palestine', 'wplingua' ),
					'id'   => 'ps',
					'flag' => 'ps',
				),
				array(
					'name' => __( 'Qatar', 'wplingua' ),
					'id'   => 'qa',
					'flag' => 'qa',
				),
				array(
					'name' => __( 'Somalia', 'wplingua' ),
					'id'   => 'so',
					'flag' => 'so',
				),
				array(
					'name' => __( 'Sudan', 'wplingua' ),
					'id'   => 'sd',
					'flag' => 'sd',
				),
				array(
					'name' => __( 'Syria', 'wplingua' ),
					'id'   => 'sy',
					'flag' => 'sy',
				),
				array(
					'name' => __( 'Tunisia', 'wplingua' ),
					'id'   => 'tn',
					'flag' => 'tn',
				),
				array(
					'name' => __( 'United Arab Emirates', 'wplingua' ),
					'id'   => 'ae',
					'flag' => 'ae',
				),
				array(
					'name' => __( 'Yemen', 'wplingua' ),
					'id'   => 'ye',
					'flag' => 'ye',
				),
			),
			'name_translation' => array(
				'ar' => 'العربية',
				'da' => 'Arabisk',
				'de' => 'Arabisch',
				'en' => 'Arabic',
				'es' => 'Árabe',
				'fi' => 'Arabia',
				'fr' => 'Arabe',
				'gr' => 'Αραβικά',
				'hi' => 'अरबी',
				'hu' => 'Arabic',
				'id' => 'Arab',
				'it' => 'Araba',
				'iw' => 'עֲרָבִית',
				'ja' => 'アラビア語',
				'ko' => '아랍어',
				'nl' => 'Arabisch',
				'pt' => 'Árabe',
				'ru' => 'Арабский',
				'zh' => '阿拉伯',
				'uk' => 'Арабський',
			),
		),
		array(
			'name'             => __( 'Chinese', 'wplingua' ),
			'id'               => 'zh',
			'flag'             => 'cn',
			'flags'            => array(
				array(
					'name' => __( 'China', 'wplingua' ),
					'id'   => 'cn',
					'flag' => 'cn',
				),
				array(
					'name' => __( 'Hong Kong', 'wplingua' ),
					'id'   => 'hk',
					'flag' => 'hk',
				),
			),
			'name_translation' => array(
				'ar' => 'الصينية',
				'da' => 'Kinesisk',
				'de' => 'Chinesisch',
				'en' => 'Chinese',
				'es' => 'Chino',
				'fi' => 'Kiinalainen',
				'fr' => 'Chinois',
				'gr' => 'Κινέζικα',
				'hi' => 'चीनी',
				'hu' => 'Kínai',
				'id' => 'Cina',
				'it' => 'Cinese',
				'iw' => 'סִינִית',
				'ja' => '中国語',
				'ko' => '중국어',
				'nl' => 'Chinees',
				'pt' => 'Chinês',
				'ru' => 'Китайский',
				'zh' => '中文',
				'uk' => 'Китайський',
			),
		),
		array(
			'name'             => __( 'Danish', 'wplingua' ),
			'id'               => 'da',
			'flag'             => 'dk',
			'flags'            => array(
				array(
					'name' => __( 'Denmark', 'wplingua' ),
					'id'   => 'dk',
					'flag' => 'dk',
				),
			),
			'name_translation' => array(
				'ar' => 'الدنماركية',
				'da' => 'Dansk',
				'de' => 'Dänisch',
				'en' => 'Danish',
				'es' => 'Danés',
				'fi' => 'Tanskalainen',
				'fr' => 'Danois',
				'gr' => 'Δανικά',
				'hi' => 'दानिश',
				'hu' => 'Dán',
				'id' => 'Denmark',
				'it' => 'Danese',
				'iw' => 'דַנִי',
				'ja' => 'デンマーク語',
				'ko' => '덴마크어',
				'nl' => 'Deens',
				'pt' => 'Dinamarquesa',
				'ru' => 'Датский',
				'zh' => '丹麦语',
				'uk' => 'Датський',
			),
		),
		array(
			'name'             => __( 'Dutch', 'wplingua' ),
			'id'               => 'nl',
			'flag'             => 'nl',
			'flags'            => array(
				array(
					'name' => __( 'Netherlands', 'wplingua' ),
					'id'   => 'nl',
					'flag' => 'nl',
				),
				array(
					'name' => __( 'Belgium', 'wplingua' ),
					'id'   => 'be',
					'flag' => 'be',
				),
				array(
					'name' => __( 'Suriname', 'wplingua' ),
					'id'   => 'sr',
					'flag' => 'sr',
				),
			),
			'name_translation' => array(
				'ar' => 'الهولندية',
				'da' => 'Hollandsk',
				'de' => 'Niederländisch',
				'en' => 'Dutch',
				'es' => 'Holandés',
				'fi' => 'Hollantilainen',
				'fr' => 'Néerlandais',
				'gr' => 'Ολλανδικά',
				'hi' => 'डच',
				'hu' => 'Holland',
				'id' => 'Belanda',
				'it' => 'Olandese',
				'iw' => 'הוֹלַנדִי',
				'ja' => 'オランダ語',
				'ko' => '네덜란드어',
				'nl' => 'Nederlands',
				'pt' => 'holandês',
				'ru' => 'Голландий',
				'zh' => '荷兰语',
				'uk' => 'Голландський',
			),
		),
		array(
			'name'             => __( 'English', 'wplingua' ),
			'id'               => 'en',
			'flag'             => 'gb',
			'flags'            => array(
				array(
					'name' => __( 'UK', 'wplingua' ),
					'id'   => 'gb',
					'flag' => 'gb',
				),
				array(
					'name' => __( 'USA', 'wplingua' ),
					'id'   => 'us',
					'flag' => 'us',
				),
			),
			'name_translation' => array(
				'ar' => 'الإنجليزية',
				'da' => 'Engelsk',
				'de' => 'Englisch',
				'en' => 'English',
				'es' => 'Inglés',
				'fi' => 'Englanti',
				'fr' => 'Anglais',
				'gr' => 'Αγγλικά',
				'hi' => 'अंग्रेज़ी',
				'hu' => 'Angol',
				'id' => 'Inggris',
				'it' => 'Inglese',
				'iw' => 'אנגלית',
				'ja' => '英語',
				'ko' => '영어',
				'nl' => 'Engels',
				'pt' => 'Inglês',
				'ru' => 'Английский',
				'zh' => '英语',
				'uk' => 'Англійський',
			),
		),
		array(
			'name'             => __( 'Finnish', 'wplingua' ),
			'id'               => 'fi',
			'flag'             => 'fi',
			'flags'            => array(
				array(
					'name' => __( 'Finland', 'wplingua' ),
					'id'   => 'fi',
					'flag' => 'fi',
				),
			),
			'name_translation' => array(
				'ar' => 'الفنلندية',
				'da' => 'Finsk',
				'de' => 'Finnisch',
				'en' => 'Finnish',
				'es' => 'Finlandés',
				'fi' => 'Suomalainen',
				'fr' => 'Finlandais',
				'gr' => 'Φινλανδική',
				'hi' => 'फिनिश',
				'hu' => 'Finn',
				'id' => 'Finlandia',
				'it' => 'Finlandese',
				'iw' => 'פִינִית',
				'ja' => 'フィンランド語 ',
				'ko' => '핀란드어',
				'nl' => 'Fins',
				'pt' => 'finlandês',
				'ru' => 'Финский',
				'zh' => '芬兰语',
				'uk' => 'Фінський',
			),
		),
		array(
			'name'             => __( 'French', 'wplingua' ),
			'id'               => 'fr',
			'flag'             => 'fr',
			'flags'            => array(
				array(
					'name' => __( 'France', 'wplingua' ),
					'id'   => 'fr',
					'flag' => 'fr',
				),
				array(
					'name' => __( 'Belgium', 'wplingua' ),
					'id'   => 'be',
					'flag' => 'be',
				),
				array(
					'name' => __( 'Canada', 'wplingua' ),
					'id'   => 'ca',
					'flag' => 'ca',
				),
			),
			'name_translation' => array(
				'ar' => 'الفرنسية',
				'da' => 'Fransk',
				'de' => 'Französisch',
				'en' => 'French',
				'es' => 'Francés',
				'fi' => 'Ranskan',
				'fr' => 'Français',
				'gr' => 'Γαλλικά',
				'hi' => 'फ़्रेंच',
				'hu' => 'Francia',
				'id' => 'Prancis',
				'it' => 'Francese',
				'iw' => 'צָרְפָתִית',
				'ja' => 'フランス語',
				'ko' => '프랑스어',
				'nl' => 'Frans',
				'pt' => 'Francês',
				'ru' => 'Французский',
				'zh' => '法语',
				'uk' => 'Французький',
			),
		),
		array(
			'name'             => __( 'German', 'wplingua' ),
			'id'               => 'de',
			'flag'             => 'de',
			'flags'            => array(
				array(
					'name' => __( 'Germany', 'wplingua' ),
					'id'   => 'de',
					'flag' => 'de',
				),
			),
			'name_translation' => array(
				'ar' => 'الألمانية',
				'da' => 'Tysk',
				'de' => 'Deutsch',
				'en' => 'German',
				'es' => 'Alemán',
				'fi' => 'Saksan',
				'fr' => 'Allemand',
				'gr' => 'Γερμανικά',
				'hi' => 'जर्मन',
				'hu' => 'Német',
				'id' => 'Jerman',
				'it' => 'Tedesco',
				'iw' => 'גֶרמָנִיָת',
				'ja' => 'ドイツ語',
				'ko' => '독일어',
				'nl' => 'Duits',
				'pt' => 'Alemão',
				'ru' => 'Немецкий',
				'zh' => '德国',
				'uk' => 'Німецький',
			),
		),
		array(
			'name'             => __( 'Greek', 'wplingua' ),
			'id'               => 'el',
			'flag'             => 'gr',
			'flags'            => array(
				array(
					'name' => __( 'Greece', 'wplingua' ),
					'id'   => 'gr',
					'flag' => 'gr',
				),
				array(
					'name' => __( 'Cyprus', 'wplingua' ),
					'id'   => 'cy',
					'flag' => 'cy',
				),
			),
			'name_translation' => array(
				'ar' => 'اليونانية',
				'da' => 'Græsk',
				'de' => 'Griechisch',
				'en' => 'Greek',
				'es' => 'Griego',
				'fi' => 'Kreikkalainen',
				'fr' => 'Grecque',
				'gr' => 'Ελληνική',
				'hi' => 'यूनानी',
				'hu' => 'Görög',
				'id' => 'Yunani',
				'it' => 'Greco',
				'iw' => 'יווני',
				'ja' => 'ギリシャ語',
				'ko' => '그리스어',
				'nl' => 'Grieks',
				'pt' => 'Grego',
				'ru' => 'Греческий',
				'zh' => '希腊文',
				'uk' => 'Грецький',
			),
		),
		array(
			'name'             => __( 'Hebrew', 'wplingua' ),
			'id'               => 'iw',
			'dir'              => 'rtl',
			'flag'             => 'il',
			'flags'            => array(
				array(
					'name' => __( 'Israel', 'wplingua' ),
					'id'   => 'il',
					'flag' => 'il',
				),
			),
			'name_translation' => array(
				'ar' => 'العبرية',
				'da' => 'Hebræisk',
				'de' => 'Hebräisch',
				'en' => 'Hebrew',
				'es' => 'Hebreo',
				'fi' => 'Heprea',
				'fr' => 'Hébreu',
				'gr' => 'Εβραϊκά',
				'hi' => 'यहूदी',
				'hu' => 'Héber',
				'id' => 'Ibrani',
				'it' => 'Ebraica',
				'iw' => 'עִברִית',
				'ja' => 'ヘブライ語',
				'ko' => '히브리어',
				'nl' => 'Hebreeuws',
				'pt' => 'hebraico',
				'ru' => 'Еврейский',
				'zh' => '希伯来文',
				'uk' => 'Єврейський',
			),
		),
		array(
			'name'             => __( 'Hindi', 'wplingua' ),
			'id'               => 'hi',
			'flag'             => 'in',
			'flags'            => array(
				array(
					'name' => __( 'India', 'wplingua' ),
					'id'   => 'in',
					'flag' => 'in',
				),
				array(
					'name' => __( 'Pakistan', 'wplingua' ),
					'id'   => 'pk',
					'flag' => 'pk',
				),
			),
			'name_translation' => array(
				'ar' => 'الهندية',
				'da' => 'Hindi',
				'de' => 'Hindi',
				'en' => 'Hindi',
				'es' => 'Hindi',
				'fi' => 'Hindi',
				'fr' => 'Hindi',
				'gr' => 'Χίντι',
				'hi' => 'हिंदी',
				'hu' => 'Hindi',
				'id' => 'Hindi',
				'it' => 'Hindi',
				'iw' => 'הינדי',
				'ja' => 'ヒンディー語',
				'ko' => '힌디어',
				'nl' => 'Hindi',
				'pt' => 'Hindi',
				'ru' => 'Хинди',
				'zh' => '北印度语',
				'uk' => 'Хінді',
			),
		),
		array(
			'name'             => __( 'Hungarian', 'wplingua' ),
			'id'               => 'hu',
			'flag'             => 'hu',
			'flags'            => array(
				array(
					'name' => __( 'Hungary', 'wplingua' ),
					'id'   => 'hu',
					'flag' => 'hu',
				),
			),
			'name_translation' => array(
				'ar' => 'الهنغارية',
				'da' => 'Ungarsk',
				'de' => 'Ungarisch',
				'en' => 'Hungarian',
				'es' => 'Húngaro',
				'fi' => 'Unkarilainen',
				'fr' => 'Hongrois',
				'gr' => 'Ουγγρικό',
				'hi' => 'हंगेरी',
				'hu' => 'Magyar',
				'id' => 'Hongaria',
				'it' => 'Ungherese',
				'iw' => 'הוּנגָרִי',
				'ja' => 'ハンガリー語',
				'ko' => '헝가리어',
				'nl' => 'Hongaars',
				'pt' => 'húngaro',
				'ru' => 'Венгерский',
				'zh' => '匈牙利语',
				'uk' => 'Угорський',
			),
		),
		array(
			'name'             => __( 'Indonesian', 'wplingua' ),
			'id'               => 'id',
			'flag'             => 'id',
			'flags'            => array(
				array(
					'name' => __( 'Indonesia', 'wplingua' ),
					'id'   => 'id',
					'flag' => 'id',
				),
			),
			'name_translation' => array(
				'ar' => 'الإندونيسية',
				'da' => 'Indonesisk',
				'de' => 'Indonesisch',
				'en' => 'Indonesian',
				'es' => 'Indonesia',
				'fi' => 'Indonesialainen',
				'fr' => 'Indonésien',
				'gr' => 'Ινδονησιακή',
				'hi' => 'इन्डोनेशियाई',
				'hu' => 'Indonéz',
				'id' => 'Indonesia',
				'it' => 'Indonesiano',
				'iw' => 'אינדונזית',
				'ja' => 'インドネシア語',
				'ko' => '인도네시아어',
				'nl' => 'Indonesisch',
				'pt' => 'indonésio',
				'ru' => 'Индонезийский',
				'zh' => '印尼语',
				'uk' => 'Індонезійський',
			),
		),
		array(
			'name'             => __( 'Italian', 'wplingua' ),
			'id'               => 'it',
			'flag'             => 'it',
			'flags'            => array(
				array(
					'name' => __( 'Italy', 'wplingua' ),
					'id'   => 'it',
					'flag' => 'it',
				),
			),
			'name_translation' => array(
				'ar' => 'الإيطالية',
				'da' => 'Italiensk',
				'de' => 'Italienisch',
				'en' => 'Italian',
				'es' => 'Italiano',
				'fi' => 'Italian',
				'fr' => 'Italien',
				'gr' => 'Ιταλικά',
				'hi' => 'इतालवी',
				'hu' => 'Olasz',
				'id' => 'Italia',
				'it' => 'Italiano',
				'iw' => 'אִיטַלְקִית',
				'ja' => 'イタリア語',
				'ko' => '이탈리아어',
				'nl' => 'Italiaans',
				'pt' => 'Italiano',
				'ru' => 'Итальянский',
				'zh' => '意大利',
				'uk' => 'Італійський',
			),
		),
		array(
			'name'             => __( 'Japanese', 'wplingua' ),
			'id'               => 'ja',
			'flag'             => 'jp',
			'flags'            => array(
				array(
					'name' => __( 'Japan', 'wplingua' ),
					'id'   => 'jp',
					'flag' => 'jp',
				),
			),
			'name_translation' => array(
				'ar' => 'اليابانية',
				'da' => 'Japansk',
				'de' => 'Japanisch',
				'en' => 'Japanese',
				'es' => 'Japonés',
				'fi' => 'Japanilainen',
				'fr' => 'Japonais',
				'gr' => 'Ιαπωνικά',
				'hi' => 'जापानी',
				'hu' => 'Japán',
				'id' => 'Jepang',
				'it' => 'Giapponese',
				'iw' => 'יַפָּנִית',
				'ja' => '日本語',
				'ko' => '일본어',
				'nl' => 'Japans',
				'pt' => 'Japonês',
				'ru' => 'Японский',
				'zh' => '日语',
				'uk' => 'Японський',
			),
		),
		array(
			'name'             => __( 'Korean', 'wplingua' ),
			'id'               => 'ko',
			'flag'             => 'kr',
			'flags'            => array(
				array(
					'name' => __( 'South Korea', 'wplingua' ),
					'id'   => 'kr',
					'flag' => 'kr',
				),
				array(
					'name' => __( 'North Korea', 'wplingua' ),
					'id'   => 'kp',
					'flag' => 'kp',
				),
			),
			'name_translation' => array(
				'ar' => 'الكورية',
				'da' => 'Koreansk',
				'de' => 'Koreanisch',
				'en' => 'Korean',
				'es' => 'Coreano',
				'fi' => 'Korean',
				'fr' => 'Coréen',
				'gr' => 'Κορεάτικα',
				'hi' => 'कोरियाई',
				'hu' => 'Koreai',
				'id' => 'Korea',
				'it' => 'Coreano',
				'iw' => 'קוריאנית',
				'ja' => '韓国語',
				'ko' => '한국어',
				'nl' => 'Koreaans',
				'pt' => 'Coreano',
				'ru' => 'Корейский',
				'zh' => '韩语',
				'uk' => 'Корейський',
			),
		),
		array(
			'name'             => __( 'Portuguese', 'wplingua' ),
			'id'               => 'pt',
			'flag'             => 'pt',
			'flags'            => array(
				array(
					'name' => __( 'Portugal', 'wplingua' ),
					'id'   => 'pt',
					'flag' => 'pt',
				),
				array(
					'name' => __( 'Brazil', 'wplingua' ),
					'id'   => 'br',
					'flag' => 'br',
				),
			),
			'name_translation' => array(
				'ar' => 'البرتغالية',
				'da' => 'Portugisisk',
				'de' => 'Portugiesisch',
				'en' => 'Portuguese',
				'es' => 'Portugués',
				'fi' => 'Portugalin',
				'fr' => 'Portugais',
				'gr' => 'Πορτογαλικά',
				'hi' => 'पुर्तगाली',
				'hu' => 'Portugál',
				'id' => 'Portugis',
				'it' => 'Portoghese',
				'iw' => 'פורטוגזית',
				'ja' => 'ポルトガル語',
				'ko' => '포르투갈어',
				'nl' => 'Portugees',
				'pt' => 'Português',
				'ru' => 'Португальский',
				'zh' => '葡语',
				'uk' => 'Португальський',
			),
		),
		array(
			'name'             => __( 'Russian', 'wplingua' ),
			'id'               => 'ru',
			'flag'             => 'ru',
			'flags'            => array(
				array(
					'name' => __( 'Russia', 'wplingua' ),
					'id'   => 'ru',
					'flag' => 'ru',
				),
			),
			'name_translation' => array(
				'ar' => 'الروسية',
				'da' => 'Russisk',
				'de' => 'Russisch',
				'en' => 'Russian',
				'es' => 'Ruso',
				'fi' => 'Venäläinen',
				'fr' => 'Russe',
				'gr' => 'Ρωσική',
				'hi' => 'रूसी',
				'hu' => 'Orosz',
				'id' => 'Rusia',
				'it' => 'Russo',
				'iw' => 'רוּסִי',
				'ja' => 'ロシア語',
				'ko' => '러시아어',
				'nl' => 'Russisch',
				'pt' => 'Russo',
				'ru' => 'Японский',
				'zh' => '日语',
				'uk' => 'Російський',
			),
		),
		array(
			'name'             => __( 'Spanish', 'wplingua' ),
			'id'               => 'es',
			'flag'             => 'es',
			'flags'            => array(
				array(
					'name' => __( 'Spain', 'wplingua' ),
					'id'   => 'es',
					'flag' => 'es',
				),
				array(
					'name' => __( 'Mexico', 'wplingua' ),
					'id'   => 'mx',
					'flag' => 'mx',
				),
			),
			'name_translation' => array(
				'ar' => 'الإسبانية',
				'da' => 'Spansk',
				'de' => 'Spanisch',
				'en' => 'Spanish',
				'es' => 'Español',
				'fi' => 'Espanjan',
				'fr' => 'Espagnol',
				'gr' => 'Ισπανικά',
				'hi' => 'स्पैनिश',
				'hu' => 'Spanisch',
				'id' => 'Spanyol',
				'it' => 'Spagnolo',
				'iw' => 'ספרדית',
				'ja' => 'スペイン語',
				'ko' => '스페인어',
				'nl' => 'Spaans',
				'pt' => 'Espanhol',
				'ru' => 'Испанский',
				'zh' => '英语',
				'uk' => 'Іспанський',
			),
		),
		array(
			'name'             => __( 'Ukrainian', 'wplingua' ),
			'id'               => 'uk',
			'flag'             => 'ua',
			'flags'            => array(
				array(
					'name' => __( 'Ukraine', 'wplingua' ),
					'id'   => 'ua',
					'flag' => 'ua',
				),
			),
			'name_translation' => array(
				'ar' => 'الأوكرانية',
				'da' => 'Ukrainsk',
				'de' => 'Ukrainisch',
				'en' => 'Ukrainian',
				'es' => 'Ucraniano',
				'fi' => 'Ukrainan',
				'fr' => 'Ukrainien',
				'gr' => 'Ουκρανικό',
				'hi' => 'यूक्रेनी',
				'hu' => 'Ukrán',
				'id' => 'Ukraina',
				'it' => 'Ucraino',
				'iw' => 'אוקראינית',
				'ja' => 'ウクライナ語',
				'ko' => '우크라이나어',
				'nl' => 'Oekraïens',
				'pt' => 'ucraniano',
				'ru' => 'Украинский',
				'zh' => '乌克兰',
				'uk' => 'Український',
			),
		),
	);
}
