$(function() {
  var imgHeight = $('.productDescImgList__img').height();

  //$('.productDescImgList__item').css({'height': imgHeight});
  $(".productDescImgList").css({'height': imgHeight});
  $('.productDescImgList__item:first').addClass('page-current');
  $('.productDescImgList__item.page-current').next('.productDescImgList__item').addClass('page-right');
  $('.productDescImgList__item.page-current').prev('.productDescImgList__item').addClass('page-left');
 
  $.fn.swipe = function( options ) {
    // Default thresholds & swipe functions
    options = $.extend(true, {}, $.fn.swipe.options, options);
     
    return this.each(function() {
     
      var self = this,
          originalCoord = { 'x': 0, 'y': 0 },
          finalCoord = { 'x': 0, 'y': 0 };
       
      // Screen touched, store the initial coordinates
      function touchStart( event ) {
        var touch = event.originalEvent.targetTouches[0];
        originalCoord.x = touch.pageX;
        originalCoord.y = touch.pageY;
      }
       
      // Store coordinates as finger is swiping
      function touchMove( event ) {
        var touch = event.originalEvent.targetTouches[0];
        finalCoord.x = touch.pageX; // Updated X,Y coordinates
        finalCoord.y = touch.pageY;
        event.preventDefault();
      }
       
      // Done swiping
      // Swipe should only be on X axis, ignore if swipe on Y axis
      // Calculate if the swipe was left or right
      function touchEnd() {
        var changeY = originalCoord.y - finalCoord.y,
            changeX,
            threshold = options.threshold,
            y = threshold.y,
            x = threshold.x;

        if ( changeY < y && changeY > (- y) ) {
          changeX = originalCoord.x - finalCoord.x;

          if ( changeX > x ) {
            options.swipeLeft.call(self);
          } else if ( changeX < (- x) ) {
            options.swipeRight.call(self);
          }
        }
      }
       
      // Swipe was canceled
      function touchCancel() {
      //console.log('Canceling swipe gesture…')
      }
       
      // Add gestures to all swipable areas
      $(self).bind({
        'touchstart.swipe': touchStart,
        'touchmove.swipe': touchMove,
        'touchend.swipe': touchEnd,
        'touchcancel.swipe': touchCancel
      });
    });
     
  };
     
  $.fn.swipe.options = {
    'threshold': {
      'x': 30,
      'y': 50
    },

    'swipeLeft': function() {
      $('.productDescImgList__item.page-current').removeClass('page-current').addClass('page-left');
      $('.productDescImgList__item.page-right').removeClass('page-right').addClass('page-current page-animating');
      $('.productDescImgList__item.page-current').next('.productDescImgList__item').addClass('page-right');
      $('.productDescImgList__item.page-current').prev('.productDescImgList__item').addClass('page-left');
    },

    'swipeRight': function() {
      $('.productDescImgList__item.page-current').removeClass('page-current').addClass('page-right');
      $('.productDescImgList__item.page-left').removeClass('page-left').addClass('page-current page-animating');
      $('.productDescImgList__item.page-current').prev('.productDescImgList__item').addClass('page-left');
    }
  };

  $('.productDescImgList__item').swipe();


});