/**
 * Работа с HISTORY API
 *
 * @requires jQuery, History.js
 *
 * @author	Zaytsev Alexandr
 *
 * @param	{Object}	global	Enter namespace
 */
;(function( ENTER ) {
	console.info('New catalog history module');

	var pageConfig = ENTER.config.pageConfig,
		utils = ENTER.utils,
		catalog = utils.extendApp('ENTER.catalog');
	// end of vars

	catalog.history = {
		/**
		 * Обработка перехода на URL
		 * Если браузер не поддерживает History API происходит обычный переход по ссылке
		 * 
		 * @param	{String}	url			Адрес на который необходимо выполнить переход
		 * @param	{Function}	callback	Функция которая будет вызвана после получения данных от сервера
		 */
		gotoUrl: function gotoUrl( url, callback ) {
			var state = {
				title: 'Enter - это выход!',
				url: url
			};

			catalog.history._callback = callback;

			if ( !catalog.enableHistoryAPI ) {
				document.location.href = url;

				return;
			}

			console.info('link handler. push state new url: '+state.url);
			History.pushState(state, state.title, state.url);

			return;
		}
	};

		/**
		 * Обработчик нажатия на линк завязанный на history api
		 */
	var historyLinkHandler = function historyLinkHandler() {
			var url = $(this).attr('href');

			catalog.history.gotoUrl(url);

			return false;
		},

		/**
		 * Обработка ошибки загрузки данных
		 */
		errorHandler = function errorHandler() {
			utils.blockScreen.unblock();
		},

		/**
		 * Получение данных от сервера
		 * Перенаправление данных в функцию обратного вызова
		 * 
		 * @param	{Object}	res	Полученные данные
		 */
		resHandler = function resHandler( res ) {
			console.info('resHandler');

			if ( typeof res === 'object' && typeof catalog.history._callback === 'function' ) {
				catalog.history._callback(res);
			}
			else {
				console.warn('res isn\'t object or catalog.history._callback isn\'t function');
				console.log(typeof res);
				console.log(typeof catalog.history._callback);
			}

			utils.blockScreen.unblock();
		},

		/**
		 * Обработчик изменения состояния истории в браузере
		 */
		stateChangeHandler = function stateChangeHandler() {
			var state = History.getState(),
				url = state.url;
			// end of vars
			
			console.info('statechange');

			utils.blockScreen.block('Загрузка товаров');

			url = url.addParameterToUrl('ajax', 'true');

			console.log(url);
			console.log(state);

			$.ajax({
				type: 'GET',
				url: url,
				success: resHandler,
				statusCode: {
					500: errorHandler,
					503: errorHandler
				}
			});
		};
	// end of functions


	History.Adapter.bind(window, 'statechange', stateChangeHandler);
	$('body').on('click', '.jsHistoryLink', historyLinkHandler);

}(window.ENTER));	