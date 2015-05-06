/**
 * Кнопка наверх
 *
 * @requires	jQuery
 * @author		Zaytsev Alexandr
 */
;(function(){
	var
		$body = $('body'),
		$window = $(window),
		$upper = $('.js-upper'),
		visible = false,
		offset = $upper.data('offset'),
		showWhenFullCartOnly = $upper.data('showWhenFullCartOnly');

	if (typeof offset == 'string') {
		var $offset = $(offset);
		if ($offset.length) {
			offset = $offset.offset().top;
		}
	}

	function checkScroll() {
		var cartLength = ENTER.UserModel.cart().length;
		if (!visible && $window.scrollTop() > offset && (!showWhenFullCartOnly || cartLength)) {
			//появление
			visible = true;
			$upper.animate({marginTop: '0'}, 400);
		} else if (visible && ($window.scrollTop() < offset || showWhenFullCartOnly && !cartLength)) {
			//исчезновение
			visible = false;
			$upper.animate({marginTop: '-55px'}, 400);
		}
	}

	$upper.bind('click', function() {
		$window.scrollTo('0px',400);
		return false;
	});

	$window.scroll(checkScroll);

	$body.on('closeBuyInfo showBuyInfo', function(){
		checkScroll();
	});

	checkScroll();
}());