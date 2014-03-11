$(function() {
  var imgHeight = $('.productDescImgList__img').height();

  //$('.productDescImgList__item').css({'height': imgHeight});
  $(".productDescImgList").css({'height': imgHeight});

  $.fn.swipe = function( callback ) {
    var touchDown = false,
        originalPosition = null,
        $el = $( this );

    function swipeInfo( event ) {
      var x = event.originalEvent.pageX,
          y = event.originalEvent.pageY,
          dx, dy;

      dx = ( x > originalPosition.x ) ? "right" : "left";
      dy = ( y > originalPosition.y ) ? "down" : "up";

      return {
        direction: {
          x: dx,
          y: dy
        },
        offset: {
          x: x - originalPosition.x,
          y: originalPosition.y - y
        }
      };
    }

    $el.on( "touchstart mousedown", function ( event ) {
      touchDown = true;
      originalPosition = {
        x: event.originalEvent.pageX,
        y: event.originalEvent.pageY
      };
    } );

    $el.on( "touchend mouseup", function () {
      touchDown = false;
      originalPosition = null;
    } );

    var draw = function draw ( offset ) {

      
    };

    $el.on( "touchmove mousemove", function ( event, offset ) {
      if ( !touchDown ) { return;}
      var info = swipeInfo( event );

      callback( info.direction, info.offset );

      if ( info.offset.x > 0 ) {

        event.preventDefault();

        $('.productDescImgList__item.page-current').css({transform: 'translate(' + info.offset.x + 'px, 0)'});

        if ( info.offset.x >= 50 ) {
        $('.productDescImgList__item.page-current').removeClass('page-current').addClass('page-left');
        $('.productDescImgList__item.page-right').removeClass('page-right').addClass('page-current page-animating');
        $('.productDescImgList__item.page-current').next('.productDescImgList__item').addClass('page-right');
        $('.productDescImgList__item.page-current').prev('.productDescImgList__item').addClass('page-left');

      }
      };
    } );

    return true;
  };

  $('.productDescImgList__item:first').addClass('page-current');
  $('.productDescImgList__item.page-current').prev('.productDescImgList__item').addClass('page-left');
  $('.productDescImgList__item.page-current').next('.productDescImgList__item').addClass('page-right');
  $('.productDescImgList__item.page-current').removeClass('page-animating');

  $(".productDescImgList__img").swipe(function( direction, offset ) {
      console.log( "Moving", direction.x, "and", direction.y );
      console.log( "Touch moved by", offset.x, "horizontally and", offset.y, "vertically" ); 
      


      

      // else if ( offset.x < '0' ){
      //   //alert('left');
      // }
    });
});