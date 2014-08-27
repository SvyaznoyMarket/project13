;(function (window, document, $, ENTER) {
	
	/**
	 * Общие настройки AJAX
	 *
	 * @requires	jQuery, ENTER.utils.logError
	 */
	$.ajaxSetup({
		timeout: 10000,
		statusCode: {
			404: function() { 
				var ajaxUrl = this.url,
					data = {
						event: 'ajax_error',
						type: '404 ошибка',
						ajaxUrl: ajaxUrl
					};
				// end of vars

				if ( typeof _gaq !== 'undefined' ) {
					_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '404 ошибка, страница не найдена']);
				}
			},
			401: function() {
				if ( $('#auth-block').length ) {
					$('#auth-block').lightbox_me({
						centered: true,
						onLoad: function() {
							$('#auth-block').find('input:first').focus();
						}
					});
				}
				else {
					if ( typeof _gaq !== 'undefined' ) {
						_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '401 ошибка, авторизуйтесь заново']);
					}
				}
					
			},
			500: function() {
				var ajaxUrl = this.url,
					data = {
						event: 'ajax_error',
						type: '500 ошибка',
						ajaxUrl: ajaxUrl
					};
				// end of vars

				if ( typeof _gaq !== 'undefined' ) {
					_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '500 сервер перегружен']);
				}
			},
			503: function() {
				var ajaxUrl = this.url,
					data = {
						event: 'ajax_error',
						type: '503 ошибка',
						ajaxUrl: ajaxUrl
					};
				// end of vars

				if ( typeof _gaq !== 'undefined' ) {
					_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '503 ошибка, сервер перегружен']);
				}
			},
			504: function() {
				var ajaxUrl = this.url,
					data = {
						event: 'ajax_error',
						type: '504 ошибка',
						ajaxUrl: ajaxUrl
					};
				// end of vars

				if ( typeof _gaq !== 'undefined' ) {
					_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '504 ошибка, проверьте соединение с интернетом']);
				}
			}
		},
		error: function ( jqXHR, textStatus, errorThrown ) {
			var ajaxUrl = this.url,
				data = {
					event: 'ajax_error',
					type: 'неизвестная ajax ошибка',
					ajaxUrl: ajaxUrl
				};
			// end of vars
			
			if ( jqXHR.statusText === 'error' ) {

				if ( typeof _gaq !== 'undefined' ) {
					_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', 'неизвестная ajax ошибка']);
				}
			}
			else if ( textStatus === 'timeout' ) {
				return;
			}
		}
	});
}(this, this.document, this.jQuery, this.ENTER));