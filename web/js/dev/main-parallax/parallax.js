;(function($) {
	var
		slotsWrap = $('#slotsWrapWrapper'),

		parallax = {
			init: function() {
				var bodyParallax = $("body.parallax");

				bodyParallax.find(".top-parallax-block, .bannersbox").css("position", "fixed");
				bodyParallax.find(".center-parallax-block").css({"position": "absolute", "top": "635px", "z-index": 1000, "width": "100%"});
				bodyParallax.find(".bottom-parallax-block").css({"position": "relative", "top": "518px", "background": "#000000", "padding-bottom": "20px"});
				bodyParallax.find(".bottom-parallax-block .footer__main").css({"background": "black"});
				bodyParallax.find(".bottom-parallax-block .footer__main #mainPageFooter .copy").css({"background": "black"});
				bodyParallax.find(".center-parallax-block .gameBandit").css({"display": "block"});

				parallax.scrollOpacityInit();
			},

			scrollOpacityInit: function() {
				var
					scrollHandler = function() {
						var
							fadeStart = 0,
							fadeUntil = $("body.parallax .top-parallax-block").height() || 635,
							offset = $(document).scrollTop(),
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