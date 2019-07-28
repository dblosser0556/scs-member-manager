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
	$(function () {
		$('#typeTable').on('click', '#editType', function (event) {
			var id = event.target.attributes['data-item'].value;
			var typeData = 'action=membership_types_update&requestType=get&&name=blank&id=' + id;
			$.when(sendRequest(typeData)).then(function (data, textStatus, jqXHR) {
				var results = $.parseJSON(data);
				if (results.success == "1") {
					$('#modalId').val(results.result['id']);
					$('#modalName').val(results.result['name']);
					$('#modalDesc').val(results.result['description']);
					$('#modalCost').val(results.result['cost']);
					$('#typeModal').modal();
				} else {
					displayAlert(results.message);
				};
			});
		});

		$('#typeTable').on('click', '#deleteType', function (event) {
			var id = event.target.attributes['data-item'].value;
			var typeData = 'action=membership_types_update&requestType=delete&name="blank"&id=' + id;
			$.when(sendRequest(typeData)).then(function (data, textStatus, jqXHR) {
				var results = $.parseJSON(data);
				if (results.success == true) {
					
					displaySuccess(results.msg);
				} else {
					displayAlert(results.msg);

				}
				// reload the page
				location.reload();
			});

			alert("delete " + id);
		});

		$('#createType').click(function (event) {
			$('#typeModal').modal();
		});

		// handle the save changes button on the modal
		$('#saveChanges').click(function (event) {


			var form = $("#formTypeModal");

			if (form[0].checkValidity() === false) {
				event.preventDefault();
				event.stopPropagation();
				form[0].classList.add('was-validated');
				return;
			}

			form[0].classList.add('was-validated');

			var typeData = $('#formTypeModal').serialize();
			typeData += '&action=membership_types_update&requestType=save';

			$.when(sendRequest(typeData)).then(function (data, textStatus, jqXHR) {
				var results = $.parseJSON(data);
				if (results.success == "1") {
					
					displaySuccess(results.msg);
				} else {
					displayAlert(results.msg);

				}
				// close the modal
				$('#typeModal').modal();

				// reset the form.
				clearForm($('#formTypeModal'));

				// reload the page
				location.reload();
			});
		});

		$('#closeModal').click(function (event) {
			$('#typeModal').modal();
			clearForm($('#formTypeModal'));
		});
	});

	function sendRequest(typeData) {
		return $.ajax({
			type: "POST",
			url: '/wp-admin/admin-ajax.php',
			data: typeData
		});
	}

	function displayAlert(msg) {
		$("#alertText").text(msg);
		$("#alert").show();
	}

	function displaySuccess(msg) {
		$("#successText").text(msg);
		$("#success").show();
	}

	function clearForm(form){
		form.find(':input').not(':button, :submit, :reset, :hidden, :checkbox').val('');
		form.find('#modalId').val(0);
		form.removeClass('was-validated');
	}

})($);