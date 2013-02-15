define('library',
	['jquery'], function ($) {

		// console function
		myConsole = function(text){
			$('#console').prepend('<p>'+text+'</p>')
			console.log(text)
		}

		// custom select
		if ($('.bCustomSelect').length){
			var selectOpen = false
			$('.bCustomSelect').toggle(function(){
				$('.bCustomSelect').removeClass('mActive')
				$(this).addClass('mActive')
				selectOpen = true
			},
			function(){
				$(this).removeClass('mActive')
				selectOpen = false
			})

			$('.bWrap').bind('click', function(event){
				if (selectOpen){
					event.preventDefault()
					$('.bCustomSelect').removeClass('mActive')
					selectOpen = false
				}
			})
		}

		// compare toggle
		checkCompare = function(element, productId){
			if (terminal.compare.hasProduct(productId)){
				element.html('Убрать из сравнения')
				return true
			}
			else{
				element.html('К сравнению')
				return false
			}
		}

		// drug plugin
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
				terminal.interactive = false
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

			self.moveEnd = function() {
				terminal.interactive = true
			}

			if (self.width()>self.parent().width()){
				self.bind("touchstart", self.start)
				self.bind("touchmove", self.moveMe)
				self.bind("touchend", self.moveEnd)
			}
		}

		// scrollTo element of or position
		scrollTo = function(element, offset){
			var aminateScroll = function(start, stop, step){
				myConsole('..start '+start+' stop '+stop )
				if ((start+step) < stop){
					start += step
					terminal.flickable.contentY = start
					myConsole(terminal.flickable.contentY)
					setTimeout( function(){
						aminateScroll(start, stop, step)
					}, 1)
				}
				else{
					terminal.flickable.contentY = stop
				}
			}
			var elementTop = element.offset().top
			var windowHeight = terminal.flickable.height
			var documentHeight = terminal.flickable.contentHeight
			var stopScroll = documentHeight-windowHeight
			var toY = elementTop-offset
			toY = (toY > stopScroll)?stopScroll:toY
			aminateScroll(terminal.flickable.contentY, toY, 75)
		}

		return { // exports fucntion
			myConsole : myConsole,
			scrollTo : scrollTo,
			checkCompare: checkCompare
		}

	})