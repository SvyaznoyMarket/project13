define('library',
	['jquery'], function ($) {
		
		function myConsole (text){
			$('#console').prepend('<p>'+text+'</p>')
		}

		$.fn.draggable = function() {
			self = this

			self.start = null
			self.stop = null

			self.start = function(e) {
				var orig = e.originalEvent
				self.start = {
					x: orig.changedTouches[0].pageX,
					left: parseInt(self.css('left'))
				}
				self.stop = {
					start: 0,
					end: self.width() - self.parent().width()
				}
			}

			self.moveMe = function(e) {
				e.preventDefault()
				var orig = e.originalEvent
				var newLeft = orig.changedTouches[0].pageX - self.start.x + self.start.left
				if ( newLeft > self.stop.start ){ // ограничение слева
					self.css({left: 0})
					self.start.left = 0
					self.start.x = orig.changedTouches[0].pageX
				}
				else if ( newLeft < -self.stop.end){
					self.css({left: -self.stop.end})
					self.start.left = -self.stop.end
					self.start.x = orig.changedTouches[0].pageX
				}
				else{
					self.css({left: newLeft})
				}
			}

			if (self.width()>self.parent().width()){
				self.bind("touchstart", self.start)
				self.bind("touchmove", self.moveMe)
			}
		}
	})