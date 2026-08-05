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

    let wplngDictionaryEntries = JSON.parse($("#wplng_dictionary_entries").val());

    /**
     * Add new dictionary entry button
     */

    $("#wplng-new-rule-button").click(function () {
        $("#wplng-section-entries-all").hide();
        $("#wplng-section-entry-new").show();
        wplngResizeTextArea($("#wplng-dictionary-entry-new .wplng-adaptive-textarea"));
        window.scrollTo(0, 0);
    });


    /**
     * Add new dictionary entry
     */

    $("#wplng-new-never-translate").change(function () {
        if (this.checked) {
            $("#wplng-new-rules").slideUp("fast");
        } else {
            $("#wplng-new-rules").slideDown("fast");
        }
    });


    /**
     * Click button save new dictionary entry
     */

    $("#wplng-new-add-button").click(function () {

        let source = $("#wplng-new-source").val();
        let rules = {};

        if (undefined == source || '' == source.trim()) {
            return;
        }

        if (!$("#wplng-edit-never-translate").prop("checked")) {
            $(".wplng-new-rule").each(function () {

                let languageId = $(this).attr("wplng-rule");
                let translate = $("textarea", this).val();

                if (undefined != translate && '' != translate.trim()) {
                    rules[languageId] = translate.trim();
                }

            });
        }

        if (0 == Object.keys(rules).length) {
            wplngDictionaryEntries.push({
                "source": source
            });
        } else {
            wplngDictionaryEntries.push({
                "source": source,
                "rules": rules
            });
        }

        $("#wplng_dictionary_entries").val(JSON.stringify(wplngDictionaryEntries));

        $("#submit").click();
    });


    /**
     * Cancel new entry
     */

    $("#wplng-new-cancel-button").click(function () {
        $("#wplng-section-entries-all").show();
        $("#wplng-section-entry-new").hide();
        window.scrollTo(0, 0);
    });


    /**
     * Remove link
     */

    $(".wplng-rule-link-remove").click(function () {

        let ruleNumber = $(this).attr("wplng-rule");
        let newDictionaryEntries = [];
        let counter = 0;

        wplngDictionaryEntries.forEach(element => {
            if (counter != ruleNumber) {
                newDictionaryEntries.push(element);
            }
            counter++;
        });

        $("#wplng_dictionary_entries").val(JSON.stringify(newDictionaryEntries));

        $("#submit").click();

    });


    /**
     * Edit link on dictionary entry
     */

    $(".wplng-rule-link-edit").click(function () {

        let ruleNumber = $(this).attr("wplng-rule");
        let editedDictionaryEntry = wplngDictionaryEntries[ruleNumber];

        $("#wplng-section-entries-all").hide();
        $("#wplng-section-entry-edit").show();
        wplngResizeTextArea($("#wplng-dictionary-entry-edit .wplng-adaptive-textarea"));

        $("#wplng-edit-source").val(editedDictionaryEntry.source);
        $("#wplng-edit-save-button").prop("wplng-rule", ruleNumber);

        if (editedDictionaryEntry.rules == undefined) {
            $("#wplng-edit-never-translate").prop("checked", true);
            $("#wplng-edit-rules").hide();
        } else {
            $("#wplng-edit-never-translate").prop("checked", false);
            $("#wplng-edit-rules").show();
            $("#wplng-edit-rules textarea").val("");
            $.each(editedDictionaryEntry.rules, function (key, value) {
                let textareaSelector = "#wplng-edit-always-translate-" + key;
                $(textareaSelector).val(value);
            });
        }

        window.scrollTo(0, 0);

    });


    /**
     * Never translate checkbox on new dictionary entry
     */

    $("#wplng-edit-never-translate").change(function () {
        if (this.checked) {
            $("#wplng-edit-rules").slideUp("fast");
        } else {
            $("#wplng-edit-rules").slideDown("fast");
        }
    });


    /**
     * Save edited dictionary entry
     */

    $("#wplng-edit-save-button").click(function () {

        let source = $("#wplng-edit-source").val();
        let rules = {};
        let ruleNumber = $(this).prop("wplng-rule");

        if (undefined == source || '' == source.trim()) {
            return;
        }

        if (!$("#wplng-edit-never-translate").prop("checked")) {
            $(".wplng-edit-rule").each(function () {

                let languageId = $(this).attr("wplng-rule");
                let translate = $("textarea", this).val();

                if (undefined != translate && '' != translate.trim()) {
                    rules[languageId] = translate.trim();
                }

            });
        }

        wplngDictionaryEntries.splice(ruleNumber, 1);

        if (0 == Object.keys(rules).length) {
            wplngDictionaryEntries.push({
                "source": source
            });
        } else {
            wplngDictionaryEntries.push({
                "source": source,
                "rules": rules
            });
        }

        $("#wplng_dictionary_entries").val(JSON.stringify(wplngDictionaryEntries));

        $("#submit").click();
    });


    /**
     * Cancel edit entry
     */

    $("#wplng-edit-cancel-button").click(function () {
        $("#wplng-section-entries-all").show();
        $("#wplng-section-entry-edit").hide();
        window.scrollTo(0, 0);
    });


    /**
     * Resize text area
     */

    function wplngResizeTextArea($element) {
        $element.each(function () {
            const el = this;
            el.style.height = '0px';
            const computed = window.getComputedStyle(el);
            if (computed.boxSizing === 'border-box') {
                const borders = parseFloat(computed.borderTopWidth) + parseFloat(computed.borderBottomWidth);
                el.style.height = (el.scrollHeight + borders) + 'px';
            } else {
                const paddings = parseFloat(computed.paddingTop) + parseFloat(computed.paddingBottom);
                el.style.height = (el.scrollHeight - paddings) + 'px';
            }
        });
    }

    let $wplngTextArea = $("textarea.wplng-adaptive-textarea");

    $wplngTextArea.off("keyup.textarea").on("keyup.textarea", function () {
        wplngResizeTextArea($(this));
    });

    $(window).resize(function () {
        $wplngTextArea.each(function () {
            wplngResizeTextArea($(this));
        });
    });

    $wplngTextArea.each(function () {
        wplngResizeTextArea($(this));
    });

    /**
     * Manage translation updating after dictionary rules updated
     */

    $(".wplng-dictionary-update-button-start").click(function () {

        // Build the queue from DOM entries before starting.
        let queue = [];
        $(".wplng-dictionary-text-to-update-entry").each(function () {
            queue.push({
                postId: $(this).data("post-id"),
                impactedLanguages: $(this).data("impacted-languages"),
                check: $(this).data("check"),
                $el: $(this),
            });
        });

        if (queue.length === 0) {
            return;
        }

        // Disable the button for the duration of the process.
        $(".wplng-dictionary-update-subsection-info").hide();
        $(".wplng-dictionary-update-subsection-launch").hide();
        $(".wplng-dictionary-update-subsection-info-progress").show();
        $(".wplng-dictionary-update-ajax-error-message").remove();

        /**
         * Process items one at a time (sequential AJAX).
         * Calls itself recursively only when the current request has completed.
         */
        function processNext(index) {

            $(".wplng-dictionary-update-subsection-info-progress .wplng-count-processed").text(index);

            if (index >= queue.length) {
                $(".wplng-dictionary-update-subsection-info-progress").hide();
                $(".wplng-dictionary-update-subsection-info-end").show();
                return;
            }

            let item = queue[index];

            item.$el.addClass("progress");

            // impacted_languages: jQuery already parsed the JSON attribute,
            // so re-stringify to send a consistent string to PHP.
            let impactedLanguagesParam = item.impactedLanguages === "all"
                ? "all"
                : JSON.stringify(item.impactedLanguages);

            $.ajax({
                url: wplngDictionaryAjax.ajaxurl,
                method: "POST",
                data: {
                    action: "wplng_dictionary_update_translations",
                    nonce: wplngDictionaryAjax.nonce,
                    post_id: item.postId,
                    check: item.check,
                    impacted_languages: impactedLanguagesParam,
                },
                success: function (response) {

                    if (response && response.success === true) {
                        item.$el.removeClass("progress");
                        item.$el.addClass("done");
                    } else {

                        item.$el.removeClass("progress");
                        item.$el.addClass("error");

                        let errorMessage = (response && response.data && response.data.message)
                            ? response.data.message
                            : wplngDictionaryAjax.errorMessageAjax;

                        item.$el.find(".wplng-dictionary-update-ajax-error-message").remove();
                        item.$el.append(
                            '<div class="wplng-dictionary-update-ajax-error-message">' +
                            $("<div>").text(errorMessage).html() +
                            '</div>'
                        );
                    }

                    processNext(index + 1);
                },
                error: function (xhr, status, error) {

                    let errorMessage = wplngDictionaryAjax.errorMessageAjax;

                    item.$el.find(".wplng-dictionary-update-ajax-error-message").remove();
                    item.$el.append(
                        '<div class="wplng-dictionary-update-ajax-error-message">' +
                        $("<div>").text(errorMessage).html() +
                        '</div>'
                    );

                    item.$el.removeClass("progress");
                    item.$el.addClass("error");

                    processNext(index + 1);
                },
            });
        }

        processNext(0);

    });

    $(".wplng-dictionary-update-button-end, .wplng-dictionary-update-button-ignore").click(function () {
        $("#wplng-section-dictionary-update-translations").hide();
        $("#wplng-section-entries-all").show();
    });

}); // End jQuery loaded event
