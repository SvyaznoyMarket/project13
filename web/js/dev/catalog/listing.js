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
    $body.on('click', '.js-listing-item', function(e){

        var index = $(this).index(),
            href = $(e.target).find('a').attr('href') || $(e.target).closest('a').attr('href'),
            $breadcrumbs = $('.bBreadcrumbs__eItem a'),
            categoryTitle = $('.js-pageTitle').text(),
            businessUnit = '';

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
            label: index + ''
        }])

    });

	$('.js-slider').goodsSlider({
		onLoad: function(goodsSlider) {
			ko.applyBindings(ENTER.UserModel, goodsSlider);
		}
	});

    $body.dropbox({
        cssSelectors: {
            container: '.js-listing-variations-dropbox-container',
            opener: '.js-listing-variations-dropbox-opener',
            content: '.js-listing-variations-dropbox-content',
            item: '.js-listing-variations-dropbox-item'
        },
        htmlClasses: {
            container: {
                opened: 'filter-btn-box--open'
            }
        },
        onClick: function(e) {
            $.ajax({
                type: 'GET',
                url: e.$item.attr('data-url'),
                success: function(res) {
                    if (!res.product) {
                        return;
                    }

                    var $template = $('#listing_item_tmpl');

                    $(Mustache.render($template.html(), res.product, $.mapObject($template.data('partial'), function(cssSelector) {
                        return $(cssSelector).html();
                    }))).replaceAll(e.$item.closest('.js-listing-item'));

                    $('.js-listing').each(function() {
                        ko.cleanNode(this);
                        ko.applyBindings(ENTER.UserModel, this);
                    });
                }
            });
        }
    });
});