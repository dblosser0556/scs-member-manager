(function ($) {
    'use strict';

    /**
     * All of the code for your public-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practice to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */
    $(document).ready(function () {

        //input masks
        $(":input").inputmask();
    });

    function submit(form) {
        var applyData = jQuery(form).serialize();

        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: applyData,
            beforeSend: function () {
                $("#loader").show();
            },

            success: function (response) {
                console.log('add success ', response);
                var res = JSON.parse(response);

               $("#loader").hide();
                if (res.success) {
                    displaySuccess(res.msg);
                    page_redirect(res.redirect_page);
                } else {
                    displayAlert(res.msg);
                    page_redirect(res.redirect_page);
                }


            },
            error: function (response) {
                console.log('add error ', response);
                const contentType = response.getResponseHeader("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    return response.json().then(data => {
                        // process your JSON data further
                        var errmsg = response.responseText;
                        displayAlert(errmsg);
                        $("#loader").hide();

                    });
                } else {
                    var errmsg = response.responseText;
                    if (errmsg == '')
                        errmsg = response.statusText;
                    displayAlert(errmsg);
                    $("#loader").hide();
                }
            }

        });
    }

    function displayAlert(msg) {
       $("#alertText").html(msg);
       $("#alert").show();
    }

    function displaySuccess(msg) {
        console.log('display success fired');
       $("#successText").html(msg);
       $("#success").show();
    }



})(jQuery);