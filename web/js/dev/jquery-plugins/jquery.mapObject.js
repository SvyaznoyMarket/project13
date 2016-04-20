(function($) {
	/**
	 * @param {Object} object
	 * @param {Function} callback
	 * @returns {Object} В отличии от jQuery.map(object, ...) возвращает объект, а не массив
	 */
    $.mapObject = function(object, callback) {
		var newObject = {};
		for (var key in object) {
			if (object.hasOwnProperty(key)) {
				newObject[key] = callback(object[key], key);
			}
		}

		return newObject;
    };
})(jQuery);