;(function(global) {
	var pageConfig = $('#page-config').data('value');

	/**
	 * Логирование данных с клиента на сервер
	 * https://wiki.enter.ru/pages/viewpage.action?pageId=11239960
	 * 
	 * @param  {Object} data данные отсылаемы на сервер
	 */
	global.logError = function logError( data ) {
		if ( data.ajaxUrl === '/log-json' ) {
			return;
		}

		if ( !pageConfig.jsonLog ) {
			return false;
		}

		$.ajax({
			type: 'POST',
			global: false,
			url: '/log-json',
			data: data
		});
	};
}(this));


/**
 * Общие настройки AJAX
 */
$.ajaxSetup({
	timeout: 10000,
	statusCode: {
		404: function() { 
			var ajaxUrl = this.url,
				pageID = $('body').data('id'),
				data = {
					event: 'ajax_error',
					type:'404 ошибка',
					pageID: pageID,
					ajaxUrl:ajaxUrl
				};
			// end of vars

			window.logError(data);

			if( typeof(_gaq) !== 'undefined' ) {
				_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '404 ошибка, страница не найдена']);
			}
		},
		401: function() {
			if( $('#auth-block').length ) {
				$('#auth-block').lightbox_me({
					centered: true,
					onLoad: function() {
						$('#auth-block').find('input:first').focus();
					}
				});
			}
			else{
				if( typeof(_gaq) !== 'undefined' ) {
					_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '401 ошибка, авторизуйтесь заново']);
				}
			}
				
		},
		500: function() {
			var ajaxUrl = this.url,
				pageID = $('body').data('id'),
				data = {
					event: 'ajax_error',
					type:'500 ошибка',
					pageID: pageID,
					ajaxUrl:ajaxUrl
				};
			// end of vars

			window.logError(data);

			if( typeof(_gaq) !== 'undefined' ) {
				_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '500 сервер перегружен']);
			}
		},
		503: function() {
			var ajaxUrl = this.url,
				pageID = $('body').data('id'),
				data = {
					event: 'ajax_error',
					type:'503 ошибка',
					pageID: pageID,
					ajaxUrl:ajaxUrl
				};
			// end of vars

			window.logError(data);

			if( typeof(_gaq) !== 'undefined' ) {
				_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '503 ошибка, сервер перегружен']);
			}
		},
		504: function() {
			var ajaxUrl = this.url,
				pageID = $('body').data('id'),
				data = {
					event: 'ajax_error',
					type:'504 ошибка',
					pageID: pageID,
					ajaxUrl:ajaxUrl
				};
			// end of vars

			window.logError(data);

			if( typeof(_gaq) !== 'undefined' ) {
				_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '504 ошибка, проверьте соединение с интернетом']);
			}
		}
	},
	error: function ( jqXHR, textStatus, errorThrown ) {
		var ajaxUrl = this.url,
			pageID = $('body').data('id'),
			data = {
				event: 'ajax_error',
				type:'неизвестная ajax ошибка',
				pageID: pageID,
				ajaxUrl:ajaxUrl
			};
		// end of vars
		
		if( jqXHR.statusText === 'error' ) {
			window.logError(data);

			if( typeof(_gaq) !== 'undefined' ) {
				_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', 'неизвестная ajax ошибка']);
			}
		}
		else if (textStatus === 'timeout') {
			return;
		}
	}
});