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

    /**
     * Load the translation, replace texts and reload the page
     */

    let wplngLoadInProgressPercentageMin = 1;
    let wplngLoadInProgressPercentageMax = 1;
    let wplngLoadInProgressErrorOccurred = false;

    wplngLoadInProgressProcess();

    function wplngLoadInProgressProcess() {

        /**
         * Check data and progress bar container
         */

        if (!$("#wplng-in-progress-container").length) {
            wplngLoadInProgressError("Mode loaded without the progress bar container");
            return;
        }

        if (typeof wplngLoadInProgressData === 'undefined') {
            wplngLoadInProgressError("Invalide data (wplngLoadInProgressData undefined).");
            console.info(wplngLoadInProgressData);
            return;
        }

        if (typeof wplngLoadInProgressData.urlAjax !== 'string') {
            wplngLoadInProgressError("Invalide data (wplngLoadInProgressData.urlAjax).");
            console.info(wplngLoadInProgressData.urlAjax);
            return;
        }

        if (typeof wplngLoadInProgressData.urlReload !== 'string') {
            wplngLoadInProgressError("Invalide data (wplngLoadInProgressData.urlReload).");
            console.info(wplngLoadInProgressData.urlReload);
            return;
        }

        if (typeof wplngLoadInProgressData.nonce !== 'string' || wplngLoadInProgressData.nonce.length === 0) {
            wplngLoadInProgressError("Invalide data (wplngLoadInProgressData.nonce).");
            console.info(wplngLoadInProgressData.nonce);
            return;
        }

        if (typeof wplngLoadInProgressData.language !== 'string'
            || wplngLoadInProgressData.language.length !== 2
        ) {
            wplngLoadInProgressError("Invalide data (wplngLoadInProgressData.language).");
            console.info(wplngLoadInProgressData.language);
            return;
        }

        if (typeof wplngLoadInProgressData.chunks !== 'object'
            || wplngLoadInProgressData.chunks.length === 0
        ) {
            wplngLoadInProgressError("Invalide data (wplngLoadInProgressData.chunks).");
            console.info(wplngLoadInProgressData.chunks);
            return;
        }

        /**
         * Load the first chunk
         */

        wplngLoadInProgressProcessCkunk(
            0,
            wplngLoadInProgressData.urlAjax,
            wplngLoadInProgressData.urlReload,
            wplngLoadInProgressData.language,
            wplngLoadInProgressData.chunks
        );

    }

    function wplngLoadInProgressProcessCkunk(counter, urlAjax, urlReload, language, chunks) {

        if (!chunks[counter]
            || typeof chunks[counter] !== 'object'
            || typeof chunks[counter].percentage !== 'number'
            || typeof chunks[counter].texts !== 'object'
            || chunks[counter].texts.length === 0
        ) {
            wplngLoadInProgressError("Invalide text chunk.");
            console.info(chunks[counter]);
            return;
        }

        let percentage = chunks[counter].percentage;
        let texts = chunks[counter].texts;

        wplngLoadInProgressPercentageMax = percentage;

        $.ajax({
            url: urlAjax,
            method: "POST",
            data: {
                action: 'wplng_load_in_progress',
                wplng_language: language,
                wplng_texts: texts,
                wplng_nonce: wplngLoadInProgressData.nonce,
            },
            success: function (response) {

                if (!response.success) {

                    if (typeof response.data === "object"
                        && typeof response.data.error_message === "string"
                    ) {
                        wplngLoadInProgressError("Translation AJAX call fail (message: " + response.data.error_message + ").");
                    } else {
                        wplngLoadInProgressError("Translation AJAX call fail (response.success is false, unknow message).");
                    }

                    return;
                }

                wplngLoadInProgressPercentageMin = percentage;

                let translations = response.data;
                let map = Object.create(null);

                if (Array.isArray(translations)) {
                    translations.forEach(function (t) {
                        if (t && typeof t.source === 'string') {
                            map[t.source.trim()] = (typeof t.translation === 'string') ? t.translation : '';
                        }
                    });
                }

                $(".wplng-in-progress-text").each(function () {
                    const domText = $(this).text().trim();
                    const translated = map[domText];
                    if (typeof translated === 'string' && translated.length > 0) {
                        $(this).replaceWith(translated);
                    }
                });

                /**
                 * Process the next chunk or exit
                 */

                if (typeof chunks[counter + 1] === 'undefined') {

                    // We are in the last chunk, page is translated, no next chunk
                    wplngLoadInProgressUpdatePercentValue(100);
                    window.location.href = urlReload;

                } else {

                    // Process the next chunk
                    wplngLoadInProgressProcessCkunk(
                        counter + 1,
                        urlAjax,
                        urlReload,
                        language,
                        chunks
                    );
                }

            },
            error: function (response) {
                wplngLoadInProgressError("Translation AJAX call fail (callback error).");
                return;
            }
        });

    }

    /**
     * Display error
     */

    function wplngLoadInProgressError(message) {

        wplngLoadInProgressErrorOccurred = true;

        $("#wplng-in-progress-message").hide();
        $("#wplng-progress-bar").hide();
        $("#wplng-in-progress-error").show();

        message = "wpLingua error - Load in progress: " + message;
        console.error(message);

    }

    $("#wplng-in-progress-error-close").on("click", function () {

        $("#wplng-in-progress-container").hide();
        $("#wpadminbar").show();

        if (typeof wplngLoadInProgressData.urlReload === 'string') {
            window.location.href = wplngLoadInProgressData.urlReload;
        }
    });

    /**
     * Update percentage for the load in progress bar
     */

    function wplngLoadInProgressUpdatePercentValue(percent) {
        $("#wplng-in-progress-percent").html(percent);
        $("#wplng-progress-bar-value").animate(
            { width: percent.toString() + "%" },
            500
        );
    }

    function wplngLoadInProgressUpdatePercent() {
        let percent = parseInt($("#wplng-in-progress-percent").html());

        if (percent < wplngLoadInProgressPercentageMin
            && !wplngLoadInProgressErrorOccurred
        ) {
            wplngLoadInProgressUpdatePercentValue(wplngLoadInProgressPercentageMin);
        } else if (percent < wplngLoadInProgressPercentageMax
            && percent < 99
            && !wplngLoadInProgressErrorOccurred
        ) {
            percent++;
            wplngLoadInProgressUpdatePercentValue(percent);
        }

    }

    wplngLoadInProgressUpdatePercent();

    if ($("#wplng-in-progress-percent").length) {
        setInterval(wplngLoadInProgressUpdatePercent, 500);
    }

    if ($("#wpadminbar").length && $("#wplng-in-progress-container").length) {
        $("#wpadminbar").hide();
    }

}); // End jQuery loaded event