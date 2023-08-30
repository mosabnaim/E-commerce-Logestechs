jQuery(document).ready(function ($) {
    let is_sending_request = false;

    var debounceTimer;
    var sortOrder = getParameterByName('sort_order');
    var sortBy = getParameterByName('sort_by');
    // Get current page number from URL
    var searchQuery = getParameterByName('search');
    var dateFrom = getParameterByName('date_from');
    var dateTo = getParameterByName('date_to');

    var currentPage = getParameterByName('paged');
    if (!currentPage) currentPage = 1;
    $('.js-logestechs-search').val(searchQuery);
    if(dateFrom && dateTo) {
        $('#date_range').val(dateFrom + ' - '+ dateTo);
    }
    $('.js-logestechs-search').on('keyup', function () {
        clearTimeout(debounceTimer);
        let that = $(this);
        debounceTimer = setTimeout(function () {
            searchQuery = that.val().trim().replace(/#/g, '');
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
    $('.js-logestechs-submit-settings').on('click', function() {
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
    }, function(start, end) {
        dateFrom = start.format('YYYY-MM-DD');
        dateTo = end.format('YYYY-MM-DD');

        load_orders();
        // You can use the 'start' and 'end' dates here as required
    });
    $('#date_range').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
    });
  
    $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
        dateFrom = '';
        dateTo = '';

        load_orders();
        $(this).val('');
    });
    $(document).on('click', '.js-logestechs-sort', function(e) {
        e.preventDefault();
        sortBy = $(this).data('sort-by');
        if(sortOrder == 'ASC') {
            sortOrder = 'DESC'; // You can toggle this based on the current sort order
        }else {
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

        // Call the load_orders function to reload the content
        load_orders();
    });
    function updateUrlWithParameters() {
        var url = new URL(window.location.href);
        var params = new URLSearchParams(url.search);
    
        // Update the parameters with the new values
        params.set('paged', currentPage);
        
        if(searchQuery) {
            params.set('search', searchQuery);
        }else {
            params.delete('search');
        }
        if(sortBy) {
            params.set('sort_by', sortBy);
        }else {
            params.delete('sort_by');
        }
        if(sortOrder) {
            params.set('sort_order', sortOrder);
        }else {
            params.delete('sort_order');
        }
        if(dateFrom) {
            params.set('date_from', dateFrom);
        }else {
            params.delete('date_from');
        }
        if(dateTo) {
            params.set('date_to', dateTo);
        }else {
            params.delete('date_to');
        }
        ['paged', 'search', 'sort_by', 'sort_order', 'date_from', 'date_to'].forEach(function(key) {
            if (!params.get(key) || params.get(key) == null) params.delete(key);
        });
        // Set the new search string back to the URL object
        url.search = params.toString();
    
        // Push the new URL to the history state
        history.pushState(null, null, url.toString());
    }
    function load_orders() {
        // Make an AJAX request to fetch the page content
        if(is_sending_request) {
            return;
        }
        is_sending_request = true;
        $.post(
            logestechs_global_data.ajax_url,
            {
                action: 'logestechs_get_orders',
                security: logestechs_global_data.security,
                paged: currentPage,
                search: searchQuery, // Include the search query here
                sort_by: sortBy,
                sort_order: sortOrder,
                date_from: dateFrom,
                date_to: dateTo
            },
            function (response) {
                is_sending_request = false;
                // Update the page content with the received HTML
                // Assuming the orders are contained in an element with the ID "logestechs_orders_table"
                $('#logestechs_orders_table tbody').html(response.orders_html);
                // Update pagination links
                $('.logestechs-pagination').html(response.pagination_links);
                $('.js-logestechs-count').html(response.total_count);

                // Replace the URL with the new page number
                updateUrlWithParameters();
                if(response.total_count) {
                    syncLogestechsOrders();
                }
            }
        );
    }
    
    load_orders();

    function syncLogestechsOrders() {
        $('span.js-logestechs-status-cell').removeClass().addClass('js-logestechs-status-cell').html('<div class="logestechs-skeleton-loader"></div>');
        $('.logestechs-dropdown').hide();
        if(is_sending_request) {
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
                date_to: dateTo
            },
            function (response) {
                is_sending_request = false;

                $('.logestechs-dropdown').show();
                $.each(response, function (orderId, newStatus) {
                    var $row = $(".js-logestechs-order[data-order-id='" + orderId + "']");
                    var $statusCell = $row.find("span.js-logestechs-status-cell");
                    $statusCell.removeClass().addClass('js-logestechs-status-cell');
                    $statusCell.addClass("logestechs-" + newStatus.toLowerCase().replace(/ /g, '-'));
                    $statusCell.text(newStatus);

                    if(newStatus.toLowerCase() == 'cancelled') {
                        $row.find('.js-normal-dropdown').addClass('hidden');
                        $row.find('.js-cancelled-dropdown').removeClass('hidden');
                    }else {
                        $row.find('.js-normal-dropdown').removeClass('hidden');
                        $row.find('.js-cancelled-dropdown').addClass('hidden');
                    }
                });
            }
        );
    }

    // Click event handler
    $(document).on('click', '.js-logestechs-sync', syncLogestechsOrders);

    // Automatically trigger the click event every 2 minutes (120000 milliseconds)
    setInterval(syncLogestechsOrders, 120000);
});
