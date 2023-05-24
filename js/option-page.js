jQuery(document).ready(function ($) {
    var mcvWebsiteLanguage = $("#mcv_website_language").val();
    var mcvTargetLanguages = JSON.parse($("#mcv_target_languages").val());

    
    // var mcvTargetflags = JSON.parse($("#mcv_target_flags").val());

    function mcvTargetLanguagesIncludes(languageId) {
        var returned = false;
        mcvTargetLanguages.forEach(targetLanguage => {
            if (targetLanguage.id == languageId) {
                returned = true;
            }
        });
        return returned;
    }

    

    // function mcvGetFlag

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

            // console.log(language.id, mcvTargetLanguagesIncludes(language.id));
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
        // return htmlTemplate;


        mcvAllLanguages.forEach((language) => {
            var htmlElement = "";
            
            if (mcvTargetLanguagesIncludes(language.id)) {

                var textCustomRadio = $("#mcv-flags-radio-original-website-custom").text();
                var flagsRadiosHTML = "";
                var flagFirstChecked = false;
                var flagCustomChecked = " checked";

                var targetFlagUrlSelector = ".mcv-target-subflag[mcv-target-lang=" + language.id + "]";
                // var targetFlagUrl = $(targetFlagUrlSelector).val();

                var targetFlagUrl = language.flag;

                // alert(targetFlagUrlSelector);

                if (targetFlagUrl == "") {
                    flagFirstChecked = true;
                }

                language.flags.forEach(flag => {
                    
                    var checked = "";

                    if (targetFlagUrl == flag.flag ) {
                        checked = " checked";
                        flagCustomChecked = "";
                    }

                    if (flagFirstChecked) {
                        checked = " checked";
                        flagCustomChecked = "";
                        flagFirstChecked = false;
                        // $(targetFlagUrlSelector).val(flag.flag);
                        value = flag.flag;
                        
                    }

                    flagsRadiosHTML +=
                        '<span class="mcv-subflags-radio">' +
                        '<input type="radio" ' +
                        'name="mcv-target-subflag-' + language.id + '" ' +
                        'value="' + flag.id + '" ' +
                        'target-subflag-url="' + flag.flag + '" ' +
                        'id="mcv-subflag-' + language.id + '-' + flag.id + '"' + checked + '>' +
                        '<label for="mcv-subflag-' + language.id + '-' + flag.id + '">' + 
                        flag.name + ' (<img src="' + flag.flag + '">)' +
                        '</label></span>';
                });

                flagsRadiosHTML +=
                    '<span class="mcv-flags-radio">' +
                    '<input type="radio" name="mcv-target-subflag-' +
                    language.id + '" id="mcv-target-flag-custom-' +  
                    language.id + '" value="custom"' + flagCustomChecked + '>' +
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
                htmlElement = htmlElement.replaceAll("[VALUE]", targetFlagUrl);
                html += htmlElement;
            }
        });

        return html;
    }

    // Option Page : Click on "Add" button for new language target
    $("#mcv-target-lang-add").on("click", function() {

        var newTargetId = $("#mcv_add_new_target_language").val();
        
        var newTarget = {
            "id": $("#mcv_add_new_target_language").val(),
            "flag": "hello"
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

    
    $('#mcv-flags-radio-original-website').on("click", "input[type=radio][name=mcv-website-flag]", function() {
        if ($("#mcv-website-flag-custom").is(':checked')) {
            $("#mcv-website-flag-container").slideDown("fast");
        } else {
            $("#mcv-website-flag-container").slideUp("fast");
            $("#mcv_website_flag").val($(this).attr("website-flag-url"));
        }
    });

    $("#mcv-target-languages-list").on("click", ".mcv-target-lang-update-flag", function() {
        var languageId = $(this).attr("mcv-target-lang");
        var selector = "#mcv-target-languages-list .mcv-flag-target-container[mcv-target-lang=" + languageId + "]";

        $(selector).slideToggle();
    });







    $('#mcv-target-languages-list').on("click", "input[type=radio]", function() {
        // alert($(".mcv-subflag-target-custom"));

        // console.log($(this).find(".mcv-subflag-target-custom"));
        console.log($(this));

        // if ($(this).find(".mcv-subflag-target-custom").is(':checked')) {
        //     $(this).find(".mcv-subflag-target-custom").slideDown("fast");
        // } else {
        //     $("#mcv-website-flag-container").slideUp("fast");
        //     $("#mcv_website_flag").val($(this).attr("website-flag-url"));
        // }
    });

    // $("#mcv-target-languages-list").on("click", ".mcv-target-lang-update-flag", function() {
    //     var languageId = $(this).attr("mcv-target-lang");
    //     var selector = "#mcv-target-languages-list .mcv-flag-target-container[mcv-target-lang=" + languageId + "]";

    //     $(selector).slideToggle();
    // });


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


