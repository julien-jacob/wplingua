jQuery(document).ready(function ($) {

    let wplngEditor = $("#wplng-translation-editor");

    /**
     * Resize text area
     */
    function wplngResizeTextArea($element) {
        $element.height(0);
        $element.height($element[0].scrollHeight);
    }

    /**
     * Decode HTML
     */
    function wplngDecodeHtml(string) {
        let returnText = string;
        returnText = returnText.replace(/&nbsp;/gi, " ");
        returnText = returnText.replace(/&amp;/gi, "&");
        returnText = returnText.replace(/&quot;/gi, `"`);
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
     * Prepare all events and default value on translation editor
     */

    function wplngUpdateEditorEvents() {

        wplngIsUpdatePost = false;
        wplngInputSignature = {
            onload: wplngGetInputSignature(),
            now: wplngGetInputSignature()
        };

        /**
         * Resize text area
         */

        let wplngTextArea = wplngEditor.find(".wplng-edit-language textarea");

        wplngTextArea.off("keyup.textarea").on("keyup.textarea", function () {
            wplngResizeTextArea($(this));
        });

        $(window).resize(function () {
            wplngTextArea.each(function () {
                wplngResizeTextArea($(this));
            });
        });

        wplngTextArea.each(function () {
            wplngResizeTextArea($(this));
        });

        /**
         * Review
         */

        wplngEditor.find('.wplng-mark-as-reviewed input[type="checkbox"]').change(function () {

            var parentSelector = wplngEditor.find("#wplng-translation-" + $(this).attr("wplng-lang"));

            parentSelector.removeClass("wplng-status-generated");
            parentSelector.removeClass("wplng-status-reviewed");
            parentSelector.removeClass("wplng-status-ungenerated");

            if (this.checked) {
                parentSelector.addClass("wplng-status-reviewed");
            } else {
                parentSelector.addClass("wplng-status-generated");
            }
        });

        wplngEditor.find('.wplng-edit-language textarea').each(function () {
            var reviewSelector = wplngEditor.find("#wplng_mark_as_reviewed_" + $(this).attr("lang"));
            reviewSelector.prop("disabled", $(this).val().trim() == "");
        });

        wplngEditor.find('.wplng-edit-language textarea').on("keyup paste", function () {

            var parentSelector = wplngEditor.find("#wplng-translation-" + $(this).attr("lang"));
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
         * Ajax translation
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

            var container = "#wplng-translation-" + target;

            wplngEditor.find(container + " .wplng-generate").attr("disabled", true);
            wplngEditor.find(container + " .wplng-generate-spin").show();

            $.ajax({
                url: wplngLocalize.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'wplng_ajax_translation',
                    text: text,
                    language_source: source,
                    language_target: target
                },
                success: function (data) {
                    if (data.success) {
                        var textarea = "#wplng_translation_" + target;
                        $(textarea).val(data.data);

                        if (data.data != "") {
                            var parentSelector = wplngEditor.find("#wplng-translation-" + target);
                            var reviewSelector = wplngEditor.find("#wplng_mark_as_reviewed_" + target);

                            parentSelector.removeClass("wplng-status-generated");
                            parentSelector.removeClass("wplng-status-reviewed");
                            parentSelector.removeClass("wplng-status-ungenerated");

                            parentSelector.addClass("wplng-status-reviewed");
                            reviewSelector.prop("checked", true);
                            reviewSelector.prop("disabled", false);
                        }

                        wplngResizeTextArea($(textarea));
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

    /**
     * Ajax edit modal
     */

    $(".wplng-edit-link[wplng_post]").click(function () {

        $("#wplng-modal-edit-save").text(wplngLocalize.message.buttonSave);
        $("#wplng-modal-edit-save").prop("disabled", true);

        let post = $(this).attr("wplng_post");

        $.ajax({
            url: wplngLocalize.ajaxUrl,
            method: 'POST',
            data: {
                action: 'wplng_ajax_edit_modal',
                post_id: post
            },
            success: function (data) {
                if (data.success) {

                    // Put new HTML in modal
                    let html = JSON.parse(data.data);
                    html = wplngDecodeHtml(html.wplng_edit_html);
                    wplngEditor.html(html);

                    // Show the editor modal
                    $("#wplng-modal-edit-container").show();

                    // Hide translation list modal
                    $("#wplng-modal-container").hide();

                    // Update save button
                    $("#wplng-modal-edit-save").attr("wplng_post", post);

                    // Hide "All languages" button if only one target language
                    if (wplngEditor.find(".wplng-edit-language").length == 1) {
                        $("#wplng-modal-edit-show-all").hide();
                    } else {
                        $("#wplng-modal-edit-show-all").show();
                    }

                    // Reload events
                    wplngUpdateEditorEvents();

                    // Hide not current languages
                    let languagesToHide = ".wplng-edit-language:not([wplng-lang=";
                    languagesToHide += wplngLocalize.currentLanguage;
                    languagesToHide += "])";

                    wplngEditor.find(languagesToHide).hide();

                } else {
                    console.log("wpLingua - Error:");
                    console.log(data);
                }
            },
            error: function (data) {
                console.log("wpLingua - Error:");
                console.log(data);
            }
        });
    });

    $("#wplng-modal-edit-return").click(function () {
        if (wplngInputSignature.onload != wplngInputSignature.now) {
            if (confirm(wplngLocalize.message.exitEditorModal)) {
                wplngCloseEditorModal();
            }
        } else {
            wplngCloseEditorModal();
        }
    });

    /**
     * Save edited translation
     */

    $("#wplng-modal-edit-save").click(function () {

        $("#wplng-modal-edit-save").text(wplngLocalize.message.buttonSaveInProgress);

        let text = $(this).text();
        let post = $(this).attr("wplng_post");
        let data = {
            action: 'wplng_ajax_save_modal',
            post_id: post,
            wplng_translation_meta_box_nonce: wplngEditor.find('#wplng_translation_meta_box_nonce').val(),
        };

        wplngEditor.find(".wplng-translation-textarea").each(function () {
            let id = $(this).attr('id');
            data[id] = $(this).val();

            if (('wplng_translation_' + wplngLocalize.currentLanguage) == id) {
                text = $(this).val();
            }
        });

        wplngEditor.find(".wplng-mark-as-reviewed input[type=checkbox]").each(function () {
            data[$(this).attr('id')] = $(this).prop("checked");
        });

        $.ajax({
            url: wplngLocalize.ajaxUrl,
            method: 'POST',
            data: data,
            success: function (data) {
                if (data.success) {

                    // replace by new text in page
                    $("body.wplingua-editor .wplng-edit-link[wplng_post=" + post + "]").text(text);
                    $("body.wplingua-list .wplng-modal-item[wplng_post=" + post + "] .wplng-item-translation").text(text);

                    // Hide the editor modal
                    wplngCloseEditorModal();

                } else {
                    console.log("wpLingua - Error:");
                    console.log(data);
                }
            },
            error: function (data) {
                console.log("wpLingua - Error:");
                console.log(data);
            }
        });
    });

}); // End jQuery loaded event