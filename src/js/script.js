jQuery(document).ready(function ($) {

    /**
     * Code for switcher
     */

    function wplngUpdateSwitcherOpening() {
        var windowMiddle = $(window).height() / 2;

        $(".wplng-switcher").each(function (e) {

            if (!$(this).hasClass("style-dropdown")) {
                return;
            }

            var offsetFromWindow = $(this).offset().top - $(window).scrollTop();

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

    $("#wplng_style").on("input", function () {
        wplngUpdateSwitcherOpening();
    });

    wplngUpdateSwitcherOpening();

    /**
     * Code for preloading
     */

    $("#wplng-in-progress-iframe").load(function () {
        window.location.href = $(this).attr("wplng-reload");
    });

    if ($("#wpadminbar").length && $("#wplng-in-progress-container").length) {
        $("#wpadminbar").hide();
    }

}); // End jQuery loaded event
