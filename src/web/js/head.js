blink = function (element_id,number_of_blink) {
  number_of_blink = (number_of_blink)?number_of_blink:3;
  for(i=0;i<number_of_blink;i++) {
    $(element_id).fadeTo('normal', 0.5).fadeTo('fast', 1.0);
  }
}

$(function(){
  // menu
//  $('#s'+$('#topnav ul li.current').attr('id')).show();
//  $('#topnav ul li').each(function() {
//    $(this).hover(function() {
//      $('#topnav ul li').each(function() {$('#s'+this.id).hide();});
//      $('#s'+this.id).show();
//    });
    
//    $(this).click(function (){
//      $('#topnav ul li').each(function() {
//        $('#s'+this.id).hide();
//      });
//      $('#s'+this.id).show();
//      if($(this).children().attr("href") == "#"){
//        return false;
//      }
//    });
//  });
//  
//  $('.item_bc').click(function(){
//    if($(this).children().attr('href') == '#'){
//      menu_id = $(this).children().attr('class');
//      $('#topnav ul li').each(function() {
//        $('#s'+this.id).hide();
//      });
//      $('#s'+menu_id).show();
//      return false;
//    }
//  });
//  
//  var menu = $('#scrollnav_container');
//  if(menu.length > 0) {
//    var menupos = menu.offset();
//    $(window).scroll(function(){
//      if($(this).scrollTop() > menupos.top+menu.height()){
//        menu.css({
//          'position':'fixed',
//          'top':'-1px',
//          'width':'100%',
//          'z-index':'5555'
//        }).fadeIn('fast');
//      } else if ($(this).scrollTop() < menupos.top) {
//        menu.attr('style','');
//      }
//    });
//  }
  
  var flash = $("#flash");
  if(flash.length>0){
    blink('#flash');
  }
});