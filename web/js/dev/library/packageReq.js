/**
 * Пакетная отправка данных на сервер
 *
 * @author	Zaytsev Alexandr
 */
;(function( global ) {
	var pageConfig = global.ENTER.config.pageConfig,
		utils = global.ENTER.utils;
	// end of vars

	utils.packageReq = function packageReq( reqArray ) {
		console.info('Пакетный запрос');

		var dataToSend = {},
			callbacks = [],

			i, len;
		// end of vars
		
		dataToSend.actions = [];
		
		var resHandler = function resHandler( res ) {
			console.info('Обработка ответа пакетого запроса');

			for ( i = 0, len = res.length - 1; i <= len; i++ ) {
				callbacks[i](res[i]);
			}
		};

		for ( i = 0, len = reqArray.length - 1; i <= len; i++ ) {
			dataToSend.actions.push({
				url: reqArray[i].url,
				method: reqArray[i].type,
				data: reqArray[i].data || null
			});

			callbacks[i] = reqArray[i].callback;
		}

		$.ajax({
			url: pageConfig.routeUrl,
			type: 'POST',
			data: dataToSend,
			success: resHandler
		});
	};
}(this));