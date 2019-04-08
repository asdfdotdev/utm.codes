(function ($) {

  $(function () {

    $('#utmdclink_shortener').on('change', function(event){
      if('none' !== $(this).val()) {
        $('#utmdclink_shortener_api_row').removeClass("hidden");
      } else {
        $('#utmdclink_shortener_api_row').addClass("hidden");
      }
    });

  });

})(jQuery);
