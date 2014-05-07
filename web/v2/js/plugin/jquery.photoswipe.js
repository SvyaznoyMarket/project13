$(function() {

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
  // end vars

  // базовые установки слайдера
  slideWrap.css({'background' : 'url("/v2/css/modules/mainStyles/img/ajaxnoti.gif") no-repeat 50% 50%'});
  slideWrapItem.css({'display' : 'inline-block'});
  slideList.css({'display' : 'none'});
  slidePag.css({'display' : 'none'});

  var
      /*
        * Функция ресайза блока слайдера изображений товара
       */
      resizeSlides = function resizeSlides() {
        var slideWrapHeight = 350,
            slideImg = slideWrapItem.find('.slidesItemsList_img');
        // end vars

        slideWrapWidth = $('.slidesItems').width();

        if ( slideWrapWidth < 360 ) {
            slideWrapHeight = slideWrapWidth;
        };

        slideWrap.css({'background' : 'none'});
        slideList.fadeIn('300').css({'width' : slideWrapWidth * countItem});
        slideWrapItem.css({'width' : slideWrapWidth});
        slideImg.css({'height' : slideWrapHeight - 30});

        //скрываем кнопки и пагинатор, если слайдер имеет один элемент
        if ( countItem <= 1 ) {
            btnSlidesLeft.hide();
            btnSlidesRight.hide();
        }
        else {
          btnSlidesRight.fadeIn('300');
          slidePag.fadeIn('300');
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
  // end var

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

  $(window).on('load resize', resizeSlides);

  btnSlidesRight.on('click', nextSlides);
  btnSlidesLeft.on('click', prevSlides);
  paginationSlides();
});