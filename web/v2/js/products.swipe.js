$(function() {
 
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

  var imgHeight = $('.productDescImgList__img').height(),

      swipeItem = $('.productDescImgList__item'),
      swipeItemLeft = $('.productDescImgList__item.page-left'),
      swipeItemCurrent = $('.productDescImgList__item.page-current'),
      swipeItemRight = $('.productDescImgList__item.page-right'),

      leftClass = 'page-left',
      currentClass = 'page-current',
      rightClass = 'page-right',
      animClass = 'page-animating',
      currentAnimClass = 'page-current page-animating';

    var imgHeight = $('.productDescImgList__img').height();

    $(".productDescImgList").css({'height': imgHeight});
    $('.productDescImgList__item').css({'opacity': '0'});
    $('.productDescImgList__item:first').addClass(currentClass).css({'opacity':'1'});
    $('.productDescImgList__item.page-current').next().addClass(rightClass);

    

    $('.leftSwipe').hide();

    var 
      slideSwipeLeft = function slideSwipeLeft() {
        $('.rightSwipe').show();

        $('.productDescImgList__item.page-right').removeClass('page-right').css({'opacity':'0'});
        $('.productDescImgList__item.page-current').removeClass('page-current').removeClass('page-animating').addClass('page-right').css({'opacity':'0'});
        $('.productDescImgList__item.page-left').removeClass('page-left').addClass('page-current page-animating').css({'opacity':'1'});
        $('.productDescImgList__item.page-current').prev('.productDescImgList__item').addClass('page-left').css({'opacity':'0'});

        if( $('.productDescImgList__item').first().hasClass('page-current') ) {
          $('.leftSwipe').hide();
        }
      },

      slideSwipeRight = function slideSwipeRight() {
        console.info("swipe/slide right");

        $('.leftSwipe').show();

        $('.productDescImgList__item.page-left').removeClass('page-left');
        $('.productDescImgList__item.page-current').removeClass('page-current').removeClass('page-animating').addClass('page-left').css({'opacity':'0'});
        $('.productDescImgList__item.page-right').removeClass('page-right').addClass('page-current page-animating').css({'opacity':'1'});
        $('.productDescImgList__item.page-current').next('.productDescImgList__item').addClass('page-right').css({'opacity':'0'});
        $('.productDescImgList__item.page-current').prev('.productDescImgList__item').addClass('page-left').css({'opacity':'0'});

        if( $('.productDescImgList__item').last().hasClass('page-current') ) {
          $('.rightSwipe').hide();
        }

      };
     
  $.fn.swipe.options = {
    'threshold': {
      'x': 30,
      'y': 50
    },

    'swipeLeft': function() {
      slideSwipeRight();
    },

    'swipeRight': function() {
      slideSwipeLeft();
    }
  };

  $('.productDescImgList__item').swipe();

  $('.leftSwipe').bind('click', slideSwipeLeft);

  $('.rightSwipe').bind('click', slideSwipeRight);
});