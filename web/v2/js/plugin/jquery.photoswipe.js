; (function( $ ){

  var curSlide = 0,
      body = $('body'),
      slideWrap = $('.slidesItems'),
      slideWrapWidth = slideWrap.width(),
      slideList = $('.slidesItemsList'),
      slideWrapItem = slideList.find('.slidesItemsList_item'),
      countItem = slideWrapItem.length,

      slidePag = $('.slidesItemsBtnPag'),

      btnSlidesLeft = $('.jsBtnSlidesLeft'),
      btnSlidesRight = $('.jsBtnSlidesRight');
  // end of vars

  var
      /*
        * Функция ресайза блока слайдера изображений товара
       */
      resizeSlides = function resizeSlides() {
        slideWrapItem.css({'display' : 'block', 'float' : 'left'});

        var slideWrapHeight = 350,
            slideImg = slideWrapItem.find('.slidesItemsList_img');
        // end of vars

        slideWrapWidth = $('.slidesItems').width();

        if ( slideWrapWidth < 360 ) {
            slideWrapHeight = slideWrapWidth;
        };

        slideList.css({'width' : slideWrapWidth * countItem});
        slideWrapItem.css({'width' : slideWrapWidth});

        //скрываем кнопки и пагинатор, если слайдер имеет один элемент
        if ( countItem <= 1 ) {
            btnSlidesLeft.hide();
            btnSlidesRight.hide();
        } else {
          btnSlidesRight.show();
          slidePag.show();
        };

        var slideListLeftNew = -1 * slideWrapWidth * curSlide;

        slideList.css({'left' : slideListLeftNew});
      },

      /*
        * Пагинация слайдера
       */
      paginationSlides = function paginationSlides() {

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
          slideList.stop(true, true).animate({'left' : slideListLeftNew});

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
          slideList.stop(true, true).animate({'left' : slideListLeftNew});

          slidePagItemActive.removeClass(pagActive);
          slidePagItemActive.prev().addClass(pagActive);
        }

        if( curSlide < (countItem - 1) ) {
          $('.jsBtnSlidesRight').show();
        }
      };
  // end of vars

  $('.productDescImg').touchwipe({
    wipeLeft : function() {
      if ( curSlide < countItem - 1 ) {
        nextSlides();
      }
    },
    wipeRight : function() {
      if ( curSlide > 0 ) {
        prevSlides();
      }
    }
  });

  $(window).on('resize', resizeSlides);
  resizeSlides();

  btnSlidesRight.on('click', nextSlides);
  btnSlidesLeft.on('click', prevSlides);

  paginationSlides();
})( jQuery );