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
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(window).ready(function () {
		var dependants = 0;

		$("button#addDependants").click(function () {
			//$('#spinner').css('display', 'inline');
			var dependantHTML;
			dependants++;
			// get the dependants template from file
			$.get(tmpl_path, function (response) {

				// create unique input names by adding number to the name
				dependantHTML = response.replace(/XX/g, dependants);

				// get the list of relationship options and add them to the text
				$.when(getRelatioinshipTypes()).done(function (options, textStatus, jqXHR) {

					dependantHTML = dependantHTML.replace('<option></option>', options);

					// add the updated HTML to the table body.
					$('#dependantTable tbody').append(dependantHTML);
				});
				//$('#spinner').css('display', 'none');
			});
			//$('#spinner').css('display', 'none');
		});

		$("#dependantTable").on('click', '#removeDependant', function (event) {
			let row = event.target.getAttribute('data-row');
			let row_number = event.target.getAttribute('data-row-number');
			$('#' + row).remove();

			// renumber all the rows below the row deleted.
			for (let i = parseInt(row_number) + 1; i <= dependants; i++) {
				var findrow = 'row' + i;
				$('[data-row=' + findrow + ']').each(function (index) {
					switch (this.id) {
						case "firstName":
							this.name = "firstName" + (i - 1);
							$(this).attr('data-row', "row" + (i - 1));
							break;

						case "lastName":
							this.name = "lastName" + (i - 1);
							$(this).attr('data-row', "row" + (i - 1));
							break;
						case "relationship":
							this.name = "relationship" + (i - 1);
							$(this).attr('data-row', "row" + (i - 1));
							break;
						case "phone":
							this.name = "phone" + (i - 1);
							$(this).attr('data-row', "row" + (i - 1));
							break;
						case "mobile":
							this.name = "mobile" + (i - 1);
							$(this).attr('data-row', "row" + (i - 1));
							break;
						case "email":
							this.name = "email" + (i - 1);
							$(this).attr('data-row', "row" + (i - 1));
							break;

					}

				});

				// fix the header
				$('#header-row' + i).each(function(index) {
					$(this).text('Dependant ' + (i - 1));
					this.id = 'row' + (i - 1);
				});

				// find the table row id
				$('#' + findrow).each( function(index) {
					this.id = 'row' + (i - 1);
				});

			}
			dependants--;
		});

		$("button#mmsubmit").click(function (event) {

			var form = $("#applyform");

			if (form[0].checkValidity() === false) {
				event.preventDefault();
				event.stopPropagation();
				form[0].classList.add('was-validated');
				return;
			}

			form[0].classList.add('was-validated');


			var applyData = jQuery('#applyform').serialize();


			jQuery.ajax({
				type: "POST",
				url: '/wp-admin/admin-ajax.php',
				data: applyData,
				beforeSend: function () {
					jQuery("#loader").show();
				},

				success: function (response) {
					console.log('add success ', response);
					var res = JSON.parse(response);


					if (res.success)
						displaySuccess(res.msg);
					else
						displayAlert(res.msg);

					jQuery("#loader").hide();
					jQuery("button#delete").show();
				},
				error: function (response) {
					console.log('add error ', response);
					var errmsg = JSON.parse(response.responseText);
					displayAlert(errmsg.msg);
					jQuery("#loader").hide();
					jQuery("button#delete").show();
				}
			});
		});

		function getMembershipTypes() {
			jQuery.ajax({
				type: "POST",
				url: '/wp-admin/admin-ajax.php',
				data: {
					action: 'membership_types'
				},

				success: function (response) {
					var types = JSON.parse(response);
					return types;


				},
				error: function (response) {
					console.log('add error ', response);
					var errmsg = JSON.parse(response.responseText);
					displayAlert(errmsg.msg);
					jQuery("button#delete").show();
					return null;
				}
			});

		}

		function getRelatioinshipTypes() {
			return jQuery.ajax({
				type: "POST",
				url: '/wp-admin/admin-ajax.php',
				data: {
					action: 'relationship_types'
				}
			});

		}


		function displayAlert(msg) {
			jQuery("#alertText").text(msg);
			jQuery("#alert").show();
		}

		function displaySuccess(msg) {
			console.log('display success fired');
			jQuery("#successText").text(msg);
			jQuery("#success").show();
		}

	});

})(jQuery);