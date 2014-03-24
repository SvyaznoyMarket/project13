$(function() {

  var
    swipeWrap = $(".productDescImgList"),
    swipeItem = $(".productDescImgList").find('.productDescImgList_item'),
    swipeItemWidth = swipeItem.width(),

    IMG_WIDTH = swipeItemWidth,
    currentImg = 0,
    maxImages = swipeItem.size(),
    speed= 500,

    imgs = $("#imgs"),
      
    swipeOptions = {
      triggerOnTouchEnd : true, 
      swipeStatus : swipeStatus,
      allowPageScroll: "vertical",
      threshold:75      
    },

    /*
     * Функция изменяет высоту врапера swipe блока
     */
    getImageSize = function getImageSize() {

      swipeItem.each(function() {
        console.log(imgHeight);
        var $this = $(this),
            imgHeight = $this.children().height(),
            imgWidth = $this.width();

        swipeWrap.css({
          'height': imgHeight,
          'width' : '100%'
        });
      });
    };
  // end var

/**
* Catch each phase of the swipe.
* move : we drag the div.
* cancel : we animate back to where we were
* end : we animate to the next image
*/      
function swipeStatus(event, phase, direction, distance) {
  //If we are moving before swipe, and we are going Lor R in X mode, or U or D in Y mode then drag.
  if( phase=="move" && (direction=="left" || direction=="right") )
  {
    var duration=0;
    
    if (direction == "left")
      scrollImages((IMG_WIDTH * currentImg) + distance, duration);
    
    else if (direction == "right")
      scrollImages((IMG_WIDTH * currentImg) - distance, duration);
    
  }
  
  else if ( phase == "cancel")
  {
    scrollImages(IMG_WIDTH * currentImg, speed);
  }
  
  else if ( phase =="end" )
  {
    if (direction == "right")
      previousImage()
    else if (direction == "left")     
      nextImage()
  }
};
    


function previousImage() {
  currentImg = Math.max(currentImg-1, 0);
  scrollImages( IMG_WIDTH * currentImg, speed);
};

function nextImage() {
  currentImg = Math.min(currentImg+1, maxImages-1);
  scrollImages( IMG_WIDTH * currentImg, speed);
};
  
/**
* Manuallt update the position of the imgs on drag
*/
function scrollImages(distance, duration) {
  imgs.css("transition-duration", (duration/1000).toFixed(1) + "s");
  
  //inverse the number we set in the css
  var value = (distance<0 ? "" : "-") + Math.abs(distance).toString();
  
  imgs.css("transform", "translate3d("+value +"px,0px,0px)");
};

  $(window).on('resize load', getImageSize);
  imgs.swipe( swipeOptions );

  console.log(maxImages);
  console.log(swipeItemWidth);
});