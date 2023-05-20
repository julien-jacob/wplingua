<?php
/*
Plugin Name: Machiavel
description: Aussi est-il nécessaire au Prince qui se veut conserver qu'il apprenne à pouvoir n'être pas bon...
Version: 0.1
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


define( 'MCV_UPLOADS_PATH', WP_CONTENT_DIR . '/uploads/machiavel/' );
define( 'MCV_API', 'http://machiavel-api.local/v0.1/last/' );

// require_once 'inc/translation-storage.php';

add_action('wp_enqueue_scripts', function() {
	wp_enqueue_script( 'jquery' );
});


global $machiavel_language_target;
$machiavel_language_target = false;

// var_dump(mcv_translate( 'fr', 'en', 'machiavel' ));
// die;

function mcv_get_language_source() {
	return 'fr';
}

function mcv_get_language_target() {
	// return 'de';
	// var_dump($_SERVER); die;
	global $machiavel_language_target;

	if ( $machiavel_language_target !== false ) {
		return $machiavel_language_target;
	}

	$current_path         = $_SERVER['REQUEST_URI'];
	$mcv_language_target  = false;
	$mcv_languages_target = [
		'en',
		'de',
		'pt',
		'es',
	];

	foreach ( $mcv_languages_target as $key => $language ) {
		if ( str_starts_with( $current_path, '/' . $language . '/' ) ) {
			$mcv_language_target = $language;
			break;
		}
	}

	$machiavel_language_target = $mcv_language_target;

	return $mcv_language_target;
}

add_action( 'init', 'mcv_init' );
function mcv_init() {

	if ( is_admin() || empty( mcv_get_language_target() ) ) {
		return;
	}

	$current_path           = $_SERVER['REQUEST_URI'];
	$origin_path            = '/' . substr( $current_path, 4, strlen( $current_path ) - 1 );
	$_SERVER['REQUEST_URI'] = $origin_path;
	ob_start( 'mcv_ob_callback' );
}

add_action( 'after_body', 'mcv_after_body' );
function mcv_after_body() {
	ob_end_flush();
}



// function mcv_multiexplode( $delimiters, $string ) {

// 	$ready  = str_replace( $delimiters, $delimiters[0], $string );
// 	$launch = explode( $delimiters[0], $ready );
// 	return $launch;
// }



function mcv_ob_callback( $html ) {

	$mcv_language_target = mcv_get_language_target();

	// Prepare HTML

	// Remove tab and break line
	// $html = str_replace( array( "\r", "\n", '  ', "\t" ), '', $html );
	$html = preg_replace( '#<!--.*-->#', '', $html );

	require_once 'simple_html_dom.php';

	$dom = str_get_html( $html );

	if ( $dom === false ) {
		return $html;
	}

	foreach ( $dom->find( '#wpadminbar' ) as $element ) {
		$element->outertext = '';
	}

	foreach ( $dom->find( '.mcv-switcher' ) as $element ) {
		$element->outertext = '';
	}

	foreach ( $dom->find( 'style' ) as $element ) {
		$element->outertext = '';
	}

	foreach ( $dom->find( 'script' ) as $element ) {
		$element->outertext = '';
	}

	$dom->save();
	$dom = str_get_html( $dom );

	$strings = [];
	foreach ( $dom->find( 'text' ) as $element ) {

		$s         = $element->innertext();
		$strings[] = $s;

	}

	foreach ( $strings as $key => $string ) {
		$strings[ $key ] = trim( $string );
	}

	$strings = array_filter( $strings ); // Remove empty
	$strings = array_unique( $strings ); // Remove duplicate

	$dir_uploads = WP_CONTENT_DIR . '/uploads/machiavel/';

	$translations         = [];
	$translations_current = [];

	if ( file_exists( $dir_uploads ) ) {
		$translations = json_decode( file_get_contents( $dir_uploads . 'translations-' . $mcv_language_target . '.json' ), true );
		if ( empty( $translations ) ) {
			$translations = [];
		}
	} else {

		$default_json = json_encode(
			[
				'wpRock' => 'wpRock',
			]
		);
		mkdir( $dir_uploads );
		file_put_contents( $dir_uploads . 'translations-' . $mcv_language_target . '.json', $default_json );
	}

	foreach ( $strings as $key => $string ) {
		if ( ! empty( $translations[ $string ] ) ) {
			// if ( ! empty( $translations[ $string ][ $mcv_language_target ] ) ) {
				unset( $strings[ $key ] );
				$translations_current[ $string ] = $translations[ $string ];
			// }
		}
	}

	foreach ( $strings as $key => $string ) {
		$translations_current[ $string ] = mcv_translate( 'fr', $mcv_language_target, $string );
	}

	// $translations = array_merge($translations_current, $translations);

	file_put_contents( $dir_uploads . 'translations-' . $mcv_language_target . '.json', json_encode( array_merge( $translations_current, $translations ) ) );
	// return '<pre>' . esc_html( var_export( array_merge($translations_current, $translations), true ) ) . '</pre>';

	$dom = str_get_html( $html );

	foreach ( $dom->find( 'text' ) as $element ) {

		if ( empty( $element->innertext ) || ctype_space( $element->innertext ) ) {
			continue;
		}

		$text = trim( $element->innertext );

		foreach ( $translations_current as $key => $translation ) {
			if ( $text == $key ) {
				$element->innertext = $translation;
			}
		}
	}

	// foreach ( $dom->find( 'a' ) as $element ) {
	// 	$element->href;

	// }

	$dom->save();

	$dom = (string) $dom;

	$dom = preg_replace(
		'/<html (.*?)?lang=(\"|\')(\S*)(\"|\')/',
		'<html $1lang=$2' . $mcv_language_target . '$4',
		$dom
	);

	return $dom;
	return '<pre>' . esc_html( var_export( $translations_current, true ) ) . '</pre>';
}



function mcv_translate( $language_source, $language_target, $text ) {

	$body = array(
		'api-key' => '1111111111111111',
		'r'       => 'translate',
		'source'  => $language_source,
		'target'  => $language_target,
		'text'    => $text,
	);
	$args = array(
		'method' => 'POST',
		'timeout' => 20,
		'sslverify' => false,
		'body'   => $body,
	);

	error_log(var_export($body, true));

	$request = wp_remote_post( MCV_API, $args );

	if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		error_log( print_r( $request, true ) );
		return '';
	}

	// return (wp_remote_retrieve_body( $request ));
	$response = json_decode( wp_remote_retrieve_body( $request ), true );

	if ( ! isset( $response['translation'] ) ) {
		return 'Erreur :: [' . $text . ']';
	}

	return (string) $response['translation'];
}





add_action( 'wp_footer', 'mcv_inline_script' );
function mcv_inline_script() {

	if ( is_admin() ) {
		return;
	}

	?>
<script>
jQuery(document).ready(function($) {
	var path = window.location.pathname;
	var currentLanguage = false;
	var languages = [
		'en',
		'de',
		'pt',
		'es'
	];

	var emojiFlags = {
		"fr" : "🇫🇷",
		"en" : "🇺🇸",
		"de" : "🇩🇪",
		"pt" : "🇧🇷",
		"es" : "🇲🇽"
	};

	languages.forEach(language => {
		if (path.startsWith('/' + language + '/')) {
			currentLanguage = language;
		}
	});
	
	var sourcePath = "";
	if (currentLanguage === false) {
		sourcePath = path;
	} else {
		sourcePath = path.substring(3);
	}


	$("body").append('<div class="mcv-switcher"></div>');

	$(".mcv-switcher").append('<a class="mcv-language" href="' + window.location.protocol + "//" + window.location.host + sourcePath + '">🇫🇷 fr</a>');
	
	languages.forEach(language => {
		
		$(".mcv-switcher").append('<a class="mcv-language" href="' + window.location.protocol + "//" + window.location.host + '/' + language + sourcePath + '">' + emojiFlags[language] + ' ' + language + '</a>');
	});

	$('a:not(.mcv-language)').each(function() {
		var href = this.href;
		if (href.indexOf('?') != -1) {
			href = href + '&redirect_lang=' + currentLanguage;
		} else {
			href = href + '?redirect_lang=' + currentLanguage;
		}
		
		$(this).attr('href', href);
	});

}); // End jQuery loaded event
</script>
	<?php
}

add_action( 'wp_head', 'mcv_inline_style' );
function mcv_inline_style() {

	if ( is_admin() ) {
		return;
	}

	?>
	<script>
		var $_GET = [];
		var parts = window.location.search.substr(1).split("&");
		for (var i = 0; i < parts.length; i++) {
			var temp = parts[i].split("=");
			$_GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
		}

		if ($_GET["redirect_lang"] != undefined) {
			window.location.href = window.location.protocol + "//" + window.location.host + '/' + $_GET["redirect_lang"] + window.location.pathname;
		}

	</script>
	<style>
		.mcv-switcher {
			position: fixed;
			bottom: 20px;
			background-color: black;
			border-radius: 16px;
			padding: 20px 10px;
			right: 30px;
			border: 2px gray solid;
			z-index: 9999;
		}

		.mcv-language {
			border: 2px solid gray;
			border-radius: 16px;
			padding: 10px 15px;
			margin: 2px;
			text-transform: uppercase;
			text-decoration: none;
		}

		.mcv-language:hover {
			background-color: lightgray;
		}
	</style>
	<?php
}
