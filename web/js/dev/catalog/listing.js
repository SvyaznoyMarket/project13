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

    $body.on('click', '.js-listing-item, .js-jewelListing', function(e){

        var index = $(this).index(),
            href = $(e.target).find('a').attr('href') || $(e.target).closest('a').attr('href'),
            $breadcrumbs = $('.bBreadcrumbs__eItem a'),
            categoryTitle = $('.js-pageTitle').text(),
            businessUnit = '',
            hitcallback = typeof href == 'string' && href.indexOf('/product/') == 0 ? href : null;

        if ($breadcrumbs.length) {
            $.each($breadcrumbs, function(i,val){ businessUnit += $(val).text() + '_'});
        }

        businessUnit += categoryTitle;

        // лишние пробелы
        businessUnit = businessUnit.replace(/ +/g,' ');

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

    $('.js-listing-variation').click(function(e) {
        e.preventDefault();

        var
            $self = $(e.currentTarget),
            $variation = $self.closest('.js-listing-variation');

        $.ajax({
            type: 'GET',
            url: ENTER.utils.generateUrl('ajax.product.variation', {
                productUi: $self.closest('.js-listing-item').data('product-ui'),
                variationId: $variation.data('variation-id')
            }),
            success: function(res) {
                if (res.contentHtml) {
                    $variation.html(res.contentHtml);
                    $variation.find('.js-listing-variation-item').dropbox({
                        titleCssSelector: '.js-listing-variation-item-title',
                        openerCssSelector: '.js-listing-variation-item-opener',
                        openCssClass: 'open',
                        onChoose: function(value, $input) {
                            $input.closest('.js-listing-item').find('.jsBuyButton')
                                .attr('href', ENTER.utils.generateUrl('cart.product.setList', {
                                    products: [{ui: $input.data('product-ui'), quantity: '+1', up: 1}]
                                }))
                                .attr('data-product-id', $input.data('product-id'))
                                .attr('data-product-ui', $input.data('product-ui'));
                        }
                    });
                }
            }
        });
    });
});