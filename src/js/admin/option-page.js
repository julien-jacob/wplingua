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

    $("[wplng-help-box], [wplng-help-box-right]").click(function () {

        let selector = $(this).attr("wplng-help-box");

        if (undefined == selector) {
            selector = $(this).attr("wplng-help-box-right");
        }

        $(selector).slideToggle();

        if ("1" == $(this).attr("wplng-help-box-open")) {
            $(this).attr("wplng-help-box-open", "0");
            $(this).attr("style", "");
        } else {
            $(this).attr("wplng-help-box-open", "1");
            $(this).css('color', '#69a8bb');
            $(this).css('opacity', '1');
        }

    })

}); // End jQuery loaded event