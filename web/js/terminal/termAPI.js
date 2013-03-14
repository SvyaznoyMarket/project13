define('termAPI',
	['jquery', 'library'], function ($, library) {

		library.myConsole('termAPI.js loaded')

		$(document).ready(function() {
			// createBreadcrumps()
			$('.jsRedirect').live('click', redirectHandler)
			$('.jsBuyButton').live('click', buyHandler)
			$('.jsWhereBuy').live('click', whereBuyHandler)
			$('.jsCompare').live('click', compareHandler)
		})


		/**
		 * Проверка типа страницы
		 *
		 * @author Aleksandr Zaytsev
		 * @private
		 * @return {string} pageType тип текущей страницы (product|product_listing|category)
		 */
		checkPageType = function() {
			if ( $('.bContent').attr('data-pagetype') == undefined )
				return false

			var pageType = $('.bContent').attr('data-pagetype')
			return pageType
		}


		/**
		 * Вывод хлебных крошек
		 *
		 * @author Aleksandr Zaytsev
		 */
		// createBreadcrumps = function() {
		// 	if ( !$('.bBreadcrumps').length )
		// 		return false
		// 	library.myConsole('go!')
		// 	var aPathList = terminal.screen.path
		// 	var currPage = checkPageType()

		// 	if (!currPage)
		// 		return false

		// 	for (var i in aPathList){
		// 		$('.bBreadcrumps').append('/ <a class="bBreadcrumps__eItem" href="#">'+i+'</a>').bind('click', toScreen(aPathList[i].screenType, aPathList[i].parametrs)) 
		// 	}
		// }


		/**
		 * Переход к экрану
		 *
		 * @author Aleksandr Zaytsev
		 * @private
		 * @param {string} screenType тип экрана на который нужно перейти
		 * @param {object} parametrs параметры открываемого экрана
		 */
		toScreen = function(screenType, parametrs) {
			terminal.screen.push(screenType, parametrs)
		}


		/**
		 * Выбор кнопки у которой будем менять состояние
		 *
		 * @author Aleksandr Zaytsev
		 * @private
		 * @param {number} productId идентификатор продукта
		 * @return {jQuery object} t объект кнопки сравнения
		 */
		createElement = function(productId) {
			var obj = $('#compare_'+productId)
			return obj
		}

		/**
		 * Проверка находится ли товар в сравнении. Установка нужного состояния кнопке
		 *
		 * @author Aleksandr Zaytsev
		 * @public
		 * @requires createElement
		 * @param {number} productId идентификатор продукта
		 * @return {boolean} true если товар находится в сравнении, false если товар не находится в сравнении
		 */
		checkCompare = function(productId) {
			var element = createElement(productId)
			if (terminal.compare.hasProduct(productId)){
				element.html('Убрать из сравнения')
				return true
			}
			else{
				element.html('К сравнению')
				return false
			}
		}

		/**
		 * Обработка кнопки сравнения
		 * 
		 * @author Aleksandr Zaytsev
		 * @private
		 * @requires checkCompare
		 */
		compareHandler = function() {
			var id = $(this).attr('id')
			var productId = id.substr(8, 5)
			if (checkCompare(productId)){
				terminal.compare.removeProduct(productId)
				checkCompare(productId)
			}
			else{
				terminal.compare.addProduct(productId)
				checkCompare(productId)
			}
		}
		terminal.compare.productRemoved.connect(checkCompare)
		terminal.compare.productAdded.connect(checkCompare)


		/**
		 * Обработка перехода на страницу
		 * 
		 * @author Aleksandr Zaytsev
		 * @private
		 * @requires toScreen
		 */
		redirectHandler = function() {
			if ( $(this).attr('data-screentype') == undefined )
				return false

			var screenType = $(this).data('screentype')
			switch (screenType) {
				/** Показ страницы с картинками, на которой можно посмотреть 3d и увеличить картинку */
				case 'media':
					var pId = $(this).data('productid')
					var iIndex = $(this).data('imageindex')
					toScreen(screenType, {productId:pId, currentIndex:iIndex})
					break
				/** Переход на карточку товара */
				case 'product':
					var pId = $(this).data('productid')
					toScreen(screenType, {productId:pId})
					break
				/** Показ попапа для подробной информации по услуге */
				case 'service':
					var pId = $(this).data('productid')
					var sId = $(this).data('serviceid')
					var isBuy = $(this).data('isbuy')
					toScreen(screenType, {serviceId: sId, productId: pId, isBuyable: isBuy})
					break
			}
		}


		/**
		 * Обработка покупки товара\улуги\гарантии
		 * 
		 * @author Aleksandr Zaytsev
		 * @private
		 */
		buyHandler = function() {
			if ( $(this).attr('data-productid') == undefined )
				return false
			
			var productId = $(this).data('productid')

			if ( $(this).attr('data-warrantyid') !== undefined ) {
				// buy warranty
				var warrantyId = $(this).data('warrantyid')
				terminal.cart.setWarranty(productId, warrantyId)
				return false
			}

			if ( $(this).attr('data-serviceid') !== undefined ) {
				// buy service
				var serviceId = $(this).data('serviceid')
				terminal.cart.addService(productId, serviceId)
				return false
			}

			// buy product
			terminal.cart.addProduct(productId)
		}


		/**
		 * Обработка кнопки «Где купить»
		 * 
		 * @author Aleksandr Zaytsev
		 * @private
		 * @requires toScreen
		 */
		whereBuyHandler = function() {
			if ( $(this).attr('data-productid') == undefined )
				return false
			
			var id = $(this).data('productid')
			var screenType = 'other_shops'

			toScreen(screenType, { productId: id })
		}


		//
		// exports function
		//
		return { 
			checkCompare: checkCompare
		}

	})