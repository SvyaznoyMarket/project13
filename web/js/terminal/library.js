define('library',
	['jquery'], function ($) {

		// console function
		myConsole = function(text){
			$('#console').prepend('<p>'+text+'</p>')
			console.log(text)
		}

		myConsole('library.js loaded')

		// custom select
		if ($('.bCustomSelect').length){
			var selectOpen = false
			$('.bCustomSelect').bind('click', function(e){
				e.stopPropagation()
				e.preventDefault()
				if ($(this).hasClass('mActive')){
					$('.bCustomSelect').removeClass('mActive')
					selectOpen = false
				}
				else{
					$('.bCustomSelect').removeClass('mActive')
					$(this).addClass('mActive')
					selectOpen = true
				}
			})

			$('.bWrap').bind('click', function(event){
				if (selectOpen){
					event.preventDefault()
					$('.bCustomSelect').removeClass('mActive')
					selectOpen = false
				}
			})
		}

		// drug plugin
		$.fn.draggable = function() {
			self = this

			self.start = null
			self.stop = null

			self.startTime = 0

			self.moveStart = function(e) {
				var orig = e.originalEvent
				self.start = {
					x: orig.changedTouches[0].pageX,
					left: parseInt(self.css('left'))
				}
				self.stop = {
					start: 0,
					end: self.width() - self.parent().width()
				}
				self.startTime = new Date().getTime()
			}

			self.moveMe = function(e) {
				terminal.interactive = false
				e.preventDefault()
				var orig = e.originalEvent
				var newLeft = orig.changedTouches[0].pageX - self.start.x + self.start.left
				self.checkMove( newLeft, orig.changedTouches[0].pageX )
			}

			self.checkMove = function( newLeft, touchEvent ){
				if ( newLeft > self.stop.start ){
					self.css({left: 0})
					self.start.left = 0
					self.start.x = (touchEvent) ? touchEvent : 0
					return false
				}
				else if ( newLeft < -self.stop.end){
					self.css({left: -self.stop.end})
					self.start.left = -self.stop.end
					self.start.x = (touchEvent) ? touchEvent : 0
					return false
				}
				else{
					self.css({left: newLeft})
					return true
				}
			}

			self.moveEnd = function(e) {
				terminal.interactive = true
				e.preventDefault()
				e.stopPropagation()
				var orig = e.originalEvent
				var stopTime = new Date().getTime()
				var deltaTime = self.startTime - stopTime
				var a = (self.start.x - orig.changedTouches[0].pageX) / deltaTime
				self.autoMove(a)
				return false
			}

			self.autoMove = function(a){
				var left = parseInt(self.css('left')) + (a*15)
				var acceleration = (a>0) ? (a - 0.05) : (a + 0.05)
				if ( self.checkMove(left) && ( Math.abs(acceleration)>0.05 ) ){
					setTimeout( function(){
						self.autoMove(acceleration)
					}, 1)
				}
			}

			if (self.width()>self.parent().width()){
				self.bind("touchstart", self.moveStart)
				self.bind("touchmove", self.moveMe)
				self.bind("touchend", self.moveEnd)
			}
		}

		// scrollTo element of or position
		scrollTo = function(element, offset, time){
			var aminateScroll = function(start, stop, step){
				if ((start+step) < stop){
					start += step
					terminal.flickable.contentY = start
					setTimeout( function(){
						aminateScroll(start, stop, step)
					}, 1)
				}
				else{
					terminal.flickable.contentY = stop
				}
			}
			var offset = (offset)?offset:0
			var time = (time)?time:10
			var elementTop = element.offset().top
			var windowHeight = terminal.flickable.height
			var documentHeight = terminal.flickable.contentHeight
			var stopScroll = documentHeight-windowHeight
			var toY = elementTop-offset
			toY = (toY > stopScroll)?stopScroll:toY
			var step = (toY - terminal.flickable.contentY)/(time*0.1)
			aminateScroll(terminal.flickable.contentY, toY, step)
		}

		// exports fucntion
		return {
			myConsole : myConsole,
			scrollTo : scrollTo
		}

	})