define('library',
	['jquery'], function ($) {

		/**
		 * Вывод в отладочную консоль
		 *
		 * @author Aleksandr Zaytsev
		 * @param {string} text текст который нужно вывести
		 */
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

		/**
		 * Плагин горизонатльного тач-скроллинга
		 *
		 * @author Aleksandr Zaytsev
		 */
		$.fn.draggable = function() {
			self = this

			self.start = null
			self.stop = null

			self.startTime = 0

			/**
			 * Обработчик для первого касания пальца
			 *
			 * @inner
			 * @param {event} e
			 */
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

			/**
			 * Обработчик для передвижения пальца
			 *
			 * @inner
			 * @param {event} e
			 */
			self.moveMe = function(e) {
				terminal.interactive = false
				e.preventDefault()
				var orig = e.originalEvent
				var newLeft = orig.changedTouches[0].pageX - self.start.x + self.start.left
				self.checkMove( newLeft, orig.changedTouches[0].pageX )
			}

			/**
			 * Проверка возможности совершать движение на заданное значение
			 *
			 * @inner
			 * @param {number} newLeft новое значение позиции элемента
			 * @param {number} touchEvent текущее положение пальца
			 */
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

			/**
			 * Обработчик для завершения движения пальца (палец отпущен)
			 *
			 * @inner
			 * @param {event} e
			 */
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

			/**
			 * Акселерация после скролинга
			 *
			 * @inner
			 * @param {number} a вычесленное значение акселерации
			 */
			self.autoMove = function(a){
				var left = parseInt(self.css('left')) + (a*15)
				var acceleration = (a>0) ? (a - 0.05) : (a + 0.05)
				if ( self.checkMove(left) && ( Math.abs(acceleration)>0.05 ) ){
					setTimeout( function(){
						self.autoMove(acceleration)
					}, 1)
				}
			}

			/**
			 * Вешаем события, только если родительский элемент больше
			 */
			if (self.width()>self.parent().width()){
				self.bind("touchstart", self.moveStart)
				self.bind("touchmove", self.moveMe)
				self.bind("touchend", self.moveEnd)
			}
		}

		/**
		 * Горизонатльный слайдер
		 *
		 * @author Aleksandr Zaytsev
		 */
		$.fn.horizSlider = function() {
			self = this

			self.nowLeft = 0

			sliderWrap = this.find('.bSlider_eWrap')
			arrowL = this.find('.bSlider_eArrow.mLeft')
			arrowR = this.find('.bSlider_eArrow.mRight')

			/**
			 * Просчет нового значения отступа
			 *
			 * @inner
			 */
			self.calcMove = function() {
				myConsole('click!')
				var step = 200
				var direction = ( $(this).hasClass('mLeft') ) ? 1 : -1
				myConsole('1! dir '+direction)
				// self.nowLeft = sliderWrap.css('left')
				myConsole('2! '+self.nowLeft)
				var newLeft = self.nowLeft + direction*step
				myConsole('3! '+newLeft)
				move(newLeft)
			}

			/**
			 * Анимация перемещения и проверка возможности перемещения на заданное значение.
			 * Скрытие\отображение стрелок управления
			 *
			 * @inner
			 * @param {number} newLeft новое значение отступа
			 */
			var move = function(left) {
				myConsole('move! '+left)
				// self.sliderWrap.animate({'left': left})
			}

			/**
			 * Вешаем события, только если родительский элемент больше
			 */
			myConsole('wrap '+sliderWrap.width())
			myConsole('self '+self.width())
			if ( sliderWrap.width() > this.width() ) {
				arrowR.show().bind('click', self.calcMove)
				arrowL.bind('click', self.calcMove)
			}
		}


		/**
		 * Скролинг до элемента
		 *
		 * @author Aleksandr Zaytsev
		 * @param {jQuery object} element объект до которого необходимо скролить
		 * @param {number} [offset="0"] отступ от элемента
		 * @param {number} [time="300"] время за которое необходимо совершить анимацию
		 */
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
			var time = (time)?time:300
			var elementTop = element.offset().top
			var windowHeight = terminal.flickable.height
			var documentHeight = terminal.flickable.contentHeight
			var stopScroll = documentHeight-windowHeight
			var toY = elementTop-offset
			toY = (toY > stopScroll)?stopScroll:toY
			var step = (toY - terminal.flickable.contentY)/(time*0.1)
			aminateScroll(terminal.flickable.contentY, toY, step)
		}

		// exports function
		return {
			myConsole : myConsole,
			scrollTo : scrollTo
		}

	})