;(function(){
	(function() {
		var clone = docCookies.setItem;
		docCookies.setItem = function(){
			var args = Array.prototype.slice.call(arguments); // making true array
			if (typeof args[4] == 'undefined') args[4] = '.' + /[A-Za-z0-9]+\.[A-Za-z0-9]+$/.exec(window.location.hostname)[0]; // set domain to ".enter.ru" or ".enter.loc" or ".ent3.ru", etc
			return clone.apply(this, args);
		};
	})();

	(function() {
		var clone = docCookies.removeItem;
		docCookies.removeItem = function(){
			var args = Array.prototype.slice.call(arguments); // making true array
			if (typeof args[2] == 'undefined') args[2] = '.' + /[A-Za-z0-9]+\.[A-Za-z0-9]+$/.exec(window.location.hostname)[0]; // set domain to ".enter.ru" or ".enter.loc" or ".ent3.ru", etc
			return clone.apply(this, args);
		};
	})();
}());