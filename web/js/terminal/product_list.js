// product list
define('product_list',
	['jquery', 'ejs', 'library', 'termAPI'], function ($, EJS, library, termAPI) {

	library.myConsole('product_list.js v_2 loaded')


	/**
	 * URL по которому необходимо получать товары
	 */
	var listingGetUrl = $('#categoryData').data('url')

	/**
	 * Количество уже прогруженных товаров
	 */
	var currentLoadedItems = 0

	/**
	 * Происходит ли загрузка в данный момент
	 */
	var loadingItems = false


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

		return zoom
	}


	/**
	 * Прорисовка карточки для товара. Прелоад изображения.
	 *
	 * @author  Aleksandr Zaytsev
	 * @param  {object} data данные для рендеринга
	 */
	var renderItem = function(data){
		var html = new EJS ({url: '/js/terminal/view/listing_itemProduct.ejs'}).render(data)

		$('.bProductListWrap').append(html)
		
		var img = $('#productInList'+data.id+' .bProductListItem__eImg')
		var src = img.attr('src')

		termAPI.checkCompare(data.id)

		img.load(src, function(){
			$(this).fadeIn(300)
		})
	}



	/**
	 * Формирование данных для шаблона
	 *
	 * @author  Aleksandr Zaytsev
	 * @param  {Object} data данные для рендеринга шаблона
	 */
	var preparedData = function(data){
		for (var i = 0; i< data.length; i++){
			var template = {
				id : data[i].id,
				article : data[i].article,
				image : data[i].image,
				name : data[i].name,
				price : library.formatMoney(data[i].price),
				description : data[i].description,
				isBuyable : data[i].isBuyable,
				isInShop : data[i].isInShop,
				isInShowroom : data[i].isInShowroom,
				isInStore : data[i].isInStore,
				hasSupplier : data[i].hasSupplier,
				isInOtherShop : data[i].isInOtherShop
			}
			renderItem(template)
		}
	}

	
	/**
	 * Прогрузка строк с новыми товарами
	 *
	 * @author  Aleksandr Zaytsev
	 * @param {number} lines количество строк, которое необходиом прогрузить
	 */
	var getItems = function(lines){
		if (loadingItems)
			return false

		loadingItems = true

		var resFromServer = function(res){
			loadingItems = false
			if (!res.success)
				return false
			currentLoadedItems += res.products.length
			preparedData(res.products)
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
	 * Обработка скролинга страницы
	 *
	 * @author  Aleksandr Zaytsev
	 * @param {number} y текущий отступ прокрутки
	 * @param {number} windowHeight высота окна
	 * @param {number} documentHeight высота документа
	 * @param {number} offset отступ снизу, после которого срабатывает загрузка новых элементов
	 */
	// var terminalScrolling = function(){
	// 	if (!$('.bProductListItem').length)
	// 		return false

	// 	var y = terminal.flickable.contentY
	// 	var windowHeight = terminal.flickable.height
	// 	var documentHeight = terminal.flickable.contentHeight
	// 	var offset = windowHeight

	// 	if (documentHeight - y - offset <= windowHeight )
	// 		getItems(6 - currentZoom())
	// }
	// terminal.flickable.scrollValueChanged.connect(terminalScrolling)


	/**
	 * Автодоводка скрола под текущий размер сетки
	 *
	 * @author  Aleksandr Zaytsev
	 */
	var rowScrolling = function(){		
		var nowZoom = currentZoom()
		var heights = [180, 247, 377]
		var windowHeight = terminal.flickable.height
		var documentHeight = terminal.flickable.contentHeight

		if ( terminal.flickable.contentY + windowHeight + heights[nowZoom]/2 >= documentHeight ){
			return false
		}

		var toY = Math.round((terminal.flickable.contentY-30)/heights[nowZoom])*heights[nowZoom] + 30

		library.scrollTo(toY, 0, 150)
	}
	terminal.flickable.movementEnded.connect(rowScrolling)
	// terminal.flickable.flickEnded.connect(rowScrolling)


	/**
	 * Изменение масштаба карточек товаров в листинге
	 *
	 * @author  Aleksandr Zaytsev
	 * @param   {jQuery}   el            обертка карточек товара
	 * @param   {array}    startTouches  координаты начала касания
	 * @param   {array}    sizes         массив названия классов размеров
	 * @param   {array}    heights 		 высоты элементов при разных масштабав с учетом margin-bottom и padding-top + padding-bottom
	 * @param   {number}   nowZoom       текущий масштаб
	 */
	var listingZoom = function(el){
		var nowZoom = currentZoom()
		var startTouches = []
		var sizes = ['mSizeLittle','mSizeMid','mSizeBig']
		var heights = [180, 247, 377]


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
			// var y = terminal.flickable.contentY - 30
			// var cols = 4 - nowZoom
			// var nowElCount = (y/heights[nowZoom])*cols

			el.removeClass(sizes[nowZoom])
			if (zoom == 'up'){
				nowZoom = ((nowZoom + 1) > 2) ? 2 : nowZoom + 1
			}
			if (zoom == 'down'){
				nowZoom = ((nowZoom - 1) < 0) ? 0 : nowZoom - 1
			}
			el.addClass(sizes[nowZoom])

			
			// cols = 4 - nowZoom

			// var nowRows = nowElCount/cols | 0
			// var newY = (nowRows*heights[nowZoom]) + 30

			// library.scrollTo(newY, 0 , 10)
			// terminal.flickable.contentY = newY
			rowScrolling()
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
				terminal.interactive = false
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
			terminal.interactive = true
		}

		el.bind('touchstart', moveStart)
		el.bind('touchmove', moveMe)
		el.bind('touchend', moveEnd)
	}
	listingZoom($('.bProductListWrap'));
	

	(initPage = function(){
		/**
		 * Прогрузка товаров при инициализации страницы
		 */
		var initalLinesCount = 12 - currentZoom()
		getItems(initalLinesCount)
	}())

})