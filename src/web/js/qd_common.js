//* detect touch devices 
function is_touch_device() {
  return !!('ontouchstart' in window);
}
$(document).ready(function() {
  //* accordion change actions
  $('#side_accordion').on('hidden shown', function () {
    qd_sidebar.make_active();
    qd_sidebar.update_scroll();
  });
  //* resize elements on window resize
  var lastWindowHeight = $(window).height();
  var lastWindowWidth = $(window).width();
  $(window).on("debouncedresize",function() {
    if($(window).height()!=lastWindowHeight || $(window).width()!=lastWindowWidth){
      lastWindowHeight = $(window).height();
      lastWindowWidth = $(window).width();
      qd_sidebar.update_scroll();
      if(!is_touch_device()){
        $('.sidebar_switch').qtip('hide');
      }
    }
  });
  //* tooltips
  qd_tips.init();
  if(!is_touch_device()){
    //* popovers
    qd_popOver.init();
  }
  //* sidebar
  qd_sidebar.init();
  qd_sidebar.make_active();
  //* breadcrumbs
  qd_crumbs.init();
  //* accordion icons
  qd_acc_icons.init();
  //* main menu mouseover
  qd_nav_mouseover.init();
  //* top submenu
  qd_submenu.init();

  qd_sidebar.make_scroll();
  qd_sidebar.update_scroll();
});

qd_sidebar = {
  init: function() {
    // sidebar onload state
    if($(window).width() > 979){
      if(!$('body').hasClass('sidebar_hidden')) {
        if( $.cookie('qd_sidebar') == "hidden") {
          $('body').addClass('sidebar_hidden');
          $('.sidebar_switch').toggleClass('on_switch off_switch').attr('title','Show Sidebar');
        }
      } else {
        $('.sidebar_switch').toggleClass('on_switch off_switch').attr('title','Show Sidebar');
      }
    } else {
      $('body').addClass('sidebar_hidden');
      $('.sidebar_switch').removeClass('on_switch').addClass('off_switch');
    }

    qd_sidebar.info_box();
    //* sidebar visibility switch
    $('.sidebar_switch').click(function(){
      $('.sidebar_switch').removeClass('on_switch off_switch');
      if( $('body').hasClass('sidebar_hidden') ) {
        $.cookie('qd_sidebar', null);
        $('body').removeClass('sidebar_hidden');
        $('.sidebar_switch').addClass('on_switch').show();
        $('.sidebar_switch').attr( 'title', "Hide Sidebar" );
      } else {
        $.cookie('qd_sidebar', 'hidden');
        $('body').addClass('sidebar_hidden');
        $('.sidebar_switch').addClass('off_switch');
        $('.sidebar_switch').attr( 'title', "Show Sidebar" );
      }
      qd_sidebar.info_box();
      qd_sidebar.update_scroll();
      $(window).resize();
    });
    //* prevent accordion link click
    $('.sidebar .accordion-toggle').click(function(e){
      console.log($(this).attr("href"));
      if($(this).attr("href").substring(0,1) == "#"){
        e.preventDefault()
      } else {
        // mangga di lanjut kliknya
      }
    });
  },
  info_box: function(){
    var s_box = $('.sidebar_info');
    var s_box_height = s_box.actual('height');
    s_box.css({
      'height'        : s_box_height
    });
    $('.push').height(s_box_height);
    $('.sidebar_inner').css({
      'margin-bottom' : '-'+s_box_height+'px',
      'min-height'    : '100%'
    });
  },
  make_active: function() {
    var thisAccordion = $('#side_accordion');
    thisAccordion.find('.accordion-heading').removeClass('sdb_h_active');
    var thisHeading = thisAccordion.find('.accordion-body.in').prev('.accordion-heading');
    if(thisHeading.length) {
      thisHeading.addClass('sdb_h_active');
    }
  },
  make_scroll: function() {
    antiScroll = $('.antiScroll').antiscroll().data('antiscroll');
  },
  update_scroll: function() {
    if($('.antiScroll').length) {
      if( $(window).width() > 979 ){
        $('.antiscroll-inner,.antiscroll-content').height($(window).height() - 40);
      } else {
        $('.antiscroll-inner,.antiscroll-content').height('400px');
      }
      antiScroll.refresh();
    }
  }
};

//* tooltips
qd_tips = {
  init: function() {
    if(!is_touch_device()){
      var shared = {
        style		: {
          classes: 'ui-tooltip-shadow ui-tooltip-tipsy'
        },
        show		: {
          delay: 100,
          event: 'mouseenter focus'
        },
        hide		: {
          delay: 0
        }
      };
      if($('.ttip_b').length) {
        $('.ttip_b').qtip( $.extend({}, shared, {
          position	: {
            my		: 'top center',
            at		: 'bottom center',
            viewport: $(window)
          }
        }));
      }
      if($('.ttip_t').length) {
        $('.ttip_t').qtip( $.extend({}, shared, {
          position: {
            my		: 'bottom center',
            at		: 'top center',
            viewport: $(window)
          }
        }));
      }
      if($('.ttip_l').length) {
        $('.ttip_l').qtip( $.extend({}, shared, {
          position: {
            my		: 'right center',
            at		: 'left center',
            viewport: $(window)
          }
        }));
      }
      if($('.ttip_r').length) {
        $('.ttip_r').qtip( $.extend({}, shared, {
          position: {
            my		: 'left center',
            at		: 'right center',
            viewport: $(window)
          }
        }));
      };
    }
  }
};

//* popovers
qd_popOver = {
  init: function() {
    $(".pop_over").popover();
  }
};

//* breadcrumbs
qd_crumbs = {
  init: function() {
    if($('#jCrumbs').length) {
      $('#jCrumbs').jBreadCrumb({
        endElementsToLeaveOpen: 0,
        beginingElementsToLeaveOpen: 0,
        timeExpansionAnimation: 500,
        timeCompressionAnimation: 500,
        timeInitialCollapse: 500,
        previewWidth: 30
      });
    }
  }
};

//* accordion icons
qd_acc_icons = {
  init: function() {
    var accordions = $('.main_content .accordion');

    accordions.find('.accordion-group').each(function(){
      var acc_active = $(this).find('.accordion-body').filter('.in');
      acc_active.prev('.accordion-heading').find('.accordion-toggle').addClass('acc-in');
    });
    accordions.on('show', function(option) {
      $(this).find('.accordion-toggle').removeClass('acc-in');
      $(option.target).prev('.accordion-heading').find('.accordion-toggle').addClass('acc-in');
    });
    accordions.on('hide', function(option) {
      $(option.target).prev('.accordion-heading').find('.accordion-toggle').removeClass('acc-in');
    });	
  }
};

//* main menu mouseover
qd_nav_mouseover = {
  init: function() {
    $('header li.dropdown').mouseenter(function() {
      if($('body').hasClass('menu_hover')) {
        $(this).addClass('navHover')
      }
    }).mouseleave(function() {
      if($('body').hasClass('menu_hover')) {
        $(this).removeClass('navHover open')
      }
    });
  }
};

//* submenu
qd_submenu = {
  init: function() {
    $('.dropdown-menu li').each(function(){
      var $this = $(this);
      if($this.children('ul').length) {
        $this.addClass('sub-dropdown');
        $this.children('ul').addClass('sub-menu');
      }
    });

    $('.sub-dropdown').on('mouseenter',function(){
      $(this).addClass('active').children('ul').addClass('sub-open');
    }).on('mouseleave', function() {
      $(this).removeClass('active').children('ul').removeClass('sub-open');
    })

  }
};