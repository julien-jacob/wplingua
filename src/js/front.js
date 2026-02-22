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

    // ------------------------------------------------------------------------
    // Code for nav menu switcher
    // ------------------------------------------------------------------------

    /**
     * Set flags images in nav menu switcher
     */

    $("a[data-wplng-flag][data-wplng-alt]").each(function () {

        let img = '';

        img += '<img';
        img += ' src="' + $(this).attr("data-wplng-flag") + '" ';
        img += ' alt="' + $(this).attr("data-wplng-alt") + '" ';
        img += ' class="wplng-menu-flag"';
        img += '> ';

        $(this).html(img + $(this).html());
        $(this).removeAttr("data-wplng-flag");
        $(this).removeAttr("data-wplng-alt");
    });


    // ------------------------------------------------------------------------
    // Code for switcher UI
    // ------------------------------------------------------------------------

    function wplngUpdateSwitcherOpening() {

        let windowMiddle = $(window).height() / 2;

        $(".wplng-switcher.style-dropdown").each(function (e) {

            let offsetFromWindow = $(this).offset().top - $(window).scrollTop();

            if (offsetFromWindow < windowMiddle) {
                if (!$(this).hasClass("open-bottom")) {
                    $(this).addClass("open-bottom");
                    $(this).removeClass("open-top");

                    let htmlLanguages = $(".wplng-languages", this).prop('outerHTML');
                    let htmlLanguagecurrent = $(".wplng-language-current", this).prop('outerHTML');
                    $(".switcher-content", this).html(htmlLanguagecurrent + htmlLanguages);
                }
            } else {
                if (!$(this).hasClass("open-top")) {
                    $(this).addClass("open-top");
                    $(this).removeClass("open-bottom");

                    let htmlLanguages = $(".wplng-languages", this).prop('outerHTML');
                    let htmlLanguagecurrent = $(".wplng-language-current", this).prop('outerHTML');
                    $(".switcher-content", this).html(htmlLanguages + htmlLanguagecurrent);
                }
            }
        });
    }

    $(window).scroll(function () {
        wplngUpdateSwitcherOpening();
    });

    $("#wplng_style").on("input", function () {
        wplngUpdateSwitcherOpening();
    });

    wplngUpdateSwitcherOpening();

    // ------------------------------------------------------------------------
    // Code for switcher Cookie for language browser redirection
    // ------------------------------------------------------------------------

    /**
     * Handle clicks on all language links that have the data attribute.
     */

    $('a[data-wplng-lang-id]').on('click', function (event) {
        // Check for the onclick attribute and its value
        const onclickAttribute = $(this).attr('onclick');

        if (onclickAttribute && onclickAttribute.trim() === 'event.preventDefault();') {
            // If the onclick attribute is present and its value is 'event.preventDefault();',
            // we assume this link is not meant for navigation. So, we don't set the cookie.
            return;
        }

        // Get the language code directly from the data attribute.
        const langCode = $(this).data('wplng-lang-id');

        // Set the cookie with the retrieved language code.
        const expires = new Date();
        expires.setFullYear(expires.getFullYear() + 1);
        document.cookie = 'wplingua-lang=' + langCode + '; expires=' + expires.toUTCString() + '; path=/';

    });

    // ------------------------------------------------------------------------
    // Code for overload bar
    // ------------------------------------------------------------------------

    $("#wplng-overloaded-close").on("click", function () {
        $("#wplng-overloaded-container").hide();
    });

    // ------------------------------------------------------------------------
    // Clear URL after "Load in progress" reload
    // ------------------------------------------------------------------------

    try {
        const url = new URL(window.location.href);
        const params = url.searchParams;
        let changed = false;

        // Remove wplng-load only when it's "translated"
        if (params.has('wplng-load') && params.get('wplng-load') === 'translated') {
            params.delete('wplng-load');
            changed = true;
        }

        // Remove nocache regardless of its value
        if (params.has('nocache')) {
            params.delete('nocache');
            changed = true;
        }

        if (changed) {
            const search = params.toString();
            const newUrl = url.origin + url.pathname + (search ? '?' + search : '') + (url.hash || '');
            history.replaceState(null, '', newUrl);
        }
    } catch (e) {
        // ignore on unsupported environments
    }

}); // End jQuery loaded event
