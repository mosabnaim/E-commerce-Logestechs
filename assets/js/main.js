jQuery(document).ready(function ($) {
	let $transfer_popup = $('#logestechs-order-transfer-popup'); // cache the original hidden popup
	let $companies_popup = $('#logestechs-order-companies-popup'); // cache the original hidden popup
	let $details_popup = $('#logestechs-order-details-popup'); // cache the original hidden popup

	$('.js-open-transfer-popup').on('click', function (e) {
		e.preventDefault();
		$transfer_popup.fadeIn(); // append the clone to the body and show it

		let order_id = $(this).data('order-id'); // Get order id from data-order-id attribute of clicked element
		$('.js-logestechs-assign-company').attr('data-order-id', order_id); // Set order id to the assign company button

		fetch_companies();
	});
	$('.js-open-companies-popup').on('click', function (e) {
		e.preventDefault();
		$companies_popup.fadeIn(); // append the clone to the body and show it
		fetch_companies();
	});
	$('.js-open-details-popup').on('click', function (e) {
		e.preventDefault();
		$details_popup.fadeIn(); // append the clone to the body and show it
	});

	$('.close-popup').on('click', function (e) {
		e.preventDefault();
		$transfer_popup.fadeOut(); // remove the cloned popup
		$companies_popup.fadeOut(); // remove the cloned popup
		$details_popup.fadeOut(); // remove the cloned popup

		$('.js-logestechs-company').removeClass('selected');
		$('#domain, #email, #password').val('');
		$('.js-logestechs-add-company').show();
		$('.js-logestechs-update-company').hide().removeAttr('data-id');
	});


	// Fetch Companies
	function fetch_companies() {
		$.post(
			logestechs_global_data.ajax_url,
			{
				action: 'logestechs_fetch_companies',
				security: logestechs_global_data.security,
			},
			function (response) {
				let companies = response.data;
				let companiesContainer = $('.js-logestechs-companies');
				companiesContainer.empty(); // Clear the existing companies

				$.each(companies, function (index, company) {
					let companyElement = renderCompany(company);
					companiesContainer.append(companyElement);
				});
			}
		);
	}

	// Event handler for add company form submission
	$(document).on('submit', '.js-company-form', function (e) {
		e.preventDefault();
		// Input validation
		let isValid = true;
		$(this).find('input').each(function () {
			if ($(this).val() === '') {
				isValid = false;
				$(this).addClass('error');
			} else {
				$(this).removeClass('error');
			}
		});

		if (!isValid) {
			alert('Please fill out all fields');
			return; // Exit event handler
		}

		// Disable buttons
		$('.js-logestechs-add-company, .js-logestechs-update-company').addClass('disabled').prop('disabled', true);

		let company_id = $(this).find('.js-logestechs-update-company').attr('data-id');
		$.post(
			logestechs_global_data.ajax_url,
			{
				action: 'logestechs_save_company',
				domain: $('#domain').val(),
				email: $('#email').val(),
				password: $('#password').val(),
				company_id: company_id,
				security: logestechs_global_data.security,
			},
			function (response) {
				let company = response.data;
				if (response.data && response.data.message === undefined) {
					let companiesContainer = $('.js-logestechs-companies');
					let companyElement = renderCompany(company);
					if (!company_id) {
						companiesContainer.prepend(companyElement);
					} else {
						$('.js-logestechs-company[data-id="' + company_id + '"]').replaceWith(companyElement);
					}
					$('#domain, #email, #password').val('');
					$('.js-logestechs-add-company').show();
					$('.js-logestechs-update-company').hide().removeAttr('data-id');
				}

				// Enable buttons
				$('.js-logestechs-add-company, .js-logestechs-update-company').removeClass('disabled').prop('disabled', false);
			}
		);
	});

	function renderCompany(company) {
		let feedback = company.feedback ? '<span class="logestechs-feedback-error">' + company.feedback + '</span>' : '';
		return '<div class="logestechs-row js-logestechs-company" data-id="' + company.id + '" data-logestechs-id="' + company.company_id + '">'
			+ '<div class="logestechs-row-main">'
			+ '<img src="' + company.logo_url + '" alt="company logo">'
			+ '<div class="logestechs-company-details">'
			+ '<div>'
			+ '<p class="logestechs-row-main-text js-company-domain">' + company.domain + '</p>'
			+ feedback
			+ '</div>'
			+ '<span class="js-company-email">' + company.email + '</span>'
			+ '</div>'
			+ '</div>'
			+ '<div class="logestechs-icons-flex">'
			+ '<img class="logestechs-edit-icon js-logestechs-edit-company" data-id="' + company.id + '" src="' + logestechs_global_data.images.edit + '" alt="edit">'
			+ '<img class="logestechs-delete-icon js-logestechs-delete-company" data-id="' + company.id + '" src="' + logestechs_global_data.images.trash + '" alt="delete">'
			+ '</div>'
			+ '</div>';
	}
	// Event handler for delete company click
	$(document).on('click', '.js-logestechs-delete-company', function () {
		let company_id = $(this).attr('data-id');
		let row = $(this).closest('.js-logestechs-company');

		// Confirmation alert
		let confirmation = confirm("Are you sure you want to delete this company?");
		if (!confirmation) {
			return; // Exit the function if the user clicked Cancel
		}

		$.post(
			logestechs_global_data.ajax_url,
			{
				action: 'logestechs_delete_company',
				company_id: company_id,
				security: logestechs_global_data.security,
			},
			function (response) {
				if (response.success) {
					row.remove();
				}
			}
		);
	});

	// On clicking the edit icon, fetch the respective company's data.
	$(document).on('click', '.js-logestechs-edit-company', function () {
		const company_id = $(this).attr('data-id');
		const company = $(this).closest('.js-logestechs-company');
		const companyDomain = company.find('.js-company-domain').html();
		const companyEmail = company.find('.js-company-email').html();

		$('.js-logestechs-add-company').hide();
		$('.js-logestechs-update-company').show().attr('data-id', company_id);
		$('#domain').val(companyDomain);
		$('#email').val(companyEmail);
	});
	$(document).on('click', '#logestechs-order-transfer-popup .js-logestechs-company', function () {
		$('.js-logestechs-company').removeClass('selected'); // Remove 'selected' class from all
		$(this).addClass('selected'); // Add 'selected' class to the clicked one
		$('.js-logestechs-assign-company').removeClass('disabled').prop('disabled', false); // Set order id to the assign company button
	});

	$(document).on('click', '.js-logestechs-assign-company', function () {
		let company_id = $('.js-logestechs-company.selected').attr('data-id');
		let order_id = $(this).data('order-id');  // Assuming each assign button holds order id in 'data-order-id' attribute

		$.post(
			logestechs_global_data.ajax_url,
			{
				action: 'logestechs_assign_company',
				company_id: company_id,
				order_id: order_id,
				security: logestechs_global_data.security,
			},
			function (response) {
				if (response.success) {
					location.reload(); // Reload the page
					// alert('Company has been assigned successfully.');
					// Refresh page or update part of the page based on response.
				} else {
					alert('There was an error: ' + response.data);
				}
			}
		);
	});


	// Cancel order
	$('.js-logestechs-cancel').on('click', function (e) {
		e.preventDefault();
		let order_id = $(this).data('order-id');  // Assuming each assign button holds order id in 'data-order-id' attribute

		$.post(
			logestechs_global_data.ajax_url,
			{
				action: 'logestechs_cancel_order',
				order_id: order_id,
				security: logestechs_global_data.security,
			},
			function (response) {
				if (response.success) {
					location.reload(); // Reload the page
					// alert('Company has been assigned successfully.');
					// Refresh page or update part of the page based on response.
				} else {
					alert('There was an error: ' + response.data);
				}
			}
		);
	});

	$(document).on('click', '.js-logestechs-print', function (e) {
		e.preventDefault();

		// Optional: You might want to get some data from the clicked element, such as an order ID
		var order_id = $(this).data('order-id');

		// Make an AJAX request to your server to get the PDF URL
		$.post(
			logestechs_global_data.ajax_url,
			{
				action: 'logestechs_print_order',
				order_id: order_id,
				security: logestechs_global_data.security,
			},
			function (response) {
				if (response.success) {
					// Check if the response contains the URL
					if (response.data && response.data.url) {
						// Check if the response contains the URL
						if (response.data && response.data.url) {
							// Create an anchor element to download the PDF
							var link = document.createElement('a');
							link.href = response.data.url;
							link.target = '_blank'; // Open in a new window/tab
							link.download = 'order_' + order_id + '.pdf'; // You can name the file as you like
							link.style.display = 'none'; // Hide the link

							// Append the link to the body
							document.body.appendChild(link);

							// Trigger the click event
							link.click();

							// Optional: remove the link after triggering the download
							document.body.removeChild(link);

						}

					} else {
						// Handle the error if the URL is not provided
						alert('Failed to download the PDF.');
					}
				} else {
					alert('An error occurred. Please try again later.');
				}
			}
		);
	});

});
