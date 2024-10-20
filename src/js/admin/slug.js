jQuery(document).ready(function ($) {

    let wplngEditor = $("#wplng-slug-editor");

    /**
     * Decode HTML
     */
    function wplngDecodeHtml(string) {
        let returnText = string;
        returnText = returnText.replace(/&nbsp;/gi, " ");
        returnText = returnText.replace(/&amp;/gi, "&");
        returnText = returnText.replace(/&quot;/gi, '"');
        returnText = returnText.replace(/&lt;/gi, "<");
        returnText = returnText.replace(/&gt;/gi, ">");
        return returnText;
    }

    /**
     * CLose editor modal
     */
    function wplngCloseEditorModal() {
        $("#wplng-modal-edit-container").hide();
        $("#wplng-modal-edit-save").prop("disabled", false);
        $("#wplng-modal-container").show();
        wplngInputSignature.onload = '';
        wplngInputSignature.now = '';
    }

    /**
     * Prepare all events and default value on slug editor
     */

    function wplngUpdateEditorEvents() {

        wplngIsUpdatePost = false;
        wplngInputSignature = {
            onload: wplngGetInputSignature(),
            now: wplngGetInputSignature()
        };


        /**
         * Review
         */

        wplngEditor.find('.wplng-mark-as-reviewed input[type="checkbox"]').change(function () {

            var parentSelector = wplngEditor.find("#wplng-slug-" + $(this).attr("wplng-lang"));

            parentSelector.removeClass("wplng-status-generated");
            parentSelector.removeClass("wplng-status-reviewed");
            parentSelector.removeClass("wplng-status-ungenerated");

            if (this.checked) {
                parentSelector.addClass("wplng-status-reviewed");
            } else {
                parentSelector.addClass("wplng-status-generated");
            }
        });

        wplngEditor.find('.wplng-edit-language .wplng-slug-input').each(function () {
            var reviewSelector = wplngEditor.find("#wplng_mark_as_reviewed_" + $(this).attr("lang"));
            reviewSelector.prop("disabled", $(this).val().trim() == "");
        });

        wplngEditor.find('.wplng-edit-language .wplng-slug-input').on("keyup paste", function () {

            var parentSelector = wplngEditor.find("#wplng-slug-" + $(this).attr("lang"));
            var reviewSelector = wplngEditor.find("#wplng_mark_as_reviewed_" + $(this).attr("lang"));

            parentSelector.removeClass("wplng-status-generated");
            parentSelector.removeClass("wplng-status-reviewed");
            parentSelector.removeClass("wplng-status-ungenerated");

            if ($(this).val().trim() == "") {
                parentSelector.addClass("wplng-status-ungenerated");
                reviewSelector.prop("checked", false);
                reviewSelector.prop("disabled", true);
            } else {
                parentSelector.addClass("wplng-status-reviewed");
                reviewSelector.prop("checked", true);
                reviewSelector.prop("disabled", false);
            }

        });

        /**
         * Ajax slug
         */

        wplngEditor.find(".wplng-generate-spin").hide();

        wplngEditor.find(".wplng-generate").on("click", function () {

            if ("disabled" == $(this).attr("disabled")) {
                return;
            }

            var source = wplngEditor.find("#wplng-original-language").attr("wplng-lang");
            var target = wplngEditor.find(this).attr("wplng-lang");
            var text = wplngEditor.find("#wplng-source").html();

            if (undefined == source || undefined == target || undefined == text) {
                return;
            }

            var container = "#wplng-slug-" + target;

            wplngEditor.find(container + " .wplng-generate").attr("disabled", true);
            wplngEditor.find(container + " .wplng-generate-spin").show();

            $.ajax({
                url: wplngLocalize.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'wplng_ajax_slug',
                    text: text,
                    language_source: source,
                    language_target: target
                },
                success: function (data) {
                    if (data.success) {
                        var textarea = "#wplng_slug_" + target;
                        $(textarea).val(data.data);

                        if (data.data != "") {
                            var parentSelector = wplngEditor.find("#wplng-slug-" + target);
                            var reviewSelector = wplngEditor.find("#wplng_mark_as_reviewed_" + target);

                            parentSelector.removeClass("wplng-status-generated");
                            parentSelector.removeClass("wplng-status-reviewed");
                            parentSelector.removeClass("wplng-status-ungenerated");

                            parentSelector.addClass("wplng-status-reviewed");
                            reviewSelector.prop("checked", true);
                            reviewSelector.prop("disabled", false);
                        }

                        wplngCheckInputSignature();

                        $(container + " .wplng-generate-spin").hide();

                        setTimeout(function () {
                            wplngEditor.find(container + " .wplng-generate").attr("disabled", false);
                        }, 8000);

                    } else {
                        console.log("wpLingua - Error:");
                        console.log(data);
                        wplngEditor.find(container + " .wplng-generate-spin")
                            .removeClass("dashicons-update")
                            .removeClass("wplng-spin")
                            .addClass("dashicons-no");
                    }
                },
                error: function (data) {
                    console.log("wpLingua - Error:");
                    console.log(data);
                    wplngEditor.find(container + " .wplng-generate-spin")
                        .removeClass("dashicons-update")
                        .removeClass("wplng-spin")
                        .addClass("dashicons-no");
                }
            });

        });

        /**
         * Alert if page is leave without saving
         * Disable / Enable save button
         */

        wplngEditor
            .find(".wplng-edit-language textarea, .wplng-edit-language input")
            .on("change input propertychange", wplngCheckInputSignature);

        $('#submitpost [type=submit], #wplng-modal-edit-save').click(function () {
            wplngIsUpdatePost = true;
        });

        $(window).on('beforeunload', function () {
            if (!wplngIsUpdatePost
                && wplngInputSignature.onload != wplngInputSignature.now
            ) {
                return confirm(wplngLocalize.message.exitPage);
            }
        });

    }

    /**
     * Show all languages
     */

    $("#wplng-modal-edit-show-all").on("click", function () {
        $(this).hide();
        wplngEditor.find(".wplng-edit-language").show();
    });


    /**
     * Alert if page is leave without saving
     */

    let wplngIsUpdatePost = false;
    let wplngInputSignature = {
        onload: wplngGetInputSignature(),
        now: wplngGetInputSignature()
    };

    function wplngGetInputSignature() {

        let signature = "";

        wplngEditor.find(".wplng-edit-language textarea").each(function () {
            signature += $(this).val();
        });

        wplngEditor.find(".wplng-edit-language input[type=checkbox]").each(function () {
            signature += $(this).prop("checked");
        });

        return signature;
    }

    function wplngCheckInputSignature() {

        wplngInputSignature.now = wplngGetInputSignature();

        if (wplngInputSignature.onload == wplngInputSignature.now) {
            $("#wplng-modal-edit-save").prop("disabled", true);
        } else {
            $("#wplng-modal-edit-save").prop("disabled", false);
        }
    }


    wplngUpdateEditorEvents();



}); // End jQuery loaded event