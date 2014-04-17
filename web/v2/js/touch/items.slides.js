;(function( window, $, undefined ) {

  $.fn.enterSlides = function( params ) {
    console.log("enterSlides");

    var SlidesAction = function( mainNode ) {

      var options = $.extend(
                  {},
                  $.fn.enterSlides.defaults,
                  params),
          $self = mainNode,

          body = $('body'),

          slideLeft = 0,

          slidesWrap = $self.find(options.slidesWrapSelector),
          slidesList = $self.find(options.slidesSelector),
          slidesListItem = $self.find(options.itemSelector),

          btnPrev = $self.find(options.leftBtnSelector),
          btnNext = $self.find(options.rightBtnSelector),

          itemCount = slidesListItem.length,
          itemW = slidesListItem.width() + parseInt(slidesListItem.css('paddingLeft'),10) + parseInt(slidesListItem.css('paddingRight'),10),
          sliderW = slidesWrap.width(),
          listW = itemW * itemCount,
          fitCount = Math.floor( sliderW / itemW );
      // end of vars

      slidesList.css({'width' : listW, 'left' : 0});
      btnNext.show();
      
      var 
        /**
         * Прокрутка слайдера к следующему слайду
         */
        nextSlides = function nextSlides() {
        
          btnPrev.show();

          if ( slideLeft + fitCount * itemW >= slidesList.width()-fitCount * itemW ) {
            slideLeft = slidesList.width() - fitCount * itemW;
            btnNext.hide();
          }
          else {
            slideLeft = slideLeft + fitCount * itemW;
            btnNext.show();
          }

          slidesList.stop(true, false).animate({'left' : -slideLeft});

          console.log(slideLeft);
          console.log(itemW);
          console.log(fitCount)

          return false;

        },

        /**
         * Прокрутка слайдера к предыдущему слайду
         */
        prevSlides = function prevSlides() {

          btnNext.show();

          if ( slideLeft - fitCount * itemW <= 0 ) {
            slideLeft = 0;
            btnPrev.hide();
          }
          else {
            slideLeft = slideLeft - fitCount * itemW;
            btnPrev.show();
          }

          slidesList.stop(true, false).animate({'left' : -slideLeft});

          return false;

        },

        /**
         * Состояния контролов слайдера
         */
        controlsSlides = function controlsSlides() {
          if ( itemCount <= fitCount ) {
            btnPrev.hide();
            btnNext.hide();
          }

          if ( slideLeft == 0 ) {
            btnPrev.hide();
          }
        };
      // end of function

      // var swipeOptionsResp = {
      //       triggerOnTouchEnd: true, 
      //       swipeStatus: swipeStatusPesp,
      //       allowPageScroll: "vertical",
      //       threshold: 75      
      //     },

      // swipeStatusPesp = function swipeStatusPesp( event, phase, direction, distance ) {
      //   if ( phase =="end" ) {
      //     if ( direction == "right" ) {
      //       prevRespSlide();
      //     }
      //     else if ( direction == "left")  {   
      //       nextRespSlide();
      //     }
      //   }
      // };

      controlsSlides();
      btnNext.on('click', nextSlides);
      btnPrev.on('click', prevSlides);
      //slidesList.swipe( swipeOptionsResp );
    };

    return this.each(function() {
      var $self = $(this);

      new SlidesAction($self);
    });
  };

  $.fn.enterSlides.defaults = {
    leftBtnSelector: '.sliderBox_btn__left',
    rightBtnSelector: '.sliderBox_btn__right',
    slidesWrapSelector: '.sliderBox_inner',
    slidesSelector: '.sliderBoxItems',
    itemSelector: '.sliderBoxItems_item'
  };

})( window, jQuery );

$(function(){
    $(window).on('load resize', function() { $('.js-productSlider').enterSlides() });
});