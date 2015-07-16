+function(){
    modules.define('enter.buyButton', ['jQuery', 'enter.cart'], function(provide, $, cart) {

        var module = {};

        // Инициализация
        module.init = function(elem) {
            var $button = $(elem),
            url = $button.attr('href');
            // closure
            (function ($button, url) {
                $button.on('click', function (e) {
                    e.preventDefault();
                    module.buy(url)
                })
            })($button, url)
        };

        // Покупка
        module.buy = function(url) {
            // Добавление в корзину на сервере
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    console.log('Added to cart', data);
                    if (data.product) {
                        cart.addProduct(data.product);
                    }
                },
                error: function() {
                    console.error('Error adding to cart')
                }
            });
        };

        provide(module);
    });
}();
