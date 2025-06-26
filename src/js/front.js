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

    // ------------------------------------------------------------------------
    // Code for nav menu switcher
    // ------------------------------------------------------------------------

    /**
     * Set flags images in nav menu switcher
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


    // ------------------------------------------------------------------------
    // Code for switcher
    // ------------------------------------------------------------------------

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


    // ------------------------------------------------------------------------
    // Code for preloading
    // ------------------------------------------------------------------------

    /**
     * Load the translation and reload the page
     */

    let wplngMaxCount = 0;

    function wplngLoadNewTranslations() {

        wplngMaxCount++;

        if ($("#wplng-in-progress-container").length && wplngMaxCount <= 8) {

            let loadUrl = $("#wplng-in-progress-container").attr("wplng-load");

            if (loadUrl && loadUrl.trim() !== "") {

                $.ajax({
                    url: loadUrl,
                    method: "GET",
                    success: function (response) {

                        /**
                         * Basic response check
                         */

                        // Check ie error is return
                        if (response.wplingua_error != undefined) {
                            console.log("wpLingua error: " + response.wplingua_error);
                            return;
                        }

                        // Check if response is valid
                        if (response.wplingua_load == undefined
                            || response.wplingua_load != "loading"
                        ) {
                            console.log("wpLingua error: Invalid call response");
                            return;
                        }

                        /**
                         * Update the percentage
                         */

                        if (response.percentage != undefined) {
                            $("#wplng-in-progress-percent").html(response.percentage);
                            $("#wplng-progress-bar-value").attr("style", "width: " + response.percentage.toString() + "%");
                        }

                        /**
                         * Set new translations on page
                         */

                        if (response.translations != undefined
                            && Array.isArray(response.translations)
                        ) {

                            response.translations.forEach(function (translation) {

                                let source = translation.source;
                                let translated = translation.translation;

                                $(".wplng-in-progress-text").each(function () {
                                    let progressText = $(this).text();

                                    if (source == progressText) {
                                        $(this).replaceWith(translated);
                                    }
                                });
                            });
                        }

                        /**
                         * Check if reloading is needed or make a new call
                         */

                        if (response.url_reload != undefined
                            && response.url_reload.trim() != ""
                        ) {

                            /**
                             * Reloading is needed
                             */

                            window.location.href = response.url_reload.trim();

                        } else if (response.url_load != undefined
                            && response.url_load.trim() != ""
                        ) {

                            /**
                             * Make a new call
                             */

                            $("#wplng-in-progress-container").attr("wplng-load", response.url_load.trim());
                            wplngLoadNewTranslations();

                        }
                    }
                });

            } else {

                let reloadUrl = $("#wplng-in-progress-container").attr("wplng-reload");

                if (reloadUrl && reloadUrl.trim() !== "") {
                    window.location.href = reloadUrl.trim();
                }

            }

        }

    }

    wplngLoadNewTranslations();


    /**
     * Update percentage for the load in progress bar
     */

    function wplngUpdatePercent() {
        let percent = parseInt($("#wplng-in-progress-percent").html());
        if (percent < 99) {
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


    // ------------------------------------------------------------------------
    // Code for overload bar
    // ------------------------------------------------------------------------

    $("#wplng-overloaded-close").on("click", function () {
        $("#wplng-overloaded-container").hide();
    });

}); // End jQuery loaded event
