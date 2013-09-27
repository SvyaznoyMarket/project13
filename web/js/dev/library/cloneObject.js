;(function( ENTER ) {
	var utils = ENTER.utils;

	utils.cloneObject = function cloneObject( obj ) {
		var copy,
			attr,
			i,
			len;
		
		// Handle the 3 simple types, and null or undefined
		if ( obj == null || typeof obj !== 'object' ) {
			return obj;
		}
		
		// Handle Date
		if ( obj instanceof Date ) {
			copy = new Date();
			copy.setTime(obj.getTime());

			return copy;
		}
		
		// Handle Array
		if ( obj instanceof Array ) {
			copy = [];
			
			for ( i = 0, len = obj.length; i < len; i++ ) {
				copy[i] = cloneObject(obj[i]);
			}
			
			return copy;
		}
		
		// Handle Object
		if ( obj instanceof Object ) {
			copy = {};
			
			for ( attr in obj ) {
				if ( obj.hasOwnProperty(attr) ) {
					copy[attr] = cloneObject(obj[attr]);
				}
			}
			
			return copy;
		}
	};
}(window.ENTER));