jQuery(document).ready(function ($) {

    function wplngUpdateSwitcherOpening() {
        var windowMiddle = $(window).height() / 2;

        $(".wplng-switcher").each(function (e) {

            if (!$(this).hasClass("style-dropdown")) {
                return;
            }

            var offsetFromWindow = $(this).offset().top - $(window).scrollTop();

            // wplng-language
            

            if (offsetFromWindow < windowMiddle) {
                if (!$(this).hasClass("open-bottom")) {
                    $(this).addClass("open-bottom");
                    $(this).removeClass("open-top");
                    
                    var htmlLanguages = $(".wplng-languages", this).prop('outerHTML');
                    var htmlLanguagecurrent = $(".wplng-language-current", this).prop('outerHTML');
                    $(".switcher-content", this).html(htmlLanguagecurrent + htmlLanguages);
                }
            } else {
                if (!$(this).hasClass("open-top")) {
                    $(this).addClass("open-top");
                    $(this).removeClass("open-bottom");

                    var htmlLanguages = $(".wplng-languages", this).prop('outerHTML');
                    var htmlLanguagecurrent = $(".wplng-language-current", this).prop('outerHTML');
                    $(".switcher-content", this).html(htmlLanguages + htmlLanguagecurrent);
                }
            }
        });
    }

    $(window).scroll(function () {
        wplngUpdateSwitcherOpening();
    });

    wplngUpdateSwitcherOpening();





    // var path = window.location.pathname;
    // var currentLanguage = false;
    // var languages = [];

    // $("link[rel=alternate][hreflang]").each(function() {
    //     languages.push($(this).attr("hreflang"));
    // });


    // languages.forEach(language => {
    // 	if (path.startsWith('/' + language + '/')) {
    // 		currentLanguage = language;
    // 	}
    // });

    // var $_GET = [];
    // var parts = window.location.search.substr(1).split("&");
    // for (var i = 0; i < parts.length; i++) {
    // 	var temp = parts[i].split("=");
    // 	$_GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
    // }

    // if (
    //     $_GET["redirect_lang"] != undefined 
    //     && $_GET["wplingua-visual-editor"] == undefined
    // ) {
    // 	window.location.href = window.location.protocol + "//" + window.location.host + '/' + $_GET["redirect_lang"] + window.location.pathname;
    // }


    // if ($_GET["wplingua-visual-editor"] == undefined) {

    //     var sourcePath = "";
    //     if (currentLanguage === false) {
    //         sourcePath = path;
    //     } else {
    //         sourcePath = path.substring(3);
    //         $('a:not(.wplng-language)').each(function() {

    //             var href = this.href;
    //             if (href.indexOf('redirect_lang=') == -1) {
    //                 if (href.indexOf('?') != -1) {
    //                     href = href + '&redirect_lang=' + currentLanguage;
    //                 } else {
    //                     href = href + '?redirect_lang=' + currentLanguage;
    //                 }

    //                 $(this).attr('href', href);
    //             }

    //         });
    //     }
    // }






}); // End jQuery loaded event