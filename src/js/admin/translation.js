jQuery(document).ready(function ($) {

    /**
     * Resize text area
     */

    function wplngResizeTextArea($element) {
        $element.height(0);
        $element.height($element[0].scrollHeight);
    }

    var $wplngTextArea = $("#wplng_meta_box_translation textarea");

    $wplngTextArea.off("keyup.textarea").on("keyup.textarea", function () {
        wplngResizeTextArea($(this));
    });

    $wplngTextArea.each(function () {
        wplngResizeTextArea($(this));
    });

}); // End jQuery loaded event