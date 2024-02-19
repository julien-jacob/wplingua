jQuery(document).ready(function ($) {

    // TODO : Revoir les noms des id / class
    // TODO : Revoir les commentaires

    /**
     * Cancel
     */

    $("#wplng-new-cancel-button").click(function () {
        $("#wplng-section-entries-all").show();
        $("#wplng-section-entry-new").hide();
    });

    /**
     * New
     */

    $("#wplng-new-rule-button").click(function () {
        $("#wplng-section-entries-all").hide();
        $("#wplng-section-entry-new").show();
        wplngResizeTextArea($("#wplng-dictionary-entry-new .wplng-adaptive-textarea"));
    });

    /**
     * Add new entry
     */

    $("#wplng_new_never_translate").change(function () {
        if (this.checked) {
            $("#wplng-new-rules").slideUp("fast");
        } else {
            $("#wplng-new-rules").slideDown("fast");
        }
    });


    $("#wplng-new-add-button").click(function () {

        let dictionaryEntries = JSON.parse($("#wplng_dictionary_entries").val());
        let source = $("#wplng-new-source").val();
        let rules = {};

        if (undefined == source || '' == source.trim()) {
            return;
        }

        $(".wplng-new-rule").each(function () {

            let languageId = $(this).attr("wplng-rule");
            let translate = $("textarea", this).val();

            if (undefined != translate && '' != translate.trim()) {
                rules[languageId] = translate.trim();
            }

        });

        if (0 == Object.keys(rules).length) {
            dictionaryEntries.push({
                "source": source
            });
        } else {
            dictionaryEntries.push({
                "source": source,
                "rules": rules
            });
        }

        $("#wplng_dictionary_entries").val(JSON.stringify(dictionaryEntries));

        $("#submit").click();
    })

    /**
     * Remove link
     */

    $(".wplng-rule-link-remove").click(function () {

        let ruleNumber = $(this).attr("wplng-rule");
        let dictionaryEntries = JSON.parse($("#wplng_dictionary_entries").val());
        let newDictionaryEntries = [];
        let counter = 0;

        dictionaryEntries.forEach(element => {
            if (counter != ruleNumber) {
                newDictionaryEntries.push(element);
            }
            counter++;
        });

        $("#wplng_dictionary_entries").val(JSON.stringify(newDictionaryEntries));

        $("#submit").click();

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
