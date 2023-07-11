jQuery(document).ready(function ($) {

    // console.log(wplngAllLanguages);

    var html = "";

    wplngAllLanguages.forEach((language) => {

        html += '<option value="' + language.id + '">' + language.name + "</option>";
    });
    $("#wplng-language-website").html(html);
    $("#wplng-language-target").html(html);

    // var wplngRegisterInputSelector = "#wplng-website-url, #wplng-email, #wplng-language-website, #wplng-language-target, #wplng-accept-eula";
    // var wplngRegisterInputSelector = "#submit[get-free-api=1]";
    var wplngRegisterInputSelector = "#wplng-get-free-api-submit";
    
    $(wplngRegisterInputSelector).on("click", function(event) {
        wplngUpdateRegisterInput();
    });

    function wplngUpdateRegisterInput() {
        var registerData = {
            r: 'register',
            mail_address: $("#wplng-email").val(),
            website: $("#wplng-website-url").val(),
            language_original: $("#wplng-language-website").val(),
            languages_target: $("#wplng-language-target").val(),
            accept_eula: $("#wplng-accept-eula").is(':checked')
        };

        $("#wplng_request_free_key").val(JSON.stringify(registerData))
    }


}); // End jQuery loaded event


