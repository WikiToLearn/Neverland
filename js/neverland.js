/* Fix for Echo in Refreshed */
if ( document.getElementById( 'echo' ) ) {
        $( '#pt-notifications' ).prependTo( '#echo' );
}

if ( $( '.mw-echo-notifications-badge' ).hasClass( 'mw-echo-unread-notifications' ) ) {
        $( '#pt-notifications-personaltools a' ).addClass( 'pt-notifications-personaltools-unread' );
}

function isBreakpoint( alias ) {
  return $('.device-' + alias).is(':visible');
}

$( document ).ready(function() {
  /*
  When user is editing with VE the button doesn't get actived because
  the code used to make it selected is rendered via javascript and
  not in PHP. This prevents Neverland.php to recognize the right 
  attribute to give to the element and it ends with a select attribute
  on the 'view' button.
  References: ve.init.mw.DesktopArticleTarget.sj:1107
  */
  mw.hook('ve.activate').add(function () {
    if ($('#ca-ve-edit').hasClass('selected')) {
      $('#ca-ve-edit').removeClass('selected').addClass('btn-success');
      $('#ca-view').removeClass('btn-success').addClass('btn-default');
    }
  });

$('#searchform').removeClass('navbar-search').removeClass('pull-right').addClass('navbar-form').addClass('navbar-right');
$('#searchInput').addClass("form-control");
$('form[name=userlogin]').addClass("col-xs-12");
$('#userloginForm').addClass("row");
$('#userlogin2').addClass("col-xs-12");

if( $('.breakpoint-xs').is(':hidden') ) {
  $('.footer-wtl').addClass(" text-center ").removeClass(" text-left ");
  $('#views').addClass('btn-group-justified');
}
if( $('.breakpoint-sm').is(':hidden') ) {
  $('.wtl-menu-mobile').hide();
}

$('#mw-createaccount-cta').removeAttr('id');

    $('.contributionscores.plainlinks').removeClass('wikitable').addClass('table-bordered');

  $('.divider').hide();
  var active_breadcrumb = $('li.active');
  $('ul.breadcrumb>li:empty').remove();

  $('a.btn.btn-mini').css('padding','1%');
});

// borrowed from https://developer.mozilla.org/en-US/docs/Web/API/Fullscreen_API
function toggleFullScreen() {
  if (!document.fullscreenElement &&    // alternative standard method
      !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement ) {  // current working methods
    if (document.documentElement.requestFullscreen) {
      document.documentElement.requestFullscreen();
    } else if (document.documentElement.msRequestFullscreen) {
      document.documentElement.msRequestFullscreen();
    } else if (document.documentElement.mozRequestFullScreen) {
      document.documentElement.mozRequestFullScreen();
    } else if (document.documentElement.webkitRequestFullscreen) {
      document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
    }
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.msExitFullscreen) {
      document.msExitFullscreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
      document.webkitExitFullscreen();
    }
  }
}

// Reader mode
$(function() {
  $( ".reader .fullscreen").click(function() {
    toggleFullScreen();
  });

  $( ".toggle_reader" ).each(function() {
    $(this).click(function() {
      $( ".reader" ).toggleClass( "active" );
      if ($( ".reader").hasClass("active")) {
        $( ".reader" ).css({
          height: $( document ).height() + "px"
        });
      } else {
        $( ".reader" ).css({
          height : 0
        });
      }
      $('#firstHeading').toggleClass("heading-reader");
      $('#bodyContent').toggleClass("readermode");
      $('.reader > .container').toggleClass('container-reader')
      if(!$.trim($(".reader .container").html())) {
        console.log("reader is empty, cloning...");
        $( "#content" ).appendTo(".reader .container");
      } else {
        $(".nav.nav-tabs.noprint").after($( "#content" ));
      }
      var offset = $( ".reader .container" ).offset();
      $( ".reader .reader_logo img" ).css({
        "max-width": (offset.left - 10) + "px"
      });
      $(window).on('resize', function(){
        var offset = $( ".reader .container" ).offset();
        $( ".reader .reader_logo img" ).css({
          "max-width": (offset.left - 10) + "px"
        });
      });
    });
  });
});
