/**
 * Подсказки к характеристикам
 *
 * @author	Zaytsev Alexandr
 * @requires jQuery
 */
(function(){
	var hintShower = function(){
		var hintPopup = $('.bHint_ePopup');
		var hintLnk = $('.bHint_eLink');
		var hintCloseLnk = $('.bHint_ePopup .close');

		var hintShow = function(){
			hintPopup.hide();
			$(this).parent().find('.bHint_ePopup').fadeIn(150);
			return false;
		};

		var hintClose = function(){
			hintPopup.fadeOut(150);
			return false;
		};


		hintLnk.bind('click', hintShow);

		hintCloseLnk.bind('click', hintClose);
	};


	$(document).ready(function() {
		if ($('.bHint').length){
			hintShower();
		}
	});
}());