/**
 * Разбиение числа по разрядам
 *
 * @author	Zaytsev Alexandr
 * @param	{number|string}		число которое нужно отформатировать
 * @return	{string}			отформатированное число
 */
(function( global ) {
	global.printPrice = function( num ) {
		var str = (num || '').toString();

		return str.replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
	};
}(this));