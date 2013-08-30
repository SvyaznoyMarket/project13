;(function ( global ) {
	global.cloneObject = function cloneObject( obj ) {
		if  ( obj == null || typeof( obj ) !== 'object' ) {
			return obj;
		}
		var temp = {};

		for ( var key in obj ) {
			temp[key] = cloneObject(obj[key]);
		}

		return temp;
	};
}(this));