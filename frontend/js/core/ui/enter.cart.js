;(function(){

    var module = {};

    module.addProduct = function(product) {
        if (module.cart().some(function(p){ return p.id == product.id })) {
            module.setProductQuantity(product.id, product.quantity)
        } else {
            product.quantity = ko.observable(product.quantity);
            module.cart.unshift(product);
        }
    };

    module.setProductQuantity = function (id, quantity) {
        module.cart().forEach(function(product){
            if (product.id == id) {
                product.quantity(quantity);
                return false;
            }
        })
    };

    module.deleteProduct = function(product) {
        if (!product.deleteUrl) return;
        $.ajax({
            url: product.deleteUrl,
            success: function(data) {
                if (data.product) module.cart.remove(function(product) { return product.id == data.product.id })
            }
        })
    };

    modules.define('enter.cart', ['jQuery', 'ko', 'enter.user'], function(provide, $, ko, userModule){

        module.cart = ko.observableArray();

        module.getProductQuantity = ko.computed(function(){
            return module.cart().reduce(function(prev, product) {
                return prev + product.quantity();
            }, 0)
        });

        // добавляем товары, которые уже есть в корзине
        if (userModule.cartProducts) $.each(userModule.cartProducts, function (i, product) {
            module.addProduct(product);
        });

        // биндинги на все элементы
        $('.jsKnockoutCart').each(function () {
            ko.applyBindings(module, this)
        });

        provide(module);
    })

})();