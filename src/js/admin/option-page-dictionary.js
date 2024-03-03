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
