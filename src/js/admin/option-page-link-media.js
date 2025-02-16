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

    let wplngLinkMediaEntries = JSON.parse($("#wplng_link_media_entries").val());

    /**
     * Add new entry button
     */

    $("#wplng-new-rule-button").click(function () {
        $("#wplng-section-entries-all").hide();
        $("#wplng_new_mode_exactly").prop("checked", true);
        $("#wplng-section-entry-new").show();
        window.scrollTo(0, 0);
    });

    /**
     * Click button save new entry
     */

    $("#wplng-new-add-button").click(function () {

        let source = $("#wplng-new-source").val();
        let mode = "exactly";
        let modeRadio = $("input[type='radio'][name='wplng_new_mode']:checked");
        let rules = {};

        if (undefined == source || '' == source.trim()) {
            return;
        }

        if (modeRadio.length > 0) {
            mode = modeRadio.val();
        }

        if (mode !== 'partially' && mode !== 'regex') {
            mode = "exactly";
        }

        $(".wplng-new-rule").each(function () {

            let languageId = $(this).attr("wplng-rule");
            let translate = $('input[type="text"]', this).val();

            if (undefined != translate && '' != translate.trim()) {
                rules[languageId] = translate.trim();
            }

        });

        if (0 == Object.keys(rules).length) {
            return;
        }

        wplngLinkMediaEntries.push({
            "source": source,
            "mode": mode,
            "rules": rules
        });

        $("#wplng_link_media_entries").val(JSON.stringify(wplngLinkMediaEntries));

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
        let newLinkMediaEntries = [];
        let counter = 0;

        wplngLinkMediaEntries.forEach(element => {
            if (counter != ruleNumber) {
                newLinkMediaEntries.push(element);
            }
            counter++;
        });

        $("#wplng_link_media_entries").val(JSON.stringify(newLinkMediaEntries));

        $("#submit").click();

    });


    /**
     * Edit link on entry
     */

    $(".wplng-rule-link-edit").click(function () {

        let ruleNumber = $(this).attr("wplng-rule");
        let editedLinkMediaEntry = wplngLinkMediaEntries[ruleNumber];

        $("#wplng-section-entries-all").hide();
        $("#wplng-section-entry-edit").show();

        $("#wplng-edit-source").val(editedLinkMediaEntry.source);

        switch (editedLinkMediaEntry.mode) {
            case "exactly":
                $("#wplng_edit_mode_exactly").prop("checked", true);
                $("#wplng_edit_mode_partially").prop("checked", false);
                $("#wplng_edit_mode_regex").prop("checked", false);
                break;

            case "partially":
                $("#wplng_edit_mode_exactly").prop("checked", false);
                $("#wplng_edit_mode_partially").prop("checked", true);
                $("#wplng_edit_mode_regex").prop("checked", false);
                break;

            case "regex":
                $("#wplng_edit_mode_exactly").prop("checked", false);
                $("#wplng_edit_mode_partially").prop("checked", false);
                $("#wplng_edit_mode_regex").prop("checked", true);
                break;
        }

        $("#wplng-edit-save-button").prop("wplng-rule", ruleNumber);

        $('#wplng-edit-rules input[type="text"]').val("");
        $.each(editedLinkMediaEntry.rules, function (key, value) {
            let textareaSelector = "#wplng-edit-always-translate-" + key;
            $(textareaSelector).val(value);
        });

        window.scrollTo(0, 0);

    });

    /**
     * Save edited entry
     */

    $("#wplng-edit-save-button").click(function () {

        let source = $("#wplng-edit-source").val();
        let mode = "exactly";
        let modeRadio = $("input[type='radio'][name='wplng_edit_mode']:checked");
        let rules = {};
        let ruleNumber = $(this).prop("wplng-rule");

        if (undefined == source || '' == source.trim()) {
            return;
        }

        if (modeRadio.length > 0) {
            mode = modeRadio.val();
        }


        if (mode !== 'partially' && mode !== 'regex') {
            mode = "exactly";
        }


        $(".wplng-edit-rule").each(function () {

            let languageId = $(this).attr("wplng-rule");
            let translate = $('input[type="text"]', this).val();

            if (undefined != translate && '' != translate.trim()) {
                rules[languageId] = translate.trim();
            }

        });

        wplngLinkMediaEntries.splice(ruleNumber, 1);

        if (0 == Object.keys(rules).length) {
            return;
        }

        wplngLinkMediaEntries.push({
            "source": source,
            "mode": mode,
            "rules": rules
        });

        $("#wplng_link_media_entries").val(JSON.stringify(wplngLinkMediaEntries));

        // console.log(mode);
        // return;
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

}); // End jQuery loaded event
