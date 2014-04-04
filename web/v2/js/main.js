;(function(app, window, $, _, mustache, backbone, undefined) {
    app.Event = _.clone(Backbone.Events);

    app.Event.on('product:change', function(data) {
        var product = data.product;
        if (!product instanceof app.Model.Product) {
            console.error('app.Event.product:change', 'Неверный параметр product', data);
            return;
        }

        app.model.cart.addProduct(product);
    });

    app.Model = {};
    app.Collection = {};
    app.View = {};

    // Model & Collection
    app.Model.Cart = backbone.Model.extend({
        initialize: function(config) {
            this.set('products', new app.Collection.Product(config.product || []));
        },
        addProduct: function(product) {
            console.info('app.Model.Cart.addProduct', product);
            this.get('products').create(product, {
                wait: true,
                //merge: true,
                type: 'POST',
                url: product.get('cart').get('setUrl'),
                success: function(response) {
                    //console.info(response);
                }
            });
        }
    });
    app.Collection.Cart = {};
    app.Collection.Cart.Product = backbone.Collection.extend({
        model: app.Model.Product,
        initialize: function() {}
    });

    app.Model.Product = backbone.Model.extend({
        initialize: function(data) {
            this.set('cart', new app.Model.Product.Cart(data.cart || {}));
        },
        parse: function(response) {
            // TODO: сделать cart моделью, возможно использовать плагин
            return response.result;
        }
    });
    app.Model.Product.Cart = backbone.Model.extend({
        validate: function(attrs) {
            console.info('app.Model.Product.Cart.validate', attrs, typeof attrs.quantity);
            if (!_.isFinite(attrs.quantity) || attrs.quantity <= 0) {
                var error = {message: 'Количество должно быть большим нуля'};
                console.warn('app.Model.Product.Cart.validate', error);

                return error;
            }
        }
    });
    app.Collection.Product = backbone.Collection.extend({
        model: app.Model.Product
    });

    // View
    app.View.Cart = {};
    app.View.Cart.BuyButton = backbone.View.extend({
        events: {
            'click .js-link': 'setProduct'
        },
        initialize: function() {
            this.template = $('#tplCartBuyButton').html();

            //this.model.get('cart').on('change', this.render, this);

            //this.render();
        },
        render: function() {
            console.info('app.View.Cart.BuyButton.render', this.template, this.model.get('buyButton').templateData);
            this.$el.html(mustache.render(this.template, this.model.get('buyButton').templateData));

            return this;
        },
        setProduct: function(e) {
            console.info('app.View.Cart.BuyButton.setProduct', this.$el, this.model);
            e.preventDefault(); // TODO: убрать

            this.$el.addClass('mProgress');

            app.Event.trigger('product:change', {
                product: this.model
            });

            //e.preventDefault();
        }
    });
    app.View.Cart.BuySpinner = backbone.View.extend({
        events: {
            'click .js-up': 'incQuantity',
            'click .js-down': 'decQuantity',
            'change .js-value': 'setQuantity'
        },
        initialize: function() {
            this.model.get('cart').on('change', this.render, this);
            this.model.get('cart').on('invalid', this.render, this);
        },
        render: function() {
            console.info('app.View.Cart.BuySpinner.render', this.$el, this.model);
            this.$el.find('.js-value').val(this.model.get('cart').get('quantity'));

            return this;
        },
        incQuantity: function() {
            console.info('app.View.Cart.BuySpinner.incQuantity', this.$el, this.model);

            this.model.get('cart').set('quantity', this.model.get('cart').get('quantity') - 1, {validate: true});
        },
        decQuantity: function() {
            console.info('app.View.Cart.BuySpinner.decQuantity', this.$el, this.model);

            this.model.get('cart').set('quantity', this.model.get('cart').get('quantity') + 1, {validate: true});
        },
        setQuantity: function() {
            console.info('app.View.Cart.BuySpinner.setQuantity', this.$el, this.model);

            this.model.get('cart').set('quantity', parseInt(this.$el.find('.js-value').val()), {validate: true});
        }
    });


    app.model = {};
    app.collection = {};
    app.view = {};


    app.initialize = function() {
        // инициализация коллекций
        $('.js-collection').each(function(i, el) {
            var $el = $(el);
            var collectionName = $el.data('collection');

            if (!collectionName || !app.Collection[collectionName]) return; // continue

            console.info('app.initialize collection ', collectionName);

            app.collection[collectionName[0].toLowerCase() + collectionName.slice(1)] = new app.Collection[collectionName]($el.data('value'));
        });

        app.collection.product.each(function(model) {
            new app.View.Cart.BuyButton({model: model, el: model.get('buyButton').selector});
            new app.View.Cart.BuySpinner({model: model, el: model.get('buySpinner').selector});
        });

        app.model.cart = new app.Model.Cart({});
    };


    $(function() {
        app.initialize();
    });

}(window.Enter = window.Enter || {}, window, window.jQuery, window._, window.Mustache, window.Backbone));