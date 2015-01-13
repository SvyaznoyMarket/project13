/**
 * Разбиение числа по разрядам
 *
 * @author	Zaytsev Alexandr
 * @param	{number|string}		число которое нужно отформатировать
 * @return	{string}			отформатированное число
 */
(function( global ) {
	global.printPrice = function( num ) {
		num = num + '';
		if ((parseInt(num) + '').length >= 5) {
			num = num.replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1&thinsp;');
		}

		return num;
	};
}(this));