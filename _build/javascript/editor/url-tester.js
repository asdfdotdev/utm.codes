(function ($) {

	$(function () {
		var feedbackElement = $("label.utmdclink_url span");

    function processResponse(status) {
      switch (status) {
        case 200:
          feedbackElement.addClass("valid");
          break;

        case 400:
        case 401:
        case 403:
        case 404:
        case 500:
        case 502:
          feedbackElement.addClass("invalid");
          break;

        default:
          feedbackElement.addClass("unknown");
          break;
      }
    }

		$("#utmdclink_url").on("blur", function(event){
			feedbackElement.removeClass();

			if ($(this).val() !== "") {
				$.ajax({
					url: ajaxurl,
					data: {
						key: utmdcRestApi.actionKey,
						action: "utmdc_check_url_response",
						url: $(this).val()
					}
				}).done(
					function (response) {
						processResponse(response.status);
					}
				).fail(
					function (response) {
						processResponse(response.status);
					}
				);
			}
		});
	});

}(jQuery));
