define('termAPI',
	['jquery', 'library'], function ($, library) {

		library.myConsole('termAPI.js loaded')


		/**
		 * Проверка типа страницы
		 *
		 * @author Aleksandr Zaytsev
		 * @private
		 * @return {string} pageType тип текущей страницы (product|product_listing|category)
		 */
		var checkPageType = function() {
			if ( $('.bContent').attr('data-pagetype') == undefined )
				return false

			var pageType = $('.bContent').attr('data-pagetype')
			return pageType
		}


		/**
		 * Вывод хлебных крошек
		 *
		 * @author Aleksandr Zaytsev
		 * @private
		 */
		var createBreadcrumps = function() {
			if ( !$('.bBreadcrumps__eItem').length )
				return false

			library.myConsole('go!')
			var aPathList = terminal.screen.path
			library.myConsole('type '+typeof(aPathList)+' and '+aPathList)

			// var currPage = checkPageType()

			// if (!currPage)
			// 	return false

			for (var i in aPathList){
				library.myConsole('type path '+typeof(aPathList[i].type)+' and '+aPathList[i].type )
				// $('.bBreadcrumps').append('/ <a class="bBreadcrumps__eItem" href="#">'+i+'</a>').bind('click', toScreen(aPathList[i].screenType, aPathList[i].parametrs)) 
			}
		}


		/**
		 * Переход к экрану
		 *
		 * @author Aleksandr Zaytsev
		 * @private
		 * @param {string} screenType тип экрана на который нужно перейти
		 * @param {object} parametrs параметры открываемого экрана
		 */
		var toScreen = function(screenType, parametrs) {
			terminal.screen.push(screenType, parametrs)
		}


		/**
		 * Проверка находится ли товар в сравнении. Установка нужного состояния кнопке
		 *
		 * @author Aleksandr Zaytsev
		 * @public
		 * @param {number} productId идентификатор продукта
		 * @return {boolean} true если товар находится в сравнении, false если товар не находится в сравнении
		 */
		var checkCompare = function(productId) {
			var element = $('#compare_'+productId)

			if (terminal.compare.hasProduct(productId)){
				element.html('Убрать из&nbsp;сравнения').addClass('mActive')
			}
			else{
				element.html('Сравнить').removeClass('mActive')
			}
		}


		/**
		 * Обработка кнопки сравнения
		 * 
		 * @author Aleksandr Zaytsev
		 * @private
		 * @requires checkCompare
		 */
		var compareHandler = function() {
			var productId = $(this).data('productid')

			if (terminal.compare.hasProduct(productId)){
				terminal.compare.removeProduct(productId)
			}
			else{
				terminal.compare.addProduct(productId)
			}
		}


		/**
		 * События удаления\добавление товара в сравнения на терминале
		 * 
		 * @param  {number} pid идентификатор продукта
		 */
		terminal.compare.productRemoved.connect(function(pid){
			checkCompare(pid)
		})
		terminal.compare.productAdded.connect(function(pid){
			checkCompare(pid)
		})


		/**
		 * Обработка перехода на страницу
		 * 
		 * @author Aleksandr Zaytsev
		 * @private
		 * @requires toScreen
		 */
		var redirectHandler = function() {
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
		var buyHandler = function() {
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
		var whereBuyHandler = function() {
			if ( $(this).attr('data-productid') == undefined )
				return false
			
			var id = $(this).data('productid')
			var screenType = 'other_shops'

			toScreen(screenType, { productId: id })
		}


		$(document).ready(function() {
			// createBreadcrumps()
			$('.jsRedirect').tapEvent(redirectHandler)
			$('.jsBuyButton').tapEvent(buyHandler)
			$('.jsWhereBuy').tapEvent(whereBuyHandler)
			$('.jsCompare').tapEvent(compareHandler)
		})


		//
		// exports function
		//
		return { 
			checkCompare: checkCompare
		}

	})