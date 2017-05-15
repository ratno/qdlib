$(function(){
  $(".datagrid-box").css("cursor","auto");
  $(".w-box-header:not(.datagrid-box)").on("click",function(){
    if($(this).siblings().css("display") == "none"){
      $(this).children(".head-collapse").removeClass("ui-custom-icon ui-custom-icon-triangle-1-e").addClass("ui-custom-icon ui-custom-icon-triangle-1-s");
    } else {
      $(this).children(".head-collapse").removeClass("ui-custom-icon ui-custom-icon-triangle-1-s").addClass("ui-custom-icon ui-custom-icon-triangle-1-e");
    }
    $(this).siblings().slideToggle("slow");
  });
});
