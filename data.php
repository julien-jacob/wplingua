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
			array( 'yith_wcwl_l10n', 'labels', 'cookie_disabled' ),
			array( 'wc_add_to_cart_params', 'i18n_view_cart' ),
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
			'flags'            => array(
				array(
					'name' => __( 'Germany', 'wplingua' ),
					'id'   => 'de',
					'flag' => 'de',
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
			'flags'            => array(
				array(
					'name' => __( 'Italy', 'wplingua' ),
					'id'   => 'it',
					'flag' => 'it',
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
	);
}
