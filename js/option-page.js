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



    function mcvGetTargetLanguagesListHTML() {
        var html = "";
        var htmlTemplate = $("#mcv-target-language-template").html();
        console.log(htmlTemplate);

        // mcvTargetLanguages.forEach((languageid) => {});

        mcvAllLanguages.forEach((language) => {
            var htmlElement = "";
            if (mcvTargetLanguages.includes(language.id)) {
                htmlElement = htmlTemplate;
                htmlElement = htmlElement.replace("[NAME]", language.name);
                htmlElement = htmlElement.replace("[LANG]", language.id);
                html += htmlElement;
            }
        });

        return html;
    }



    // Option Page : Click on "Add" button for new language target
    $("#mcv-target-lang-add").on("click", function () {

        var newTarget = $("#mcv_add_new_target_language").val();

        if (! mcvTargetLanguages.includes(newTarget)) {
            mcvTargetLanguages.push(newTarget);
        }

        mcvUpdateOptionPage();

    });


    $("#mcv-target-languages-list").on('click', '.mcv-target-lang-remove', (event) => {

        
        // alert($(this).attr("mcv-target-lang"));
        var newTargetLanguages = [];
        var removed = $(event.target).attr("mcv-target-lang");
        
        console.log(removed);

        mcvTargetLanguages.forEach(language => {
            if (language != removed) {
                newTargetLanguages.push(language);
            }
        });

        mcvTargetLanguages = newTargetLanguages;

        mcvUpdateOptionPage();
    });


    $("#mcv_website_language").on("change", function() {
        mcvWebsiteLanguage = $("#mcv_website_language").val();
        $("#mcv_add_new_target_language").html(mcvGetOptionstargetLanguagesHTML());
    });


    function mcvUpdateOptionPage() {
        $("#mcv_website_language").html(mcvGetOptionsWebsiteLanguageHTML());
        $("#mcv_add_new_target_language").html(mcvGetOptionstargetLanguagesHTML());
        $("#mcv-target-languages-list").html(mcvGetTargetLanguagesListHTML());
        $("#mcv_target_languages").val(JSON.stringify(mcvTargetLanguages));
    }

    mcvUpdateOptionPage();

}); // End jQuery loaded event
