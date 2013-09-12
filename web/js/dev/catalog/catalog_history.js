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
		gotoUrl: function gotoUrl( url ) {
			var state = {
				title: 'history link to '+url,
				url: url
			};

			// Для старых браузеров просто переходим по ссылке
			if ( !History.enabled ) {
				document.location.href = url;

				return;
			}

			state.url.addParameterToUrl('ajax', 'true');
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
		 * Обработчик изменения состояния истории в браузере
		 */
		stateChangeHandler = function stateChangeHandler() {
			var state = History.getState(),
				url = state.url;
			// end of vars
			
			console.info('statechange');

			console.log(url);
			console.log(state);
		};
	// end of functions


	History.Adapter.bind(window, 'statechange', stateChangeHandler);
	$('body').on('click', '.jsHistoryLink', historyLinkHandler);

}(window.ENTER));