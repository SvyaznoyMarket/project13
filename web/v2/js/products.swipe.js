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

        deltaSwipe();
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

      var 
        deltaSwipe = function deltaSwipe() {
          var changeX = originalCoord.x - finalCoord.x;

          if ( changeX > 0 ) {
            $('.productDescImgList__item.page-current').css({transform:'translate('+ changeX +'px, 0)'});
          }

          if ( changeX < 0 ) {
            $('.productDescImgList__item.page-current').css({transform:'translate('+ changeX +'px, 0)'});
          }
      };
       
      // Add gestures to all swipable areas
      $(self).bind({
        'touchstart.swipe': touchStart,
        'touchmove.swipe': touchMove,
        'touchend.swipe': touchEnd,
        'touchcancel.swipe': touchCancel
      });
    });
     
  };

  var swipeItem = $('.productDescImgList__item'),
      swipeItemLeft = $('.productDescImgList__item.page-left'),
      swipeItemCurrent = $('.productDescImgList__item.page-current'),
      swipeItemRight = $('.productDescImgList__item.page-right'),

      leftClass = 'page-left',
      currentClass = 'page-current',
      rightClass = 'page-right',
      animClass = 'page-animating',
      currentAnimClass = 'page-current page-animating';
  // end var

  $('.leftSwipe').hide();

  var 
    /*
     * Добавляем классы к элементам списка swipe
     */
    addClassItem = function addClassItem() {
      console.log(swipeItemCurrent.next());
      swipeItem.first().addClass(currentClass);
      swipeItem.first().next().addClass(rightClass);
    },

    /*
     * Прокрутка swipe влево
     */
    slideSwipeLeft = function slideSwipeLeft() {
      console.info("swipe/slide left");
      $('.rightSwipe').show();

      $('.productDescImgList__item.page-right').removeClass(rightClass);
      $('.productDescImgList__item.page-current').removeClass(currentClass).removeClass(animClass).addClass(rightClass);
      $('.productDescImgList__item.page-left').removeClass(leftClass).addClass(currentAnimClass);
      $('.productDescImgList__item.page-current').prev().addClass(leftClass);

      if( $('.productDescImgList__item').first().hasClass(currentClass) ) {
        $('.leftSwipe').hide();
      }
    },

    /*
     * Прокрутка swipe вправо
     */
    slideSwipeRight = function slideSwipeRight() {
      console.info("swipe/slide right");

      $('.leftSwipe').show();

      $('.productDescImgList__item.page-left').removeClass(leftClass);
      $('.productDescImgList__item.page-current').removeClass(currentClass).removeClass(animClass).addClass(leftClass);
      $('.productDescImgList__item.page-right').removeClass(rightClass).addClass(currentAnimClass);
      $('.productDescImgList__item.page-current').next().addClass(rightClass);
      $('.productDescImgList__item.page-current').prev().addClass(leftClass);

      if( $('.productDescImgList__item').last().hasClass(currentClass) ) {
        $('.rightSwipe').hide();
      }
    },

    /*
     * Функция изменяет высоту врапера swipe блока
     */
    getImageSize = function getImageSize() {

      $(".productDescImgList img").each(function() {
        console.log(imgHeight);
        var $this = $(this),
            imgHeight = $this.height();

        $(".productDescImgList").css({'height': imgHeight});
      });
    };
  // end var
     
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

  $(window).on('load', addClassItem);

  $('.productDescImgList__item').swipe();

  $('.leftSwipe').on('click', slideSwipeLeft);

  $('.rightSwipe').on('click', slideSwipeRight);

  $(window).on('resize load', getImageSize);
});