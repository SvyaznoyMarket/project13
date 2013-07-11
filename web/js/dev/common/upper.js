/**
 * Кнопка наверх
 *
 * @requires	jQuery
 * @author		Zaytsev Alexandr
 */
;(function(){
	var upper = $('#upper');
	var trigger = false;//сработало ли появление языка

	var pageScrolling = function(){
		if (($(window).scrollTop() > 600)&&(!trigger)){
			//появление языка
			trigger = true;
			upper.animate({'marginTop':'0'},400);
		}
		else if (($(window).scrollTop() < 600)&&(trigger)){
			//исчезновение
			trigger = false;
			upper.animate({'marginTop':'-30px'},400);
		}
	};

	var goUp = function(){
		$(window).scrollTo('0px',400);
		return false;
	};

	$(window).scroll(pageScrolling);
	upper.bind('click',goUp);
}());