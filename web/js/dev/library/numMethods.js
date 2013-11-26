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

		/**
		 * Суммирование чисел с плавающей точкой
		 * WARNING: только для чисел до 2 знака после запятой
		 * 
		 * @param	{String}	a	Первое число
		 * @param	{String}	b	Второе число
		 * 
		 * @return	{String}		Результат сложения
		 */
		sumDecimal 	= function sumDecimal( a, b ) {

			console.group('sumDecimal');

			var 
				overA = ( ( parseFloat(a).toFixed(2) ).toString() ).replace(/\./,''),
				overB = ( ( parseFloat(b).toFixed(2) ).toString() ).replace(/\./,''),
				overSum = (parseInt(overA) + parseInt(overB)).toString(),
				firstNums = overSum.substr(0, overSum.length - 2),
				lastNums = overSum.substr(-2),
				res;
			// end of vars

			console.log(a);
			console.log(overA);
			console.log(b);
			console.log(overB);
			console.log(overSum);

			if ( lastNums == '00' ) {
				res = firstNums
			}
			else {
				res = firstNums + '.' + lastNums;
			}

			console.log(res);

			console.groupEnd();

			return res;
		};


		return {
			sumDecimal: sumDecimal
		};
	}());

}(window.ENTER));