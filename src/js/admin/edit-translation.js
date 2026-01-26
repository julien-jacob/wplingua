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

    let wplngEditor = $("#wplng-translation-editor");

    /**
     * Expand wpLingua sub-menu
     */

    $("#toplevel_page_wplingua-settings")
        .removeClass("wp-not-current-submenu")
        .addClass("wp-has-current-submenu");

    $("a.toplevel_page_wplingua-settings")
        .addClass("wp-menu-open")
        .addClass("wp-has-current-submenu");

    /**
     * Resize text area
     */
    function wplngResizeTextArea($element) {
        $element.height(0);
        $element.height($element[0].scrollHeight - 4);
    }

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
     * Go to top button
     */

    function wplngToggleGoToTopButton() {
        if ($("#wplng-modal-container").scrollTop() > 0) {
            $("#wplng-scroll-to-top").fadeIn(400);
        } else {
            $("#wplng-scroll-to-top").fadeOut(400);
        }
    }

    wplngToggleGoToTopButton();
    $("#wplng-modal-container").scroll(wplngToggleGoToTopButton);

    $("#wplng-scroll-to-top").click(function () {
        $("#wplng-modal-container").animate({ scrollTop: 0 }, 800);
    });

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

            let parentSelector = wplngEditor.find("#wplng-translation-" + $(this).attr("wplng-lang"));

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
            let reviewSelector = wplngEditor.find("#wplng_mark_as_reviewed_" + $(this).attr("lang"));
            reviewSelector.prop("disabled", $(this).val().trim() == "");
        });

        wplngEditor.find('.wplng-edit-language textarea').on("keyup paste", function () {

            let parentSelector = wplngEditor.find("#wplng-translation-" + $(this).attr("lang"));
            let reviewSelector = wplngEditor.find("#wplng_mark_as_reviewed_" + $(this).attr("lang"));

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

            let source = wplngEditor.find("#wplng-original-language").attr("wplng-lang");
            let target = wplngEditor.find(this).attr("wplng-lang");
            let text = wplngEditor.find("#wplng-source").html();

            if (undefined == source || undefined == target || undefined == text) {
                return;
            }

            let container = "#wplng-translation-" + target;

            wplngEditor.find(container + " .wplng-generate").attr("disabled", true);
            wplngEditor.find(container + " .wplng-generate-spin").show();

            $.ajax({
                url: wplngI18nTranslation.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'wplng_ajax_translation',
                    text: text,
                    language_source: source,
                    language_target: target
                },
                success: function (data) {
                    if (data.success) {
                        let textarea = "#wplng_translation_" + target;
                        $(textarea).val(data.data);

                        if (data.data != "") {
                            let parentSelector = wplngEditor.find("#wplng-translation-" + target);
                            let reviewSelector = wplngEditor.find("#wplng_mark_as_reviewed_" + target);

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
                return confirm(wplngI18nTranslation.message.exitPage);
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

    $(".wplng-edit-link[data-wplng-post]").click(wplngEdit);

    function wplngEdit() {

        // Change cursor to "progress" to indicate loading
        $("html, body").css("cursor", "progress");

        $("#wplng-modal-edit-save").text(wplngI18nTranslation.message.buttonSave);
        $("#wplng-modal-edit-save").prop("disabled", true);

        // Get post ID
        let post = $(this).attr("data-wplng-post");

        // Get edit link
        let editURL = $("#wplng-modal-edit-post").attr("data-wplng-edit-template");
        editURL = editURL.replace('WPLNG_TRANSLATION_ID', post);

        $.ajax({
            url: wplngI18nTranslation.ajaxUrl,
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

                    // Set edit URL
                    $("#wplng-modal-edit-post").attr("href", editURL);

                    // Show the editor modal
                    $("#wplng-modal-edit-container").show();

                    // Hide translation list modal
                    $("#wplng-modal-container").hide();

                    // Update save button
                    $("#wplng-modal-edit-save").attr("data-wplng-post", post);

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
                    languagesToHide += wplngI18nTranslation.currentLanguage;
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
            },
            complete: function () {
                // Reset cursor to default after AJAX completes
                $("html, body").css("cursor", "default");
            }
        });
    }

    $("#wplng-modal-edit-return").click(function () {
        if (wplngInputSignature.onload != wplngInputSignature.now) {
            if (confirm(wplngI18nTranslation.message.exitEditorModal)) {
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

        $("#wplng-modal-edit-save").text(wplngI18nTranslation.message.buttonSaveInProgress);

        let text = $(this).text();
        let post = $(this).attr("data-wplng-post");
        let data = {
            action: 'wplng_ajax_save_modal',
            post_id: post,
            wplng_translation_meta_box_nonce: wplngEditor.find('#wplng_translation_meta_box_nonce').val(),
        };

        wplngEditor.find(".wplng-translation-textarea").each(function () {
            let id = $(this).attr('id');
            data[id] = $(this).val();

            if (('wplng_translation_' + wplngI18nTranslation.currentLanguage) == id) {
                text = $(this).val();
            }
        });

        let isReview = (true == $("#wplng_mark_as_reviewed_" + wplngI18nTranslation.currentLanguage).prop("checked"));

        wplngEditor.find(".wplng-mark-as-reviewed input[type=checkbox]").each(function () {
            data[$(this).attr('id')] = $(this).prop("checked");
        });

        $.ajax({
            url: wplngI18nTranslation.ajaxUrl,
            method: 'POST',
            data: data,
            success: function (data) {
                if (data.success) {

                    let editLink = $("body.wplingua-editor .wplng-edit-link[data-wplng-post=" + post + "]");
                    let modalItem = $("body.wplingua-list .wplng-modal-item[data-wplng-post=" + post + "]")

                    // replace by new text in page
                    editLink.text(text);
                    modalItem.find(".wplng-item-translation").text(text);

                    // Add or remove wplng-is-review class
                    if (isReview) {
                        editLink.addClass('wplng-is-review');
                        modalItem.addClass('wplng-is-review');
                    } else {
                        editLink.removeClass('wplng-is-review');
                        modalItem.removeClass('wplng-is-review');
                    }

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


    /**
     * Search
     */

    $("#wplng-filter-search, #wplng-filter-status").on('input', wplngFilterSearch);

    function wplngFilterSearch() {

        let status = $("#wplng-filter-status").val();
        let itemsVisible = false;

        let searched = $("#wplng-filter-search").val();
        searched = searched.trim().toLowerCase();

        $(".wplng-modal-item").each(function (key) {

            let text_translation = $(this).find(".wplng-item-translation").html();
            let text_source = $(this).find(".wplng-item-source").html();

            text_translation = text_translation.toLowerCase();
            text_source = text_source.toLowerCase();

            let is_show_search = searched == "";
            is_show_search = is_show_search || text_translation.indexOf(searched) >= 0;
            is_show_search = is_show_search || text_source.indexOf(searched) >= 0;

            let is_show_status = status == "all";
            is_show_status = is_show_status || (status == "reviewed" && $(this).hasClass("wplng-is-review"));
            is_show_status = is_show_status || (status == "unreviewed" && !$(this).hasClass("wplng-is-review"));

            if (is_show_search && is_show_status) {
                $(this).show();
                itemsVisible = true;
            } else {
                $(this).hide();
            }
        });

        // Show or hide the "no items found" message
        if (itemsVisible) {
            $("#wplng-modal-no-item-found").hide();
        } else {
            $("#wplng-modal-no-item-found").show();
        }
    }

    /**
     * Ordering
     */

    $("#wplng-filter-order").on('input', wplngFilterOrdering);

    function wplngFilterOrdering() {

        let order = $("#wplng-filter-order").val();
        let items = [];
        let html = "";

        switch (order) {
            case "alphabetical-sources":
                items = wplngSortAlphabetical(
                    ".wplng-modal-item",
                    ".wplng-item-source"
                );
                break;

            case "alphabetical-translations":
                items = wplngSortAlphabetical(
                    ".wplng-modal-item",
                    ".wplng-item-translation"
                );
                break;

            default: // occurrence
                items = wplngSortNumber(
                    ".wplng-modal-item",
                    "data-wplng-order"
                );
                break;
        }



        items.each(function (key) {
            html += $(this).prop('outerHTML');
        });

        $("#wplng-modal-items").html(html);

        $("#wplng-modal-items").find(".wplng-edit-link[data-wplng-post]").click(wplngEdit);

    }

    function wplngSortAlphabetical(selectorParent, selectorText) {
        return $($(selectorParent).toArray().sort(function (a, b) {

            let aVal = $(a).find(selectorText).text();
            let bVal = $(b).find(selectorText).text();

            let returned = 0;
            if (aVal < bVal) {
                returned = -1;
            } else if (aVal > bVal) {
                returned = 1;
            }

            return returned;
        }));
    }

    function wplngSortNumber(selectorParent, attrName) {
        return $($(selectorParent).toArray().sort(function (a, b) {
            let aVal = parseInt(a.getAttribute(attrName)),
                bVal = parseInt(b.getAttribute(attrName));
            return aVal - bVal;
        }));
    }


}); // End jQuery loaded event