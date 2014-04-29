/**
 * Работа с числами
 * 
 * @requires ENTER.utils
 * @author	Zaytsev Alexandr
 *
 * @param	{Object}	ENTER	Enter namespace
 */
;(function( ENTER ) {

	console.info('utils.numMethods module init');

	var 
		utils = ENTER.utils;
	// end of vars
	
	utils.numMethods = (function() {
		var
			/**
			 * Суммирование чисел с плавающей точкой
			 * WARNING: только для чисел до 2 знака после запятой
			 * 
			 * @param	{String}	a	Первое число
			 * @param	{String}	b	Второе число
			 * 
			 * @return	{String}		Результат сложения
			 */
			sumDecimal = function sumDecimal( a, b ) {

				var 
					overA = ( ( parseFloat(a).toFixed(2) ).toString() ).replace(/\./,''),
					overB = ( ( parseFloat(b).toFixed(2) ).toString() ).replace(/\./,''),
					overSum = (parseInt(overA, 10) + parseInt(overB, 10)).toString(),
					firstNums = overSum.substr(0, overSum.length - 2),
					lastNums = overSum.substr(overSum.length - 2),
					res;
				// end of vars

				if ( lastNums === '00' ) {
					res = firstNums;
				}
				else {
					res = firstNums + '.' + lastNums;
				}

				return res;
			};
		// end of functions


		return {
			sumDecimal: sumDecimal
		};
	}());

}(window.ENTER));