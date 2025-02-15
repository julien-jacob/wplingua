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

    /**
     * Code for first loading
     */

    if ($("#wplng-notice-first-loading-loading").length) {
        $("#toplevel_page_wplingua-settings .wp-submenu-wrap").hide();
    }

    $("#wplng-first-load-iframe").load(function () {
        $("#wplng-notice-first-loading-loading").hide();
        $("#wplng-notice-first-loading-loaded").slideDown();
        $("#wplng-option-settings-form").slideDown();
        $("#toplevel_page_wplingua-settings .wp-submenu-wrap").slideDown();
    });

    /**
     * Code for input
     */

    let wplngWebsiteLanguage = $("#wplng_website_language").val();
    let wplngTargetLanguages = JSON.parse($("#wplng_target_languages").val());


    function wplngTargetLanguagesIncludes(languageId) {

        let returned = false;

        wplngTargetLanguages.forEach(targetLanguage => {
            if (targetLanguage.id == languageId) {
                returned = true;
            }
        });

        return returned;
    }


    function wplngLanguagesIsPrivate(languageId) {
        let returned = false;
        wplngTargetLanguages.forEach(targetLanguage => {
            if (targetLanguage.id == languageId && targetLanguage.private == true) {
                returned = true;
                return;
            }
        });
        return returned;
    }


    function wplngGetOptionsWebsiteLanguageHTML() {

        let languagesOptionsHTML = "";

        wplngAllLanguages.forEach((language) => {

            let selected = "";
            let disabled = "";

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


    function wplngGetOptionsTargetLanguagesHTML() {

        let languagesOptionsHTML = "";
        let hideFieldset = true;

        wplngAllLanguages.forEach((language) => {
            let disabled = "";
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

        if (
            $("#fieldset-add-target-language").is(":visible")
            && $("#wplng-target-languages-container").is(":visible")
        ) {
            $("#wplng-languages-target-separator").show();
        } else {
            $("#wplng-languages-target-separator").hide();
        }

        return languagesOptionsHTML;
    }


    function wplngGetWebsiteLanguageNameHTML() {

        let html = "";

        wplngAllLanguages.forEach((language) => {

            if (language.id == wplngWebsiteLanguage) {

                html += '<img src="' + language.flag + '" id="wplng-website-flag"></img> ';
                html += language.name;

            }
        });

        return html;

    }


    function wplngGetWebsiteLanguageFlagsHTML() {

        let flagsRadiosHTML = "";

        wplngAllLanguages.forEach((language) => {

            if (language.id == wplngWebsiteLanguage) {

                let textCustomRadio = $("#wplng-flags-radio-original-website-custom").text();
                let websiteFlagUrl = $("#wplng_website_flag").val();
                let flagFirstChecked = false;
                let flagCustomChecked = " checked";

                if (websiteFlagUrl == "") {
                    flagFirstChecked = true;
                }

                language.flags.forEach(flag => {

                    let checked = "";

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

        let html = "";
        let htmlTemplate = $("#wplng-target-language-template").html();

        wplngAllLanguages.forEach((language) => {

            let htmlElement = "";

            if (wplngTargetLanguagesIncludes(language.id)) {

                let textCustomRadio = $("#wplng-flags-radio-original-website-custom").text();
                let flagsRadiosHTML = "";
                let flagFirstChecked = false;
                let flagCustomChecked = " checked";
                let targetFlagUrl = language.flag;

                if (targetFlagUrl == "") {
                    flagFirstChecked = true;
                }

                language.flags.forEach(flag => {

                    let checked = "";

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

                let isPrivate = wplngLanguagesIsPrivate(language.id);
                let inputPrivate = '';

                inputPrivate += '<input ';
                inputPrivate += ' type="checkbox"';
                inputPrivate += ' id="wplng-language-private-' + language.id + '"';
                inputPrivate += ' name="wplng-language-private"';
                inputPrivate += ' value="private"';
                inputPrivate += ' wplng-target-lang="' + language.id + '"';

                if (isPrivate) {
                    inputPrivate += ' checked';
                }

                inputPrivate += '/>';

                htmlElement = htmlTemplate;
                htmlElement = htmlElement.replaceAll("[PRIVATE_INPUT]", inputPrivate);
                htmlElement = htmlElement.replaceAll("[NAME]", language.name);
                htmlElement = htmlElement.replaceAll("[LANG]", language.id);
                let htmlFlag =
                    '<img src="' + targetFlagUrl + '" class="wplng-target-flag">';
                htmlElement = htmlElement.replaceAll("[FLAG]", htmlFlag);
                htmlElement = htmlElement.replaceAll("[FLAGS_OPTIONS]", flagsRadiosHTML);

                if (isPrivate) {
                    htmlElement = htmlElement.replaceAll(
                        'class="wplng-target-language"',
                        'class="wplng-target-language wplng-is-private"'
                    );
                }

                let htmlInput = '<input type="url" class="wplng-target-subflag" wplng-target-lang="' + language.id + '" value="' + language.flag + '" />';
                htmlElement = htmlElement.replaceAll("[INPUT]", htmlInput);

                if (flagCustomChecked == "") {
                    htmlElement = htmlElement.replaceAll(
                        'class="wplng-subflag-target-custom"',
                        'class="wplng-subflag-target-custom hide"'
                    );
                } else {
                    htmlElement = htmlElement.replaceAll(
                        'class="wplng-subflag-target-custom"',
                        'class="wplng-subflag-target-custom show"'
                    );
                }

                html += htmlElement;
            }
        });

        if ("" == html) {
            $("#wplng-target-languages-container").hide();
        } else {
            $("#wplng-target-languages-container").show();
        }

        if (
            $("#fieldset-add-target-language").is(":visible")
            && $("#wplng-target-languages-container").is(":visible")
        ) {
            $("#wplng-languages-target-separator").show();
        } else {
            $("#wplng-languages-target-separator").hide();
        }

        return html;
    }


    // Option Page : Click on "Add" button for new language target
    $("#wplng-target-lang-add").on("click", function () {

        if (
            wplngTargetLanguages.length != 0
            && !confirm($("#wplng_add_new_target_language_message").text())
        ) {
            return;
        }

        let newTargetId = $("#wplng_add_new_target_language").val();
        let newTargetFlag = "";

        wplngAllLanguages.forEach((language) => {
            if (language.id == newTargetId) {
                newTargetFlag = language.flag;
            }
        });

        let newTarget = {
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

            let newTargetLanguages = [];
            let removed = $(event.target).attr("wplng-target-lang");

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
        $("#wplng_add_new_target_language").html(wplngGetOptionsTargetLanguagesHTML());
        $("#wplng-flags-radio-original-website").html(wplngGetWebsiteLanguageFlagsHTML());

        $("#wplng-website-language").html(wplngGetWebsiteLanguageNameHTML());
    });


    $('#wplng-flags-radio-original-website').on("click", "input[type=radio][name=wplng-website-flag]", function () {
        if ($("#wplng-website-flag-custom").is(':checked')) {
            $("#wplng-website-flag-container").slideDown("fast");
        } else {
            $("#wplng-website-flag-container").slideUp("fast");
            $("#wplng_website_flag").val($(this).val());
        }
    });


    $("#wplng-website-lang-update-flag").on("click", function () {
        $("#wplng-flag-website-container").slideToggle();
    });


    $("#wplng-target-languages-list").on("click", ".wplng-target-lang-update-flag", function () {
        let languageId = $(this).attr("wplng-target-lang");
        let selector = "#wplng-target-languages-list .wplng-flag-target-container[wplng-target-lang=" + languageId + "]";

        $(selector).slideToggle();
    });


    $('#wplng-target-languages-list').on("click", "input[type=radio]", function () {

        let selectedFlagId = $(this).attr("wplng-target-lang");
        let selectedFlagVal = $(this).val();
        let selectorSubflagContainer = ".wplng-subflag-target-custom[wplng-target-lang=" + selectedFlagId + "]";

        if (selectedFlagVal == "custom") {
            $(selectorSubflagContainer).slideDown("fast");
        } else {
            $(selectorSubflagContainer).slideUp("fast");
            $(".wplng-target-subflag[wplng-target-lang=" + selectedFlagId + "]").val(selectedFlagVal);

            let newTargetLanguages = [];
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


    $('#wplng-target-languages-list').on("click", "input[type=checkbox]", function () {

        let languageId = $(this).attr("wplng-target-lang");
        let isPrivate = $(this).is(":checked");

        if (isPrivate) {
            $(this).parents(".wplng-target-language").addClass("wplng-is-private");
        } else {
            $(this).parents(".wplng-target-language").removeClass("wplng-is-private");
        }

        console.log($(this).parents(".wplng-target-language"));

        let newTargetLanguages = [];
        wplngTargetLanguages.forEach(language => {
            if (language.id == languageId) {
                newTargetLanguages.push({
                    "id": language.id,
                    "flag": language.flag,
                    "private": isPrivate
                });
            } else {
                newTargetLanguages.push(language);
            }
        });

        wplngTargetLanguages = newTargetLanguages;
        $("#wplng_target_languages").val(JSON.stringify(newTargetLanguages));
    });


    $("#wplng-target-languages-list").on("input", ".wplng-target-subflag", function () {

        let selectedFlagId = $(this).attr("wplng-target-lang");
        let selectedFlagVal = $(this).val();
        let newTargetLanguages = [];

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
        $("#wplng_add_new_target_language").html(wplngGetOptionsTargetLanguagesHTML());
        $("#wplng-flags-radio-original-website").html(wplngGetWebsiteLanguageFlagsHTML());
        $("#wplng-target-languages-list").html(wplngGetTargetLanguagesListHTML());
        $("#wplng_target_languages").val(JSON.stringify(wplngTargetLanguages));
    }

    wplngUpdateOptionPage();

    /**
     * Show / Hide API key
     */

    $("#wplng-api-key-show").click(function () {
        $("#wplng-api-key-show").hide();
        $("#wplng-api-key-hide").show();
        $("#wplng-api-key-fake").hide();
        $("#wplng_api_key").show();
    });


    $("#wplng-api-key-hide").click(function () {
        $("#wplng-api-key-hide").hide();
        $("#wplng-api-key-show").show();
        $("#wplng-api-key-fake").show();
        $("#wplng_api_key").hide();
    });

}); // End jQuery loaded event
