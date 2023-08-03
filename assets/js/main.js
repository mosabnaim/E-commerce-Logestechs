jQuery(document).ready(function ($) {
  var $transfer_popup = $('#logestechs-order-transfer-popup'); // cache the original hidden popup
  var $companies_popup = $('#logestechs-order-companies-popup'); // cache the original hidden popup
  var $details_popup = $('#logestechs-order-details-popup'); // cache the original hidden popup

  $('.js-open-transfer-popup').on('click', function (e) {
    e.preventDefault();
    $transfer_popup.fadeIn(); // append the clone to the body and show it
  });
  $('.js-open-companies-popup').on('click', function (e) {
    e.preventDefault();
    $companies_popup.fadeIn(); // append the clone to the body and show it
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
  });

  // Transfer order
  $('#transfer-button').on('click', function (e) {
    e.preventDefault();

    var data = {
      action: 'logestechs_transfer_order',
      order_id: 'your_order_id', // replace this with the actual order ID
      security: logestechs_ajax_object.security,
    };
    $.post(logestechs_ajax_object.ajax_url, data, function (response) {
      console.log(response);
    });
  });

  // Cancel order
  $('#logestechs-cancel-order').on('click', function (e) {
    e.preventDefault();

    var data = {
      action: 'logestechs_cancel_order',
      order_id: 'your_order_id', // replace this with the actual order ID
      security: logestechs_ajax_object.security,
    };
    $.post(logestechs_ajax_object.ajax_url, data, function (response) {
      console.log(response);
    });
  });
});
