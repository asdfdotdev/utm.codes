(function ($) {

	$(function () {

		$("#utmdclink_batch").on("change", function(event){
			$(this)
				.parents("#utmdc_link_meta_box")
				.toggleClass("batch-active", $(this).is(":checked"))
				.find("#utmdclink_source")
				.attr("required", !$(this).is(":checked"));
		});

	});

}(jQuery));
