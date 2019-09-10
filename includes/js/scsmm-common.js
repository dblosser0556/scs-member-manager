'use strict';

/**
 * this is a set of common functions.  So they need to be set at the global
 * scope.  Therefore we don't encapsulate them in the function that defines the JQUERY as $
 * as that limits there scope to tha over all function.

 */
jQuery(document).ready(function () {

    //input masks
    jQuery(":input").inputmask();


});


function save_form(form) {
    var form_data = jQuery(form).serialize();

    jQuery.ajax({
        type: "POST",
        url: '/wp-admin/admin-ajax.php',
        data: form_data,
        beforeSend: function () {
            jQuery("#loader").show();
        },

        success: function (response) {

            var res = JSON.parse(response);

            jQuery("#loader").hide();
            if (res.success) {
                displaySuccess(res.msg);
                if (res.redirect !== null  && res.redirect !== undefined) {
                    pageRedirect(res.redirect);
                }
 
            } else {
                displayAlert(res.msg);
                if (res.redirect !== null  && res.redirect !== undefined) {
                    pageRedirect(res.redirect);
                }
                    
            }


        },
        error: function (response) {
            console.log('add error ', response);
            const contentType = response.getResponseHeader("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                return response.json().then(data => {
                    // process your JSON data further
                    var err_msg = response.responseText;
                    displayAlert(err_msg);
                    jQuery("#loader").hide();

                });
            } else {
                var err_msg = response.responseText;
                if (err_msg == '')
                    err_msg = response.statusText;
                displayAlert(err_msg);
                jQuery("#loader").hide();
            }
        }

    });
}

function displayAlert(msg) {
    jQuery("#alertText").html(msg);
    jQuery("#alert").show();
}

function displaySuccess(msg) {
    jQuery("#successText").html(msg);
    jQuery("#success").show();
}

function pageRedirect(page) {
    if (page !== null && page !== undefined) {
        window.location.href = page;
    }
}