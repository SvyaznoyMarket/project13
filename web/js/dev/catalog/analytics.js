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

        // клик по кнопке "Бренды и параметры"
        $body.on('click', '.js-category-filter-otherParamsToggleButton', function(e) {
            $body.trigger('trackGoogleEvent', {
                category: 'filter',
                action: 'cost_sale',
                label: ('string' === typeof sliderData.category) ? sliderData.category.name : ''
            });
        });

        // клик по фильтру "Цена"
        $body.on('mousedown', '.js-category-v1-filter-element-price', function(e) {
            $body.trigger('trackGoogleEvent', {
                category: 'filter',
                action: 'cost_range',
                label: ('string' === typeof sliderData.category) ? sliderData.category.name : ''
            });
        });

        // клик по другим фильтрам
        $body.on('mousedown', '.js-category-filter-param', function(e) {
            var $el = $(this);

            $body.trigger('trackGoogleEvent', {
                category: 'filter',
                action: 'other_' + $el.find('span').text(),
                label: ('string' === typeof sliderData.category) ? sliderData.category.name : ''
            });
        });
    }
});
