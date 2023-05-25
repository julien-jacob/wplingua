<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function mcv_get_switcher_html() {
	$html = '<div class="mcv-switcher">';

	$languagt_website = mcv_get_language_website();
	$languages_target = mcv_get_languages_target();

	$html .= '<a class="mcv-language" href="http://machiavel.local/">';
	$html .= '</a>';

	// <a class="mcv-language" href="http://machiavel.local/"><img draggable="false" role="img" class="emoji" alt="🇫🇷" src="https://s.w.org/images/core/emoji/14.0.0/svg/1f1eb-1f1f7.svg"> fr</a><a class="mcv-language" href="http://machiavel.local/en/"><img draggable="false" role="img" class="emoji" alt="🇺🇸" src="https://s.w.org/images/core/emoji/14.0.0/svg/1f1fa-1f1f8.svg"> en</a><a class="mcv-language" href="http://machiavel.local/de/"><img draggable="false" role="img" class="emoji" alt="🇩🇪" src="https://s.w.org/images/core/emoji/14.0.0/svg/1f1e9-1f1ea.svg"> de</a><a class="mcv-language" href="http://machiavel.local/pt/"><img draggable="false" role="img" class="emoji" alt="🇧🇷" src="https://s.w.org/images/core/emoji/14.0.0/svg/1f1e7-1f1f7.svg"> pt</a><a class="mcv-language" href="http://machiavel.local/es/"><img draggable="false" role="img" class="emoji" alt="🇲🇽" src="https://s.w.org/images/core/emoji/14.0.0/svg/1f1f2-1f1fd.svg"> es</a>

	$html .= '</div>';
	return $html;
}