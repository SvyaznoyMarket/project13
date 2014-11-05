;(function($) {
	var seoList = $('.js-seo-list');

	seoList.find('.js-seo-list-item:lt(11)').css({'display' : 'inline-block'});

	seoList.each(function() {
		if ( $(this).children('.js-seo-list-item').length > 11 ) {
			$(this).append('<li class="bPopularSection_more js-seo-list-item-more">Показать ещё</li>');
		}
	});

	var seoListToggle = function seoListToggle() {
		var text = $(this).text();
		$(this).parent().children().toggleClass('seotext-show');
    	$(this).text(text == " Скрыть " ? " Показать ещё " : " Скрыть ");
	};

	seoList.find('.js-seo-list-item-more').on('click', seoListToggle);

})(jQuery);