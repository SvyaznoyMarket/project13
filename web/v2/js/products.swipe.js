$(function() {
  var curSlide = 0,
      countItem = 0,
      slideWrap = $('.slidesItems'),
      slideWrapWidth = $('.slidesItems').width(),
      slideWrapItem = $('.slidesItems').find('.slidesItemsList_item'),

      btnSlidesLeft = $('.jsBtnSlidesLeft'),
      btnSlidesRight = $('.jsBtnSlidesRight'),

      /*
        * Функция ресайза блока слайдера изображений товара
       */
      resizeSlides = function resizeSlides() {
        var slideList = $('.slidesItemsList'),
            slideWrap = $('.slidesItems'),
            slideWrapHeight = 500;
        
        slideWrapWidth = $('.slidesItems').width()   

        if ( slideWrapWidth < 360 ) {
            slideWrapHeight = slideWrapWidth;
        };

        slideWrap.css({'height' : slideWrapHeight});

        countItem = 0;

        slideWrapItem.each(function() {
          countItem++;

          var slideImg = $(this).find('.slidesItemsList_img'),
              slideImgWidth = slideImg.width(),
              slideImgHeight = slideImg.height();

          slideList.css({'width' : slideWrapWidth * countItem});
          $(this).css({'width' : slideWrapWidth});
          slideImg.css({'height' : slideWrapHeight});
        });

        if ( countItem <= 1 ) {
            btnSlidesLeft.hide();
            btnSlidesRight.hide();
        };

        var slideListLeftNew = -1 * slideWrapWidth * curSlide;

        $('.slidesItemsList').css({'left' : slideListLeftNew});
      },

      /*
        * Пагинация слайдера
       */
      paginationSlides = function paginationSlides() {
        var slidePag = $('.slidesItemsBtnPag'),
            i;
     
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
          $('.slidesItemsList').animate({'left' : slideListLeftNew});

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

        var slideListLeft = $('.slidesItemsList').css('left');
            slideListLeftNew = -1 * slideWrapWidth * curSlide,

            slidePag = $('.slidesItemsBtnPag'),
            slidePagItemActive = slidePag.find('.slidesItemsBtnPag_item__active'),
            pagActive = 'slidesItemsBtnPag_item__active';
         
        if ( curSlide <= 0 ) {
            $('.jsBtnSlidesLeft').hide();
        }

        if( curSlide >= 0 ) {
          $('.slidesItemsList').animate({'left' : slideListLeftNew});

          slidePagItemActive.removeClass(pagActive);
          slidePagItemActive.prev().addClass(pagActive);
        }

        if( curSlide < (countItem - 1) ) {
          $('.jsBtnSlidesRight').show();
        }
      };
  // end var
      
  $(window).on('load resize', resizeSlides);
  btnSlidesRight.bind('click', nextSlides);
  btnSlidesLeft.bind('click', prevSlides);
  paginationSlides();
});


