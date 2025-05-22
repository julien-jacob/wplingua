/*!*
 **                 _     _                         
 ** __      ___ __ | |   (_)_ __   __ _ _   _  __ _ 
 ** \ \ /\ / / '_ \| |   | | '_ \ / _` | | | |/ _` |
 **  \ V  V /| |_) | |___| | | | | (_| | |_| | (_| |
 **   \_/\_/ | .__/|_____|_|_| |_|\__, |\__,_|\__,_|
 **          |_|                  |___/             
 **
 **        -- wpLingua | WordPress plugin --
 **   Translate and make your website multilingual
 **
 **     https://github.com/julien-jacob/wplingua
 **      https://wordpress.org/plugins/wplingua/
 **              https://wplingua.com/
 **
 **/

jQuery(document).ready(function ($) {

    /**
     * Code for flags images in nav menu switcher
     */

    $("a[data-wplng-flag][data-wplng-alt]").each(function () {

        let img = '';

        img += '<img';
        img += ' src="' + $(this).attr("data-wplng-flag") + '" ';
        img += ' alt="' + $(this).attr("data-wplng-alt") + '" ';
        img += ' class="wplng-menu-flag"';
        img += '> ';

        $(this).html(img + $(this).html());
        $(this).removeAttr("data-wplng-flag");
        $(this).removeAttr("data-wplng-alt");
    });

    /**
     * Code for switcher
     */

    function wplngUpdateSwitcherOpening() {

        let windowMiddle = $(window).height() / 2;

        $(".wplng-switcher.style-dropdown").each(function (e) {

            let offsetFromWindow = $(this).offset().top - $(window).scrollTop();

            if (offsetFromWindow < windowMiddle) {
                if (!$(this).hasClass("open-bottom")) {
                    $(this).addClass("open-bottom");
                    $(this).removeClass("open-top");

                    let htmlLanguages = $(".wplng-languages", this).prop('outerHTML');
                    let htmlLanguagecurrent = $(".wplng-language-current", this).prop('outerHTML');
                    $(".switcher-content", this).html(htmlLanguagecurrent + htmlLanguages);
                }
            } else {
                if (!$(this).hasClass("open-top")) {
                    $(this).addClass("open-top");
                    $(this).removeClass("open-bottom");

                    let htmlLanguages = $(".wplng-languages", this).prop('outerHTML');
                    let htmlLanguagecurrent = $(".wplng-language-current", this).prop('outerHTML');
                    $(".switcher-content", this).html(htmlLanguages + htmlLanguagecurrent);
                }
            }
        });
    }

    $(window).scroll(function () {
        wplngUpdateSwitcherOpening();
    });

    $("#wplng_style").on("input", function () {
        wplngUpdateSwitcherOpening();
    });

    wplngUpdateSwitcherOpening();

    /**
     * Code for preloading
     */

    function wplngReloadInProgress() {

        let iframeSrc = $("#wplng-in-progress-iframe").attr("src");

        if (iframeSrc) {
            if (iframeSrc.includes("wplng-load=loading")) {
                let urlReload = $("#wplng-in-progress-container").attr("wplng-reload");
                window.location.href = urlReload;
            }
        }

    }

    // Check if the iframe is already loaded
    if ($("#wplng-in-progress-iframe")[0] && $("#wplng-in-progress-iframe")[0].contentDocument.readyState === "complete") {
        wplngReloadInProgress();
    }

    // Add an event for iframe loading
    $("#wplng-in-progress-iframe").on("load", function () {
        wplngReloadInProgress();
    });

    function wplngUpdatePercent() {
        let percent = parseInt($("#wplng-in-progress-percent").html());
        if (percent < 100) {
            percent++;
            $("#wplng-in-progress-percent").html(percent);
            $("#wplng-progress-bar-value").attr("style", "width: " + percent.toString() + "%");
        }
    }

    wplngUpdatePercent();

    if ($("#wplng-in-progress-percent").length) {
        setInterval(wplngUpdatePercent, 2000);
    }

    if ($("#wpadminbar").length && $("#wplng-in-progress-container").length) {
        $("#wpadminbar").hide();
    }

    /**
     * Code for overload bar
     */

    $("#wplng-overloaded-close").on("click", function () {
        $("#wplng-overloaded-container").hide();
    });

    /**
     * Manage dropdown width
     */

    // $(".wplng-switcher.style-dropdown").each( function() {
    //     if ($(this).width() <= 40) {
    //         $(this).addClass("dropdown-min-width");
    //     }
    // });

}); // End jQuery loaded event
