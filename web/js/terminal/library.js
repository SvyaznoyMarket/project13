define('library',
	['jquery'], function ($) {
		function myConsole(text){
			$('#console').prepend('<p>'+text+'</p>')
		}

		$.fn.draggable = function() {
			self = $(this)
			var start = null
			var stop = null
			var start = function(e) {
				var orig = e.originalEvent
				start = {
					x: orig.changedTouches[0].pageX,
					left: parseInt(self.css('left'))
				}
				stop = {
					start: 0,
					end: self.width() - self.parent().width()
				}
			}
			var moveMe = function(e) {
				e.preventDefault()
				var orig = e.originalEvent
				var newLeft = orig.changedTouches[0].pageX - start.x + start.left
				// myConsole(left)
				if ( newLeft > stop.start ){ // ограничение слева
					$(this).css({left: 0})
					start.left = 0
					start.x = orig.changedTouches[0].pageX
				}
				else if ( newLeft < -stop.end){
					$(this).css({left: -stop.end})
					start.left = -stop.end
					start.x = orig.changedTouches[0].pageX
				}
				else{
					$(this).css({left: newLeft})
				}
			}
			this.bind("touchstart", start)
			this.bind("touchmove", moveMe)
		}
	})