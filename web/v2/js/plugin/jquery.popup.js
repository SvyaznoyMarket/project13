; (function( $ ){

    $.fn.enterPopup = function( params ) {
    	console.log("popup");

  		return this.each(function() {
  			var options = $.extend(
							{},
							$.fn.enterPopup.defaults,
							params),
				$self = $(this),
				$overlay = $('<div class="popupOverlay"></div>');
			// end of vars

            showPopup();
            $(window).bind('load resize', propertyPopup);

            function showPopup() {
                $('body').append($overlay);
                $overlay.fadeIn(300);
                $self.fadeIn(300);

                propertyPopup();
            };

            function propertyPopup() {
                var widthBody = $('body').width(),
                    heightHtml = $('body').height(),
                    heightPopup = $self.height()-20;

                $self.css({'width' : widthBody - 40, 'margin-top' :  -heightPopup/2}).css(options.popupCSS);;
            }

            function closePopup() {
                $overlay.fadeOut(300, function(){ $overlay.remove() });
                $self.fadeOut(300);
            };

            if (options.closeClick) {
                $overlay.click(function(e) { closePopup(); e.preventDefault; });
                $self.find(options.closeSelector).click(function(e) { closePopup(); e.preventDefault; });
            };

            if(options.closeBtn) {
                $self.find(options.closeSelector).css({'display' : 'block'});
            };
  		});
    };

    $.fn.enterPopup.defaults = {
    	// закрыть popup
        closeSelector: ".popupBox_close",
        closeClick: true,
        closeBtn: true,

        // поведение
        showOverlay: true,

	    // стили
	    popupCSS: {top: '50%'}
	};	

})( jQuery );
