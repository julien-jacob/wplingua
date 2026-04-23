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

    // Snapshot of the value saved in DB (used to detect changes and to restore on cancel)
    let wplngDictionaryEntriesOriginal = $("#wplng_dictionary_entries").val();


    // =========================================================
    // OVERLAY HELPERS
    // =========================================================

    function wplngDictionaryOverlaySetState(state) {
        $("#wplng-dictionary-loading-section").hide();
        $("#wplng-dictionary-confirm-section").hide();
        $("#wplng-dictionary-progress-section").hide();
        $("#wplng-dictionary-success-section").hide();
        $("#wplng-dictionary-error-section").hide();
        if (state) {
            $("#wplng-dictionary-" + state + "-section").show();
        }
    }

    function wplngDictionaryOverlayShow() {
        $("#wplng-section-entries-all").hide();
        $("#wplng-section-entry-new").hide();
        $("#wplng-section-entry-edit").hide();
        $("#wplng-section-entry-confirm").show();
        window.scrollTo(0, 0);
    }

    function wplngDictionaryOverlayHide() {
        $("#wplng-section-entry-confirm").hide();
        $("#wplng-section-entries-all").show();
        window.scrollTo(0, 0);
    }

    function wplngDictionaryShowLoading(message) {
        $("#wplng-dictionary-overlay-title").text(wplngDictionaryData.i18n.loadingTitle);
        $("#wplng-dictionary-overlay-message").text(message || "");
        wplngDictionaryOverlaySetState("loading");
    }

    function wplngDictionaryShowConfirm(count, token) {
        let message = wplngDictionaryData.i18n.confirmMessage.replace("%d", count);
        $("#wplng-dictionary-overlay-title").text(wplngDictionaryData.i18n.confirmTitle);
        $("#wplng-dictionary-overlay-message").text(message);
        $("#wplng-dictionary-confirm-btn").text(wplngDictionaryData.i18n.confirmButton);
        $("#wplng-dictionary-cancel-btn").text(wplngDictionaryData.i18n.cancelButton);
        wplngDictionaryOverlaySetState("confirm");

        $("#wplng-dictionary-confirm-btn").off("click.wplng").on("click.wplng", function () {
            wplngDictionaryStartProgress(count, token);
        });

        $("#wplng-dictionary-cancel-btn").off("click.wplng").on("click.wplng", function () {
            wplngDictionaryCancel();
        });
    }

    function wplngDictionaryStartProgress(total, token) {
        $("#wplng-dictionary-overlay-title").text(wplngDictionaryData.i18n.progressTitle);
        $("#wplng-dictionary-overlay-message").text("");
        wplngDictionaryOverlaySetState("progress");
        wplngDictionaryUpdateProgress(0, total);
        wplngDictionaryRunBatch(token, total);
    }

    function wplngDictionaryUpdateProgress(processed, total) {
        let pct = total > 0 ? Math.round((processed / total) * 100) : 100;
        $("#wplng-dictionary-progress-bar").css("width", pct + "%");
        let text = wplngDictionaryData.i18n.progressText
            .replace("%1$d", processed)
            .replace("%2$d", total);
        $("#wplng-dictionary-progress-text").text(text);
    }

    function wplngDictionaryShowError(message) {
        $("#wplng-dictionary-overlay-title").text(wplngDictionaryData.i18n.errorTitle);
        $("#wplng-dictionary-overlay-message").text(message);
        $("#wplng-dictionary-error-close-btn").text(wplngDictionaryData.i18n.errorCloseBtn);
        wplngDictionaryOverlaySetState("error");

        $("#wplng-dictionary-error-close-btn").off("click.wplng").on("click.wplng", function () {
            wplngDictionaryCancel();
        });
    }

    function wplngDictionaryCancel() {
        wplngDictionaryOverlayHide();
        // Restore in-memory entries and the hidden textarea to the original DB value
        wplngDictionaryEntries = JSON.parse(wplngDictionaryEntriesOriginal);
        $("#wplng_dictionary_entries").val(wplngDictionaryEntriesOriginal);
        // Restore UI to the entries list
        $("#wplng-section-entries-all").show();
        $("#wplng-section-entry-new").hide();
        $("#wplng-section-entry-edit").hide();
    }


    // =========================================================
    // BATCH PROCESSING
    // =========================================================

    function wplngDictionaryRunBatch(token, total) {
        $.ajax({
            url: wplngDictionaryData.ajaxUrl,
            type: "POST",
            data: {
                action: "wplng_ajax_dictionary_apply_batch",
                nonce: wplngDictionaryData.nonce,
                token: token,
            },
            success: function (response) {
                if (!response.success) {
                    wplngDictionaryShowError(
                        response.data && response.data.message
                            ? response.data.message
                            : wplngDictionaryData.i18n.errorMessage
                    );
                    return;
                }

                let processed = response.data.processed;
                let done = response.data.done;

                if (total > 0) {
                    wplngDictionaryUpdateProgress(processed, total);
                }

                if (done) {
                    wplngDictionaryOverlaySetState("success");
                    $("#wplng-dictionary-overlay-title").text(wplngDictionaryData.i18n.successTitle);
                    $("#wplng-dictionary-overlay-message").text(wplngDictionaryData.i18n.successMessage);
                    setTimeout(function () {
                        window.location.reload();
                    }, 1500);
                } else {
                    wplngDictionaryRunBatch(token, total);
                }
            },
            error: function () {
                wplngDictionaryShowError(wplngDictionaryData.i18n.errorMessage);
            },
        });
    }


    // =========================================================
    // FORM SUBMIT INTERCEPT
    // =========================================================

    $("form").on("submit.wplng_dictionary", function (e) {
        let newVal = $("#wplng_dictionary_entries").val();

        if (newVal === wplngDictionaryEntriesOriginal) {
            // No dictionary change — let the form submit normally
            return;
        }

        e.preventDefault();

        wplngDictionaryShowLoading();
        wplngDictionaryOverlayShow();

        $.ajax({
            url: wplngDictionaryData.ajaxUrl,
            type: "POST",
            data: {
                action: "wplng_ajax_dictionary_preview",
                nonce: wplngDictionaryData.nonce,
                old_entries: wplngDictionaryEntriesOriginal,
                new_entries: newVal,
            },
            success: function (response) {
                if (!response.success) {
                    wplngDictionaryShowError(
                        response.data && response.data.message
                            ? response.data.message
                            : wplngDictionaryData.i18n.errorMessage
                    );
                    return;
                }

                let count = response.data.count;
                let token = response.data.token;

                if (count === 0) {
                    // No translations affected — save immediately with a brief message
                    wplngDictionaryShowLoading(wplngDictionaryData.i18n.noImpactMessage);
                    wplngDictionaryRunBatch(token, 0);
                } else {
                    wplngDictionaryShowConfirm(count, token);
                }
            },
            error: function () {
                wplngDictionaryShowError(wplngDictionaryData.i18n.errorMessage);
            },
        });
    });


    // =========================================================
    // EXISTING UI HANDLERS
    // =========================================================

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
        $element.height(0);
        $element.height($element[0].scrollHeight);
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

}); // End jQuery loaded event
