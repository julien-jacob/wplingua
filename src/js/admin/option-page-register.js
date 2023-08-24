jQuery(document).ready(function ($) {

    /**
     * Set HTML options for languages
     */
    var wplngHtmlLanguagesOptions = "";
    wplngAllLanguages.forEach((language) => {
        wplngHtmlLanguagesOptions += '<option value="' + language.id + '">' + language.name + "</option>";
    });
    $("#wplng-language-website").html(wplngHtmlLanguagesOptions);
    $("#wplng-language-target").html(wplngHtmlLanguagesOptions);


    /**
     * Set default option for website language
     */
    $("#wplng-language-website option[value=" + $("#wplng-website-locale").text() + "]").attr("selected", true);


    /**
     * Add disable attribute on #wplng-language-target 
     * depend #wplng-language-website 
     */
    wplngDisableLanguagesOptions();
    $("#wplng-language-website").on("input", function (event) {
        wplngDisableLanguagesOptions();
    });

    function wplngDisableLanguagesOptions() {
        var selectedLanguage = $("#wplng-language-website").val();
        $("#wplng-language-target option").attr("disabled", false);
        $("#wplng-language-target option[value=" + selectedLanguage + "]").attr("disabled", true);

        if ($("#wplng-language-website").val() == selectedLanguage) {
            $("#wplng-language-target option").attr("selected", false);
            $("#wplng-language-target option[value!=" + selectedLanguage + "]").first().attr("selected", true);
        }
    }


    /**
     * Prepare data for free reister submit
     */
    $("#wplng-get-free-api-submit").on("click", function (event) {
        wplngUpdateRegisterInput();
    });

    function wplngUpdateRegisterInput() {

        var wplngRegisterInputSelector = "#wplng-website-url, #wplng-email, #wplng-language-website, #wplng-language-target, #wplng-accept-eula";

        $(wplngRegisterInputSelector).attr('required', true);

        var registerData = {
            request: 'register',
            mail_address: $("#wplng-email").val(),
            website: $("#wplng-website-url").val(),
            language_original: $("#wplng-language-website").val(),
            languages_target: $("#wplng-language-target").val(),
            accept_eula: $("#wplng-accept-eula").is(':checked')
        };

        $("#wplng_request_free_key").val(JSON.stringify(registerData))
    }

}); // End jQuery loaded event
