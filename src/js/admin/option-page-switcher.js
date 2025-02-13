jQuery(document).ready(function ($) {

    function wplngSwitcherUpdateInsert(val) {
        $(".wplng-switcher")
            .removeClass("insert-bottom-right")
            .removeClass("insert-bottom-center")
            .removeClass("insert-bottom-left")
            .addClass("insert-" + val);
    }

    function wplngSwitcherUpdateTheme(val) {
        $(".wplng-switcher")
            .removeClass("theme-light-double-smooth")
            .removeClass("theme-grey-double-smooth")
            .removeClass("theme-dark-double-smooth")
            .removeClass("theme-light-double-square")
            .removeClass("theme-grey-double-square")
            .removeClass("theme-dark-double-square")
            .removeClass("theme-light-simple-smooth")
            .removeClass("theme-grey-simple-smooth")
            .removeClass("theme-dark-simple-smooth")
            .removeClass("theme-light-simple-square")
            .removeClass("theme-grey-simple-square")
            .removeClass("theme-dark-simple-square")
            .removeClass("theme-blurwhite-double-smooth")
            .removeClass("theme-blurwhite-double-square")
            .removeClass("theme-blurwhite-simple-smooth")
            .removeClass("theme-blurwhite-simple-square")
            .removeClass("theme-blurblack-double-smooth")
            .removeClass("theme-blurblack-double-square")
            .removeClass("theme-blurblack-simple-smooth")
            .removeClass("theme-blurblack-simple-square")
            .addClass("theme-" + val);

        if (
            $(".wplng-switcher").hasClass("theme-blurwhite-double-smooth")
            || $(".wplng-switcher").hasClass("theme-blurwhite-double-square")
            || $(".wplng-switcher").hasClass("theme-blurwhite-simple-smooth")
            || $(".wplng-switcher").hasClass("theme-blurwhite-simple-square")
        ) {
            $("#wplng-switcher-preview-container").attr("style", "background-color: #1d2327;")
        } else {
            $("#wplng-switcher-preview-container").attr("style", "")
        }
    }

    function wplngSwitcherUpdateStyle(val) {
        $(".wplng-switcher")
            .removeClass("style-list")
            .removeClass("style-block")
            .removeClass("style-dropdown")
            .addClass("style-" + val);
    }

    function wplngSwitcherUpdateTitle(val) {

        if ("none" == val && "none" == $("#wplng_flags_style").val()) {
            $("#wplng_flags_style").val("rectangular");
            wplngSwitcherUpdateFlagsStyle("rectangular");
        }

        $(".wplng-switcher")
            .removeClass("title-name")
            .removeClass("title-original")
            .removeClass("title-id")
            .removeClass("title-none")
            .addClass("title-" + val);
    }

    function wplngSwitcherUpdateFlagsStyle(val) {

        if (!$(".wplng-switcher").length) {
            return;
        }

        if ("none" == val && "none" == $("#wplng_name_format").val()) {
            $("#wplng_name_format").val("name");
            wplngSwitcherUpdateTitle("name");
        }

        $(".wplng-switcher")
            .removeClass("flags-circle")
            .removeClass("flags-rectangular")
            .removeClass("flags-wave")
            .removeClass("flags-none")
            .addClass("flags-" + val);

        if ("none" != val) {
            let html = $(".wplng-switcher").html();

            html = html.replaceAll(
                "/wplingua/assets/images/circle/",
                "/wplingua/assets/images/" + val + "/",
            );

            html = html.replaceAll(
                "/wplingua/assets/images/rectangular/",
                "/wplingua/assets/images/" + val + "/",
            );

            html = html.replaceAll(
                "/wplingua/assets/images/wave/",
                "/wplingua/assets/images/" + val + "/",
            );

            $(".wplng-switcher").html(html);
        }
    }

    /**
     * Bind event
     */

    $("#wplng_insert").on("input", function () {
        wplngSwitcherUpdateInsert($(this).val());
    });

    $("#wplng_theme").on("input", function () {
        wplngSwitcherUpdateTheme($(this).val());
    });

    $("#wplng_style").on("input", function () {
        wplngSwitcherUpdateStyle($(this).val());
    });

    $("#wplng_name_format").on("input", function () {
        wplngSwitcherUpdateTitle($(this).val());
    });

    $("#wplng_flags_style").on("input", function () {
        wplngSwitcherUpdateFlagsStyle($(this).val());
    });

    /**
     * Init
     */

    wplngSwitcherUpdateInsert($("#wplng_insert").val());
    wplngSwitcherUpdateTheme($("#wplng_theme").val());
    wplngSwitcherUpdateStyle($("#wplng_style").val());
    wplngSwitcherUpdateTitle($("#wplng_name_format").val());
    wplngSwitcherUpdateFlagsStyle($("#wplng_flags_style").val());

    /**
     * CodeMirror CSS editor
     */

    let editor = wp.codeEditor.initialize($("#wplng_custom_css"), cm_settings);

    $(document).on("keypress", ".CodeMirror", function () {
        $("#wplingua-inline-css").html(editor.codemirror.doc.getValue());
    });
    $(document).on("mouseup", ".CodeMirror", function () {
        $("#wplingua-inline-css").html(editor.codemirror.doc.getValue());
    });
    $(document).on("blur", ".CodeMirror", function () {
        $("#wplingua-inline-css").html(editor.codemirror.doc.getValue());
    });

}); // End jQuery loaded event
