jQuery(document).ready(function ($) {
    let is_sending_request = false;

    var debounceTimer;
    var sortOrder = getParameterByName('sort_order');
    var sortBy = getParameterByName('sort_by');
    // Get current page number from URL
    var searchQuery = getParameterByName('search');
    var statusFilter = getParameterByName('status_filter');
    var dateFrom = getParameterByName('date_from');
    var dateTo = getParameterByName('date_to');

    var currentPage = getParameterByName('paged');
    if (!currentPage) currentPage = 1;
    $('.js-logestechs-search').val(searchQuery);
    if (dateFrom && dateTo) {
        $('#date_range').val(dateFrom + ' - ' + dateTo);
    }
    $('.js-logestechs-search').on('keyup', function () {
        clearTimeout(debounceTimer);
        let that = $(this);
        debounceTimer = setTimeout(function () {
            searchQuery = that.val().trim().replace(/#/g, '');
            $('.js-logestechs-select-all').prop('checked', false).trigger('change');
            load_orders();
        }, 800);
    });
    // Function to get query parameter by name
    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }
    $('.js-logestechs-submit-settings').on('click', function () {
        $('#logestechs-settings-submit').trigger('click');
    });
    // Highlight the current page
    $('.logestechs-pagination .page-numbers').each(function () {
        if ($(this).text() == currentPage) {
            $(this).addClass('current').siblings().removeClass('current');
        }
    });
    $("#date_range").daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'YYYY-MM-DD'
        }
    }, function (start, end) {
        dateFrom = start.format('YYYY-MM-DD');
        dateTo = end.format('YYYY-MM-DD');
        $('.js-logestechs-select-all').prop('checked', false).trigger('change');

        load_orders();
        // You can use the 'start' and 'end' dates here as required
    });
    $('#date_range').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
    });

    $('#date_range').on('cancel.daterangepicker', function (ev, picker) {
        dateFrom = '';
        dateTo = '';

        $('.js-logestechs-select-all').prop('checked', false).trigger('change');
        load_orders();
        $(this).val('');
    });
    $(document).on('click', '.js-logestechs-sort', function (e) {
        e.preventDefault();
        sortBy = $(this).data('sort-by');
        if (sortOrder == 'ASC') {
            sortOrder = 'DESC'; // You can toggle this based on the current sort order
        } else {
            sortOrder = 'ASC'; // You can toggle this based on the current sort order
        }
        
        load_orders();
    });

    // Handle click on pagination links
    $(document).on('click', '.logestechs-pagination .page-numbers', function (e) {
        e.preventDefault();

        // If the clicked element has a class 'next', increase the current page by 1
        if ($(this).hasClass('next')) {
            currentPage += 1;
        }
        // If the clicked element has a class 'prev', decrease the current page by 1
        else if ($(this).hasClass('prev')) {
            currentPage -= 1;
        }
        // Otherwise, set the current page to the number clicked
        else {
            currentPage = parseInt($(this).text(), 10);
        }
        $('.js-logestechs-select-all').prop('checked', false).trigger('change');

        // Call the load_orders function to reload the content
        load_orders();
    });
    function updateUrlWithParameters() {
        var url = new URL(window.location.href);
        var params = new URLSearchParams(url.search);

        // Update the parameters with the new values
        params.set('paged', currentPage);

        if (searchQuery) {
            params.set('search', searchQuery);
        } else {
            params.delete('search');
        }
        if (statusFilter) {
            params.set('status_filter', statusFilter);
        } else {
            params.delete('status_filter');
        }
        if (sortBy) {
            params.set('sort_by', sortBy);
        } else {
            params.delete('sort_by');
        }
        if (sortOrder) {
            params.set('sort_order', sortOrder);
        } else {
            params.delete('sort_order');
        }
        if (dateFrom) {
            params.set('date_from', dateFrom);
        } else {
            params.delete('date_from');
        }
        if (dateTo) {
            params.set('date_to', dateTo);
        } else {
            params.delete('date_to');
        }
        ['paged', 'search', 'sort_by', 'sort_order', 'date_from', 'date_to', 'status_filter'].forEach(function (key) {
            if (!params.get(key) || params.get(key) == null) params.delete(key);
        });
        // Set the new search string back to the URL object
        url.search = params.toString();

        // Push the new URL to the history state
        history.pushState(null, null, url.toString());
    }
    // Listening to checkbox changes
    $(document).on('change', 'input[type=checkbox][name=selected_orders]', function () {
        let selectedCount = $('input[type=checkbox][name=selected_orders]:checked').length;

        // Show/hide the selection actions header
        if (selectedCount > 0) {
            $('.js-logestechs-selection-actions').show();
            $('.js-logestechs-table-head').hide();
        } else {
            $('.js-logestechs-table-head').show();
            $('.js-logestechs-selection-actions').hide();
        }

        if ($('input[type=checkbox][name=selected_orders]:checked').length == 0) {
            $('.js-logestechs-select-all').prop('checked', false);
        } else if ($('input[type=checkbox][name=selected_orders]:not(:checked)').length == 0) {
            $('.js-logestechs-select-all').prop('checked', true);
        }

        // Update the selected packages count
        $('.js-logestechs-selection-count').text(selectedCount);

        // Enable/Disable bulk actions
        if (selectedCount == 0) {
            $('.js-logestechs-bulk-print, .js-logestechs-bulk-transfer').addClass('disabled').prop('disabled', true);
        } 
        // Check if all selected rows have 'js-logestechs-submittable' class
        const $selectedCheckboxes = $('input[type=checkbox][name=selected_orders]:checked');
        const $submittableRows = $selectedCheckboxes.closest('tr').filter('.js-logestechs-submittable');
        
        if ($selectedCheckboxes.length === $submittableRows.length && selectedCount > 0) {
            $('.js-logestechs-bulk-transfer').removeClass('disabled').prop('disabled', false).show();
            $('.js-logestechs-bulk-transfer').attr('title', ''); // Clear the tooltip
        } else {
            $('.js-logestechs-bulk-transfer').addClass('disabled').prop('disabled', true);
            $('.js-logestechs-bulk-transfer').attr('title', logestechs_global_data.localization.bulk_transfer_error);
        }

        check_if_printable();
    });

    // Select all checkboxes
    $('.js-logestechs-select-all').on('change', function () {
        let isChecked = $(this).prop('checked');
        $('input[type=checkbox][name=selected_orders]').prop('checked', isChecked).trigger('change');
    });
    $('.js-logestechs-select-all-btn').on('click', function () {
        $('.js-logestechs-select-all').prop('checked', true).trigger('change');
    });
    $('.js-logestechs-cancel-selection').on('click', function () {
        $('.js-logestechs-select-all').prop('checked', false).trigger('change');

        $('.js-logestechs-table-head').show();
        $('.js-logestechs-selection-actions').hide();
    });
    function check_if_printable() {  
        // Initialize an empty array to hold unique company names
        let companyNames = [];

        // Loop through each selected checkbox
        $('input[type=checkbox][name=selected_orders]:checked').each(function() {
            // Get closest row
            const $row = $(this).closest('tr');

            // Get the company name from that row
            const companyName = $row.find('.logestechs-company-name').text();

            // If the company name is not in the array, add it
            if ($.inArray(companyName, companyNames) === -1) {
                companyNames.push(companyName);
            }
        });

        bulkPrintButton = $('.js-logestechs-bulk-print');
        // Check if all selected rows are from the same company
        if (companyNames.length === 1) {
            bulkPrintButton.removeClass('disabled').prop('disabled', false);
            bulkPrintButton.attr('title', ''); // Clear the tooltip
        } else {
            bulkPrintButton.addClass('disabled').prop('disabled', true);
            bulkPrintButton.attr('title', logestechs_global_data.localization.bulk_print_error);
        }
    }
    function load_orders() {
        // Make an AJAX request to fetch the page content
        if (is_sending_request) {
            return;
        }
        is_sending_request = true;
        $.post(
            logestechs_global_data.ajax_url,
            {
                action: 'logestechs_get_orders',
                security: logestechs_global_data.security,
                paged: currentPage,
                search: searchQuery,
                sort_by: sortBy,
                sort_order: sortOrder,
                date_from: dateFrom,
                date_to: dateTo,
                status_filter: statusFilter
            },
            function (response) {
                is_sending_request = false;
                // Update the page content with the received HTML
                // Assuming the orders are contained in an element with the ID "logestechs_orders_table"
                $('#logestechs_orders_table tbody').html(response.orders_html);
                // Update pagination links
                $('.logestechs-pagination').html(response.pagination_links);
                $('.js-logestechs-count').html(response.total_count);

                updateUrlWithParameters();
                if (response.total_count) {
                    syncLogestechsOrders();
                }
            }
        );
    }

    load_orders();

    function syncLogestechsOrders() {
        $('span.js-logestechs-status-cell').removeClass().removeAttr('data-status').addClass('js-logestechs-status-cell').html('<div class="logestechs-skeleton-loader"></div>');
        $('span.js-logestechs-notes-cell').removeClass().removeAttr('data-notes').addClass('js-logestechs-notes-cell').html('<div class="logestechs-skeleton-loader"></div>');
        
        $('.logestechs-dropdown').hide();
        if (is_sending_request) {
            return;
        }
        is_sending_request = true;
        $.post(
            logestechs_global_data.ajax_url,
            {
                action: 'logestechs_sync_orders_status',
                security: logestechs_global_data.security,
                paged: currentPage,
                search: searchQuery, // Include the search query here
                sort_by: sortBy,
                sort_order: sortOrder,
                date_from: dateFrom,
                date_to: dateTo,
                status_filter: statusFilter
            },
            function (response) {
                is_sending_request = false;

                $('.logestechs-dropdown').show();
                $.each(response, function (orderId, package) {
                    let newStatus = package.status;
                    let notes = package.notes; // Assuming that notes are returned from the server
                    let $row = $(".js-logestechs-order[data-order-id='" + orderId + "']");
                    let $statusCell = $row.find("span.js-logestechs-status-cell");
                    let $notesCell = $row.find("span.js-logestechs-notes-cell"); // New notes cell
    
                    // Update status
                    $statusCell.text(logestechs_global_data?.status_array[newStatus]);
                    $statusCell.attr('data-status', newStatus);
    
                    // Update notes
                    let displayNotes = notes ? notes : '-'; // Display '-' if notes are empty
                    $notesCell.text(displayNotes); // Updating the notes cell with the new notes
                    $notesCell.attr('data-notes', displayNotes); // Add data attribute if you need
                    const dropdown = $row.find('.js-logestechs-dropdown');
                    dropdown.find('.js-dynamic-option').remove();

                    // Update dropdowns based on status
                    const ACCEPTABLE_CANCEL_STATUS = logestechs_global_data?.acceptable_cancel_status;
                    const ACCEPTABLE_PICKUP_STATUS = logestechs_global_data?.acceptable_pickup_status;
                    const ACCEPTABLE_TRANSFER_STATUS = logestechs_global_data?.acceptable_transfer_status;
                    dropdown.append('<div class="js-open-details-popup js-dynamic-option" data-order-id="' + orderId + '">'+logestechs_global_data.localization.track+'</div>');

                    // Dynamic dropdown logic
                    if (ACCEPTABLE_CANCEL_STATUS.includes(newStatus)) {
                        dropdown.append('<div class="js-logestechs-cancel js-dynamic-option" data-order-id="' + orderId + '">'+logestechs_global_data.localization.cancel+'</div>');
                    }
                    if (ACCEPTABLE_TRANSFER_STATUS.includes(newStatus)) {
                        dropdown.append('<div class="js-open-transfer-popup logestechs-white-btn js-dynamic-option" data-order-id="' + orderId + '">'+logestechs_global_data.localization.transfer_order+'</div>');
                    }
                    if (ACCEPTABLE_PICKUP_STATUS.includes(newStatus)) {
                        dropdown.append('<div class="js-open-pickup-popup js-logestechs-request-return logestechs-white-btn js-dynamic-option" data-order-id="' + orderId + '">'+logestechs_global_data.localization.request_pickup+'</div>');
                    }
                });
            }
        );
    }
    $(document).on('click', '.logestechs-dropdown-filter', function(e) {
        e.stopPropagation();
        $('.logestechs-dropdown').removeClass('visible');
		$('.logestechs-village-results').hide();
        $('.js-logestechs-status-filter-dropdown').show();
    });
	$(document).on('click', function (e) {
		$('.js-logestechs-status-filter-dropdown').hide();
	});
    // Hide the popup when the close button is clicked
    $('.logestechs-cancel-button').click(function() {
        $('.logestechs-overlaypanel').hide();
    });
    // Listen for input in the search field
    $('#logestechs-status-search').on('input', function() {
        
        // Get the current search term
        var searchTerm = $(this).val().toLowerCase();
        
        // Loop through each status option
        $('.logestechs-status-options div').each(function() {
            
            // Get the text of the current status option
            var optionText = $(this).text().toLowerCase();
            
            // Check if the search term is present in the option text
            if (optionText.indexOf(searchTerm) > -1) {
                $(this).show();  // Show the option if the term is present
            } else {
                $(this).hide();  // Hide the option if the term is not present
            }
        });
    });

    // Show the popup when the button is clicked
    $('#manageAccountButton').click(function(e) {
        e.stopPropagation();
        $('.logestechs-overlaypanel').show();
    });
    $('.logestechs-overlaypanel').click(function(e) {
        e.stopPropagation();
    });
    
    // Add a new event listener for when a status is clicked
    $('.logestechs-status-options div').on('click', function () {
        // Get the clicked status
        statusFilter = $(this).data('value');
		$('.js-logestechs-status-filter-dropdown').hide();
        $('.logestechs-status-filter').html($(this).html());
        $('.js-logestechs-select-all').prop('checked', false).trigger('change');
        // Load orders with the new filter

        load_orders();
    });

    // Click event handler
    $(document).on('click', '.js-logestechs-sync', syncLogestechsOrders);

    // Automatically trigger the click event every 2 minutes (120000 milliseconds)
    setInterval(syncLogestechsOrders, 120000);
});
