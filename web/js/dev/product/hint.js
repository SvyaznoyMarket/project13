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

		var hintAnalytics = function(data){
			if (typeof(_gaq) !== 'undefined') {
				_gaq.push(['_trackEvent', 'Hints', data.hintTitle, data.url]);
			}
		};

		var hintShow = function(){
			hintPopup.hide();
			$(this).parent().find('.bHint_ePopup').fadeIn(150);

			var analyticsData = {
				hintTitle: $(this).html(),
				url: window.location.href
			};
			hintAnalytics(analyticsData);

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