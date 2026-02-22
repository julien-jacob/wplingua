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

document.addEventListener('DOMContentLoaded', function () {

    // ------------------------------------------------------------------------
    // Code for nav menu switcher
    // ------------------------------------------------------------------------

    /**
     * Set flags images in nav menu switcher
     */

    document.querySelectorAll('a[data-wplng-flag][data-wplng-alt]').forEach(function (element) {

        const img = document.createElement('img');
        img.src = element.getAttribute('data-wplng-flag');
        img.alt = element.getAttribute('data-wplng-alt');
        img.className = 'wplng-menu-flag';

        element.insertBefore(img, element.firstChild);
        element.insertBefore(document.createTextNode(' '), img.nextSibling);
        element.removeAttribute('data-wplng-flag');
        element.removeAttribute('data-wplng-alt');
    });


    // ------------------------------------------------------------------------
    // Code for switcher UI
    // ------------------------------------------------------------------------

    function wplngUpdateSwitcherOpening() {

        const windowMiddle = window.innerHeight / 2;

        document.querySelectorAll('.wplng-switcher.style-dropdown').forEach(function (switcher) {

            const rect = switcher.getBoundingClientRect();
            const offsetFromWindow = rect.top;

            if (offsetFromWindow < windowMiddle) {
                if (!switcher.classList.contains('open-bottom')) {
                    switcher.classList.add('open-bottom');
                    switcher.classList.remove('open-top');

                    const languages = switcher.querySelector('.wplng-languages');
                    const languageCurrent = switcher.querySelector('.wplng-language-current');
                    const switcherContent = switcher.querySelector('.switcher-content');

                    if (switcherContent && languages && languageCurrent) {
                        switcherContent.innerHTML = '';
                        switcherContent.appendChild(languageCurrent.cloneNode(true));
                        switcherContent.appendChild(languages.cloneNode(true));
                    }
                }
            } else {
                if (!switcher.classList.contains('open-top')) {
                    switcher.classList.add('open-top');
                    switcher.classList.remove('open-bottom');

                    const languages = switcher.querySelector('.wplng-languages');
                    const languageCurrent = switcher.querySelector('.wplng-language-current');
                    const switcherContent = switcher.querySelector('.switcher-content');

                    if (switcherContent && languages && languageCurrent) {
                        switcherContent.innerHTML = '';
                        switcherContent.appendChild(languages.cloneNode(true));
                        switcherContent.appendChild(languageCurrent.cloneNode(true));
                    }
                }
            }
        });
    }

    window.addEventListener('scroll', function () {
        wplngUpdateSwitcherOpening();
    });

    const wplngStyleInput = document.getElementById('wplng_style');
    if (wplngStyleInput) {
        wplngStyleInput.addEventListener('input', function () {
            wplngUpdateSwitcherOpening();
        });
    }

    wplngUpdateSwitcherOpening();

    // ------------------------------------------------------------------------
    // Code for switcher Cookie for language browser redirection
    // ------------------------------------------------------------------------

    /**
     * Handle clicks on all language links that have the data attribute.
     */

    document.querySelectorAll('a[data-wplng-lang-id]').forEach(function (element) {
        element.addEventListener('click', function (event) {
            // Check for the onclick attribute and its value
            const onclickAttribute = this.getAttribute('onclick');

            if (onclickAttribute && onclickAttribute.trim() === 'event.preventDefault();') {
                return;
            }

            // Get the language code directly from the data attribute.
            const langCode = this.dataset.wplngLangId;

            // Set the cookie with the retrieved language code.
            const expires = new Date();
            expires.setFullYear(expires.getFullYear() + 1);
            document.cookie = 'wplingua-lang=' + langCode + '; expires=' + expires.toUTCString() + '; path=/';
        });
    });

    // ------------------------------------------------------------------------
    // Code for overload bar
    // ------------------------------------------------------------------------

    const overloadedClose = document.getElementById('wplng-overloaded-close');
    if (overloadedClose) {
        overloadedClose.addEventListener('click', function () {
            const container = document.getElementById('wplng-overloaded-container');
            if (container) {
                container.style.display = 'none';
            }
        });
    }

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

}); // End DOMContentLoaded event