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
     * The cookie expires in 30 days and is available site-wide.
     */
    function wplngCookieSet() {
        const date = new Date();
        date.setTime(date.getTime() + (30 * 24 * 60 * 60 * 1000));
        document.cookie = "wplingua=1;expires=" + date.toUTCString() + ";path=/";
    }

    /**
     * Checks if the "wplingua" cookie is set.
     * @returns {boolean} True if the cookie is set, false otherwise.
     */
    function wplngCookieIsSet() {
        const cookies = document.cookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            const cookie = cookies[i].trim();
            if (cookie.startsWith("wplingua=")) {
                return true;
            }
        }
        return false;
    }


    // Check if the "wplingua" cookie is not set
    if (!wplngCookieIsSet()) {
        // If the cookie is not set, set it and reload the page
        wplngCookieSet();
        location.reload();
    }

    // Ensure the "wplingua" cookie is set (redundant but ensures the cookie exists)
    wplngCookieSet();


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
