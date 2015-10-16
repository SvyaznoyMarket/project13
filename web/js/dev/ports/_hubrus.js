ANALYTICS.hubrusJS = function() {

    var productData = $('.hubrusProductData').data('value'),
        hubrusDataDiv = $('.hubrusData'),
        hubrusProperty = hubrusDataDiv.data('property'),
        hubrusValue = hubrusDataDiv.data('value'),
        lsCacheKey = 'hubrus_viewed_items',
        viewedItems, hubrusVars = {};

    // Если есть данные по продукту на странице (пользователь открыл страницу продукта)
    if (productData) {
        viewedItems = lscache.get(lsCacheKey) ? lscache.get(lsCacheKey) : [];
        // проверка на уникальность
        if ($.grep(viewedItems, function(p){ return productData.id == p.id }).length == 0) viewedItems.unshift(productData);
        hubrusVars.viewed_items = viewedItems.splice(0,10);
        lscache.set(lsCacheKey, hubrusVars.viewed_items);
    }

    if (hubrusProperty && hubrusValue) {
        hubrusVars[hubrusProperty] = hubrusValue
    }

    /** Событие добавления в корзину */
    $body.on('addtocart removeFromCart', function hubrusAddToCart(event) {
        var	smpix = window.smartPixel1,
            type = event.type;

        if (!smpix || typeof smpix['trackState'] !== 'function') return;

        smpix.trackState(type == 'addtocart' ? 'add_to_cart' : 'remove_from_cart',
            {
                cart_items: $.map(ENTER.UserModel.cart().products(), function(product){
                    return {
                        id: product.id,
                        price: product.price,
                        category: product.rootCategory ? product.rootCategory.id : 0
                    }
                })
            }
        );
    });

    $body.on('click', '.jsOneClickButton', function(){
        var smpix = window.smartPixel1,
            product = $('#jsProductCard').data('value'),
            categoryId = 0;

        if (!smpix || typeof smpix['trackState'] !== 'function' || !product) return;
        if ($.isArray(product.category) && product.category.length > 0) categoryId = product.category[product.category.length - 1].id;

        smpix.trackState('oneclick',
            { oneclick_item: [{
                id: product.id,
                price: product.price,
                category: categoryId
            }]
            });

    });

    window.smCustomVars = hubrusVars;

    $LAB.script('http://pixel.hubrus.com/containers/enter/dist/smartPixel.min.js');
};