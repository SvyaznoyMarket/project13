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
	var
		utils = ENTER.utils,
		catalog = utils.extendApp('ENTER.catalog'),
		updateState = true;
	// end of vars

	console.info('New catalog history module');

	catalog.history = {
		/**
		 * Ссылка на функцию обратного вызова по-умолчанию после получения данных с сервера при изменении history state
		 * 
		 * @type	{Function}
		 */
		_defaultCallback: catalog.filter.renderCatalogPage,

		/**
		 * Кастомная функция обратного вызова после получения данных с сервера
		 * 
		 * @type	{Function}
		 */
		_customCallback: null,

		/**
		 * Обработка перехода на URL
		 * Если браузер не поддерживает History API происходит обычный переход по ссылке
		 * 
		 * @param	{String}	url			Адрес на который необходимо выполнить переход
		 * @param	{Function}	callback	Функция которая будет вызвана после получения данных от сервера
		 * @param	{Boolean}	onlychange	Показывает что необходимо только изменить URL и не запрашивать данные
		 */
		gotoUrl: function gotoUrl( url, customCallback, onlychange ) {
			console.info('gotoUrl');
			var state = {
					title: document.title,
					url: url,
					data: {
						scrollTop: $(window).scrollTop(),
						_onlychange: (onlychange) ? true : false
					}
				};
			// end of vars

			if ( !catalog.enableHistoryAPI ) {
				document.location.href = url;

				return;
			}

			catalog.history._customCallback = (customCallback) ? customCallback : null;

			console.info('link handler. push state new url: ' + state.url);
			updateState = false;
			History.pushState(state, state.title, state.url);

			return;
		},

		updateUrl: function updateUrl( url, customCallback ) {
			var callback = (customCallback) ? customCallback : null;

			catalog.history.gotoUrl( url, callback, true );

			return;
		},

		/**
		 * Запросить новые данные с сервера по url
		 * 
		 * @param	{String}	url
		 */
		getDataFromServer: function getDataFromServer( url, callback ) {
			console.info('getDataFromServer ' + url);
			
			if ( catalog.loader ) {
				catalog.loader.loading();
			}

			console.log('getDataFromServer::loading');

			// utils.blockScreen.block('Загрузка товаров');

				/**
				 * Обработка ошибки загрузки данных
				 */
			var errorHandler = function errorHandler() {
					// utils.blockScreen.unblock();
					if ( catalog.loader ) {
						catalog.infScroll.loading = false;
						catalog.loader.error();
					}
				},

				/**
				 * Получение данных от сервера
				 * Перенаправление данных в функцию обратного вызова
				 * 
				 * @param	{Object}	res	Полученные данные
				 */
				resHandler = function resHandler( res ) {
					console.info('resHandler');

					if ( typeof res === 'object') {
						callback(res);
					}
					else {
						console.warn('res isn\'t object');
						console.log(typeof res);
					}

					// utils.blockScreen.unblock();
					if ( catalog.loader ) {
						catalog.loader.complete();
					}
				};
			// end of functions

			$.ajax({
				type: 'GET',
				url: url,
				success: resHandler,
				statusCode: {
					500: errorHandler,
					503: errorHandler
				},
				error: errorHandler
			});
		}
	};

		/**
		 * Обработчик изменения состояния истории в браузере
		 */
	var stateChangeHandler = function stateChangeHandler() {
			var state = History.getState(),
				url = state.url,
				data = state.data.data,
				callback = ( typeof catalog.history._customCallback === 'function' ) ? catalog.history._customCallback : catalog.history._defaultCallback;
			// end of vars

			console.info('statechange');
			console.log(state);

			// SITE-4941
			setTimeout(function() {
				if (data.scrollTop) {
					$(window).scrollTop(data.scrollTop);
				}
			}, 0);

			// SITE-4894 Не изменяются выбранные фильтры при переходе назад
			if (updateState) {
				ENTER.catalog.filter.updateOnChange = false;
				catalog.filter.resetForm();
				catalog.filter.updateFilter(utils.parseUrlParams(url));
				ENTER.catalog.filter.updateOnChange = true;
			}

			updateState = true;

			if ( data && data._onlychange ) {
				console.info('only update url ' + url);

				callback();
			}
			else {
				url = url.addParameterToUrl('ajax', 'true');
				catalog.history.getDataFromServer(url, callback);
			}

			catalog.history._customCallback = null;
		};
	// end of functions

	History.Adapter.bind(window, 'statechange', stateChangeHandler);
	
}(window.ENTER));	