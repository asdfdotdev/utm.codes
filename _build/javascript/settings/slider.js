(function ($) {

  $(function () {

    if($(".utmdclinks-settings-slider input").length) {
      $(".utmdclinks-settings-slider input").siblings("output").html(
        $(".utmdclinks-settings-slider input").val()
      );
    }

    $(".utmdclinks-settings-slider input").on("input", function(event){
      $(this).siblings("output").html($(this).val());
    });

    $("#utmdclink_notes_show").on("change", function(event){
      if( $(this).attr("checked") === "checked" ) {
        $("#utmdclinks_notes_preview_row").removeClass("hidden");
      } else {
        $("#utmdclinks_notes_preview_row").addClass("hidden");
      }
    });

  });

}(jQuery));
