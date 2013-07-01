/**
 * Разбиение числа по разрядам
 *
 * @author	Zaytsev Alexandr
 * @param	{number|string}	число которое нужно отформатировать
 * @return	{string}			отформатированное число
 */
var printPrice = function(num){
	var str = num+'';
	return str.replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
};