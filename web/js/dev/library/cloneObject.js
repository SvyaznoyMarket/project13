;(function( ENTER ) {
	var utils = ENTER.utils;

	utils.cloneObject = function cloneObject( obj ) {
		if  ( obj == null || typeof( obj ) !== 'object' ) {
			return obj;
		}

		var temp = {},
			key;

		for ( key in obj ) {
			if ( obj.hasOwnProperty(key) ) {
				temp[key] = cloneObject(obj[key]);
			}
		}

		return temp;
	};
}(window.ENTER));