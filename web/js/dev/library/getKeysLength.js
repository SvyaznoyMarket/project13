/**
 * Получение количества свойств объекта
 */
;(function ( global ) {
	global.getKeysLength = function getKeysLength( obj ) {
		var len = 0;
		
		for ( var i in obj ) {
			if ( !obj.hasOwnProperty(i) ){
				continue;
			}
			
			len++;
		}
		
		return len;
	};
}(this));