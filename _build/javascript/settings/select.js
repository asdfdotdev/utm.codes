(function ($) {

  $(function () {

    $('#utmdclink_shortener').on('change', function(event){

      if('none' !== $(this).val()) {
        $('#utmdclinks_shortener_api_row').removeClass("hidden");
      } else {
        $('#utmdclinks_shortener_api_row').addClass("hidden");
      }

      if('rebrandly' === $(this).val()) {
        $('#utmdclinks_shortener_custom_domain_row').removeClass("hidden");
      } else {
        $('#utmdclinks_shortener_custom_domain_row').addClass("hidden");
      }

    });

  });

})(jQuery);
