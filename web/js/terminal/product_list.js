// product list
define('product_list',
	['jquery', 'ejs', 'library', 'termAPI'], function ($, EJS, library, termAPI) {

	library.myConsole('product_list.js v_2 loaded')

	terminal.interactive = false


	/**
	 * Тип загружаемой страницы
	 * @type {String}
	 */
	var pageType = $('article').data('pagetype')

	/**
	 * URL по которому необходимо получать товары
	 * @type {String}
	 */
	var listingGetUrl = $('#categoryData').data('url')

	/**
	 * Количество загруженных товаров
	 * @type {Number}
	 */
	var currentLoadedItems = 0

	/**
	 * Количество отрисованных элементов
	 * @type {Number}
	 */
	var currentRenderedItem = 0

	/**
	 * высоты элементов при разных масштах с учетом margin-bottom и padding-top + padding-bottom
	 * @type {Array}
	 */
	var heights = (pageType === 'product_model_list') ? [195.5] : [180, 247, 377]

	/**
	 * Данные продуктов
	 * @type {Array}
	 */
	var productData = []

	/**
	 * Количество проскроленных элементов
	 * @type {Number}
	 */
	var scrolingElements = 0

	/**
	 * Тригер изменения зума
	 * @type {Boolean}
	 */
	var changingZoom = false


	/**
	 * Определение текущего масштаба
	 *
	 * @author  Aleksandr Zaytsev
	 * @return {number} текущий масштаб
	 */
	var currentZoom = function(){
		var zoom = null
		if ($('.bProductListWrap').hasClass('mSizeLittle')){
			zoom = 0
		}
		else if ($('.bProductListWrap').hasClass('mSizeMid')){
			zoom = 1
		}
		else if ($('.bProductListWrap').hasClass('mSizeBig')){
			zoom = 2
		}
		else if ($('.bProductListWrap').hasClass('mModelList')){
			// zoom = 3
			zoom = 0
		}

		return zoom
	}


	/**
	 * Прорисовка карточки для товара. Прелоад изображения.
	 *
	 * @author  Aleksandr Zaytsev
	 * @param  {object} data данные для рендеринга
	 */
	var renderItem = function(data){
		if (pageType === 'product_model_list'){
			var html = new EJS ({url: '/js/terminal/view/listing_itemLine.ejs'}).render(data)
		}
		else{
			var html = new EJS ({url: '/js/terminal/view/listing_itemProduct.ejs'}).render(data)
		}

		$('.bProductListWrap').append(html)
		
		var img = $('#productInList'+data.id+' .bProductListItem__eImg')
		var src = img.attr('src')

		termAPI.checkCompare(data.id)

		img.load(src, function(){
			$(this).fadeIn(300).parent().removeClass('mLoading')
		})
	}



	/**
	 * Формирование данных для шаблона
	 *
	 * @author  Aleksandr Zaytsev
	 * @param  {Object} data данные для рендеринга шаблона
	 */
	var preparedData = function(start, end){
		currentRenderedItem = currentRenderedItem+(end-start)
		for (var i = start; i< end; i++){
			if (pageType === 'product_model_list'){
				var template = {
					id : productData[i].line.id,
					image : productData[i].image,
					name : productData[i].line.name,
					price : library.formatMoney(productData[i].price),
					kitCount: productData[i].line.kitQuantity,
					productCount: productData[i].line.productQuantity,
					productid : productData[i].id,
				}
			}
			else{
				var template = {
					id : productData[i].id,
					article : productData[i].article,
					image : productData[i].image,
					name : productData[i].name,
					price : library.formatMoney(productData[i].price),
					description : productData[i].description,
					isBuyable : productData[i].isBuyable,
					isInShop : productData[i].isInShop,
					isInShowroom : productData[i].isInShowroom,
					isInStore : productData[i].isInStore,
					hasSupplier : productData[i].hasSupplier,
					isInOtherShop : productData[i].isInOtherShop
				}
			}
			renderItem(template)
			$('.bListing').removeClass('mLoading')
		}
	}

	
	/**
	 * Прогрузка строк с новыми товарами
	 *
	 * @author  Aleksandr Zaytsev
	 * @param {Number} lines количество строк, которое необходиом прогрузить
	 */
	var getItems = function(lines){

		var resFromServer = function(res){
			if (!res.success)
				return false

			if (res.products.length == 0)
				return false
			
			currentLoadedItems += res.products.length
			productData = productData.concat(res.products)

			if (currentRenderedItem == 0){
				preparedData(currentRenderedItem, currentRenderedItem+res.products.length)
			}
			
			getItems(5 - currentZoom())
		}

		var itemsLimit = (4 - currentZoom())*lines
		var data = {
			limit : itemsLimit,
			offset : currentLoadedItems
		}

		$.ajax({
			type: "POST",
			url: listingGetUrl,
			data: data,
			success: resFromServer
		})
	}

	
	/**
	 * Свайп-скролинг товаров
	 *
	 * @author  Aleksandr Zaytsev
	 * @param {jQuery object} el Селектор элемента скролинга
	 */
	var listingSwipe = function(el){

		/**
		 * Кордината начала движения
		 * @type {Number}
		 */
		var startY = null

		/**
		 * Первоначальный отступ
		 * @type {Number}
		 */
		var startOffset = null

		/**
		 * Новое значение отступа
		 * @type {Number}
		 */
		var newOffset = null

		/**
		 * Время анимации
		 * @type {Number}
		 */
		var time = 100

		/**
		 * Флаг происходящей анимации
		 * @type {Boolean}
		 */
		var animated = false

		/**
		 * Анимация доводки скролинга
		 *
		 * @author  Aleksandr Zaytsev
		 * @param  {number} start Координата начала скролинга
		 * @param  {number} stop  Координата конеца скролинга
		 * @param  {number} step  Шаг анимации
		 */
		var aminateScroll = function(start, stop, step){
			animated = true
			if (start > stop){
				if (start - step > stop) {
					start -= step
					setTimeout( function(){
						el.css('top',start)
						aminateScroll(start, stop, step)
					}, 1)
				}
				else {
					animated = false
					el.css('top',stop)
					setTimeout( function(){
						preparedData(currentRenderedItem, currentRenderedItem + Math.pow((4-currentZoom()),2) )
					}, 10)
					// var moreLoad = (pageType === 'product_model_list') ? currentRenderedItem + 4 : currentRenderedItem + Math.pow((4-currentZoom()),2)
					// preparedData(currentRenderedItem, moreLoad )
				}

			}
			else{
				if (start + step < stop) {
					start += step
					setTimeout( function(){
						el.css('top',start)
						aminateScroll(start, stop, step)
					}, 1)
				}
				else {
					animated = false
					el.css('top',stop)
				}
			}
		}

		/**
		 * Хандлер начала скролинга
		 * 
		 * @param  {event} e
		 */
		var moveStart = function(e){
			e.preventDefault()

			var orig = e.originalEvent

			startY = orig.changedTouches[0].pageY
			startOffset = parseInt(el.css('top'))
		}

		/**
		 * Хандлер движения
		 * 
		 * @param  {event} e
		 */
		var moveMe = function(e){
			e.preventDefault()
		
			var orig = e.originalEvent
			var touch = orig.changedTouches[0].pageY
			var len = orig.changedTouches.length

			if (animated||changingZoom||(len > 1))
				return false
			
			newOffset = touch - startY + startOffset
			el.css('top',newOffset)
		}

		/**
		 * Хандлер окончания движения
		 * 
		 * @param  {event} e
		 */
		var moveEnd = function(e){
			e.preventDefault()
			
			if (animated||changingZoom) 
				return false

			var orig = e.originalEvent
			var stopY = orig.changedTouches[0].pageY
			var diff = Math.abs(startY-stopY)
			var zoom = currentZoom()
			var listingWindowH = heights[zoom]*(4-zoom)
			var step = (Math.abs(startOffset - newOffset))/(time*0.08)
			var toY = null

			if ( diff >= listingWindowH/5 ){
				toY = (startOffset > newOffset) ? startOffset - listingWindowH : startOffset + listingWindowH
				toY = (toY > 0) ? 0 : toY
				toY = (Math.abs(toY) >= el.height()) ? startOffset : toY
			}
			else{
				toY = startOffset
			}

			aminateScroll(newOffset, toY, step)

			// scrolingElements = (pageType === 'product_model_list') ? scrolingElements = Math.abs((toY/listingWindowH)*(4-zoom)*4) : Math.abs((toY/listingWindowH)*(4-zoom)*(4-zoom))
			scrolingElements = Math.abs((toY/listingWindowH)*(4-zoom)*(4-zoom))
			// library.myConsole('scEl '+scrolingElements)
		}

		el.bind("touchstart", moveStart)
		el.bind("touchmove", moveMe)
		el.bind("touchend", moveEnd)
	};


	/**
	 * Изменение масштаба карточек товаров в листинге
	 *
	 * @author  Aleksandr Zaytsev
	 * @param   {jQuery}   el   обертка карточек товара
	 */
	var listingZoom = function(el){

		/**
		 * Текущий масштаб листинга
		 * @type {Number}
		 */
		var nowZoom = currentZoom()

		/**
		 * Массив текущих пальцев на экране
		 * @type {Array}
		 */
		var startTouches = []

		/**
		 * Массив названий классов масштабов для обертки
		 * @type {Array}
		 */
		var sizes = ['mSizeLittle','mSizeMid','mSizeBig']


		/**
		 * Смена масшаба
		 *
		 * @inner
		 * @param  {string} zoom вверх или вниз
		 * @param  {number} y отступ до верха документа
		 * @param  {number} cols количество колонок при текущем зуме
		 * @param  {number} nowelCount количество элементов, которые мы уже проскролили
		 */
		var changeZoom = function(zoom){

			changingZoom = true

			el.removeClass(sizes[nowZoom])
			if (zoom == 'up'){
				nowZoom = ((nowZoom + 1) > 2) ? 2 : nowZoom + 1
			}
			if (zoom == 'down'){
				nowZoom = ((nowZoom - 1) < 0) ? 0 : nowZoom - 1
			}
			el.addClass(sizes[nowZoom])
			var stringCount = Math.round(scrolingElements/(4-nowZoom))
			library.myConsole('strC '+stringCount)
			var newOffset = -stringCount*heights[nowZoom]
			library.myConsole('newOf '+newOffset)
			el.animate({'top':newOffset},50, function(){
				changingZoom = false
			})
		}

		/**
		 * Обработчик касания пальца
		 *
		 * @inner
		 * @param  {event}  e
		 * @param  {object} nowTouch координаты текущего касания
		 */
		var moveStart = function(e){
			e.preventDefault()

			var orig = e.originalEvent
			var nowTouch = {
				x:orig.changedTouches[0].pageX,
				y:orig.changedTouches[0].pageY
			}
			
			startTouches.push(nowTouch)

		}

		/**
		 * Обработчик движения
		 *
		 * @inner
		 * @param  {event}  e
		 * @param  {number} len количество пальцев участвующих в движении
		 * @param  {array}  moveTouches массив координат пальцев участвующих в движении
		 * @param  {object} moveTouch координаты пальца участвующего в движении
		 * @param  {number} startDelta разница координат начала движения
		 * @param  {number} nowDelta разница координат текущих положений пальцев
		 */
		var moveMe = function(e){
			e.preventDefault()
			var orig = e.originalEvent
			var len = orig.changedTouches.length
			
			if (startTouches.length > 1){
				var moveTouches = []
				for (var i = 0; i< len; i++){
					var moveTouch = {
						x:orig.changedTouches[i].pageX,
						y:orig.changedTouches[i].pageY
					}
					moveTouches.push(moveTouch)
				}

				var startDelta = Math.abs(startTouches[0].x - startTouches[1].x) + Math.abs(startTouches[0].y - startTouches[1].y)
				var nowDelta = Math.abs(moveTouches[0].x - moveTouches[1].x) + Math.abs(moveTouches[0].y - moveTouches[1].y)

				if ( (startDelta > nowDelta) && (Math.abs(startDelta-nowDelta) > 120) ){
					startTouches = moveTouches.slice()
					changeZoom('down')
				}
				else if ( (startDelta < nowDelta) && (Math.abs(startDelta-nowDelta) > 120) ){
					startTouches = moveTouches.slice()
					changeZoom('up')
				}
			}
		}

		/**
		 * Обработчик завершения движения
		 *
		 * @inner
		 * @param  {event} e
		 */
		var moveEnd = function(e){
			e.preventDefault()
			
			var orig = e.originalEvent
			
			startTouches = []
		}

		el.bind('touchstart', moveStart)
		el.bind('touchmove', moveMe)
		el.bind('touchend', moveEnd)
	};

	(initPage = function(){
		/**
		 * Прогрузка товаров при инициализации страницы
		 */
		var initalLinesCount = 10 - currentZoom()
		getItems(initalLinesCount)

		/**
		 * Инициалзация зумирования
		 */
		if (pageType !== 'product_model_list'){
			listingZoom($('.bProductListWrap'))
		}

		/**
		 * Инициализация прокрутки свайпами
		 */
		listingSwipe($('.bProductListWrap'))
	}())

})