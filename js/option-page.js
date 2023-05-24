jQuery(document).ready(function ($) {
    var mcvWebsiteLanguage = $("#mcv_website_language").val();
    var mcvTargetLanguages = JSON.parse($("#mcv_target_languages").val());

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

            if (mcvTargetLanguages.includes(language.id)) {
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

            if (mcvTargetLanguages.includes(language.id)) {
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

                    if (websiteFlagUrl == flag.flag ) {
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
                        'value="mcv-flag-' + flag.id + '" ' +
                        'website-flag-url="' + flag.flag + '" ' +
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

        // mcvTargetLanguages.forEach((languageid) => {});

        mcvAllLanguages.forEach((language) => {
            var htmlElement = "";
            if (mcvTargetLanguages.includes(language.id)) {
                htmlElement = htmlTemplate;
                htmlElement = htmlElement.replaceAll("[NAME]", language.name);
                htmlElement = htmlElement.replaceAll("[LANG]", language.id);
                var htmlFlag =
                    '<img src="' + language.flag + '" class="mcv-target-language">';
                htmlElement = htmlElement.replaceAll("[FLAG]", htmlFlag);
                html += htmlElement;
            }
        });

        return html;
    }

    // Option Page : Click on "Add" button for new language target
    $("#mcv-target-lang-add").on("click", function () {
        var newTarget = $("#mcv_add_new_target_language").val();

        if (!mcvTargetLanguages.includes(newTarget)) {
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
                if (language != removed) {
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

    
    $('#mcv-flags-radio-original-website').on("click", "input[type=radio][name=mcv-website-flag]", function() {
        if ($("#mcv-website-flag-custom").is(':checked')) {
            $("#mcv-website-flag-container").slideDown("fast");
        } else {
            $("#mcv-website-flag-container").slideUp("fast");
            $("#mcv_website_flag").val($(this).attr("website-flag-url"));
        }

        
    });


    // function toggleWebsiteFlagContainer() {
    //     if ($("#mcv-website-flag-custom").is(':checked')) {
    //         $("#mcv-website-flag-container").show();
    //     } else {
    //         $("#mcv-website-flag-container").hide();
    //     }
    // }
    // toggleWebsiteFlagContainer();

    function mcvUpdateOptionPage() {
        $("#mcv_website_language").html(mcvGetOptionsWebsiteLanguageHTML());
        $("#mcv_add_new_target_language").html(mcvGetOptionstargetLanguagesHTML());
        $("#mcv-flags-radio-original-website").html(mcvGetWebsiteLanguageFlagsHTML());
        $("#mcv-target-languages-list").html(mcvGetTargetLanguagesListHTML());
        $("#mcv_target_languages").val(JSON.stringify(mcvTargetLanguages));
    }

    mcvUpdateOptionPage();
    

}); // End jQuery loaded event


