jQuery(document).ready(function($) {
	$('#tajx-form').submit(function() {
		$('.output-message').html('<img src="/wp-admin/images/wpspin_light.gif" class="waiting" />');
		$(this).attr('disabled', 1);
		data = {
			'action'	: 'get_results'
		};
		$.post(ajaxurl, data, function(response) {
			$('.output-message').html(response);
		});
		return false;
	});
});