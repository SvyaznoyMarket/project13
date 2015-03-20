/**
 * Разбиение числа по разрядам
 *
 * @author	Zaytsev Alexandr
 * @param	{number|string}		число которое нужно отформатировать
 * @return	{string}			отформатированное число
 */
(function( global ) {
	global.printPrice = function(price) {
		price = String(price);
		price = price.replace(',', '.');
		price = price.replace(/\s/g, '');
		price = String(Number(price).toFixed(2));
		price = price.split('.');

		if (price[0].length >= 5) {
			price[0] = price[0].replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1&thinsp;'); // TODO: заменить &thinsp; на соответствующий unicode символ
		}

		if (price[1] == 0) {
			price = price.slice(0, 1);
		}

		return price.join('.');
	};
}(this));