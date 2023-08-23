jQuery(document).ready(function ($) {
	let $transfer_popup = $('#logestechs-order-transfer-popup'); // cache the original hidden popup
	let $companies_popup = $('#logestechs-order-companies-popup'); // cache the original hidden popup
	let $details_popup = $('#logestechs-order-details-popup'); // cache the original hidden popup
	let r;
	let is_sending_request = false;
	function debounce(func, delay) {
		var timeout;
		return function () {
			var context = this, args = arguments;
			clearTimeout(timeout);
			timeout = setTimeout(function () {
				func.apply(context, args);
			}, delay);
		};
	}
	function fetch_villages() {
		return debounce(function () {
			var that = $(this);
			var wrapper = that.closest('.logestechs-search-wrapper');
			var resultsDiv = wrapper.find('.logestechs-village-results');
			var query = that.val().trim();
			if (query.length < 2) {
				resultsDiv.html('<p class="js-logestechs-loading">Please write at least 2 characters.</p>').show();
				return;
			};
	
			var order_id = $('.logestechs-order-settings-popup').attr('data-order-id');
			resultsDiv.html('<p class="js-logestechs-loading">loading...</p>').show();
	
			if (is_sending_request) return;
			is_sending_request = true;
			
			$.post(
				logestechs_global_data.ajax_url,
				{
					action: 'logestechs_fetch_villages',
					security: logestechs_global_data.security,
					query: query,
					order_id: order_id,
				},
				function (response) {
					is_sending_request = false;
					resultsDiv.empty().show();
					const villages = response.data.villages.data;
	
					if (villages && villages.length > 0) {
						villages.forEach(function (village) {
							var resultItem = $('<div></div>').text(village.name);
							resultItem.data('id', village.id); // Storing the id in the element's data attributes
							resultItem.data('city-id', village.cityId); // Storing the id in the element's data attributes
							resultItem.data('region-id', village.regionId); // Storing the id in the element's data attributes
							resultItem.on('click', function () {
								that.val(village.name); // Set the selected value using 'name'
								// You can now access the id using $(this).data('id') if needed
								resultsDiv.hide();
								$('.js-logestechs-transfer-order').removeClass('disabled').prop('disabled', false);
								wrapper.find('.js-logestechs-selected-village').val($(this).data('id'));
								wrapper.find('.js-logestechs-selected-region').val($(this).data('region-id'));
								wrapper.find('.js-logestechs-selected-city').val($(this).data('city-id'));
							});
							resultsDiv.append(resultItem);
						});
					} else {
						var noVillagesMessage = $('<p class="js-logestechs-loading"></p>').text('No matches found.');
						resultsDiv.append(noVillagesMessage);
					}
				}
			);
		}, 300);
	}
	
	$('#logestechs-destination-village-search').on('input', fetch_villages());
	$('#logestechs-store-village-search').on('input', fetch_villages());
	$(document).on('click', '.logestechs-dropdown img', function (e) {
		e.stopPropagation();
		$(this).parent().addClass('visible');
	});
	$(document).on('click', function (e) {
		$('.logestechs-dropdown').removeClass('visible');
		$('.logestechs-village-results').hide();
	});
	$('#logestechs-custom-store-checkbox').on('change', function() {
        $('.js-logestechs-store-details input').attr('required', this.checked);
    });
	$(document).on('click', '.js-open-transfer-popup', function (e) {
		e.preventDefault();
		$transfer_popup.fadeIn(); // append the clone to the body and show it
		$('.js-logestechs-assign-company').addClass('disabled').prop('disabled', true); // Set order id to the assign company button

		let order_id = $(this).data('order-id'); // Get order id from data-order-id attribute of clicked element
		$('.js-logestechs-assign-company').attr('data-order-id', order_id); // Set order id to the assign company button
		const next = document.querySelector('.js-logestechs-assign-company');
		const submit = document.querySelector('.js-logestechs-transfer-order');
		fetch_companies();
		if (r) {
			return;
		}
		r = new rive.Rive({
			src: logestechs_global_data.images.loader,
			canvas: $('#logestechs-loader')[0],
			autoplay: true,
			stateMachines: 'State Machine 1',
			fit: rive.Fit.cover,
			onLoad: (_) => {
				const inputs = r.stateMachineInputs('State Machine 1');
				const isLoading = inputs.find(i => i.name === 'loading');
				const isFinished = inputs.find(i => i.name === 'finished');
				const isStop = inputs.find(i => i.name === 'stopped');
				const isReset = inputs.find(i => i.name === 'reset');
				next.onclick = (e) => {
					if(is_sending_request) return;
					is_sending_request = true;

					let company_id = $('.js-logestechs-company.selected').attr('data-id');
					let order_id = $(this).data('order-id');
					$.post(
						logestechs_global_data.ajax_url,
						{
							action: 'logestechs_prepare_order_popup',
							security: logestechs_global_data.security,
							company_id: company_id,
							order_id: order_id
						},
						function (response) {
							is_sending_request = false;

							if (response.success) {
								$('.js-logestechs-order-address').html(response.data);
								$('.logestechs-order-settings-popup').fadeIn();
								$('.logestechs-order-settings-popup').find('[name="company_id"]').val(company_id);
								$('.logestechs-order-settings-popup').find('[name="order_id"]').val(order_id);
							}
						}
					);
				}

				$('form.logestechs-order-settings-popup').on('submit', function(e) {
					e.preventDefault();
					$('.logestechs-order-settings-popup').fadeOut();
					$('.js-loader-screen').fadeIn();
					isLoading.fire();

					// Serialize the form data
					let formData = $(this).serializeArray();
					
					// Optionally add specific values or merge with other data
					formData.push({
						name: 'action',
						value: 'logestechs_assign_company'
					});
					formData.push({
						name: 'security',
						value: logestechs_global_data.security,
					});
					var loadingStartTime = Date.now(); // Capture the time when loading started
					if(is_sending_request) return;
					is_sending_request = true;
					$.post(
						logestechs_global_data.ajax_url,
						formData,
						function (response) {
							is_sending_request = false;

							var loadingEndTime = Date.now(); // Capture the time when loading finished
							var loadingDuration = loadingEndTime - loadingStartTime; // Calculate the duration of the loading animation

							// Calculate the time remaining for the next loop completion
							var animationDuration = 1300; // Duration of one loop in milliseconds
							var remainingTime = Math.ceil(loadingDuration / animationDuration) * animationDuration - loadingDuration;

							if (response.success) {
								setTimeout(function () {
									isFinished.fire(); // Reload the page
									setTimeout(function () {
										location.reload();
									}, 1800);
								}, remainingTime);
							} else {
								setTimeout(function () {
									isStop.fire();
									let errorMessage = 'Something went wrong!'; // Default error message
									if (response.data.errors) {
										errorMessage += '<ul>';
										response.data.errors.forEach(function (error) {
											errorMessage += '<li>' + error + '</li>'; // Adding each error to a bulleted list
										});
										errorMessage += '</ul>';
									} else if (response.data) {
										errorMessage = response.data;
									}
									setTimeout(function () {
										$('.js-loader-screen').fadeOut();
										Swal.fire({
											title: 'Oops...',
											html: errorMessage,
											icon: 'error',
											confirmButtonColor: '#f24f41',
										});
										isReset.fire();

									}, 3800);
								}, 2000);
							}
						}
					);
				});

			}
		});
	});
	$(document).on('click', '.js-open-companies-popup', function (e) {
		e.preventDefault();
		$companies_popup.fadeIn(); // append the clone to the body and show it
		fetch_companies();
	});

	$(document).on('click', '.js-open-details-popup', function (e) {
		e.preventDefault();
		var orderId = $(this).data('order-id');
		$details_popup.fadeIn();

		$('.js-tracking-data').html('');
		$('.js-logestechs-order-value').each(function (index, element) {
			// Create variations using index or random values
			var width = 50 + Math.random() * 30 + 'px'; // Width ranging from 70% to 100%

			$(element).find('.logestechs-skeleton-loader').css({
				'width': width,
			});
		});
		if(is_sending_request) return;
		is_sending_request = true;
		$.post(
			logestechs_global_data.ajax_url,
			{
				action: 'logestechs_fetch_order_details',
				security: logestechs_global_data.security,
				order_id: orderId
			},
			function (response) {
				is_sending_request = false;

				// Update the order ID
				$('#order-id').text(response.order_id);

				// Update the popup with the new details
				$('.js-logestechs-order-value').each(function () {
					var key = $(this).attr('data-key');
					if (response[key]) {
						$(this).text(response[key]);
					}
				});
				// Process the tracking data
				if (response.tracking_data) {
					var trackingHTML = '';
					$.each(response.tracking_data, function (index, tracking) {
						if (index === 0) {
							trackerLine = '<span class="logestechs-tracker-line"></span>';
						}
						trackingHTML +=
							'<div class="logestechs-tracking-row">' +
							'<div class="logestechs-date-wrapper">' +
							'<p class="logestechs-date">' + tracking.date + '</p>' +
							'<p class="logestechs-time">' + tracking.time + '</p>' +
							'</div>' +
							'<div class="logestechs-tracking-circle">' +
							trackerLine +
							'<span></span>' +
							'<div class="logestechs-circle"></div>' +
							'</div>' +
							'<div class="logestechs-tracking-data">' + tracking.name + '</div>' +
							'</div>';
					});
					$('.js-tracking-data').html(trackingHTML);
				}
			}
		);
	});

	$(document).on('click', '.js-close-popup', function (e) {
		e.preventDefault();
		$(this).closest('.logestechs-popup').fadeOut(); // remove the cloned popup
		if (!$(this).closest('.logestechs-popup').hasClass('logestechs-order-settings-popup')) {
			$('.js-logestechs-company').removeClass('selected');
		}
		$('[name="domain"], [name="email"], [name="password"], #logestechs-village-search').val('');
		$('.js-logestechs-add-company').show();
		$('.js-logestechs-update-company').hide().removeAttr('data-id');
	});


	// Fetch Companies
	function fetch_companies() {
		if(is_sending_request) return;
		is_sending_request = true;
		$.post(
			logestechs_global_data.ajax_url,
			{
				action: 'logestechs_fetch_companies',
				security: logestechs_global_data.security,
			},
			function (response) {
				is_sending_request = false;

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
			Swal.fire({
				title: 'Error!',
				text: 'Please fill out all fields',
				icon: 'error',
				confirmButtonColor: '#f24f41',
			});
			return; // Exit event handler
		}

		// Disable buttons
		$('.js-logestechs-add-company, .js-logestechs-update-company').addClass('disabled').prop('disabled', true);

		let company_id = $(this).find('.js-logestechs-update-company').attr('data-id');
		if(is_sending_request) return;
		is_sending_request = true;
		$.post(
			logestechs_global_data.ajax_url,
			{
				action: 'logestechs_save_company',
				domain: $('[name="domain"]').val(),
				email: $('[name="email"]').val(),
				password: $('[name="password"]').val(),
				company_id: company_id,
				security: logestechs_global_data.security,
			},
			function (response) {
				is_sending_request = false;

				let company = response.data;
				if (response.success) {
					let companiesContainer = $('.js-logestechs-companies');
					let companyElement = renderCompany(company);
					if (!company_id) {
						companiesContainer.prepend(companyElement);
					} else {
						$('.js-logestechs-company[data-id="' + company_id + '"]').replaceWith(companyElement);
					}
					$('[name="domain"], [name="email"], [name="password"]').val('');
					$('.js-logestechs-add-company').show();
					$('.js-logestechs-update-company').hide().removeAttr('data-id');
					Swal.fire({
						title: 'Success!',
						text: company_id ? 'Company updated successfully.' : 'Company added successfully.',
						icon: 'success',
						confirmButtonColor: '#05b272',
					});
				} else if (response.data) {
					Swal.fire({
						title: 'Oops...',
						text: response.data,
						icon: 'error',
						confirmButtonColor: '#f24f41',
					});
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
	// Event handler for delete company click
	$(document).on('click', '.js-logestechs-delete-company', function () {
		let company_id = $(this).attr('data-id');
		let row = $(this).closest('.js-logestechs-company');

		// Confirmation alert using SweetAlert2
		Swal.fire({
			title: 'Are you sure?',
			text: 'You are about to delete this company. This action cannot be undone.',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Yes, delete it!',
			cancelButtonText: 'No, keep it!',
			confirmButtonColor: '#b2050b',
			reverseButtons: true
		}).then((result) => {
			if (result.isConfirmed) {
				if(is_sending_request) return;
				is_sending_request = true;
				$.post(
					logestechs_global_data.ajax_url,
					{
						action: 'logestechs_delete_company',
						company_id: company_id,
						security: logestechs_global_data.security,
					},
					function (response) {
						is_sending_request = false;

						if (response.success) {
							row.remove();
							Swal.fire({
								title: 'Deleted!',
								text: 'The company has been deleted.',
								icon: 'success',
								confirmButtonColor: '#f24f41',
							});
						} else {
							let errorMessage = 'Something went wrong!'; // Default error message
							if (response.data) {
								errorMessage = response.data; // Specific error message
							}

							Swal.fire({
								title: 'Oops...',
								text: errorMessage,
								icon: 'error',
								confirmButtonColor: '#f24f41',
							});
						}
					}
				);
			} else if (result.dismiss === Swal.DismissReason.cancel) {
				Swal.fire({
					title: 'Kept',
					text: 'The company has not been deleted.',
					icon: 'info',
					confirmButtonColor: '#f24f41',
				});
			}
		});
	});


	// On clicking the edit icon, fetch the respective company's data.
	$(document).on('click', '.js-logestechs-edit-company', function () {
		const company_id = $(this).attr('data-id');
		const company = $(this).closest('.js-logestechs-company');
		const companyDomain = company.find('.js-company-domain').html();
		const companyEmail = company.find('.js-company-email').html();

		$('.js-logestechs-add-company').hide();
		$('.js-logestechs-update-company').show().attr('data-id', company_id);
		$('[name="domain"]').val(companyDomain);
		$('[name="email"]').val(companyEmail);
	});
	$(document).on('click', '#logestechs-order-transfer-popup .js-logestechs-company', function () {
		$('.js-logestechs-company').removeClass('selected'); // Remove 'selected' class from all
		$(this).addClass('selected'); // Add 'selected' class to the clicked one
		$('.js-logestechs-assign-company').removeClass('disabled').prop('disabled', false); // Set order id to the assign company button
	});

	// Cancel order
	$(document).on('click', '.js-logestechs-cancel', function (e) {
		e.preventDefault();
		let order_id = $(this).data('order-id');  // Assuming each assign button holds order id in 'data-order-id' attribute
		Swal.fire({
			title: 'Are you sure?',
			text: 'You are about to cancel the order. This action cannot be undone.',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Yes, cancel it!',
			cancelButtonText: 'No, keep it!',
			confirmButtonColor: '#b2050b',
			reverseButtons: true
		}).then((result) => {
			if (result.isConfirmed) {
				// Code to execute the cancellation
				// You might want to call a specific function here, e.g., cancel_order_in_logestechs(order_id);
				if(is_sending_request) return;
				is_sending_request = true;
				$.post(
					logestechs_global_data.ajax_url,
					{
						action: 'logestechs_cancel_order',
						order_id: order_id,
						security: logestechs_global_data.security,
					},
					function (response) {
						is_sending_request = false;

						if (response == '') {
							Swal.fire({
								title: 'Cancelled!',
								text: 'The order has been cancelled.',
								icon: 'success',
								confirmButtonColor: '#f24f41',
							}).then((result) => {
								location.reload(); // Reload the page
							});
							// alert('Company has been assigned successfully.');
							// Refresh page or update part of the page based on response.
						} else {
							let errorMessage = 'Something went wrong!'; // Default error message
							if (response.data && response.data.error) {
								errorMessage = response.data.error; // Specific error message
							}

							Swal.fire({
								title: 'Oops...',
								text: errorMessage,
								icon: 'error',
								confirmButtonColor: '#f24f41',
							});
						}
					}
				);
			} else if (result.dismiss === Swal.DismissReason.cancel) {
				Swal.fire({
					title: 'Kept',
					text: 'The order has not been cancelled.',
					icon: 'info',
					confirmButtonColor: '#f24f41',
				})
			}
		});
	});

	$(document).on('click', '.js-logestechs-print', function (e) {
		e.preventDefault();
		let that = $(this);
		that.addClass('disabled').prop('disabled', true).html('Downloading...');
		// Optional: You might want to get some data from the clicked element, such as an order ID
		var order_id = $(this).data('order-id');

		// Make an AJAX request to your server to get the PDF URL
		if(is_sending_request) return;
		is_sending_request = true;
		$.post(
			logestechs_global_data.ajax_url,
			{
				action: 'logestechs_print_order',
				order_id: order_id,
				security: logestechs_global_data.security,
			},
			function (response) {
				is_sending_request = false;

				that.removeClass('disabled').prop('disabled', false).html('Print Invoice');
				// Check if the response contains the URL
				if (response.success && response.data && response.data.url) {
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
					Swal.fire({
						title: 'Oops...',
						text: 'Failed to download the PDF.',
						icon: 'error',
						confirmButtonColor: '#f24f41',
					});
				}
			}
		);
	});
	$(document).on('change', '#logestechs-custom-store-checkbox', function(e) {
		if(this.checked) {
			$('.js-logestechs-store-details').show();
		}else {
			$('.js-logestechs-store-details').hide();
		}
	});
});
