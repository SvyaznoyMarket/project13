$(document).ready(function(){

    var menuItems = $('.menu-item'),
		subscribeBtn = $('.subscribe-form__btn');

    // Выделение активного пункта в боковом меню
    $.each(menuItems, function() {
        var $this = $(this);
        if ($this.find('a').attr('href') == location.pathname ) {
            $this.addClass('active');
            return false;
        }
    });

	if ( subscribeBtn.length ) {
		var
			form = $('.subscribe-form'),
			input = $('.subscribe-form__email') || form.find('input[name="email"]'),
			channel = form.find('input[name="channel"]').val() || 1;
		// end of vars
		
		var subscribing = function subscribing() {
			var url = $(this).data('url'),
				email = input.val(),
				error_msg = $(this).data('error-msg'),
				data = {};
			// end of vars

			if ( email && email.search('@') !== -1 ) {
				data = {email: email, channel: channel};

				if ('undefined' !== typeof(error_msg) && error_msg) {
					data.error_msg = error_msg;
				}

				$.post(url, data, function(res){
                    var errorDiv = form.find('.formErrorMsg');

					if ( res.hasOwnProperty('data') && undefined != typeof(res.data) ) {
						form.html('<div class="subscribe-form__title">' + res.data + '</div>');
					}

                    if ( res.error ) {
                        if (errorDiv.length == 0) {
                            form.append($('<div class="formErrorMsg" style="margin-left: 135px; clear: both; color: red;"/>').text(res.error));
                        } else {
                            errorDiv.text(res.error).show();
                        }
                        form.find('.formErrorMsg').delay(2000).slideUp(600);
                    }

					if( !res.success ) {
						return false;
					}

					window.docCookies.setItem('subscribed', channel, 157680000, '/');

					if (typeof(_gaq) != 'undefined') {
						if (location.pathname == '/enter-friends') {
							_gaq.push(['_trackEvent', 'subscription', 'subscribe_enter_friends']);
						} else if (location.pathname == '/special_offers') {
							_gaq.push(['_trackEvent', 'subscription', 'subscribe_special_offers']);
						}
					}
				});
			}
			else {
				input.addClass('mError');
			}
			return false;
		};

		subscribeBtn.bind('click', subscribing);

	}



	/**
	 * Бесконечный скролл
	 */
    $('.js-scms-changeRegion').on('click',function(){
		$('.jsChangeRegion').click();
    });

    $(document).ready(function(){
        $('.subscribe-block[data-type="background"]').each(function(){
            var $this = $(this),
                $scrolled = $this.find('.scrolled-bg'),
                $window = $(window),
                lastScrollTop = $(window).scrollTop(),
                delta = 0;


                $window.scroll(function() {

                    var st = $(window).scrollTop();
                    delta = lastScrollTop - st;

                    lastScrollTop = st;

                    if ( ($window.scrollTop() + $window.height()) >= $this.offset().top ){


                        var prevCoords = $scrolled.css('backgroundPosition').split(' '),
                            prevY = parseInt( prevCoords[1] ),
                            coords = 'center '+ (prevY + (delta / $this.data('speed')) ) + 'px';

                        $scrolled.css({ 'background-position': coords });
                    }

                });

        });
    });
});
