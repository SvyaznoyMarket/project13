(function($) {
	var popups = [];

    $.fn.enterLightboxMe = function(options) {
		// Значения по умолчанию
		options = $.extend(true, {

		}, options);

		// Чтобы прокрутка отключалась и тогда, когда попап уменьшается в размере
		if (options.preventScroll) {
			$('body').css('overflow', 'hidden');
		}

		this.each(function() {
			popups.push(this);
		});

		return this.lightbox_me(options);
    };

	$.enterLightboxMe = {};
	$.enterLightboxMe.closeAll = function() {
		for (var i = 0; i < popups.length; i++) {
			$(popups[i]).trigger('close');
		}

		popups = [];
	};
})(jQuery);