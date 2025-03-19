/**
 *                 _     _                         
 * __      ___ __ | |   (_)_ __   __ _ _   _  __ _ 
 * \ \ /\ / / '_ \| |   | | '_ \ / _` | | | |/ _` |
 *  \ V  V /| |_) | |___| | | | | (_| | |_| | (_| |
 *   \_/\_/ | .__/|_____|_|_| |_|\__, |\__,_|\__,_|
 *          |_|                  |___/             
 *
 *        -- wpLingua | WordPress plugin --
 *   Translate and make your website multilingual
 *
 *     https://github.com/julien-jacob/wplingua
 *      https://wordpress.org/plugins/wplingua/
 *              https://wplingua.com/
 *
 **/

jQuery(document).ready(function ($) {

    function wplngHeartBeat() {
        for (let i = 0; i < 5; i++) {
            setTimeout(() => {
                $.ajax({
                    url: "[admin-ajax-php]",
                    method: "POST",
                    data: { action: "wplng_ajax_heartbeat" }
                });
            }, i * 1000 * 60 * 11);
        }
    }

    setTimeout(function () {
        wplngHeartBeat();
    }, 5000);
});
