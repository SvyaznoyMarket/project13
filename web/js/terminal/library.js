define('library',
	['jquery'], function ($) {


		/**
		 * Отлавливание ошибок
		 * 
		 * @param  {string} msg  сообщение об ошибке
		 * @param  {string} url  
		 * @param  {number} line строка
		 * @return {boolean}
		 */
		window.onerror = function(msg, url, line) {
			terminal.log.write(line + ' - ' +msg)
			return true
		}

		/**
		 * Вывод в отладочную консоль
		 *
		 * @author Aleksandr Zaytsev
		 * @public
		 * @param {string} text текст который нужно вывести
		 */
		var myConsole = function(text){
			terminal.log.write(text)

			var c = $('#console')

			if (!develop)
				return false

			if (!c.length)
				return false
			
			/**
			 * Позиция консоли при скролинге
			 */
			var consolePos = function(){
				var y = terminal.flickable.contentY
				c.css('top', y)
			}

			c.prepend('<p>'+text+'</p>')
			console.log(text)
			c.show()
			terminal.flickable.scrollValueChanged.connect(consolePos)
		}		
		myConsole('user agent '+navigator.userAgent)
		myConsole('library.js v4 loaded')



		/**
		 * Настройки AJAX
		 */
		$.ajaxSetup({
			async:true,
			cache:false,
			timeout: 10000,
			error: function (jqXHR, textStatus, errorThrown){
				terminal.log.write('AJAX error '+textStatus+' '+errorThrown+' '+jqXHR.status)
			}
		})


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
		 * Плагин имитации тач тапа
		 *
		 * @param {function} callback
		 */
		$.fn.tapEvent = function(callback) {
			var touchStartTime = null
			var touchEndTime = null
			var startX = null
			var startY = null
			var stopX = null
			var stopY = null

			var doTouchLogic = function(el) {
				var duration = touchEndTime - touchStartTime
				var deltaX =  Math.abs(startX - stopX)
				var deltaY = Math.abs(startY - stopY)

				if ((duration <= 100) && (duration > 0) && (deltaY < 10) && (deltaX < 10)) {
					callback.apply(el)
					return true
				}
				return false
			}

			this.live('mousedown', function(e) {
				var d = new Date()
				var orig = e.originalEvent

				touchStartTime = d.getTime()
				// startX = orig.changedTouches[0].pageX
				startX = e.pageX
				// startY = orig.changedTouches[0].pageY
				startY = e.pageY
			})

			this.live('mouseup', function(e) {
				var d = new Date()
				var self = $(this)
				var orig = e.originalEvent

				touchEndTime= d.getTime()
				// stopX = orig.changedTouches[0].pageX
				stopX = e.pageX
				// stopY = orig.changedTouches[0].pageY
				stopY = e.pageY
				if (doTouchLogic(self))
					e.preventDefault()
					e.stopPropagation()
			})
		}


		/**
		 * Плагин горизонатльного тач-скроллинга
		 *
		 * @public
		 * @param {string} [direction="horiz"] направление
		 * @author Aleksandr Zaytsev
		 */
		$.fn.draggable = function(direction) {

			var self = this

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
				var orig = e.originalEvent
				var stopTime = new Date().getTime()
				var deltaTime = self.startTime - stopTime
				var touch = (dir) ? orig.changedTouches[0].pageX : orig.changedTouches[0].pageY
				var a = (self.start.x - touch) / deltaTime
				self.autoMove(a)
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
			if (self.width()>self.parent().width()){
				self.bind("touchstart", self.moveStart)
				self.bind("touchmove", self.moveMe)
				self.bind("touchend", self.moveEnd)
			}

			return this
		}



		/**
		 * Горизонатльный слайдер
		 *
		 * @author Aleksandr Zaytsev
		 * @public
		 */
		$.fn.bSlider = function() {

			var self = this

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
		var scrollTo = function(element, offset, time){
			var aminateScrollUp = function(start, stop, step){
				if ( (start+step) > stop ){
					start += step
					terminal.flickable.contentY = start
					setTimeout( function(){
						aminateScrollUp(start, stop, step)
					}, 1)
				}
				else {
					terminal.flickable.contentY = stop
					terminal.interactive = true
				}
			}
			var aminateScrollDown = function(start, stop, step){
				if ((start+step) < stop){
					start += step
					terminal.flickable.contentY = start
					setTimeout( function(){
						aminateScrollDown(start, stop, step)
					}, 1)
				}
				else {
					terminal.flickable.contentY = stop
					terminal.interactive = true
				}
			}

			terminal.interactive = false

			var offset = (offset)?offset:0
			var time = (time)?time:300
			var nowY = terminal.flickable.contentY
			var elementTop = (typeof(element) == 'number') ? element : element.offset().top
			var windowHeight = terminal.flickable.height
			var documentHeight = terminal.flickable.contentHeight
			var stopScroll = documentHeight-windowHeight
			var toY = elementTop-offset

			toY = (toY > stopScroll)?stopScroll:toY

			var step = (toY - nowY)/(time*0.1)

			if (nowY < toY){
				aminateScrollDown(nowY, toY, step)
			}
			else{
				aminateScrollUp(nowY, toY, step)
			}
		}


		/**
		 * Разбиение числа по разрядам
		 *
		 * @public
		 * @param  {number|string}
		 * @return {string} отформатированное число
		 */
		var formatMoney = function(num){
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