jQuery(document).ready(function($) {
	// var path = window.location.pathname;
	// var currentLanguage = false;
	// var languages = [
	// 	'en',
	// 	'de',
	// 	'pt',
	// 	'es'
	// ];

	// var emojiFlags = {
	// 	"fr" : "🇫🇷",
	// 	"en" : "🇺🇸",
	// 	"de" : "🇩🇪",
	// 	"pt" : "🇧🇷",
	// 	"es" : "🇲🇽"
	// };

	// languages.forEach(language => {
	// 	if (path.startsWith('/' + language + '/')) {
	// 		currentLanguage = language;
	// 	}
	// });
	
	// var sourcePath = "";
	// if (currentLanguage === false) {
	// 	sourcePath = path;
	// } else {
	// 	sourcePath = path.substring(3);
	// }


	// $("body").append('<div class="mcv-switcher"></div>');

	// $(".mcv-switcher").append('<a class="mcv-language" href="' + window.location.protocol + "//" + window.location.host + sourcePath + '">🇫🇷 fr</a>');
	
	// languages.forEach(language => {
		
	// 	$(".mcv-switcher").append('<a class="mcv-language" href="' + window.location.protocol + "//" + window.location.host + '/' + language + sourcePath + '">' + emojiFlags[language] + ' ' + language + '</a>');
	// });

	// $('a:not(.mcv-language)').each(function() {
	// 	var href = this.href;
	// 	if (href.indexOf('?') != -1) {
	// 		href = href + '&redirect_lang=' + currentLanguage;
	// 	} else {
	// 		href = href + '?redirect_lang=' + currentLanguage;
	// 	}
		
	// 	$(this).attr('href', href);
	// });


    var $_GET = [];
	var parts = window.location.search.substr(1).split("&");
	for (var i = 0; i < parts.length; i++) {
		var temp = parts[i].split("=");
		$_GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
	}

	if ($_GET["redirect_lang"] != undefined) {
		window.location.href = window.location.protocol + "//" + window.location.host + '/' + $_GET["redirect_lang"] + window.location.pathname;
	}

}); // End jQuery loaded event