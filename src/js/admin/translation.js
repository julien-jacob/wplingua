jQuery(document).ready(function($) {

    /**
     * Resize text area
     */

    var $wplngTextArea = $("#wplng_meta_box_translation textarea");
    
    $wplngTextArea.off("keyup.textarea").on("keyup.textarea", function() {
        wplngResizeTextArea($(this));
    });

    function wplngResizeTextArea($element) {
        $element.height(0);
        $element.height($element[0].scrollHeight);
    }

    wplngResizeTextArea($wplngTextArea);
    
}); // End jQuery loaded event