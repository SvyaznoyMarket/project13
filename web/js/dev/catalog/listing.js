$(function() {
	var $body = $('body');

	$body.on('click', '.js-listing-item-img, .js-listing-item-viewButton', function(e) {
		var
			$target = $(e.currentTarget),
			$item = $target.closest('.js-listing-item');

		var action = 'listing';
		if ($item.data('is-slot')) {
			action = 'listing-marketplace-slot';
		} else if ($item.data('is-only-from-partner')) {
			action = 'listing-marketplace';
		}

		$body.trigger('trackGoogleEvent', ['View', action, $target.is('.js-listing-item-img') ? 'image' : 'button']);
	});

    // Клик по элементу листинга
    $body.on('click', '.js-listing-item, .js-jewelListing', function(e){

        var index = $(this).index(),
            href = $(e.target).find('a').attr('href') || $(e.target).closest('a').attr('href'),
            $breadcrumbs = $('.bBreadcrumbs__eItem a'),
            categoryTitle = $('.js-pageTitle').text(),
            businessUnit = '',
            hitcallback = typeof href == 'string' && href.indexOf('/product/') == 0 && !$(this).find('.js-orderButton').hasClass('jsOneClickButton') ? href : null;

        if ($breadcrumbs.length) {
            $.each($breadcrumbs, function(i,val){ businessUnit += $(val).text() + '_'});
        }

        businessUnit += categoryTitle;

        // лишние пробелы
        businessUnit = businessUnit.replace(/ +/g,' ');

        ENTER.utils.analytics.addProduct(this, {
            position: index
        });

        ENTER.utils.analytics.setAction('click', {
            list: location.pathname.indexOf('/search') === 0 ? 'Search results' : 'Catalog'
        });

        if (businessUnit && href) $body.trigger('trackGoogleEvent', [{
            category: 'listing_position',
            action: businessUnit,
            label: index + '',
            hitCallback: hitcallback
        }])

    });

	$('.js-slider').goodsSlider({
		onLoad: function(goodsSlider) {
			ko.applyBindings(ENTER.UserModel, goodsSlider);
		}
	});
});