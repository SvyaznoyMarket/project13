/**
 * Работа с HISTORY API
 *
 * @requires	jQuery, History.js, ENTER.utils, ENTER.config, ENTER.catalog
 *
 * @author		Zaytsev Alexandr
 *
 * @param		{Object}	global	Enter namespace
 */
;(function( ENTER ) {
	var pageConfig = ENTER.config.pageConfig,
		utils = ENTER.utils,
		catalog = utils.extendApp('ENTER.catalog');
	// end of vars

	console.info('New catalog history module');

	catalog.history = {
		/**
		 * Флаг обновления данных с сервера
		 * true - только обновить URL, false - запросить новые данные с сервера
		 * 
		 * @type {Boolean}
		 */
		_onlychange: false,

		/**
		 * Функция обратного вызова после получения данных с сервера
		 * 
		 * @type	{Function}
		 */
		_callback: null,


		/**
		 * Обработка перехода на URL
		 * Если браузер не поддерживает History API происходит обычный переход по ссылке
		 * 
		 * @param	{String}	url			Адрес на который необходимо выполнить переход
		 * @param	{Function}	callback	Функция которая будет вызвана после получения данных от сервера
		 * @param	{Boolean}	onlychange	Показывает что необходимо только изменить URL и не запрашивать данные
		 */
		gotoUrl: function gotoUrl( url, callback, onlychange ) {
			var state = {
					title: document.title,
					url: url
				};
			// end of vars

			catalog.history._callback = callback;
			catalog.history._onlychange = ( onlychange ) ? true : false;

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
		 * Обработка ошибки загрузки данных
		 */
	var errorHandler = function errorHandler() {
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
				catalog.history._callback = null;
			}
			else {
				console.warn('res isn\'t object or catalog.history._callback isn\'t function');
				console.log(typeof res);
				console.log(typeof catalog.history._callback);
			}

			utils.blockScreen.unblock();
		},

		/**
		 * Запросить новые данные с сервера по url
		 * 
		 * @param	{String}	url
		 */
		getDataFromServer = function getDataFromServer( url ) {
			console.info('getDataFromServer ' + url);

			utils.blockScreen.block('Загрузка товаров');

			$.ajax({
				type: 'GET',
				url: url,
				success: resHandler,
				statusCode: {
					500: errorHandler,
					503: errorHandler
				}
			});
		},

		/**
		 * Обработчик изменения состояния истории в браузере
		 */
		stateChangeHandler = function stateChangeHandler() {
			var state = History.getState(),
				url = state.url;
			// end of vars
			
			console.info('statechange');
			console.log(state);

			if ( catalog.history._onlychange && typeof catalog.history._callback === 'function' ) {
				console.info('only update url ' + url);

				catalog.history._callback();
				catalog.history._onlychange = false;
				catalog.history._callback = null;
			}
			else {
				url = url.addParameterToUrl('ajax', 'true');
				getDataFromServer(url);
			}
		};
	// end of functions

	History.Adapter.bind(window, 'statechange', stateChangeHandler);
	
}(window.ENTER));	