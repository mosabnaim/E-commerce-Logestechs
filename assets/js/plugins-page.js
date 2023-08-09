jQuery(document).ready(function ($) {
	$('#wc-missing-notice').on('click', '.notice-dismiss', function () {
		$.post(
			logestechs_global_data.ajax_url,
			{
				action: 'dismiss_wc_missing_notice',
				security: logestechs_global_data.security,
			}
		);
	});
});
