/**
 * Кнопка наверх
 *
 * @requires	jQuery
 * @author		Zaytsev Alexandr
 */
;(function(){
	var upper = $('#upper');
		trigger = false,	//сработало ли появление языка

		pageScrolling = function(){
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
		},

		goUp = function(){
			$(window).scrollTo('0px',400);
			return false;
		};
	//end of vars

	$(window).scroll(pageScrolling);
	upper.bind('click',goUp);
}());