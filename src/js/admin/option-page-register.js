jQuery(document).ready(function ($) {


    if (!$("#wplng-language-website").length) {
        return;
    }

    /**
     * Set HTML options for languages
     */
    var wplngHtmlLanguagesOptions = "<option disabled selected value></option>";
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

    /**
    * Smooth scrolling to page anchor on click
    **/
    $("a[href*='#wplng-']:not([href='#'])").click(function() {
        if (
            location.hostname == this.hostname
            && this.pathname.replace(/^\//,"") == location.pathname.replace(/^\//,"")
        ) {
            var anchor = $(this.hash);
            anchor = anchor.length ? anchor : $("[name=" + this.hash.slice(1) +"]");
            if ( anchor.length ) {
                $("html, body").animate( { scrollTop: anchor.offset().top - 50 }, 1000);
            }
        }
    });

}); // End jQuery loaded event
