define('library',
	['jquery'], function ($) {

		/**
		 * Вывод в отладочную консоль
		 *
		 * @author Aleksandr Zaytsev
		 * @public
		 * @param {string} text текст который нужно вывести
		 */
		myConsole = function(text){
			if ($('#console').length)
				$('#console').prepend('<p>'+text+'</p>')
			console.log(text)
		}


		/**
		 * Позиция консоли при скролинге
		 * @return {[type]} [description]
		 */
		consolePos = function(){
			var y = terminal.flickable.contentY
			$('#console').css('top', y)
		}
		terminal.flickable.scrollValueChanged.connect(consolePos)
		

		myConsole('library.js v2 loaded')


		/**
		 * Обработчик кастомных дроп-даунов
		 *
		 * @author Aleksandr Zaytsev
		 * @private
		 */
		var selectOpen = false
		$('.bCustomSelect').live('click', function(e){
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
		$('.bWrap').live('click', function(event){
			if (selectOpen){
				event.preventDefault()
				$('.bCustomSelect').removeClass('mActive')
				selectOpen = false
			}
		})



		/**
		 * Плагин горизонатльного тач-скроллинга
		 *
		 * @public
		 * @param {string} [direction="horiz"] направление
		 * @author Aleksandr Zaytsev
		 */
		$.fn.draggable = function(direction) {
			self = this

			self.start = null
			self.stop = null

			self.startTime = 0

			/*
			 * Направление скролинга
			 * 0 - вертикальное 
			 * 1 - горизонтальное
			 */
			var dir = (direction=='vert') ? 0 : 1

			/**
			 * Обработчик для первого касания пальца
			 *
			 * @inner
			 * @param {event} e
			 */
			self.moveStart = function(e) {
				e.preventDefault()
				e.stopPropagation()
				var orig = e.originalEvent
				self.start = {
					x: (dir) ? orig.changedTouches[0].pageX : orig.changedTouches[0].pageY,
					offset: (dir) ? parseInt(self.css('left')) : parseInt(self.css('top'))
				}
				self.stop = {
					start: 0,
					end: (dir) ? self.width() - self.parent().width() : self.height() - self.parent().height()
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
				e.stopPropagation()
				var orig = e.originalEvent
				var touch = (dir) ? orig.changedTouches[0].pageX : orig.changedTouches[0].pageY
				var newOffset = touch - self.start.x + self.start.offset
				self.checkMove( newOffset, touch )
				self.trigger('sliderMoved')
			}

			/**
			 * Проверка возможности совершать движение на заданное значение
			 *
			 * @inner
			 * @param {number} newOffset новое значение позиции элемента
			 * @param {number} touchEvent текущее положение пальца
			 */
			self.checkMove = function( newOffset, touchEvent ){
				if ( newOffset > self.stop.start ){
					if (dir){
						self.css({left: 0})
					}
					else{
						self.css({top: 0})
					}
					self.start.offset = 0
					self.start.x = (touchEvent) ? touchEvent : 0
					self.trigger('sliderMoved')
					return false
				}
				else if ( newOffset < -self.stop.end){
					if (dir){
						self.css({left: -self.stop.end})
					}
					else{
						self.css({top: -self.stop.end})
					}
					self.start.offset = -self.stop.end
					self.start.x = (touchEvent) ? touchEvent : 0
					self.trigger('sliderMoved')
					return false
				}
				else{
					if (dir){
						self.css({left: newOffset})
					}
					else{
						self.css({top: newOffset})
					}
					self.trigger('sliderMoved')
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
				var touch = (dir) ? orig.changedTouches[0].pageX : orig.changedTouches[0].pageY
				var a = (self.start.x - touch) / deltaTime
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
				var nowOffset = (dir) ? parseInt(self.css('left')) : parseInt(self.css('top'))
				var offset = nowOffset + (a*15) 
				var acceleration = (a>0) ? (a - 0.05) : (a + 0.05)
				if ( self.checkMove(offset) && ( Math.abs(acceleration)>0.05 ) ){
					setTimeout( function(){
						self.autoMove(acceleration)
					}, 1)
				}
			}

			/**
			 * Вешаем события, только если родительский элемент больше
			 */
			// if (self.width()>self.parent().width()){
				self.bind("touchstart", self.moveStart)
				self.bind("touchmove", self.moveMe)
				self.bind("touchend", self.moveEnd)
			// }

			return this
		}



		/**
		 * Горизонатльный слайдер
		 *
		 * @author Aleksandr Zaytsev
		 * @public
		 */
		$.fn.bSlider = function() {

			self = this

			var wrapper = self.find('.bSlider_eWrap')
			var lArrow = self.find('.bSlider_eArrow.mLeft')
			var rArrow = self.find('.bSlider_eArrow.mRight')

			var stop = {
				start: 0,
				end: self.width() - wrapper.width()
			}

			/**
			 * Анимация перемещения и проверка возможности перемещения на заданное значение.
			 *
			 * @inner
			 * @param {number} left новое значение отступа
			 */
			var animSlideTo = function(left) {
				if (left >= stop.start){
					wrapper.animate({'left':stop.start},300, function(){
						checkArrow()
					})
				}
				else if (left <= stop.end){
					wrapper.animate({'left':stop.end},300, function(){
						checkArrow()
					})
				}
				else{
					wrapper.animate({'left':left},300, function(){
						checkArrow()
					})
				}	
			}

			/**
			 * Просчет нового значения отступа
			 *
			 * @inner
			 */
			var calcMove = function() {
				var step = 700
				var direction = ( $(this).hasClass('mLeft') ) ? 1 : -1
				var nowLeft = parseInt(wrapper.css('left'))
				var newLeft = nowLeft + (direction * step)
				animSlideTo(newLeft)
			}

			/**
			 * Скрытие\отображение стрелок управления
			 *
			 * @inner
			 */
			var checkArrow = function() {
				var nowLeft = parseInt(wrapper.css('left'))
				if (nowLeft >= stop.start){
					lArrow.hide()
				}
				else{
					lArrow.show()
				}
				if (nowLeft <= stop.end){
					rArrow.hide()
				}
				else{
					rArrow.show()
				}
				return false
			}

			/**
			 * Подписываемся на событие перемещения обертки
			 */
			wrapper.bind('sliderMoved', checkArrow)

			/**
			 * Вешаем события, только если родительский элемент больше
			 */
			if (wrapper.width() > self.width()) {
				rArrow.show().bind('click', calcMove)
				lArrow.bind('click', calcMove)
			}
			
			return this
		}


		/**
		 * Скролинг до элемента
		 *
		 * @author Aleksandr Zaytsev
		 * @public
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


		/**
		 * Разбиение числа по разрядам
		 *
		 * @public
		 * @param  {number|string}
		 * @return {string} отформатированное число
		 */
		formatMoney = function(num){
			var str = num+' '
			return str.replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ')
		}


		//
		// exports function
		//
		return {
			myConsole : myConsole,
			scrollTo : scrollTo,
			formatMoney: formatMoney
		}

	})