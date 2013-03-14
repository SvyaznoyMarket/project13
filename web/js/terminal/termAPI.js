define('termAPI',
	['jquery', 'library'], function ($, library) {

		library.myConsole('termAPI.js loaded')

		$(document).ready(function() {
			// createBreadcrumps()
		})
		

		/**
		 * Проверка типа страницы
		 *
		 * @author Aleksandr Zaytsev
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
		createBreadcrumps = function() {
			if ( !$('.bBreadcrumps').length )
				return false
			library.myConsole('go!')
			var aPathList = terminal.screen.path
			var currPage = checkPageType()

			if (!currPage)
				return false

			for (var i in aPathList){
				$('.bBreadcrumps').append('/ <a class="bBreadcrumps__eItem" href="#">'+i+'</a>').bind('click', toScreen(aPathList[i].screenType, aPathList[i].parametrs)) 
			}
		}

		/**
		 * Переход к экрану
		 *
		 * @author Aleksandr Zaytsev
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
		 * @param {number} productId идентификатор продукта
		 * @return {jQuery object} t объект кнопки сравнения
		 */
		createElement = function(productId) {
			var obj = $('#compare_'+productId)
			return obj
		}

		/**
		 * Проверка находится ли товар в сравнении
		 *
		 * @author Aleksandr Zaytsev
		 * @param {number} productId идентификатор продукта
		 * @return {bool} состояние того находится ли товар в сравнении
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
		$('.jsCompare').live('click', compareHandler)
		terminal.compare.productRemoved.connect(checkCompare)
		terminal.compare.productAdded.connect(checkCompare)


		/**
		 * Обработка покупки товара\улуги\гарантии
		 * 
		 * @author Aleksandr Zaytsev
		 */
		buyHandler = function() {
			if ( $(this).attr('data-productid') == undefined )
				return false
			
			var productId = $(this).data('productid')

			if ( $(this).attr('data-warrantyid') !== undefined ) {
				// buy warranty
				var warrantyId = $(this).data('warrantyid')
				library.myConsole('add to cart warranty '+warrantyId+' for '+productId)
				terminal.cart.setWarranty(productId, warrantyId)
				return false
			}

			if ( $(this).attr('data-serviceid') !== undefined ) {
				// buy service
				var serviceId = $(this).data('serviceid')
				library.myConsole('add to cart service '+serviceId+' for '+productId)
				terminal.cart.addService(productId, serviceId)
				return false
			}

			library.myConsole('add to cart product '+productId)
			terminal.cart.addProduct(productId)
		}
		$('.jsBuyButton').live('click', buyHandler)


		/**
		 * Обработка кнопки «Где купить»
		 * 
		 * @author Aleksandr Zaytsev
		 */
		whereBuyHandler = function() {
			if ( $(this).attr('data-productid') == undefined )
				return false
			
			var id = $(this).data('productid')

			library.myConsole('other shops for '+id)
			terminal.screen.push('other_shops', { productId: id })
		}
		$('.jsWhereBuy').live('click', whereBuyHandler)


		//
		// exports function
		//
		return { 
			checkCompare: checkCompare
		}

	})