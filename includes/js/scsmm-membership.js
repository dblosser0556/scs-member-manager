(function ($) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write $ code here, so the
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
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */
    $(document).ready(function () {

        var dependents = $('#dependent_count').val();

        // start here
        // get dependate count
        // handle increment and update of input field



        // set up the validation for the registration form.
        $("#application_form").validate({
            rules: {
                membership_type_id: "required",
                status_Id: "required",
                first_name: "required",
                last_name: "required",
                address: "required",
                city: "required",
                state: "required",
                email: {
                    required: true,
                    email: true
                },
                'dep_first_name[]': "required",
                'dep_last_name[]': "required",
                'dep_relationship_id[]': "required",
                'dep_email[]' : 'email'
            },
            messages: {
                membership_type_id: "PLease select a membership type from the list",
                status_Id: "Please select the appropriate status",
                first_name: "Please enter your first name",
                last_name: "Please enter your last name",
                address: "An address is required.",
                city: "Your city is required.",
                state: "Your state is required.  Please select.",
                email: "Please enter a valid email address",
                'dep_first_name[]': "Please provide a first name",
                'dep_last_name[]': "Please provide a last name",
                'dep_relationship_id[]': "Select a relationship",
                'dep_email[]' : 'Provide a correct email'
            },
            errorElement: "em",
            errorPlacement: function (error, element) {
                // Add the `help-block` class to the error element
                error.addClass("invalid-feedback");

                if (element.prop("type") === "checkbox") {
                    error.insertAfter(element.parent("label"));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).addClass("is-valid").removeClass("is-invalid");
            },
            submitHandler: function (form) {
                save_form(form);
            }
        });


        $("button#addDependents").click(function () {
            $('#row0').clone().appendTo('#dependantTable tbody');
            
            dependents++;
            
            updateDepNo(0, dependents);

            $('#row' + dependents).css('display', '');
            
            // bind the input masks required for elements added to the dom.
            $('#row' + dependents).find("#dep_phone").inputmask();
            $('#row' + dependents).find("#dep_mobile").inputmask();
            $('#row' + dependents).find("#dep_email").inputmask();

            $('#dependent_count').val(dependents);

        });

        $("#dependantTable").on('click', '.removeDependant', function (event) {
            let row = event.target.getAttribute('data-row');
            let row_number = event.target.getAttribute('data-row-number');
            $('#' + row).remove();

            // renumber all the rows below the row deleted.
            for (let i = parseInt(row_number) + 1; i <= dependents; i++) {
                updateDepNo(i, i - 1);
            }

            dependents--;

            $('#dependent_count').val(dependents);
        });

    });  //document ready


   

    function updateDepNo(oldRow, newRow) {
        var find_row = 'row' + oldRow;


        // fix the header
        $('#header-row' + oldRow + ':first').each(function (index) {
            $(this).text('Dependant ' + newRow);
            this.id = 'header-row' + newRow;
        });

        // fix the table row id
        $('#' + find_row + ':first').each(function (index) {
            this.id = 'row' + newRow;
        });

        // fix the delete button
        $('#button-row' + oldRow + ':first').each(function (index) {
            $(this).attr('data-row', 'row' + newRow);
            $(this).attr('data-row-number', newRow);
            this.id = 'row' + newRow;
        });

    }

})(jQuery);

    
