jQuery(document).ready(function ($) {

    var mcvWebsiteLanguage = $("#mcv_website_language").val();
    var mcvTargetLanguages = JSON.parse($("#mcv_target_languages").val());

    function mcvTargetLanguagesIncludes(languageId) {

        var returned = false;

        mcvTargetLanguages.forEach(targetLanguage => {
            if (targetLanguage.id == languageId) {
                returned = true;
            }
        });

        return returned;
    }

    function mcvGetOptionsWebsiteLanguageHTML() {

        var languagesOptionsHTML = "";

        mcvAllLanguages.forEach((language) => {

            var selected = "";
            var disabled = "";

            if (
                mcvWebsiteLanguage !== undefined &&
                mcvWebsiteLanguage === language.id
            ) {
                selected = " selected";
            }

            if (mcvTargetLanguagesIncludes(language.id)) {
                disabled = " disabled";
            }

            languagesOptionsHTML +=
                '<option value="' +
                language.id +
                '"' +
                selected +
                disabled +
                ">" +
                language.name +
                "</option>";
        });

        return languagesOptionsHTML;
    }

    function mcvGetOptionstargetLanguagesHTML() {

        var languagesOptionsHTML = "";

        mcvAllLanguages.forEach((language) => {
            var disabled = "";
            if (
                mcvWebsiteLanguage !== undefined &&
                mcvWebsiteLanguage === language.id
            ) {
                disabled = " disabled";
            }

            if (mcvTargetLanguagesIncludes(language.id)) {
                disabled = " disabled";
            }

            languagesOptionsHTML +=
                '<option value="' +
                language.id +
                '"' +
                disabled +
                ">" +
                language.name +
                "</option>";
        });

        return languagesOptionsHTML;
    }



    function mcvGetWebsiteLanguageFlagsHTML() {

        var flagsRadiosHTML = "";

        mcvAllLanguages.forEach((language) => {

            if (language.id == mcvWebsiteLanguage) {

                var textCustomRadio = $("#mcv-flags-radio-original-website-custom").text();
                var websiteFlagUrl = $("#mcv_website_flag").val();
                var flagFirstChecked = false;
                var flagCustomChecked = " checked";

                if (websiteFlagUrl == "") {
                    flagFirstChecked = true;
                }

                language.flags.forEach(flag => {

                    var checked = "";

                    if (websiteFlagUrl == flag.flag) {
                        checked = " checked";
                        flagCustomChecked = "";
                    }

                    if (flagFirstChecked) {
                        checked = " checked";
                        flagCustomChecked = "";
                        flagFirstChecked = false;
                        $("#mcv_website_flag").val(flag.flag);
                    }

                    flagsRadiosHTML +=
                        '<span class="mcv-flags-radio">' +
                        '<input type="radio" ' +
                        'name="mcv-website-flag" ' +
                        'value="' + flag.flag + '" ' +
                        'id="mcv-flag-' + flag.id + '"' + checked + '>' +
                        '<label for="mcv-flag-' + flag.id + '">' +
                        flag.name + ' (<img src="' + flag.flag + '">)' +
                        '</label></span>';
                });

                flagsRadiosHTML +=
                    '<span class="mcv-flags-radio">' +
                    '<input type="radio" name="mcv-website-flag" id="mcv-website-flag-custom" value="custom"' + flagCustomChecked + '>' +
                    '<label for="mcv-website-flag-custom">' + textCustomRadio + '</label>' +
                    '</span>';

                if ("" != flagCustomChecked) {
                    $("#mcv-website-flag-container").show();
                } else {
                    $("#mcv-website-flag-container").hide();
                }
            }
        });

        return flagsRadiosHTML;
    }



    function mcvGetTargetLanguagesListHTML() {

        var html = "";
        var htmlTemplate = $("#mcv-target-language-template").html();

        mcvAllLanguages.forEach((language) => {

            var htmlElement = "";

            if (mcvTargetLanguagesIncludes(language.id)) {

                var textCustomRadio = $("#mcv-flags-radio-original-website-custom").text();
                var flagsRadiosHTML = "";
                var flagFirstChecked = false;
                var flagCustomChecked = " checked";
                var targetFlagUrl = language.flag;

                if (targetFlagUrl == "") {
                    flagFirstChecked = true;
                }

                language.flags.forEach(flag => {

                    var checked = "";

                    if (targetFlagUrl == flag.flag) {
                        checked = " checked";
                        flagCustomChecked = "";
                    }

                    if (flagFirstChecked) {
                        checked = " checked";
                        flagCustomChecked = "";
                        flagFirstChecked = false;
                        value = flag.flag;
                    }

                    flagsRadiosHTML +=
                        '<span class="mcv-subflags-radio">' +
                        '<input type="radio" ' +
                        'name="mcv-target-subflag-' + language.id + '" ' +
                        'value="' + flag.flag + '" ' +
                        'mcv-target-lang="' + language.id + '" ' +
                        'id="mcv-subflag-' + language.id + '-' + flag.id + '"' + checked + '>' +
                        '<label for="mcv-subflag-' + language.id + '-' + flag.id + '">' +
                        flag.name + ' (<img src="' + flag.flag + '">)' +
                        '</label></span>';
                });

                flagsRadiosHTML +=
                    '<span class="mcv-flags-radio">' +
                    '<input type="radio" name="mcv-target-subflag-' + language.id +
                    '" id="mcv-target-flag-custom-' + language.id + '" value="custom"' +
                    ' mcv-target-lang="' + language.id + '" ' +
                    flagCustomChecked + '>' +
                    '<label for="mcv-target-flag-custom-' +
                    language.id + '">' + textCustomRadio + '</label>' +
                    '</span>';

                htmlElement = htmlTemplate;
                htmlElement = htmlElement.replaceAll("[NAME]", language.name);
                htmlElement = htmlElement.replaceAll("[LANG]", language.id);
                var htmlFlag =
                    '<img src="' + targetFlagUrl + '" class="mcv-target-language">';
                htmlElement = htmlElement.replaceAll("[FLAG]", htmlFlag);
                htmlElement = htmlElement.replaceAll("[FLAGS_OPTIONS]", flagsRadiosHTML);

                var htmlInput = '<input type="url" class="mcv-target-subflag" mcv-target-lang="' + language.id + '" value="' + language.flag + '" />';
                htmlElement = htmlElement.replaceAll("[INPUT]", htmlInput);
                html += htmlElement;
            }
        });

        return html;
    }

    // Option Page : Click on "Add" button for new language target
    $("#mcv-target-lang-add").on("click", function () {

        var newTargetId = $("#mcv_add_new_target_language").val();
        var newTargetFlag = "";

        mcvAllLanguages.forEach((language) => {
            if (language.id == newTargetId) {
                newTargetFlag = language.flag;
            }
        });

        var newTarget = {
            "id": newTargetId,
            "flag": newTargetFlag
        };

        if (!mcvTargetLanguagesIncludes(newTargetId)) {
            mcvTargetLanguages.push(newTarget);
        }

        mcvUpdateOptionPage();
    });

    $("#mcv-target-languages-list").on(
        "click",
        ".mcv-target-lang-remove",
        (event) => {

            var newTargetLanguages = [];
            var removed = $(event.target).attr("mcv-target-lang");

            mcvTargetLanguages.forEach((language) => {
                if (language.id != removed) {
                    newTargetLanguages.push(language);
                }
            });

            mcvTargetLanguages = newTargetLanguages;

            mcvUpdateOptionPage();
        }
    );

    $("#mcv_website_language").on("change", function () {
        mcvWebsiteLanguage = $("#mcv_website_language").val();
        $("#mcv_website_flag").val("");
        $("#mcv_add_new_target_language").html(mcvGetOptionstargetLanguagesHTML());
        $("#mcv-flags-radio-original-website").html(mcvGetWebsiteLanguageFlagsHTML());
    });


    $('#mcv-flags-radio-original-website').on("click", "input[type=radio][name=mcv-website-flag]", function () {
        if ($("#mcv-website-flag-custom").is(':checked')) {
            $("#mcv-website-flag-container").slideDown("fast");
        } else {
            $("#mcv-website-flag-container").slideUp("fast");
            $("#mcv_website_flag").val($(this).val());
        }
    });

    $("#mcv-target-languages-list").on("click", ".mcv-target-lang-update-flag", function () {
        var languageId = $(this).attr("mcv-target-lang");
        var selector = "#mcv-target-languages-list .mcv-flag-target-container[mcv-target-lang=" + languageId + "]";

        $(selector).slideToggle();
    });

    $('#mcv-target-languages-list').on("click", "input[type=radio]", function () {

        var selectedFlagId = $(this).attr("mcv-target-lang");
        var selectedFlagVal = $(this).val();
        var selectorSubflagContainer = ".mcv-subflag-target-custom[mcv-target-lang=" + selectedFlagId + "]";

        if (selectedFlagVal == "custom") {
            $(selectorSubflagContainer).slideDown("fast");
        } else {
            $(selectorSubflagContainer).slideUp("fast");
            $(".mcv-target-subflag[mcv-target-lang=" + selectedFlagId + "]").val(selectedFlagVal);

            var newTargetLanguages = [];
            mcvTargetLanguages.forEach(language => {
                if (language.id == selectedFlagId) {
                    newTargetLanguages.push({
                        "id": language.id,
                        "flag": selectedFlagVal
                    });
                } else {
                    newTargetLanguages.push(language);
                }
            });

            mcvTargetLanguages = newTargetLanguages;
            $("#mcv_target_languages").val(JSON.stringify(newTargetLanguages));
        }
    });

    $("#mcv-target-languages-list").on("input", ".mcv-target-subflag", function () {

        var selectedFlagId = $(this).attr("mcv-target-lang");
        var selectedFlagVal = $(this).val();
        var newTargetLanguages = [];

        mcvTargetLanguages.forEach(language => {
            if (language.id == selectedFlagId) {
                newTargetLanguages.push({
                    "id": language.id,
                    "flag": selectedFlagVal
                });
            } else {
                newTargetLanguages.push(language);
            }
        });

        mcvTargetLanguages = newTargetLanguages;
        $("#mcv_target_languages").val(JSON.stringify(newTargetLanguages));
    });

    function mcvUpdateOptionPage() {
        $("#mcv_website_language").html(mcvGetOptionsWebsiteLanguageHTML());
        $("#mcv_add_new_target_language").html(mcvGetOptionstargetLanguagesHTML());
        $("#mcv-flags-radio-original-website").html(mcvGetWebsiteLanguageFlagsHTML());
        $("#mcv-target-languages-list").html(mcvGetTargetLanguagesListHTML());
        $("#mcv_target_languages").val(JSON.stringify(mcvTargetLanguages));
    }

    mcvUpdateOptionPage();

}); // End jQuery loaded event


