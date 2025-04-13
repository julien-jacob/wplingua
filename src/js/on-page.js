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

    // ------------------------------------------------------------------------
    // Manage the wpLingua Cookie
    // ------------------------------------------------------------------------

    /**
     * Sets a cookie named "wplingua" with a value of 1.
     */

    document.cookie = "wplingua=1;path=/";

    /**
     * Reload the page if BODY contains the class "wplingua-reload"
     */

    if (document.body.classList.contains('wplingua-reload')) {
        location.reload();
    }

    // ------------------------------------------------------------------------
    // Manage the wpLingua HeartBeat
    // ------------------------------------------------------------------------

    /**
     * Sends periodic AJAX requests to the server to keep the session alive.
     * The requests are sent every 11 minutes, 5 times in total.
     */
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

    /**
     * Initializes the heartbeat functionality after a 6-second delay.
     */

    setTimeout(function () {
        wplngHeartBeat();
    }, 6000);

});
