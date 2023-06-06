jQuery(document).ready(function($) {
	var path = window.location.pathname;
	var currentLanguage = false;
	var languages = [];

    $("link[rel=alternate][hreflang]").each(function() {
        languages.push($(this).attr("hreflang"));
    });


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
        $('a:not(.mcv-language)').each(function() {
            var href = this.href;
            if (href.indexOf('?') != -1) {
                href = href + '&redirect_lang=' + currentLanguage;
            } else {
                href = href + '?redirect_lang=' + currentLanguage;
            }
            
            $(this).attr('href', href);
        });
	}

	


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