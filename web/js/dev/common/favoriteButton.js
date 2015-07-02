$(function() {
	var
		showClass = 'topbarfix_cmpr_popup-show',
		timer;

    $('body').on('click', '.jsFavoriteLink', function(e){
        var
            $el = $(e.currentTarget),
            xhr = $el.data('xhr')
        ;

        console.info({'.jsFavoriteLink click': $el});

        if ($el.data('ajax')) {
            e.stopPropagation();

            try {
                if (xhr)  xhr.abort();
            } catch (error) { console.error(error); }

            xhr = $.post($el.attr('href'))
                .done(function(response) {
                    $('body').trigger('updateWidgets', {
                        widgets: response.widgets,
                        callback: $el.attr('href').indexOf('delete-product') !== -1 ? null : function() {
							var
								userBarType = $(window).scrollTop() > ENTER.userBar.userBarStatic.offset().top + 10 ? 'fixed' : 'static',
								$userbar = userBarType == 'fixed' ? ENTER.userBar.userBarFixed : ENTER.userBar.userBarStatic,
								$popup = $('.js-favourite-popup', $userbar);

							$('.js-favourite-popup-closer', $popup).click(function() {
								$popup.removeClass(showClass);
							});

							$('.js-topbarfixLogin-opener, .js-topbarfixNotEmptyCart', $userbar).mouseover(function() {
								$popup.removeClass(showClass);
							});

							$('html').click(function() {
								$popup.removeClass(showClass);
							});

							$popup.click(function(e) {
								e.stopPropagation();
							});

							$(document).keyup(function(e) {
								if (e.keyCode == 27) {
									$popup.removeClass(showClass);
								}
							});

							if (timer) {
								clearTimeout(timer);
							}

							timer = setTimeout(function() {
								$popup.removeClass(showClass);
							}, 2000);

							if (userBarType == 'fixed') {
								ENTER.userBar.show();
							}

							$popup.addClass(showClass);
						}
					});
                })
                .always(function() {
                    $el.data('xhr', null);
                })
            ;
            $el.data('xhr', xhr);

            e.preventDefault();
        }
    });
});