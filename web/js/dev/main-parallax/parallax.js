;(function($) {
	var
		slotsWrap = $('#slotsWrapWrapper');
	// end of vars

		parallax = {
			"init": function() {
				var
					bodyParallax = $("body.parallax");

				bodyParallax.find(".top-parallax-block, .bannersbox").css("position", "fixed");
				bodyParallax.find(".center-parallax-block").css({"position": "absolute", "top": "635px", "z-index": 1000, "width": "100%"});
				bodyParallax.find(".center-parallax-block #slotsBack").css({"top": "0px !important", "background": "#000000 url(img/slot_background.png) no-repeat bottom", "opacity": 0});
				bodyParallax.find(".center-parallax-block #slotsBottomLine").css({"background-position": "center", "background-repeat": "no-repeat"});
				bodyParallax.find(".bottom-parallax-block").css({"position": "relative", "top": "518px", "background": "#000000", "padding-bottom": "20px"});
				bodyParallax.find(".bottom-parallax-block .footer__main").css({"background": "black"});
				bodyParallax.find(".bottom-parallax-block .footer__main #mainPageFooter .copy").css({"background": "black"});
				bodyParallax.find(".bottom-parallax-block .gameBandit").css({"display": "block"});

				parallax.scrollOpacityInit();
			},

			"scrollOpacityInit": function() {
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
			}
		};
	// end of vars

	if ( !slotsWrap.length ) {
		return;
	}

	// когда отстроили виджет инициализируем параллакс на главной
	slotsWrap.on('slotsInitialized', parallax.init);

})(jQuery);