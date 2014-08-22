;(function($) {
	var
		fadeStart = 50,
		fadeUntil = 635;
	// end of vars

	var
		scrollHandler = function() {
			var offset = $(document).scrollTop(),
				opacity = 1;
			// end of vars

			if( offset <= fadeStart ) {
				opacity = 0;
			}
			else if( offset <= fadeUntil ) {
				opacity = offset/fadeUntil;
			}

			if ( $('#slotsBack').length ) {
				$('#slotsBack').css({"filter": "alpha(opacity=" + opacity*100 + ")", "opacity": opacity});
			}
		};
	// end of functions

	$(document).scrollTop(0);
	$(window).bind('scroll', scrollHandler);

})(jQuery);