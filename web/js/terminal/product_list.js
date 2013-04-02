// product list
define('product_list',
	['jquery', 'ejs', 'library', 'termAPI'], function ($, EJS, library, termAPI) {

	library.myConsole('product_list.js loaded')

	library.myConsole('render template from JSON...')
	var data = $('#productList').data('product')
	for (var i = 0; i< data.length; i++){
		var template = {
			id : data[i].id,
			article : data[i].article,
			image : data[i].image,
			name : data[i].name,
			price : library.formatMoney(data[i].price),
			isBuyable : data[i].isBuyable
		}
		var html = new EJS ({url: '/js/terminal/view/listing_itemProduct.ejs'}).render(template)
		$('.bProductListWrap').append(html)
	}
	library.myConsole('render done')


	/**
	 * Изменение масштаба карточек товаров в листинге
	 *
	 * @author  Aleksandr Zaytsev
	 * @param   {jQuery}   el            обертка карточек товара
	 * @param   {array}    startTouches  координаты начала касания
	 * @param   {array}    sizes         массив названия классов размеров
	 * @param   {number}   nowZoom       текущий масштаб
	 */
	listingZoom = function(el){

		var startTouches = []
		var nowZoom = 0
		var sizes = ['mSizeLittle','mSizeMid','mSizeBig']

		/**
		 * Смена масшаба
		 *
		 * @inner
		 * @param  {string} zoom вверх или вниз
		 */
		changeZoom = function(zoom){
			el.removeClass(sizes[nowZoom])
			if (zoom == 'up'){
				nowZoom = ((nowZoom + 1) > 2) ? 2 : nowZoom + 1
				// library.myConsole('увеличиваем ' + nowZoom)
			}
			if (zoom == 'down'){
				nowZoom = ((nowZoom - 1) < 0) ? 0 : nowZoom - 1
				// library.myConsole('уменьшаем ' + nowZoom)
			}
			el.addClass(sizes[nowZoom])
		}

		/**
		 * Обработчик касания пальца
		 *
		 * @inner
		 * @param  {event}  e
		 * @param  {object} nowTouch координаты текущего касания
		 */
		moveStart = function(e){
			e.preventDefault()
			e.stopPropagation()

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
		moveMe = function(e){
			e.preventDefault()
			e.stopPropagation()
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
		moveEnd = function(e){
			e.preventDefault()
			e.stopPropagation()
			
			var orig = e.originalEvent
			
			startTouches = []
			terminal.interactive = true
		}

		el.bind('touchstart', moveStart)
		el.bind('touchmove', moveMe)
		el.bind('touchend', moveEnd)
	}
	listingZoom($('.bProductListWrap'))

})