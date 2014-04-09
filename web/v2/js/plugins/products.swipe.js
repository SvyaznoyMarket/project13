;(function($){
  var curSlide = 0,
      countItem = 0,
      slideWrap = $('.slidesItems'),
      slideWrapWidth = $('.slidesItems').width(),
      slideList = $('.slidesItemsList'),
      slideWrapItem = $('.slidesItems').find('.slidesItemsList_item'),

      slidePag = $('.slidesItemsBtnPag'),

      btnSlidesLeft = $('.jsBtnSlidesLeft'),
      btnSlidesRight = $('.jsBtnSlidesRight');

  slideWrap.css({'background' : 'url("/v2/css/modules/mainStyles/img/ajaxnoti.gif") no-repeat 50% 50%'});
  slideWrapItem.css({'display' : 'inline-block'});
  slideList.css({'display' : 'none'});
  slidePag.css({'display' : 'none'});

  var
      /*
        * Функция ресайза блока слайдера изображений товара
       */
      resizeSlides = function resizeSlides() {
        var slideList = $('.slidesItemsList'),
            slideWrap = $('.slidesItems'),
            slideWrapHeight = 400;
        
        slideWrapWidth = $('.slidesItems').width();   

        if ( slideWrapWidth < 360 ) {
            slideWrapHeight = slideWrapWidth;
        };

        slideWrap.css({'height' : slideWrapHeight, 'background' : 'none'});

        countItem = 0;

        slideWrapItem.each(function() {
          countItem++;

          var slideImg = $(this).find('.slidesItemsList_img');

          slideList.fadeIn('300').css({'width' : slideWrapWidth * countItem});
          $(this).css({'width' : slideWrapWidth});
          slideImg.css({'height' : slideWrapHeight});
        });

        if ( countItem <= 1 ) {
            btnSlidesLeft.hide();
            btnSlidesRight.hide();
        }
        else {
          btnSlidesRight.fadeIn('300');
          slidePag.fadeIn('300');
        };

        var slideListLeftNew = -1 * slideWrapWidth * curSlide;

        $('.slidesItemsList').css({'left' : slideListLeftNew});
      },

      /*
        * Пагинация слайдера
       */
      paginationSlides = function paginationSlides() {
     
        slideWrapItem.each(function() {
          countItem++;
        });

        if ( countItem > 1 ) { 
            for ( var i = 1; i <= countItem; i++) {
              slidePag.append('<li class="slidesItemsBtnPag_item"></li>')
            };
          }

        $('.slidesItemsBtnPag_item').first().addClass('slidesItemsBtnPag_item__active');
      },

      /*
        * Функция прокрутки вправо блока слайдера изображений товара
       */
      nextSlides = function nextSlides() {
        curSlide++;

        var slideListLeft = $('.slidesItemsList').css('left'),
            slideListLeftNew = -1 * slideWrapWidth * curSlide,

            slidePag = $('.slidesItemsBtnPag'),
            slidePagItemActive = slidePag.find('.slidesItemsBtnPag_item__active'),
            pagActive = 'slidesItemsBtnPag_item__active';
       
        if( curSlide >= (countItem - 1) ) {
          $('.jsBtnSlidesRight').hide();
        }

        if( curSlide <= (countItem - 1) ) {
          $('.slidesItemsList').stop(true, true).animate({'left' : slideListLeftNew});

            slidePagItemActive.removeClass(pagActive);
            slidePagItemActive.next().addClass(pagActive);
        }

        if( curSlide > 0 ) {
          $('.jsBtnSlidesLeft').show();
        }
      },

      /*
        * Функция прокрутки влево блока слайдера изображений товара
       */
      prevSlides = function prevSlides() {
        curSlide--;

        var slideListLeft = $('.slidesItemsList').css('left'),
            slideListLeftNew = -1 * slideWrapWidth * curSlide,

            slidePag = $('.slidesItemsBtnPag'),
            slidePagItemActive = slidePag.find('.slidesItemsBtnPag_item__active'),
            pagActive = 'slidesItemsBtnPag_item__active';
         
        if ( curSlide <= 0 ) {
            $('.jsBtnSlidesLeft').hide();
        }

        if( curSlide >= 0 ) {
          $('.slidesItemsList').stop(true, true).animate({'left' : slideListLeftNew});

          slidePagItemActive.removeClass(pagActive);
          slidePagItemActive.prev().addClass(pagActive);
        }

        if( curSlide < (countItem - 1) ) {
          $('.jsBtnSlidesRight').show();
        }
      };
  // end var

  var swipeOptions = {
        triggerOnTouchEnd: true, 
        swipeStatus: swipeStatus,
        allowPageScroll: "vertical",
        threshold: 75      
      };
  // end var

  function swipeStatus( event, phase, direction, distance ) {
    if ( phase =="end" ) {
      if ( direction == "right" && curSlide > 0 ) {
        prevSlides();
      }
      else if ( direction == "left" && curSlide < countItem-1 )  {   
        nextSlides();
      }
    }
  };
      
  $(window).on('load resize', resizeSlides);

  btnSlidesRight.on('click', nextSlides);
  btnSlidesLeft.on('click', prevSlides);
  paginationSlides();

  slideList.swipe( swipeOptions );

})(jQuery);