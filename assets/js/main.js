jQuery(document).ready(function($) {
    var $popup = $('#logestechs-popup-template'); // cache the original hidden popup

    $('#show-popup, .assign-company').on('click', function(e) {
        e.preventDefault();
        $popup.fadeIn(); // append the clone to the body and show it
    });

    $('.close-popup').on('click', function(e) {
        e.preventDefault();
        $popup.fadeOut(); // remove the cloned popup
    });
});
