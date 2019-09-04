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
		$("input.phone").mask("(999) 999-9999");
		$("input.zipcode").mask("99999");

		// set up the validation for the registration form.
		$("#registration_form").validate({
			rules: {
				first_name: "required",
				last_name: "required",
				username: {
					required: true,
					minlength: 5
				},
				password: {
					required: true,
					minlength: 5
				},
				confirm_password: {
					required: true,
					minlength: 5,
					equalTo: "#password"
				},
				email: {
					required: true,
					email: true
				},
				agree: "required"
			},
			messages: {
				first_name: "Please enter your first name",
				last_name: "Please enter your last name",
				username: {
					required: "Please enter a username",
					minlength: "Your username must consist of at least 5 characters"
				},
				password: {
					required: "Please provide a password",
					minlength: "Your password must be at least 5 characters long"
				},
				confirm_password: {
					required: "Please provide a password",
					minlength: "Your password must be at least 5 characters long",
					equalTo: "Please enter the same password as above"
				},
				email: "Please enter a valid email address",
				agree: " Please accept our policy"
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
				submit(form);
			}
		});

		// set up the validation for the contact form.
		$("#contact_form").validate({
			rules: {
				first_name: "required",
				last_name: "required",
				email: {
					required: true,
					email: true
				}
			},
			messages: {
				first_name: "Please enter your first name",
				last_name: "Please enter your last name",
				email: "Please enter a valid email address"
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
				submit(form);
			}
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

					jQuery("#loader").hide();
					if (res.success) {
						displaySuccess(res.msg);
						window.location.href = register_success_redirect;
					} else
						displayAlert(res.msg);

					

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
			$("#successText").html(msg);
			$("#success").show();
		}

	}); //document ready


})(jQuery);