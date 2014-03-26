$(function() {

  var swipeWrap = $('.productDescImg'),
      swipeItem = $('.productDescImg productDescImgList_img'),
      swipeAction = $('.jsSlides'),
  /*
   * Функция изменяет высоту врапера swipe блока
   */
  getImageSize = function getImageSize() {

    swipeItem.each(function() {
      console.log(imgHeight);
      var $this = $(this),
          imgHeight = $this.height();

      swipeWrap.css({
        'height': imgHeight
      });
    });
  };

  swipeAction.slidesjs({
    width: 350,
    height: 350
  });

  $(window).on('resize load', getImageSize);
});