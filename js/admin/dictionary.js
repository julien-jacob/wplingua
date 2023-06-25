jQuery(document).ready(function($) {

    $("[value]").val()

    if ( "on" == $("#wplng_dictionary_action_never_translate").val() ) {
        $("#wplng_languages").hide();
    }

    $("#wplng_dictionary_action_never_translate").on("change", function($e) {
        if ( "on" == $("#wplng_dictionary_action_never_translate").val() ) {
            $("#wplng_languages").hide();
        }
    });

    $("#wplng_dictionary_action_always_translate").on("change", function($e) {
        if ( "on" == $("#wplng_dictionary_action_always_translate").val() ) {
            $("#wplng_languages").show();
        }
    });

}); // End jQuery loaded event