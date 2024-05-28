jQuery(document).ready(function ($) {

    $("[wplng-help-box], [wplng-help-box-right]").click(function () {

        let selector = $(this).attr("wplng-help-box");

        if (undefined == selector) {
            selector = $(this).attr("wplng-help-box-right");
        }

        $(selector).slideToggle();

        if ("1" == $(this).attr("wplng-help-box-open")) {
            $(this).attr("wplng-help-box-open", "0");
            $(this).attr("style", "");
        } else {
            $(this).attr("wplng-help-box-open", "1");
            $(this).css('color', '#69a8bb');
            $(this).css('opacity', '1');
        }

    })

}); // End jQuery loaded event