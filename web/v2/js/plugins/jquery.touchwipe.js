;(function( $ ) {

  // плагин взят из http://www.netcu.de/jquery-touchwipe-iphone-ipad-library
  $.fn.touchwipe = function(settings) {
    
    var config = {
      min_move_x: 20,
      min_move_y: 20,
      wipeLeft: function() { },
      wipeRight: function() { },
      wipeUp: function() { },
      wipeDown: function() { },
      preventDefaultEvents: true
    };
     
    if (settings) $.extend(config, settings);
 
    this.each(function() {
      var startX;
      var startY;
      var isMoving = false;

      function cancelTouch() {
        this.removeEventListener('touchmove', onTouchMove);
        startX = null;
        isMoving = false;
      } 
     
      function onTouchMove(e) {
        if(isMoving) {
          var x = e.touches[0].pageX;
          var y = e.touches[0].pageY;
          var dx = startX - x;
          var dy = startY - y;
          if(Math.abs(dx) >= config.min_move_x) {
            cancelTouch();
            if(dx > 0) {
              config.wipeLeft();
              if(config.preventDefaultEvents) {
                e.preventDefault();
              }
            }
            else {
              config.wipeRight();
              if(config.preventDefaultEvents) {
                e.preventDefault();
              }
            }
          }
          else if(Math.abs(dy) >= config.min_move_y) {
            cancelTouch();
            if(dy > 0) {
              config.wipeDown();
            }
            else {
              config.wipeUp();
            }
          }
        }
      }
     
      function onTouchStart(e)
      {
        if (e.touches.length == 1) {
          startX = e.touches[0].pageX;
          startY = e.touches[0].pageY;
          isMoving = true;
          this.addEventListener('touchmove', onTouchMove, false);
        }
      }      
      if ('ontouchstart' in document.documentElement) {
        this.addEventListener('touchstart', onTouchStart, false);
      }
    });
 
    return this;
  };

})( jQuery );