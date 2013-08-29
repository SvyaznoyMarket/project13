/**
 * Получение данных с сервера
 * Разбиение заказа
 * Модель knockout
 * Аналитика
 *
 * @author	Zaytsev Alexandr
 */
;(function( global ) {
	console.info('Логика разбиения заказа для оформления заказа v.5');

	var serverData = $('#jsOrderDelivery').data('value');
	//var serverData = {"time":1377547200000,"action":[],"deliveryTypes":[{"id":1,"token":"standart","name":"\u0414\u043e\u0441\u0442\u0430\u0432\u043a\u0430 \u0437\u0430\u043a\u0430\u0437\u0430 \u043a\u0443\u0440\u044c\u0435\u0440\u043e\u043c","shortName":"\u0434\u043e\u0441\u0442\u0430\u0432\u043a\u0430","description":"\u041c\u044b \u043f\u0440\u0438\u0432\u0435\u0437\u0435\u043c \u0437\u0430\u043a\u0430\u0437 \u043f\u043e \u043b\u044e\u0431\u043e\u043c\u0443 \u0443\u0434\u043e\u0431\u043d\u043e\u043c\u0443 \u0432\u0430\u043c \u0430\u0434\u0440\u0435\u0441\u0443. \u041f\u043e\u0436\u0430\u043b\u0443\u0439\u0441\u0442\u0430, \u0443\u043a\u0430\u0436\u0438\u0442\u0435 \u0434\u0430\u0442\u0443 \u0438 \u0432\u0440\u0435\u043c\u044f \u0434\u043e\u0441\u0442\u0430\u0432\u043a\u0438.","states":["standart_furniture","standart_other","self","now"],"ownStates":["standart_furniture","standart_other"]},{"id":3,"token":"self","name":"\u0421\u0430\u043c\u043e\u0441\u0442\u043e\u044f\u0442\u0435\u043b\u044c\u043d\u043e \u0437\u0430\u0431\u0435\u0440\u0443 \u0432 \u043c\u0430\u0433\u0430\u0437\u0438\u043d\u0435","shortName":"\u0441\u0430\u043c\u043e\u0432\u044b\u0432\u043e\u0437","description":"\u0412\u044b \u043c\u043e\u0436\u0435\u0442\u0435 \u0441\u0430\u043c\u043e\u0441\u0442\u043e\u044f\u0442\u0435\u043b\u044c\u043d\u043e \u0437\u0430\u0431\u0440\u0430\u0442\u044c \u0442\u043e\u0432\u0430\u0440 \u0438\u0437 \u0431\u043b\u0438\u0436\u0430\u0439\u0448\u0435\u0433\u043e \u043a \u0432\u0430\u043c \u043c\u0430\u0433\u0430\u0437\u0438\u043d\u0430 Enter. \u0423\u0441\u043b\u0443\u0433\u0430 \u0431\u0435\u0441\u043f\u043b\u0430\u0442\u043d\u0430\u044f! \u0420\u0435\u0437\u0435\u0440\u0432 \u0442\u043e\u0432\u0430\u0440\u0430 \u0441\u043e\u0445\u0440\u0430\u043d\u044f\u0435\u0442\u0441\u044f 3 \u0434\u043d\u044f. \u041f\u043e\u0436\u0430\u043b\u0443\u0439\u0441\u0442\u0430, \u0432\u044b\u0431\u0435\u0440\u0438\u0442\u0435 \u043c\u0430\u0433\u0430\u0437\u0438\u043d.","states":["self","now","standart_furniture","standart_other"],"ownStates":["self"]},{"id":4,"token":"now","name":"\u0417\u0430\u0431\u0435\u0440\u0443 \u0441\u0435\u0439\u0447\u0430\u0441 \u0438\u0437 \u043c\u0430\u0433\u0430\u0437\u0438\u043d\u0430","shortName":"\u043f\u043e\u043a\u0443\u043f\u043a\u0430 \u0432 \u043c\u0430\u0433\u0430\u0437\u0438\u043d\u0435","description":"\u0412\u044b \u043c\u043e\u0436\u0435\u0442\u0435 \u0437\u0430\u0431\u0440\u0430\u0442\u044c \u0442\u043e\u0432\u0430\u0440 \u0438\u0437 \u044d\u0442\u043e\u0433\u043e \u043c\u0430\u0433\u0430\u0437\u0438\u043d\u0430 \u043f\u0440\u044f\u043c\u043e \u0441\u0435\u0439\u0447\u0430\u0441","states":["now","self","standart_furniture","standart_other"],"ownStates":["now"]}],"deliveryStates":{"self":{"name":"\u0421\u0430\u043c\u043e\u0432\u044b\u0432\u043e\u0437","products":["91622"]},"now":{"name":"\u0421\u0430\u043c\u043e\u0432\u044b\u0432\u043e\u0437","products":["91622"]},"standart_other":{"name":"\u0414\u043e\u0441\u0442\u0430\u0432\u0438\u043c","products":["91622"]}},"pointsByDelivery":{"self":"shops","now":"shops"},"products":{"91622":{"id":"91622","name":"\u0421\u043c\u0430\u0440\u0442\u0444\u043e\u043d Sony Xperia V \u0431\u0435\u043b\u044b\u0439","price":14990,"sum":14990,"quantity":1,"stock":2,"image":"http:\/\/fs01.enter.ru\/1\/1\/60\/f8\/171427.jpg","url":"\/product\/electronics\/smartfon-sony-xperia-v-beliy-2060302004630","setUrl":"\/cart\/add-product\/91622?quantity=1","deleteUrl":"\/cart\/delete-product\/91622","deliveries":{"now":{"2":{"price":0,"dates":[{"name":"27 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377547200000,"day":27,"dayOfWeek":2,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]}]},"3":{"price":0,"dates":[{"name":"27 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377547200000,"day":27,"dayOfWeek":2,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]}]},"68":{"price":0,"dates":[{"name":"27 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377547200000,"day":27,"dayOfWeek":2,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]}]},"135":{"price":0,"dates":[{"name":"27 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377547200000,"day":27,"dayOfWeek":2,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]}]}},"self":{"1":{"price":0,"dates":[{"name":"29 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377720000000,"day":29,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"30 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377806400000,"day":30,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"31 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377892800000,"day":31,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"1 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1377979200000,"day":1,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"2 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378065600000,"day":2,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"3 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378152000000,"day":3,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"4 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378238400000,"day":4,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"5 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378324800000,"day":5,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"6 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378411200000,"day":6,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"7 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378497600000,"day":7,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"8 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378584000000,"day":8,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"9 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378670400000,"day":9,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"10 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378756800000,"day":10,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"11 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378843200000,"day":11,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"12 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378929600000,"day":12,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"13 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379016000000,"day":13,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"14 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379102400000,"day":14,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"15 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379188800000,"day":15,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"16 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379275200000,"day":16,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"17 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379361600000,"day":17,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"18 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379448000000,"day":18,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"19 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379534400000,"day":19,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"20 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379620800000,"day":20,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"21 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379707200000,"day":21,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"22 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379793600000,"day":22,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"23 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379880000000,"day":23,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"24 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379966400000,"day":24,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]}]},"2":{"price":0,"dates":[{"name":"27 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377547200000,"day":27,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"29 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377720000000,"day":29,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"30 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377806400000,"day":30,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"31 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377892800000,"day":31,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"1 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1377979200000,"day":1,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"2 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378065600000,"day":2,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"3 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378152000000,"day":3,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"4 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378238400000,"day":4,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"5 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378324800000,"day":5,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"6 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378411200000,"day":6,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"7 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378497600000,"day":7,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"8 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378584000000,"day":8,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"9 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378670400000,"day":9,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"10 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378756800000,"day":10,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"11 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378843200000,"day":11,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"12 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378929600000,"day":12,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"13 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379016000000,"day":13,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"14 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379102400000,"day":14,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"15 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379188800000,"day":15,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"16 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379275200000,"day":16,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"17 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379361600000,"day":17,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"18 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379448000000,"day":18,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"19 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379534400000,"day":19,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"20 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379620800000,"day":20,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"21 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379707200000,"day":21,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"22 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379793600000,"day":22,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"23 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379880000000,"day":23,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"24 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379966400000,"day":24,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]}]},"3":{"price":0,"dates":[{"name":"27 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377547200000,"day":27,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"29 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377720000000,"day":29,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"30 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377806400000,"day":30,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"31 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377892800000,"day":31,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"1 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1377979200000,"day":1,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"2 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378065600000,"day":2,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"3 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378152000000,"day":3,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"4 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378238400000,"day":4,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"5 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378324800000,"day":5,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"6 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378411200000,"day":6,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"7 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378497600000,"day":7,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"8 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378584000000,"day":8,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"9 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378670400000,"day":9,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"10 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378756800000,"day":10,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"11 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378843200000,"day":11,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"12 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378929600000,"day":12,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"13 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379016000000,"day":13,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"14 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379102400000,"day":14,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"15 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379188800000,"day":15,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"16 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379275200000,"day":16,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"17 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379361600000,"day":17,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"18 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379448000000,"day":18,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"19 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379534400000,"day":19,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"20 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379620800000,"day":20,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"21 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379707200000,"day":21,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"22 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379793600000,"day":22,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"23 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379880000000,"day":23,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"24 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379966400000,"day":24,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]}]},"13":{"price":0,"dates":[{"name":"29 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377720000000,"day":29,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"30 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377806400000,"day":30,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"31 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377892800000,"day":31,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"1 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1377979200000,"day":1,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"2 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378065600000,"day":2,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"3 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378152000000,"day":3,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"4 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378238400000,"day":4,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"5 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378324800000,"day":5,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"6 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378411200000,"day":6,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"7 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378497600000,"day":7,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"8 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378584000000,"day":8,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"9 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378670400000,"day":9,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"10 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378756800000,"day":10,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"11 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378843200000,"day":11,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"12 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378929600000,"day":12,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"13 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379016000000,"day":13,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"14 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379102400000,"day":14,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"15 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379188800000,"day":15,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"16 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379275200000,"day":16,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"17 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379361600000,"day":17,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"18 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379448000000,"day":18,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"19 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379534400000,"day":19,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"20 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379620800000,"day":20,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"21 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379707200000,"day":21,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"22 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379793600000,"day":22,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"23 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379880000000,"day":23,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"24 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379966400000,"day":24,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]}]},"14":{"price":0,"dates":[{"name":"29 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377720000000,"day":29,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"30 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377806400000,"day":30,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"31 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377892800000,"day":31,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"1 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1377979200000,"day":1,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"2 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378065600000,"day":2,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"3 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378152000000,"day":3,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"4 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378238400000,"day":4,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"5 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378324800000,"day":5,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"6 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378411200000,"day":6,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"7 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378497600000,"day":7,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"8 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378584000000,"day":8,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"9 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378670400000,"day":9,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"10 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378756800000,"day":10,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"11 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378843200000,"day":11,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"12 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378929600000,"day":12,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"13 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379016000000,"day":13,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"14 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379102400000,"day":14,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"15 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379188800000,"day":15,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"16 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379275200000,"day":16,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"17 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379361600000,"day":17,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"18 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379448000000,"day":18,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"19 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379534400000,"day":19,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"20 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379620800000,"day":20,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"21 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379707200000,"day":21,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"22 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379793600000,"day":22,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"23 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379880000000,"day":23,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"24 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379966400000,"day":24,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]}]},"48":{"price":0,"dates":[{"name":"29 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377720000000,"day":29,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"30 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377806400000,"day":30,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"31 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377892800000,"day":31,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"1 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1377979200000,"day":1,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"2 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378065600000,"day":2,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"3 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378152000000,"day":3,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"4 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378238400000,"day":4,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"5 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378324800000,"day":5,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"6 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378411200000,"day":6,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"7 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378497600000,"day":7,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"8 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378584000000,"day":8,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"9 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378670400000,"day":9,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"10 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378756800000,"day":10,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"11 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378843200000,"day":11,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"12 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378929600000,"day":12,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"13 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379016000000,"day":13,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"14 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379102400000,"day":14,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"15 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379188800000,"day":15,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"16 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379275200000,"day":16,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"17 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379361600000,"day":17,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"18 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379448000000,"day":18,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"19 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379534400000,"day":19,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"20 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379620800000,"day":20,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"21 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379707200000,"day":21,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"22 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379793600000,"day":22,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"23 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379880000000,"day":23,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"24 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379966400000,"day":24,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]}]},"68":{"price":0,"dates":[{"name":"27 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377547200000,"day":27,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"29 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377720000000,"day":29,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"30 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377806400000,"day":30,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"31 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377892800000,"day":31,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"1 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1377979200000,"day":1,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"2 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378065600000,"day":2,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"3 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378152000000,"day":3,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"4 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378238400000,"day":4,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"5 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378324800000,"day":5,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"6 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378411200000,"day":6,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"7 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378497600000,"day":7,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"8 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378584000000,"day":8,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"9 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378670400000,"day":9,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"10 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378756800000,"day":10,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"11 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378843200000,"day":11,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"12 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378929600000,"day":12,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"13 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379016000000,"day":13,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"14 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379102400000,"day":14,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"15 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379188800000,"day":15,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"16 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379275200000,"day":16,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"17 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379361600000,"day":17,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"18 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379448000000,"day":18,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"19 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379534400000,"day":19,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"20 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379620800000,"day":20,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"21 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379707200000,"day":21,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"22 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379793600000,"day":22,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"23 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379880000000,"day":23,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"24 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379966400000,"day":24,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]}]},"69":{"price":0,"dates":[{"name":"29 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377720000000,"day":29,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"30 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377806400000,"day":30,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"31 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377892800000,"day":31,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"1 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1377979200000,"day":1,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"2 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378065600000,"day":2,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"3 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378152000000,"day":3,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"4 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378238400000,"day":4,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"5 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378324800000,"day":5,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"6 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378411200000,"day":6,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"7 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378497600000,"day":7,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"8 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378584000000,"day":8,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"9 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378670400000,"day":9,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"10 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378756800000,"day":10,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"11 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378843200000,"day":11,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"12 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378929600000,"day":12,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"13 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379016000000,"day":13,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"14 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379102400000,"day":14,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"15 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379188800000,"day":15,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"16 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379275200000,"day":16,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"17 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379361600000,"day":17,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"18 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379448000000,"day":18,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"19 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379534400000,"day":19,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"20 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379620800000,"day":20,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"21 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379707200000,"day":21,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"22 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379793600000,"day":22,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"23 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379880000000,"day":23,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"24 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379966400000,"day":24,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]}]},"75":{"price":0,"dates":[{"name":"29 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377720000000,"day":29,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"30 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377806400000,"day":30,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"31 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377892800000,"day":31,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"1 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1377979200000,"day":1,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"2 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378065600000,"day":2,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"3 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378152000000,"day":3,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"4 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378238400000,"day":4,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"5 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378324800000,"day":5,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"6 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378411200000,"day":6,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"7 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378497600000,"day":7,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"8 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378584000000,"day":8,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"9 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378670400000,"day":9,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"10 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378756800000,"day":10,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"11 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378843200000,"day":11,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"12 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378929600000,"day":12,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"13 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379016000000,"day":13,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"14 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379102400000,"day":14,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"15 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379188800000,"day":15,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"16 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379275200000,"day":16,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"17 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379361600000,"day":17,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"18 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379448000000,"day":18,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"19 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379534400000,"day":19,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"20 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379620800000,"day":20,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"21 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379707200000,"day":21,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"22 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379793600000,"day":22,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"23 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379880000000,"day":23,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"24 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379966400000,"day":24,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]}]},"81":{"price":0,"dates":[{"name":"29 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377720000000,"day":29,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"30 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377806400000,"day":30,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"31 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377892800000,"day":31,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"1 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1377979200000,"day":1,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"2 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378065600000,"day":2,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"3 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378152000000,"day":3,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"4 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378238400000,"day":4,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"5 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378324800000,"day":5,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"6 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378411200000,"day":6,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"7 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378497600000,"day":7,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"8 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378584000000,"day":8,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"9 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378670400000,"day":9,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"10 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378756800000,"day":10,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"11 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378843200000,"day":11,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"12 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378929600000,"day":12,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"13 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379016000000,"day":13,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"14 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379102400000,"day":14,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"15 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379188800000,"day":15,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"16 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379275200000,"day":16,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"17 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379361600000,"day":17,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"18 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379448000000,"day":18,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"19 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379534400000,"day":19,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"20 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379620800000,"day":20,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"21 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379707200000,"day":21,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"22 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379793600000,"day":22,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"23 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379880000000,"day":23,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"24 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379966400000,"day":24,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]}]},"87":{"price":0,"dates":[{"name":"29 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377720000000,"day":29,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"30 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377806400000,"day":30,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"31 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377892800000,"day":31,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"1 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1377979200000,"day":1,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"2 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378065600000,"day":2,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"3 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378152000000,"day":3,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"4 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378238400000,"day":4,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"5 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378324800000,"day":5,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"6 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378411200000,"day":6,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"7 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378497600000,"day":7,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"8 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378584000000,"day":8,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"9 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378670400000,"day":9,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"10 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378756800000,"day":10,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"11 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378843200000,"day":11,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"12 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378929600000,"day":12,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"13 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379016000000,"day":13,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"14 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379102400000,"day":14,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"15 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379188800000,"day":15,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"16 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379275200000,"day":16,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"17 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379361600000,"day":17,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"18 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379448000000,"day":18,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"19 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379534400000,"day":19,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"20 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379620800000,"day":20,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"21 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379707200000,"day":21,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"22 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379793600000,"day":22,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"23 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379880000000,"day":23,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"24 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379966400000,"day":24,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]}]},"88":{"price":0,"dates":[{"name":"29 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377720000000,"day":29,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"30 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377806400000,"day":30,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"31 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377892800000,"day":31,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"1 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1377979200000,"day":1,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"2 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378065600000,"day":2,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"3 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378152000000,"day":3,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"4 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378238400000,"day":4,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"5 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378324800000,"day":5,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"6 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378411200000,"day":6,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"7 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378497600000,"day":7,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"8 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378584000000,"day":8,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"9 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378670400000,"day":9,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"10 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378756800000,"day":10,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"11 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378843200000,"day":11,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"12 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378929600000,"day":12,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"13 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379016000000,"day":13,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"14 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379102400000,"day":14,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"15 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379188800000,"day":15,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"16 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379275200000,"day":16,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"17 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379361600000,"day":17,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"18 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379448000000,"day":18,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"19 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379534400000,"day":19,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"20 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379620800000,"day":20,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"21 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379707200000,"day":21,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"22 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379793600000,"day":22,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"23 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379880000000,"day":23,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"24 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379966400000,"day":24,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]}]},"134":{"price":0,"dates":[{"name":"29 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377720000000,"day":29,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"30 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377806400000,"day":30,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"31 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377892800000,"day":31,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"1 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1377979200000,"day":1,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"2 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378065600000,"day":2,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"3 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378152000000,"day":3,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"4 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378238400000,"day":4,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"5 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378324800000,"day":5,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"6 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378411200000,"day":6,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"7 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378497600000,"day":7,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"8 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378584000000,"day":8,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"9 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378670400000,"day":9,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"10 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378756800000,"day":10,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"11 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378843200000,"day":11,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"12 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378929600000,"day":12,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"13 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379016000000,"day":13,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"14 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379102400000,"day":14,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"15 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379188800000,"day":15,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"16 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379275200000,"day":16,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"17 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379361600000,"day":17,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"18 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379448000000,"day":18,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"19 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379534400000,"day":19,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"20 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379620800000,"day":20,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"21 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379707200000,"day":21,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"22 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379793600000,"day":22,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"23 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379880000000,"day":23,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"24 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379966400000,"day":24,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]}]},"135":{"price":0,"dates":[{"name":"27 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377547200000,"day":27,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"29 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377720000000,"day":29,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"30 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377806400000,"day":30,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"31 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377892800000,"day":31,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"1 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1377979200000,"day":1,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"2 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378065600000,"day":2,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"3 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378152000000,"day":3,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"4 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378238400000,"day":4,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"5 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378324800000,"day":5,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"6 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378411200000,"day":6,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"7 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378497600000,"day":7,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"8 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378584000000,"day":8,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"9 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378670400000,"day":9,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"10 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378756800000,"day":10,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"11 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378843200000,"day":11,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"12 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378929600000,"day":12,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"13 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379016000000,"day":13,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"14 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379102400000,"day":14,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"15 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379188800000,"day":15,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"16 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379275200000,"day":16,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"17 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379361600000,"day":17,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"18 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379448000000,"day":18,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"19 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379534400000,"day":19,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"20 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379620800000,"day":20,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"21 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379707200000,"day":21,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"22 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379793600000,"day":22,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"23 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379880000000,"day":23,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"24 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379966400000,"day":24,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]}]},"138":{"price":0,"dates":[{"name":"29 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377720000000,"day":29,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"30 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377806400000,"day":30,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"31 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377892800000,"day":31,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"1 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1377979200000,"day":1,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"2 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378065600000,"day":2,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"3 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378152000000,"day":3,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"4 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378238400000,"day":4,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"5 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378324800000,"day":5,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"6 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378411200000,"day":6,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"7 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378497600000,"day":7,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"8 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378584000000,"day":8,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"9 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378670400000,"day":9,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"10 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378756800000,"day":10,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"11 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378843200000,"day":11,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"12 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378929600000,"day":12,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"13 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379016000000,"day":13,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"14 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379102400000,"day":14,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"15 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379188800000,"day":15,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"16 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379275200000,"day":16,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"17 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379361600000,"day":17,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"18 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379448000000,"day":18,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"19 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379534400000,"day":19,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"20 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379620800000,"day":20,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"21 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379707200000,"day":21,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"22 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379793600000,"day":22,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"23 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379880000000,"day":23,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"24 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379966400000,"day":24,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]}]},"144":{"price":0,"dates":[{"name":"29 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377720000000,"day":29,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"30 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377806400000,"day":30,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"31 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377892800000,"day":31,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"1 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1377979200000,"day":1,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"2 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378065600000,"day":2,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"3 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378152000000,"day":3,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"4 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378238400000,"day":4,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"5 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378324800000,"day":5,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"6 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378411200000,"day":6,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"7 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378497600000,"day":7,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"8 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378584000000,"day":8,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"9 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378670400000,"day":9,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"10 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378756800000,"day":10,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"11 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378843200000,"day":11,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"12 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378929600000,"day":12,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"13 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379016000000,"day":13,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"14 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379102400000,"day":14,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"15 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379188800000,"day":15,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"16 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379275200000,"day":16,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"17 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379361600000,"day":17,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"18 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379448000000,"day":18,"dayOfWeek":3,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"19 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379534400000,"day":19,"dayOfWeek":4,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"20 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379620800000,"day":20,"dayOfWeek":5,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"21 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379707200000,"day":21,"dayOfWeek":6,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"22 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379793600000,"day":22,"dayOfWeek":0,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"23 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379880000000,"day":23,"dayOfWeek":1,"intervals":[{"start":"16:00","end":"21:00"}]},{"name":"24 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379966400000,"day":24,"dayOfWeek":2,"intervals":[{"start":"16:00","end":"21:00"}]}]}},"standart_other":[{"price":0,"dates":[{"name":"29 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377720000000,"day":29,"dayOfWeek":4,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"30 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377806400000,"day":30,"dayOfWeek":5,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"31 \u0430\u0432\u0433\u0443\u0441\u0442\u0430","value":1377892800000,"day":31,"dayOfWeek":6,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"1 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1377979200000,"day":1,"dayOfWeek":0,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"2 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378065600000,"day":2,"dayOfWeek":1,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"3 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378152000000,"day":3,"dayOfWeek":2,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"4 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378238400000,"day":4,"dayOfWeek":3,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"5 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378324800000,"day":5,"dayOfWeek":4,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"6 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378411200000,"day":6,"dayOfWeek":5,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"7 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378497600000,"day":7,"dayOfWeek":6,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"8 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378584000000,"day":8,"dayOfWeek":0,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"9 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378670400000,"day":9,"dayOfWeek":1,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"10 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378756800000,"day":10,"dayOfWeek":2,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"11 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378843200000,"day":11,"dayOfWeek":3,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"12 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1378929600000,"day":12,"dayOfWeek":4,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"13 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379016000000,"day":13,"dayOfWeek":5,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"14 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379102400000,"day":14,"dayOfWeek":6,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"15 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379188800000,"day":15,"dayOfWeek":0,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"16 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379275200000,"day":16,"dayOfWeek":1,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"17 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379361600000,"day":17,"dayOfWeek":2,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"18 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379448000000,"day":18,"dayOfWeek":3,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"19 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379534400000,"day":19,"dayOfWeek":4,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"20 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379620800000,"day":20,"dayOfWeek":5,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"21 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379707200000,"day":21,"dayOfWeek":6,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"22 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379793600000,"day":22,"dayOfWeek":0,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"23 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379880000000,"day":23,"dayOfWeek":1,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"24 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1379966400000,"day":24,"dayOfWeek":2,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]},{"name":"25 \u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","value":1380052800000,"day":25,"dayOfWeek":3,"intervals":[{"start":"09:00","end":"18:00"},{"start":"09:00","end":"14:00"},{"start":"14:00","end":"18:00"},{"start":"18:00","end":"21:00"}]}]}]}}},"shops":[{"id":"1","name":"\u043c. \u0411\u0435\u043b\u043e\u0440\u0443\u0441\u0441\u043a\u0430\u044f, \u0443\u043b. \u0413\u0440\u0443\u0437\u0438\u043d\u0441\u043a\u0438\u0439 \u0432\u0430\u043b, \u0434. 31","address":"\u0443\u043b. \u0413\u0440\u0443\u0437\u0438\u043d\u0441\u043a\u0438\u0439 \u0412\u0430\u043b, \u0434. 31","regtime":"\u0441 9.00 \u0434\u043e 22.00","latitude":55.775004,"longitude":37.581675,"products":["91622"]},{"id":"2","name":"\u043c. \u041b\u0435\u043d\u0438\u043d\u0441\u043a\u0438\u0439 \u043f\u0440\u043e\u0441\u043f\u0435\u043a\u0442, \u0443\u043b. \u041e\u0440\u0434\u0436\u043e\u043d\u0438\u043a\u0438\u0434\u0437\u0435, \u0434. 11, \u0441\u0442\u0440. 10","address":"\u0443\u043b. \u041e\u0440\u0434\u0436\u043e\u043d\u0438\u043a\u0438\u0434\u0437\u0435, \u0434. 11, \u0441\u0442\u0440. 10","regtime":"\u0441 9.00 \u0434\u043e 21.00","latitude":55.706488,"longitude":37.596997,"products":["91622","91622"]},{"id":"3","name":"\u043c. \u041a\u0438\u0435\u0432\u0441\u043a\u0430\u044f, \u0443\u043b. \u0411. \u0414\u043e\u0440\u043e\u0433\u043e\u043c\u0438\u043b\u043e\u0432\u0441\u043a\u0430\u044f, \u0434. 8","address":"\u0443\u043b. \u0411. \u0414\u043e\u0440\u043e\u0433\u043e\u043c\u0438\u043b\u043e\u0432\u0441\u043a\u0430\u044f, \u0434. 8","regtime":"\u0441 8.00 \u0434\u043e 23.00","latitude":55.746197,"longitude":37.565389,"products":["91622","91622"]},{"id":"13","name":"\u043c. \u041a\u0443\u0437\u044c\u043c\u0438\u043d\u043a\u0438, \u0412\u043e\u043b\u0433\u043e\u0433\u0440\u0430\u0434\u0441\u043a\u0438\u0439 \u043f\u0440-\u0442, \u0434. 119\u0430.","address":"\u0412\u043e\u043b\u0433\u043e\u0433\u0440\u0430\u0434\u0441\u043a\u0438\u0439 \u043f\u0440-\u0442, \u0434. 119\u0430.","regtime":"\u0441 9.00 \u0434\u043e 23.00","latitude":55.706279,"longitude":37.765371,"products":["91622"]},{"id":"14","name":"\u043c. \u0421\u0445\u043e\u0434\u043d\u0435\u043d\u0441\u043a\u0430\u044f, \u0425\u0438\u043c\u043a\u0438\u043d\u0441\u043a\u0438\u0439 \u0431\u0443\u043b\u044c\u0432\u0430\u0440, \u0434. 16, \u043a\u043e\u0440\u043f. 1.","address":"\u0425\u0438\u043c\u043a\u0438\u043d\u0441\u043a\u0438\u0439 \u0431\u0443\u043b\u044c\u0432\u0430\u0440, \u0434. 16, \u043a\u043e\u0440\u043f. 1.","regtime":"\u0441 9.00 \u0434\u043e 22.00","latitude":55.851993,"longitude":37.442905,"products":["91622"]},{"id":"48","name":"\u041f\u0443\u043d\u043a\u0442 \u0432\u044b\u0434\u0430\u0447\u0438, \u043c. \u0422\u0443\u0448\u0438\u043d\u0441\u043a\u0430\u044f, \u0412\u043e\u043b\u043e\u043a\u043e\u043b\u0430\u043c\u0441\u043a\u043e\u0435 \u0448\u043e\u0441\u0441\u0435, \u0434. 92","address":"\u0412\u043e\u043b\u043e\u043a\u043e\u043b\u0430\u043c\u0441\u043a\u043e\u0435 \u0448\u043e\u0441\u0441\u0435, \u0434. 92","regtime":"\u0441 9.00 \u0434\u043e 21.00","latitude":55.824635,"longitude":37.434667,"products":["91622"]},{"id":"68","name":"\u043c. \u041d\u043e\u0432\u043e\u0433\u0438\u0440\u0435\u0435\u0432\u043e, \u0421\u0432\u043e\u0431\u043e\u0434\u043d\u044b\u0439 \u043f\u0440-\u043a\u0442, \u0434. 33","address":"\u0421\u0432\u043e\u0431\u043e\u0434\u043d\u044b\u0439 \u043f\u0440-\u043a\u0442, \u0434. 33","regtime":"\u0441 9.00 \u0434\u043e 23.00","latitude":55.752796,"longitude":37.819324,"products":["91622","91622"]},{"id":"69","name":"\u043c. \u0421\u043e\u043a\u043e\u043b, \u041b\u0435\u043d\u0438\u043d\u0433\u0440\u0430\u0434\u0441\u043a\u0438\u0439 \u043f\u0440-\u043a\u0442, \u0434. 78","address":"\u041b\u0435\u043d\u0438\u043d\u0433\u0440\u0430\u0434\u0441\u043a\u0438\u0439 \u043f\u0440-\u043a\u0442, \u0434. 78","regtime":"\u0441 9.00 \u0434\u043e 22.00","latitude":55.806052,"longitude":37.513107,"products":["91622"]},{"id":"75","name":"\u043c. \u041e\u043a\u0442\u044f\u0431\u0440\u044c\u0441\u043a\u0430\u044f, \u0443\u043b. \u0411. \u042f\u043a\u0438\u043c\u0430\u043d\u043a\u0430, \u0434. 54","address":"\u0411. \u042f\u043a\u0438\u043c\u0430\u043d\u043a\u0430, \u0434. 54","regtime":"\u0441 9.00 \u0434\u043e 22.00","latitude":55.731337,"longitude":37.611474,"products":["91622"]},{"id":"81","name":"\u041f\u0443\u043d\u043a\u0442 \u0432\u044b\u0434\u0430\u0447\u0438, \u043c. \u041a\u0440\u044b\u043b\u0430\u0442\u0441\u043a\u043e\u0435, \u041e\u0441\u0435\u043d\u043d\u0438\u0439 \u0431\u0443\u043b\u044c\u0432\u0430\u0440, \u0434. 5, \u043a\u043e\u0440\u043f\u0443\u0441 1","address":"\u041e\u0441\u0435\u043d\u043d\u0438\u0439 \u0431\u0443\u043b\u044c\u0432\u0430\u0440, \u0434. 5, \u043a\u043e\u0440\u043f\u0443\u0441 1","regtime":"\u0441 9.00 \u0434\u043e 21.00","latitude":55.756967,"longitude":37.407103,"products":["91622"]},{"id":"87","name":"\u043c. \u0411\u0440\u0430\u0442\u0438\u0441\u043b\u0430\u0432\u0441\u043a\u0430\u044f, \u0443\u043b. \u0411\u0440\u0430\u0442\u0438\u0441\u043b\u0430\u0432\u0441\u043a\u0430\u044f \u0434. 14","address":"\u0443\u043b. \u0411\u0440\u0430\u0442\u0438\u0441\u043b\u0430\u0432\u0441\u043a\u0430\u044f \u0434. 14","regtime":"\u0441 9.00 \u0434\u043e 22.00","latitude":55.659082,"longitude":37.755054,"products":["91622"]},{"id":"88","name":"\u043c. \u0411\u0430\u0443\u043c\u0430\u043d\u0441\u043a\u0430\u044f, \u0443\u043b. \u041b\u0430\u0434\u043e\u0436\u0441\u043a\u0430\u044f, \u0434. 7","address":"\u0443\u043b. \u041b\u0430\u0434\u043e\u0436\u0441\u043a\u0430\u044f, \u0434. 7","regtime":"\u0441 9.00 \u0434\u043e 21.00","latitude":55.77163,"longitude":37.68303,"products":["91622"]},{"id":"134","name":"\u043c. \u0412\u0414\u041d\u0425, \u043f\u0440-\u043a\u0442 \u041c\u0438\u0440\u0430, \u0434. 211, \u0422\u0420\u0426 \u0022\u0417\u043e\u043b\u043e\u0442\u043e\u0439 \u0412\u0430\u0432\u0438\u043b\u043e\u043d\u0022","address":"\u043f\u0440-\u043a\u0442 \u041c\u0438\u0440\u0430, \u0434. 211","regtime":"\u0441 10.00 \u0434\u043e 22.00","latitude":55.846214,"longitude":37.663198,"products":["91622"]},{"id":"135","name":"\u043c. \u041a\u043e\u043d\u044c\u043a\u043e\u0432\u043e, \u0443\u043b. \u041f\u0440\u043e\u0444\u0441\u043e\u044e\u0437\u043d\u0430\u044f, \u0432\u043b. 118, \u0422\u0426 \u0022\u0422\u0440\u043e\u043f\u0430\u0022","address":"\u0443\u043b. \u041f\u0440\u043e\u0444\u0441\u043e\u044e\u0437\u043d\u0430\u044f, \u0432\u043b. 118","regtime":"\u0441 10.00 \u0434\u043e 22.00","latitude":55.636122,"longitude":37.521042,"products":["91622","91622"]},{"id":"138","name":"\u043c. \u042d\u043b\u0435\u043a\u0442\u0440\u043e\u0437\u0430\u0432\u043e\u0434\u0441\u043a\u0430\u044f, \u0443\u043b. \u0411\u043e\u043b\u044c\u0448\u0430\u044f \u0421\u0435\u043c\u0435\u043d\u043e\u0432\u0441\u043a\u0430\u044f, \u0434. 27, \u043a\u043e\u0440\u043f. 1.","address":"\u0443\u043b. \u0411\u043e\u043b\u044c\u0448\u0430\u044f \u0421\u0435\u043c\u0435\u043d\u043e\u0432\u0441\u043a\u0430\u044f, \u0434. 27, \u043a\u043e\u0440\u043f. 1","regtime":"\u0441 9.00 \u0434\u043e 22.00","latitude":55.782309,"longitude":37.708922,"products":["91622"]},{"id":"144","name":"\u043c. \u041f\u0435\u0440\u0432\u043e\u043c\u0430\u0439\u0441\u043a\u0430\u044f, \u0443\u043b. \u041f\u0435\u0440\u0432\u043e\u043c\u0430\u0439\u0441\u043a\u0430\u044f, \u0434. 81","address":"\u0443\u043b. \u041f\u0435\u0440\u0432\u043e\u043c\u0430\u0439\u0441\u043a\u0430\u044f, \u0434. 81","regtime":"\u0441 9.00 \u0434\u043e 21.00","latitude":55.793556,"longitude":37.801574,"products":["91622"]}],"discounts":[],"success":true, "paypalECS": true};

	/**
	 * Логика разбиения заказа на подзаказы
	 * Берутся states из выбранного способа доставки в порядке приоритета.
	 * Каждый новый states - новый подзаказ.
	 *
	 * @param	{Array}		statesPriority		Приоритет методов доставки
	 * 
	 * @param	{Object}	preparedProducts	Уже обработанные продукты, которые попали в какой-либо блок доставки
	 * @param	{Array}		productInState		Массив продуктов, которые есть в данном способе доставки
	 * @param	{Array}		productsToNewBox	Массив продуктов, которые должны попасть в новый блок доставки
	 * @param	{Number}	choosenPointForBox	Точка доставки для блока самовывоза
	 * @param	{String}	token				Временное имя для создаваемого блока
	 * @param	{String}	nowState			Текущий тип доставки который находится в обработке
	 * @param	{String}	nowProduct			Текущий id продукта который находится в обработке
	 */
	var separateOrder = function separateOrder( statesPriority ) {

		var preparedProducts = {},
			productInState = [],
			productsToNewBox = [],
			choosenPointForBox = null,
			token = null,
			nowState = null,
			nowProduct = null,

			discounts = global.OrderModel.orderDictionary.orderData.discounts;
		// end of vars
		
		if ( global.OrderModel.paypalECS() ) {
			console.info('PayPal ECS включен. Необходимо сохранить выбранные параметры в cookie');

			window.docCookies.setItem('chTypeBtn_paypalECS', global.OrderModel.deliveryTypesButton, 10 * 60);
			window.docCookies.setItem('chPoint_paypalECS', global.OrderModel.choosenPoint(), 10 * 60);
			window.docCookies.setItem('chTypeId_paypalECS', global.OrderModel.choosenDeliveryTypeId, 10 * 60);
			window.docCookies.setItem('chStetesPriority_paypalECS', JSON.stringify(global.OrderModel.statesPriority), 10 * 60);
		}


		// очищаем объект созданых блоков, удаляем блоки из модели
		global.OrderModel.createdBox = {};
		global.OrderModel.deliveryBoxes.removeAll();

		// Маркируем выбранный способ доставки
		$('#'+global.OrderModel.deliveryTypesButton).attr('checked','checked');
			
		// Обнуляем общую стоимость заказа
		global.OrderModel.totalSum(0);

		// Обнуляем блоки с доставкой на дом и генерируем событие об этом
		global.OrderModel.hasHomeDelivery(false);
		$('body').trigger('orderdeliverychange',[false]);

		// Добавляем купоны
		global.OrderModel.couponsBox(discounts);


		/**
		 * Перебор states в выбранном способе доставки в порядке приоритета
		 */
		for ( var i = 0, len = statesPriority.length; i < len; i++ ) {
			nowState = statesPriority[i];

			console.info('перебирем метод '+nowState);

			productsToNewBox = [];

			if ( !global.OrderModel.orderDictionary.hasDeliveryState(nowState) ) {
				console.info('для метода '+nowState+' нет товаров');

				continue;
			}

			productInState = global.OrderModel.orderDictionary.getProductFromState(nowState);
			
			/**
			 * Перебор продуктов в текущем deliveryStates
			 */
			for ( var j = productInState.length - 1; j >= 0; j-- ) {
				nowProduct = productInState[j];

				if ( preparedProducts[nowProduct] ) {
					// если этот товар уже находили
					console.log('товар '+nowProduct+' уже определялся к блоку');

					continue;
				}
				
				console.log('добавляем товар '+nowProduct+' в блок для метода '+nowState);

				preparedProducts[nowProduct] = true;
				productsToNewBox.push( global.OrderModel.orderDictionary.getProductById(nowProduct) );
			}

			if ( productsToNewBox.length ) {
				choosenPointForBox = ( global.OrderModel.orderDictionary.hasPointDelivery(nowState) ) ? global.OrderModel.choosenPoint() : 0;

				token = nowState+'_'+choosenPointForBox;

				if ( global.OrderModel.createdBox[token] !== undefined ) {
					// Блок для этого типа доставки в этот пункт уже существует
					global.OrderModel.createdBox[token].addProductGroup( productsToNewBox );
				}
				else {
					// Блока для этого типа доставки в этот пункт еще существует
					global.OrderModel.createdBox[token] = new DeliveryBox( productsToNewBox, nowState, choosenPointForBox, global.OrderModel );
				}
			}
		}

		console.info('Созданные блоки:');
		console.log(global.OrderModel.createdBox);

		// выбираем URL для проверки купонов - первый видимый купон
		global.OrderModel.couponUrl( $('.bSaleList__eItem:visible .jsCustomRadio').eq(0).val() );

		if ( preparedProducts.length !== global.OrderModel.orderDictionary.orderData.products.length ) {
			console.warn('не все товары были обработаны');
		}
	};


	/**
	 * Кастомный бинд для открытия окна магазинов
	 */
	ko.bindingHandlers.popupShower = {
		update: function( element, valueAccessor ) {
			var val = valueAccessor(),
				unwrapVal = ko.utils.unwrapObservable(val),
				map = null;
			// end of vars

			if ( unwrapVal ) {
				// create map
				map = new CreateMap('pointPopupMap', global.OrderModel.popupWithPoints().points, $('#mapInfoBlock'));

				$(element).lightbox_me({
					centered: true,
					onClose: function() {
						console.info('закрываем');
						val(false);
					}
				});
			}
			else {
				$('#pointPopupMap').empty();
				$(element).trigger('close');
			}
		}
	};

	/**
	 * Кастомный бинд отображения методов оплаты
	 */
	ko.bindingHandlers.paymentMethodVisible = {
		update: function( element, valueAccessor ) {
			var val = valueAccessor(),
				unwrapVal = ko.utils.unwrapObservable(val),
				node = $(element),
				maxSum = parseInt($(element).data('value')['max-sum'], 10);
			// end of vars

			if ( isNaN(maxSum) ) {
				return;
			}

			if ( maxSum < unwrapVal ) {
				node.hide();
			}
			else {
				node.show();
			}
		}
	};

	ko.bindingHandlers.couponsVisible = {
		update: function( element, valueAccessor ) {
			var val = valueAccessor(),
				unwrapVal = ko.utils.unwrapObservable(val),

				node = $(element),
				fieldNode = node.find('.mSaleInput'),
				buttonNode = node.find('.mSaleBtn'),
				titleNode = node.find('.bTitle'),

				emptyBlock = node.find('.bSaleData__eEmptyBlock');
			// end of vars

			$('.bSaleList__eItem').removeClass('hidden');

			for ( var i = unwrapVal.length - 1; i >= 0; i-- ) {
				node.find('.bSaleList__eItem[data-type="'+unwrapVal[i].type+'"]').addClass('hidden');
			}

			if ( $('.bSaleList__eItem.hidden').length === $('.bSaleList__eItem').length ) {
				// если все скидки применены
				
				fieldNode.attr('disabled', 'disabled');
				buttonNode.attr('disabled', 'disabled').addClass('mDisabled');
				emptyBlock.show();
			}
			else {
				// не все скидки применены
				
				fieldNode.removeAttr('disabled');
				buttonNode.removeAttr('disabled').removeClass('mDisabled');
				emptyBlock.hide();
			}
		}
	};


	/**
	 * === ORDER MODEL ===
	 */
	global.OrderModel = {
		updateUrl: $('#jsOrderDelivery').data('url'),
		/**
		 * Флаг завершения обработки данных
		 */
		prepareData: ko.observable(false),

		/**
		 * Флаг открытия окна с выбором точек доставки
		 */
		showPopupWithPoints: ko.observable(false),

		/**
		 * Ссылка на элемент input который соответствует выбранному методу доставки
		 */
		deliveryTypesButton: null,

		/**
		 * Приоритет методов доставок на время выбора точек доставки
		 * Если пункт доставки не был выбран - не используется
		 */
		tmpStatesPriority: null,

		/**
		 * Реальный приоритет методов доставок
		 * Сохраняется при выборе пункта доставки или методе доставки не имеющем пунктов доставки
		 */
		statesPriority: null,

		/**
		 * Флаг того что это страница PayPal: схема ECS
		 * https://jira.enter.ru/browse/SITE-1795
		 */
		paypalECS: ko.observable(false),

		/**
		 * Первоначальная сумма корзины
		 */
		cartSum: null,

		/**
		 * Ссылка на словарь
		 */
		orderDictionary: null,

		/**
		 * Идетификатор приоритетного пункта доставки выбранного пользователем
		 */
		choosenPoint: ko.observable(),

		/**
		 * Есть ли хотя бы один блок доставки на дом
		 */
		hasHomeDelivery: ko.observable(false),

		/**
		 * Массив способов доставок доступных пользователю
		 */
		deliveryTypes: ko.observableArray([]),

		/**
		 * Массив блоков доставок
		 */
		deliveryBoxes: ko.observableArray([]),

		/**
		 * Хранилище блоков доставки
		 */
		createdBox: {},

		/**
		 * Объект данных для отображения окна с пунктами доставок
		 */
		popupWithPoints: ko.observable({}),

		/**
		 * Общая сумма заказа
		 */
		totalSum: ko.observable(0),

		/**
		 * Номер введенного сертификата
		 */
		couponNumber: ko.observable(),

		/**
		 * URL по которому нужно проверять карту
		 */
		couponUrl: ko.observable(),

		/**
		 * Ошибки сертификата
		 */
		couponError: ko.observable(),

		/**
		 * Массив примененных купонов
		 */
		couponsBox: ko.observableArray([]),

		/**
		 * Блокер экрана
		 *
		 * @param	{Object}		noti		Объект jQuery блокера экрана
		 * @param	{Function}		block		Функция блокировки экрана. На вход принимает текст который нужно отобразить в окошке блокера
		 * @param	{Function}		unblock		Функция разблокировки экрана. Объект окна блокера удаляется.
		 */
		blockScreen: {
			noti: null,
			block: function( text ) {
				var self = this;

				console.warn('block screen');

				if ( self.noti ) {
					self.unblock();
				}

				self.noti = $('<div>').addClass('noti').html('<div><img src="/images/ajaxnoti.gif" /></br></br> '+ text +'</div>');
				self.noti.appendTo('body');

				self.noti.lightbox_me({
					centered:true,
					closeClick:false,
					closeEsc:false,
					onClose: function() {
						self.noti.remove();
					}
				});
			},

			unblock: function() {
				console.warn('unblock screen');

				this.noti.trigger('close');
			}
		},

		/**
		 * Проверка сертификата
		 */
		checkCoupon: function() {
			console.info('проверяем купон');

			var dataToSend = {
					number: global.OrderModel.couponNumber(),
				},
				url = global.OrderModel.couponUrl();
			// end of vars

			var couponResponceHandler = function couponResponceHandler( res ) {
				global.OrderModel.blockScreen.block('Применяем купон');

				if ( !res.success ) {
					global.OrderModel.couponError(res.error.message);
					global.OrderModel.blockScreen.unblock();

					return;
				}

				global.OrderModel.modelUpdate();
			};

			global.OrderModel.couponError('');

			if ( url === undefined ) {
				console.warn('Не выбран тип сертификата');
				global.OrderModel.couponError('Не выбран тип сертификата');

				return;
			}

			if ( dataToSend.number === undefined || !dataToSend.number.length ) {
				console.warn('Не введен номер сертификата');
				global.OrderModel.couponError('Не введен номер сертификата');

				return;
			}

			$.ajax({
				type: 'POST',
				url: url,
				data: dataToSend,
				success: couponResponceHandler
			});
		},

		/**
		 * Обработка выбора пункта доставки
		 * 
		 * @param	{String}	id				Идентификатор
		 * @param	{String}	address			Адрес
		 * @param	{Number}	latitude		Широта
		 * @param	{Number}	longitude		Долгота
		 * @param	{String}	name			Полное имя
		 * @param	{String}	regime			Время работы
		 * @param	{Array}		products		Массив идентификаторов продуктов доступных в данном пункте
		 */
		selectPoint: function( data ) {
			console.info('point selected...');
			console.log(data.parentBoxToken);

			if ( data.parentBoxToken ) {
				console.log(global.OrderModel.createdBox[data.parentBoxToken]);
				global.OrderModel.createdBox[data.parentBoxToken].selectPoint.apply(global.OrderModel.createdBox[data.parentBoxToken],[data]);

				return false;
			}

			// Сохраняем приоритет методов доставок
			global.OrderModel.statesPriority = global.OrderModel.tmpStatesPriority;

			// Сохраняем выбранную приоритетную точку доставки
			global.OrderModel.choosenPoint(data.id);

			// Скрываем окно с выбором точек доставок
			global.OrderModel.showPopupWithPoints(false);

			// Разбиваем на подзаказы
			separateOrder( global.OrderModel.statesPriority );

			return false;
		},

		/**
		 * Выбор метода доставки
		 * 
		 * @param	{Object}	data			Данные о типе доставки
		 * @param	{String}	data.token		Выбранный способ доставки
		 * @param	{String}	data.name		Имя выбранного способа доставки
		 * @param	{Array}		data.states		Варианты типов доставки подходящих к этому методу
		 *
		 * @param	{String}	priorityState	Приоритетный метод доставки из массива
		 * @param	{Object}	checkedInputId	Ссылка на элемент input по которому кликнули
		 */
		chooseDeliveryTypes: function( data, event ) {
			var priorityState = data.states[0],
				checkedInputId = event.target.htmlFor;
			// end of vars

			if ( $('#'+checkedInputId).attr('checked') ) {
				return false;
			}

			global.OrderModel.deliveryTypesButton = checkedInputId;
			global.OrderModel.tmpStatesPriority = data.states;
			global.OrderModel.choosenDeliveryTypeId = data.id;

			// если для приоритетного метода доставки существуют пункты доставки, то пользователю необходимо выбрать пункт доставки, если нет - то приравниваем идентификатор пункта доставки к 0
			if ( global.OrderModel.orderDictionary.hasPointDelivery(priorityState) ) {
				global.OrderModel.popupWithPoints({
					header: data.description,
					points: global.OrderModel.orderDictionary.getAllPointsByState(priorityState)
				});

				global.OrderModel.showPopupWithPoints(true);

				return false;
			}

			// Сохраняем приоритет методов доставок
			global.OrderModel.statesPriority = global.OrderModel.tmpStatesPriority;

			// Сохраняем выбранную приоритетную точку доставки (для доставки домой = 0)
			global.OrderModel.choosenPoint(0);

			// Разбиваем на подзаказы
			separateOrder( global.OrderModel.statesPriority );

			return false;
		},

		/**
		 * Обновление данных
		 */
		modelUpdate: function() {
            var tID = null;

			console.info('обновление данных с сервера');

			var updateResponceHandler = function updateResponceHandler( res ) {
				renderOrderData(res);
				global.OrderModel.blockScreen.unblock();

				separateOrder( global.OrderModel.statesPriority );
			};

            tID = setTimeout(function() {
                clearTimeout(tID);
                $.ajax({
                    type: 'GET',
                    url: global.OrderModel.updateUrl,
                    success: updateResponceHandler
                });
            }, 1200);
		},

		/**
		 * Удаление товара
		 * 
		 * @param	{Object}	data	Данные удалямого товара
		 */
		deleteItem: function( data ) {
			console.info('удаление');

			global.OrderModel.blockScreen.block('Удаляем');

			var itemDeleteAnalytics = function itemDeleteAnalytics() {
					var products = global.OrderModel.orderDictionary.products;
						totalPrice = 0,
						totalQuan = 0,

						toKISS = {};
					// end of vars

					if ( !data.product ) {
						return false;
					}

					for ( var product in products ) {
						totalPrice += product[product].price;
						totalQuan += product[product].quantity;
					}

					toKISS = {
						'Checkout Step 1 SKU Quantity': totalQuan,
						'Checkout Step 1 SKU Total': totalPrice,
					};

					if ( typeof _kmq !== 'undefined' ) {
						_kmq.push(['set', toKISS]);
					}

					if ( typeof _gaq !== 'undefined' ) {
						_gaq.push(['_trackEvent', 'Order card', 'Item deleted']);
					}
				},

				deleteItemResponceHandler = function deleteItemResponceHandler( res ) {
					console.log( res );
					if ( !res.success ) {
						console.warn('не удалось удалить товар');
						global.OrderModel.blockScreen.unblock();

						return false;
					}

					// обновление модели
					global.OrderModel.modelUpdate();

					// запуск аналитики
					if ( typeof _gaq !== 'undefined' || typeof _kmq !== 'undefined' ) {
						itemDeleteAnalytics();
					}
				};
			// end of functions

			console.log(data.deleteUrl);

			$.ajax({
				type: 'GET',
				url: data.deleteUrl,
				success: deleteItemResponceHandler
			});

			return false;
		}
	};

	ko.applyBindings(global.OrderModel);
	/**
	 * ===  END ORDER MODEL ===
	 */

		/**
		 * Показ сообщений об ошибках
		 * 
		 * @param	{String}	msg		Сообщение об ошибке
		 * @return	{Object}			Deferred объект
		 */
	var showError = function showError( msg ) {
			var content = '<div class="popupbox width290">' +
					'<div class="font18 pb18"> '+msg+'</div>'+
					'</div>' +
					'<p style="text-align:center"><a href="#" class="closePopup bBigOrangeButton">OK</a></p>',
				block = $('<div>').addClass('popup').html(content),

				popupIsClose = $.Deferred();
			// end of vars
			
			block.appendTo('body');

			var errorPopupCloser = function() {
				block.trigger('close');
				block.remove();

				popupIsClose.resolve();
			};

			block.lightbox_me({
				centered:true,
				closeClick:false,
				closeEsc:false
			});

			block.find('.closePopup').bind('click', errorPopupCloser);

			return popupIsClose.promise();
		},

		/**
		 * Обработка ошибок в продуктах
		 */
		productError = {
			// Товар недоступен для продажи
			800: function( product ) {
				var msg = 'Товар '+product.name+' недоступен для продажи.',

					productErrorIsResolve = $.Deferred();
				// end of vars

				$.when(showError(msg)).then(function() {
					$.ajax({
						type:'GET',
						url: product.deleteUrl
					}).then(productErrorIsResolve.resolve);
				});

				return productErrorIsResolve.promise();

			},

			// Нет необходимого количества товара
			708: function( product ) {
				var msg = 'Вы заказали товар '+product.name+' в количестве '+product.quantity+' шт. <br/ >'+product.error.message,

					productErrorIsResolve = $.Deferred();
				// end of vars

				$.when(showError(msg)).then(function() {
					$.ajax({
						type:'GET',
						url: product.setUrl
					}).then(productErrorIsResolve.resolve);
				});

				return productErrorIsResolve.promise();
			}
		},

		/**
		 * Обработка ошибок в данных
		 *
		 * @param	{Object}	res		Данные о заказе
		 * 
		 * @param	{Object}	product	Данные о продукте
		 * @param	{Number}	code	Код ошибки
		 */
		allErrorHandler = function allErrorHandler( res ) {
			var product = null,

				productsWithError = [];
			// end of vars

			// Cоздаем массив продуктов содержащих ошибки
			for ( product in res.products ) {
				if ( res.products[product].error && res.products[product].error.code ) {
					productsWithError.push(res.products[product]);
				}
			}

			// Обрабатываем ошибки продуктов по очереди
			var errorCatcher = function errorCatcher( i, callback ) {
				var code = null;

				if ( i < 0 ) {
					console.warn('return');

					callback();
					return;
				}

				code = productsWithError[i].error.code;

				$.when( productError[code](productsWithError[i]) ).then(function() {
					var newI = i - 1;

					errorCatcher( newI, callback );
				});
			};

			errorCatcher(productsWithError.length - 1, function() {
				console.warn('1 этап закончен');
				if ( res.redirect ) {
					document.location.href = res.redirect;
				}
			});
		},

		/**
		 * Обработка полученных данных
		 * Создание словаря
		 * 
		 * @param	{Object}	res		Данные о заказе
		 */
		renderOrderData = function renderOrderData( res ) {
			if ( !res.success ) {
				console.warn('Данные содержат ошибки');
				console.log(res.error);
				allErrorHandler(res);

				return false;
			}

			console.info('Данные с сервера получены');

			global.OrderModel.orderDictionary = new OrderDictionary(res);

			if ( res.paypalECS ) {
				console.info('paypal true');
				global.OrderModel.paypalECS(true);
			}

			if ( res.cart && res.cart.sum ) {
				console.info('Есть первоначальная сумма корзины : '+res.cart.sum);
				global.OrderModel.cartSum = res.cart.sum;
			}

			global.OrderModel.deliveryTypes(res.deliveryTypes);
			global.OrderModel.prepareData(true);

			if ( global.OrderModel.paypalECS() &&
				window.docCookies.hasItem('chTypeBtn_paypalECS') && 
				window.docCookies.hasItem('chPoint_paypalECS') &&
				window.docCookies.hasItem('chTypeId_paypalECS') && 
				window.docCookies.hasItem('chStetesPriority_paypalECS') ) {

				console.info('PayPal ECS включен. Необходимо применить параметры из cookie');

				global.OrderModel.deliveryTypesButton = window.docCookies.getItem('chTypeBtn_paypalECS');
				global.OrderModel.choosenPoint( window.docCookies.getItem('chPoint_paypalECS') );
				global.OrderModel.choosenDeliveryTypeId = window.docCookies.getItem('chTypeId_paypalECS');
				global.OrderModel.statesPriority = JSON.parse( window.docCookies.getItem('chStetesPriority_paypalECS') );

				separateOrder( global.OrderModel.statesPriority );
			}
		},

		selectPointOnBaloon = function selectPointOnBaloon( event ) {
			console.log('selectPointOnBaloon');
			console.log(event);

			console.log($(this).data('pointid'));
			console.log($(this).data('parentbox'));

			global.OrderModel.selectPoint({
				id: $(this).data('pointid'),
				parentBoxToken: $(this).data('parentbox')				
			});

			return false;
		},

		/**
		 * Аналитика загрузки страницы orders/new
		 * 
		 * @param	{Object}	orderData		Данные о заказе
		 */
		analyticsStep_1 = function analyticsStep1( orderData ) {
			console.info('analyticsStep_1');

			var totalPrice = 0,
				totalQuan = 0,

				toKISS = {};
			// end of vars

			for ( var product in orderData.products ) {
				totalPrice += orderData.products[product].price;
				totalQuan += orderData.products[product].quantity;
			}

			toKISS = {
				'Checkout Step 1 SKU Quantity': totalQuan,
				'Checkout Step 1 SKU Total': totalPrice,
				'Checkout Step 1 Order Type': 'cart order'
			};

			console.log(toKISS)

			if ( typeof _gaq !== 'undefined' ) {
				_gaq.push(['_trackEvent', 'New order', 'Items', totalQuan]);
			}

			if ( typeof _kmq !== 'undefined' ) {
				_kmq.push(['record', 'Checkout Step 1', toKISS]);
			}
		};
	// end of functions

	$('body').on('click', '.shopchoose', selectPointOnBaloon);

	renderOrderData( serverData );

	analyticsStep_1( serverData );
}(this));