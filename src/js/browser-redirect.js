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

(function () {

    "use strict";

    // ------------------------------------------------------------------------
    // Manage the redirection by browser language
    // ------------------------------------------------------------------------

    /**
     * Get user browser language
     */

    const userLang = navigator.language.slice(0, 2);

    /**
     * Get cookie wplingua-lang
     */

    const cookieName = 'wplingua-lang';
    let wplinguaCookie;
    const cookieParts = document.cookie.split(';');
    for (let i = 0; i < cookieParts.length; i++) {
        let cookie = cookieParts[i].trim();
        if (cookie.startsWith(cookieName + '=')) {
            wplinguaCookie = cookie.substring(cookieName.length + 1);
            break;
        }
    }

    /**
     * Get avalable languages from <link rel="alternate">
     */

    const availableLanguages = {};
    document.querySelectorAll('link[rel="alternate"][hreflang]').forEach(link => {
        const hreflang = link.getAttribute('hreflang');
        if (hreflang !== 'x-default') {
            availableLanguages[hreflang] = link.href;
        }
    });

    const currentPageLang = document.documentElement.lang.slice(0, 2).toLowerCase();

    /**
     * Redirection logical
     */

    // If the cookie is NOT set, it is a new visitor or an expired cookie.
    if (!wplinguaCookie) {
        // Vérifier si la langue du navigateur est disponible sur le site
        if (availableLanguages[userLang]) {
            // Check if the browser language is available on the site
            if (currentPageLang !== userLang) {
                // Set the cookie to remember the choice and avoid future redirection.
                const date = new Date();
                date.setTime(date.getTime() + (30 * 24 * 60 * 60 * 1000));
                const expires = "; expires=" + date.toUTCString();
                document.cookie = cookieName + "=" + (userLang || "") + expires + "; path=/";

                // Redirect to the corresponding URL
                window.location.href = availableLanguages[userLang];
            }
        }
    }

})();
