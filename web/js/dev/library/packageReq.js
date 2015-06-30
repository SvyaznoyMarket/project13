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
		console.info('Выполнение пакетного запроса', reqArray);

		var
			dataToSend = {},
			callbacks = [],

			i, len;
		// end of vars
		
		dataToSend.actions = [];
		
		var 
			resHandler = function resHandler( res ) {
				var
					i, len;
				// end of vars

				console.info('Обработка ответа пакетого запроса', res);

				if ( res.success === false || (res.actions && res.actions.length === 0) ) {
					console.warn('Route false');
					console.log(res.success);
					console.log(res.actions);
				}

				for ( i = 0, len = res.actions.length - 1; i <= len; i++ ) {
					callbacks[i](res.actions[i]);
				}
			};
		// end of functions

		for ( i = 0, len = reqArray.length - 1; i <= len; i++ ) {

			// Обход странного бага с IE
			if ( !(reqArray[i] && reqArray[i].url) ) {
				console.info('continue');

				continue;
			}

			dataToSend.actions.push({
				url: reqArray[i].url,
				method: reqArray[i].type,
				data: reqArray[i].data || null
			});

			callbacks[i] = reqArray[i].callback;
		}

		if ( !dataToSend.actions.length ) {
			return;
		}

		$.ajax({
			url: pageConfig.routeUrl,
			type: 'POST',
			data: dataToSend,
			success: resHandler
		});
	};
}(this));