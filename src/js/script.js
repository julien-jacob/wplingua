jQuery(document).ready(function ($) {

    /**
     * Code for flags in nav menu switcher
     */

    $("a[data-wplng-flag]").each(function () {

        let img = '<img ';
        img += 'src="' + $(this).attr("data-wplng-flag") + '" '
        img += 'class="wplng-menu-flag"> '

        $(this).html(img + $(this).html());
    })

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

    $("#wplng-in-progress-iframe").on("load", wplngReloadInProgress);

    function wplngReloadInProgress() {
        let urlReload = $("#wplng-in-progress-container").attr("wplng-reload");
        window.location.href = urlReload;
    }

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

}); // End jQuery loaded event
