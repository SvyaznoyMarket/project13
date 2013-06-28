/**
 * Проверка является ли строка e-mail
 *
 * @author	Zaytsev Alexandr
 * @return	{Boolean} 
 */
function isTrueEmail(){
	var t = this.toString(),
		re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(t);
}
String.prototype.isEmail = isTrueEmail; // добавляем методом для всех строк