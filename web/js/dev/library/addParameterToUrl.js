/**
 * Добавить новый параметр в URL
 * 
 * @param	{String}	key		Ключ
 * @param	{String}	value	Значение
 * @return	{String}			Сформированный URL
 */
var UpdateUrlString = function(key, value) {
	var url = this.toString();
	var re = new RegExp("([?|&])" + key + "=.*?(&|#|$)(.*)", "gi");

	if (re.test(url)) {
		if (typeof value !== 'undefined' && value !== null)
			return url.replace(re, '$1' + key + "=" + value + '$2$3');
		else {
			return url.replace(re, '$1$3').replace(/(&|\?)$/, '');
		}
	}
	else {
		if (typeof value !== 'undefined' && value !== null) {
			var separator = url.indexOf('?') !== -1 ? '&' : '?',
				hash = url.split('#');
			url = hash[0] + separator + key + '=' + value;
			if (hash[1]) {
				url += '#' + hash[1];
			}
			return url;
		}
		else{
			return url;
		}
	}
};
String.prototype.addParameterToUrl = UpdateUrlString;