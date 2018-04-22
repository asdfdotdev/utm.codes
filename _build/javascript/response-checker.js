;(function ($) {

	$(function () {
		var feedback_ele = $('label.utmdclink_url span');

		$('#utmdclink_url').on('blur', function(event){
			feedback_ele.removeClass();

			if ($(this).val() != '') {
				$.ajax({
					url: ajaxurl,
					data: {
						key: utmdc_rest_api.action_key,
						action: 'utmdc_check_url_response',
						url: $(this).val()
					}
				}).done(
					function (response) {
						process_response(response.status);
					}
				).fail(
					function (response) {
						process_response(response.status);
					}
				);
			}
		});

		function process_response(status) {
			switch (status) {
				case 200:
					feedback_ele.addClass('valid');
					break;

				case 400:
				case 401:
				case 403:
				case 404:
				case 500:
				case 502:
					feedback_ele.addClass('invalid');
					break;

				default:
					feedback_ele.addClass('unknown');
					break;
			}
		}
	});

})(jQuery);
