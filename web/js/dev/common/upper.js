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
		var cartLength = ENTER.UserModel ? ENTER.UserModel.cart().products().length : 0;
		if (!visible && $window.scrollTop() > offset && (!showWhenFullCartOnly || cartLength)) {
			//появление
			visible = true;
			$upper.fadeIn(400);
		} else if (visible && ($window.scrollTop() < offset || showWhenFullCartOnly && !cartLength)) {
			//исчезновение
			visible = false;
			$upper.fadeOut(400);
		}
	}

	$upper.bind('click', function() {
		$window.scrollTo('0px',400);
		return false;
	});

	$window.scroll(checkScroll);

	$body.on('closeFullFixedUserBar openFullFixedUserBar', function(){
		checkScroll();
	});

	checkScroll();
}());