_Bbc(function($){
  var t = setInterval(function(){
    if (typeof $.fn.datepicker === "function") {
      clearInterval(t);

      $(".datepicker").each(function () {
			  $(this).datepicker($(this).data());
			});
    }
  }, 30);
});
