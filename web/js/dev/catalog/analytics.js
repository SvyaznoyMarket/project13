$(function() {
    var
        $body = $('body'),

        sliderData = $('#jsSlice').data('value')
    ;

    if (sliderData && (true === sliderData.isSale)) {
        // клик по ссылке на категорию
        $body.on('click', '.js-productCategory-link', function() {
            var
                $el = $(this)
            ;

            $body.trigger('trackGoogleEvent', {
                category: 'slices_sale',
                action: 'category',
                label: $el.find('[data-type="name"]').text()
            });
        });

        // клик по ссылке на товар
        $body.on('click', '.js-listing-item', function() {
            $body.trigger('trackGoogleEvent', {
                category: 'slices_sale',
                action: 'product',
                label: ''
            });
        });

        // клик по кнопке "Купить"
        $body.on('click', '.jsBuyButton', function(e) {
            e.stopPropagation();

            $body.trigger('trackGoogleEvent', {
                category: 'slices_sale',
                action: 'basket',
                label: ''
            });
        });
    }
});
