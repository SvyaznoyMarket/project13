require.config({
    baseUrl: '/v2/js',
    paths: {
        'jquery': 'vendor/jquery-1.8.3',
        'underscore': 'vendor/underscore-1.6.0',
        'backbone': 'vendor/backbone-1.1.2',
        'nestedtypes': 'vendor/backbone.nestedtypes',
        'mustache': 'vendor/mustache-0.8.2'
    }
});

require(['require', 'jquery', 'underscore', 'mustache', 'backbone', 'nestedtypes'],
    function (require, $, _, mustache, backbone) {
        var Base = require( 'nestedtypes' );

        window.Enter = {};
        var app = window.Enter;

        Base.Model.prototype.parse = function (data) {
            return data.result;
        };

        app.Event = _.clone(Backbone.Events);

        app.Model = {};
        app.Collection = {};
        app.View = {};

        app.Model.Product = {};
        app.Model.Product.Cart = Base.Model.extend({
            defaults: {
                quantity: 0,
                setUrl: '',
                deleteUrl: ''
            },
            validate: function (attrs) {
                console.info('app.Model.Product.Cart.validate', attrs);

                var error = null;
                if (!_.isFinite(attrs.quantity) || attrs.quantity <= 0) {
                    error = {message: 'Количество должно быть большим нуля'};
                }

                if (error) {
                    console.warn('app.Model.Product.Cart.validate', error);
                    return error;
                }
            }
        });
        app.Model.Product = Base.Model.extend({
            defaults: {
                name: String,
                token: String,
                inCart: false,
                cart: app.Model.Product.Cart,
                buyButton: {},
                buySpinner: {}
            },
            validate: function (attrs) {
                console.info('app.Model.Product.validate', attrs);

                var error = null;
                if (attrs.inCart && (attrs.cart.quantity != this.cart.quantity)) {
                    error = {message: 'Товар уже в корзине'};
                }

                if (error) {
                    console.warn('app.Model.Product.validate', error);
                    return error;
                }
            }
        });
        app.Collection.Product = backbone.Collection.extend({
            model: app.Model.Product
        });

        // Model & Collection
        app.Collection.Cart = {};
        app.Collection.Cart.Product = backbone.Collection.extend({
            model: app.Model.Product
        });
        app.Model.Cart = Base.Model.extend({
            defaults: {
                product: app.Collection.Product
            },
            addProduct: function (product) {
                console.info('app.Model.Cart.addProduct', product);
                this.product.create(product, {
                    wait: true,
                    type: product.inCart ? 'PUT' : 'POST',
                    url: product.cart.setUrl
                });
            }
        });

        // View
        app.View.Cart = {};
        app.View.Cart.BuyButton = backbone.View.extend({
            events: {
                'click .js-link': 'onClick'
            },
            initialize: function () {
                this.template = $('#tpl-cart-buyButton').html();

                this.model.on('change:buyButton change:inCart', this.render, this);
            },
            render: function () {
                console.info('app.View.Cart.BuyButton.render', this.template, this.model.buyButton.templateData);
                this.$el.html(mustache.render(this.template, this.model.buyButton.templateData));
                this.$el.toggleClass('mDisabled', this.model.inCart);

                return this;
            },
            onClick: function (e) {
                console.info('app.View.Cart.BuyButton.onClick', this.$el, this.model);

                if (true !== this.model.inCart) {
                    app.model.cart.addProduct(this.model);
                    e.preventDefault();
                }
            }
        });
        app.View.Cart.BuySpinner = backbone.View.extend({
            events: {
                'click .js-inc': 'incQuantity',
                'click .js-dec': 'decQuantity',
                'change .js-value': 'setQuantity',
                'keyup .js-value': 'setQuantity'
            },
            initialize: function () {
                this.model.on('change:cart change:inCart invalid:cart', this.render, this);
            },
            render: function () {
                console.info('app.View.Cart.BuySpinner.render', this.$el, this.model);
                this.$el.find('.js-value').val(this.model.cart.quantity);
                if (this.model.inCart) {
                    this.$el.addClass('mDisabled');
                }

                return this;
            },
            incQuantity: function () {
                console.info('app.View.Cart.BuySpinner.incQuantity', this.$el, this.model);

                if (this.model.inCart) return;

                this.model.cart.set('quantity', this.model.cart.quantity - 1, {validate: true});
            },
            decQuantity: function () {
                console.info('app.View.Cart.BuySpinner.decQuantity', this.$el, this.model);

                if (this.model.inCart) return;

                this.model.cart.set('quantity', this.model.cart.quantity + 1, {validate: true});
            },
            setQuantity: function () {
                console.info('app.View.Cart.BuySpinner.setQuantity', this.$el, this.model);

                if (this.model.inCart) return;

                this.model.cart.set('quantity', parseInt(this.$el.find('.js-value').val()), {validate: true});
            }
        });


        app.model = {};
        app.collection = {};
        app.view = {};


        app.initialize = function () {
            // инициализация коллекций
            $('.js-collection').each(function (i, el) {
                var $el = $(el);
                var collectionName = $el.data('collection');

                if (!collectionName || !app.Collection[collectionName]) return; // continue

                console.info('app.initialize collection ', collectionName);

                app.collection[collectionName[0].toLowerCase() + collectionName.slice(1)] = new app.Collection[collectionName]($el.data('value'));
            });

            app.collection.product.each(function (model) {
                new app.View.Cart.BuyButton({model: model, el: model.buyButton.selector});
                new app.View.Cart.BuySpinner({model: model, el: model.buySpinner.selector});
            });

            app.model.cart = new app.Model.Cart({});
        };

        $(function () {
            app.initialize();
        });
    }
);
