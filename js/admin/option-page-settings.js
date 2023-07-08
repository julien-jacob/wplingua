jQuery(document).ready(function ($) {

    var wplngWebsiteLanguage = $("#wplng_website_language").val();
    var wplngTargetLanguages = JSON.parse($("#wplng_target_languages").val());

    function wplngTargetLanguagesIncludes(languageId) {

        var returned = false;

        wplngTargetLanguages.forEach(targetLanguage => {
            if (targetLanguage.id == languageId) {
                returned = true;
            }
        });

        return returned;
    }

    function wplngGetOptionsWebsiteLanguageHTML() {

        var languagesOptionsHTML = "";

        wplngAllLanguages.forEach((language) => {

            var selected = "";
            var disabled = "";

            if (
                wplngWebsiteLanguage !== undefined &&
                wplngWebsiteLanguage === language.id
            ) {
                selected = " selected";
            }

            if (wplngTargetLanguagesIncludes(language.id)) {
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

    function wplngGetOptionstargetLanguagesHTML() {

        var languagesOptionsHTML = "";
        var hideFieldset = true;

        wplngAllLanguages.forEach((language) => {
            var disabled = "";
            if (
                ( 
                    wplngWebsiteLanguage !== undefined 
                    && wplngWebsiteLanguage === language.id 
                )
                || wplngTargetLanguagesIncludes(language.id)
            ) {
                disabled = " disabled";
            } else {
                hideFieldset = false;
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

        if (hideFieldset) {
            $("#fieldset-add-target-language").hide();
        } else {
            $("#fieldset-add-target-language").show();
        }

        return languagesOptionsHTML;
    }



    function wplngGetWebsiteLanguageFlagsHTML() {

        var flagsRadiosHTML = "";

        wplngAllLanguages.forEach((language) => {

            if (language.id == wplngWebsiteLanguage) {

                var textCustomRadio = $("#wplng-flags-radio-original-website-custom").text();
                var websiteFlagUrl = $("#wplng_website_flag").val();
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
                        $("#wplng_website_flag").val(flag.flag);
                    }

                    flagsRadiosHTML +=
                        '<span class="wplng-flags-radio">' +
                        '<input type="radio" ' +
                        'name="wplng-website-flag" ' +
                        'value="' + flag.flag + '" ' +
                        'id="wplng-flag-' + flag.id + '"' + checked + '>' +
                        '<label for="wplng-flag-' + flag.id + '">' +
                        flag.name + ' (<img src="' + flag.flag + '">)' +
                        '</label></span>';
                });

                flagsRadiosHTML +=
                    '<span class="wplng-flags-radio">' +
                    '<input type="radio" name="wplng-website-flag" id="wplng-website-flag-custom" value="custom"' + flagCustomChecked + '>' +
                    '<label for="wplng-website-flag-custom">' + textCustomRadio + '</label>' +
                    '</span>';

                if ("" != flagCustomChecked) {
                    $("#wplng-website-flag-container").show();
                } else {
                    $("#wplng-website-flag-container").hide();
                }
            }
        });

        return flagsRadiosHTML;
    }



    function wplngGetTargetLanguagesListHTML() {

        var html = "";
        var htmlTemplate = $("#wplng-target-language-template").html();

        wplngAllLanguages.forEach((language) => {

            var htmlElement = "";

            if (wplngTargetLanguagesIncludes(language.id)) {

                var textCustomRadio = $("#wplng-flags-radio-original-website-custom").text();
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
                        '<span class="wplng-subflags-radio">' +
                        '<input type="radio" ' +
                        'name="wplng-target-subflag-' + language.id + '" ' +
                        'value="' + flag.flag + '" ' +
                        'wplng-target-lang="' + language.id + '" ' +
                        'id="wplng-subflag-' + language.id + '-' + flag.id + '"' + checked + '>' +
                        '<label for="wplng-subflag-' + language.id + '-' + flag.id + '">' +
                        flag.name + ' (<img src="' + flag.flag + '">)' +
                        '</label></span>';
                });

                flagsRadiosHTML +=
                    '<span class="wplng-flags-radio">' +
                    '<input type="radio" name="wplng-target-subflag-' + language.id +
                    '" id="wplng-target-flag-custom-' + language.id + '" value="custom"' +
                    ' wplng-target-lang="' + language.id + '" ' +
                    flagCustomChecked + '>' +
                    '<label for="wplng-target-flag-custom-' +
                    language.id + '">' + textCustomRadio + '</label>' +
                    '</span>';

                htmlElement = htmlTemplate;
                htmlElement = htmlElement.replaceAll("[NAME]", language.name);
                htmlElement = htmlElement.replaceAll("[LANG]", language.id);
                var htmlFlag =
                    '<img src="' + targetFlagUrl + '" class="wplng-target-language">';
                htmlElement = htmlElement.replaceAll("[FLAG]", htmlFlag);
                htmlElement = htmlElement.replaceAll("[FLAGS_OPTIONS]", flagsRadiosHTML);

                var htmlInput = '<input type="url" class="wplng-target-subflag" wplng-target-lang="' + language.id + '" value="' + language.flag + '" />';
                htmlElement = htmlElement.replaceAll("[INPUT]", htmlInput);
                html += htmlElement;
            }
        });

        return html;
    }

    // Option Page : Click on "Add" button for new language target
    $("#wplng-target-lang-add").on("click", function () {

        var newTargetId = $("#wplng_add_new_target_language").val();
        var newTargetFlag = "";

        wplngAllLanguages.forEach((language) => {
            if (language.id == newTargetId) {
                newTargetFlag = language.flag;
            }
        });

        var newTarget = {
            "id": newTargetId,
            "flag": newTargetFlag
        };

        if (!wplngTargetLanguagesIncludes(newTargetId)) {
            wplngTargetLanguages.push(newTarget);
        }

        wplngUpdateOptionPage();
    });

    $("#wplng-target-languages-list").on(
        "click",
        ".wplng-target-lang-remove",
        (event) => {

            var newTargetLanguages = [];
            var removed = $(event.target).attr("wplng-target-lang");

            wplngTargetLanguages.forEach((language) => {
                if (language.id != removed) {
                    newTargetLanguages.push(language);
                }
            });

            wplngTargetLanguages = newTargetLanguages;

            wplngUpdateOptionPage();
        }
    );

    $("#wplng_website_language").on("change", function () {
        wplngWebsiteLanguage = $("#wplng_website_language").val();
        $("#wplng_website_flag").val("");
        $("#wplng_add_new_target_language").html(wplngGetOptionstargetLanguagesHTML());
        $("#wplng-flags-radio-original-website").html(wplngGetWebsiteLanguageFlagsHTML());
    });


    $('#wplng-flags-radio-original-website').on("click", "input[type=radio][name=wplng-website-flag]", function () {
        if ($("#wplng-website-flag-custom").is(':checked')) {
            $("#wplng-website-flag-container").slideDown("fast");
        } else {
            $("#wplng-website-flag-container").slideUp("fast");
            $("#wplng_website_flag").val($(this).val());
        }
    });

    $("#wplng-target-languages-list").on("click", ".wplng-target-lang-update-flag", function () {
        var languageId = $(this).attr("wplng-target-lang");
        var selector = "#wplng-target-languages-list .wplng-flag-target-container[wplng-target-lang=" + languageId + "]";

        $(selector).slideToggle();
    });

    $('#wplng-target-languages-list').on("click", "input[type=radio]", function () {

        var selectedFlagId = $(this).attr("wplng-target-lang");
        var selectedFlagVal = $(this).val();
        var selectorSubflagContainer = ".wplng-subflag-target-custom[wplng-target-lang=" + selectedFlagId + "]";

        if (selectedFlagVal == "custom") {
            $(selectorSubflagContainer).slideDown("fast");
        } else {
            $(selectorSubflagContainer).slideUp("fast");
            $(".wplng-target-subflag[wplng-target-lang=" + selectedFlagId + "]").val(selectedFlagVal);

            var newTargetLanguages = [];
            wplngTargetLanguages.forEach(language => {
                if (language.id == selectedFlagId) {
                    newTargetLanguages.push({
                        "id": language.id,
                        "flag": selectedFlagVal
                    });
                } else {
                    newTargetLanguages.push(language);
                }
            });

            wplngTargetLanguages = newTargetLanguages;
            $("#wplng_target_languages").val(JSON.stringify(newTargetLanguages));
        }
    });

    $("#wplng-target-languages-list").on("input", ".wplng-target-subflag", function () {

        var selectedFlagId = $(this).attr("wplng-target-lang");
        var selectedFlagVal = $(this).val();
        var newTargetLanguages = [];

        wplngTargetLanguages.forEach(language => {
            if (language.id == selectedFlagId) {
                newTargetLanguages.push({
                    "id": language.id,
                    "flag": selectedFlagVal
                });
            } else {
                newTargetLanguages.push(language);
            }
        });

        wplngTargetLanguages = newTargetLanguages;
        $("#wplng_target_languages").val(JSON.stringify(newTargetLanguages));
    });

    function wplngUpdateOptionPage() {
        $("#wplng_website_language").html(wplngGetOptionsWebsiteLanguageHTML());
        $("#wplng_add_new_target_language").html(wplngGetOptionstargetLanguagesHTML());
        $("#wplng-flags-radio-original-website").html(wplngGetWebsiteLanguageFlagsHTML());
        $("#wplng-target-languages-list").html(wplngGetTargetLanguagesListHTML());
        $("#wplng_target_languages").val(JSON.stringify(wplngTargetLanguages));
    }

    wplngUpdateOptionPage();

}); // End jQuery loaded event


