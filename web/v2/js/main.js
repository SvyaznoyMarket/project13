;(function(app, window, $, _, mustache, backbone, undefined) {

    app.Event = _.clone(Backbone.Events);

    app.Model = {};
    app.Collection = {};
    app.View = {};

    app.Model.Cart = {};
    app.Model.Cart.Product = backbone.Model.extend({
        initialize: function() {},
        validate: function(attrs) {
            if (attrs.quantity < 0) {
                return {message: 'Количество должно быть большим нуля'}
            }
        }
    });
    app.Collection.Cart = {};
    app.Collection.Cart.Product = backbone.Collection.extend({
        model: app.Model.Cart.Product,
        initialize: function() {}
    });

    app.Model.Product = backbone.Model.extend({});
    app.Collection.Product = backbone.Collection.extend({
        model: app.Model.Product
    });

    app.View.Cart = {};
    app.View.Cart.BuyButton = backbone.View.extend({
        events: {
            'click .jsBuyButtonLink': 'setProduct'
        },
        initialize: function() {
            this.template = $('#tplCartBuyButton').html();

            this.model.on('change', this.render, this);

            //this.render();
        },
        render: function() {
            //console.info('View.Cart.BuyButton:render', this.model.toJSON());
            //this.$el.html(mustache.render(this.template, this.model.toJSON()));
            this.$el.html(mustache.render(this.template, this.model.get('buyButton').data));

            return this;
        },
        setProduct: function(e) {
            console.info('app.View.Cart.BuyButton.setProduct', this.$el, this.model);

            this.model.get('buyButton').data.text = 'В корзине';
            this.model.get('buyButton').data.class += ' mDisabled';
            this.model.trigger('change');
            this.model.trigger('change:buyButton');

            app.Event.trigger('buyButton:setProduct', {
                id: this.model.get('id'),
                name: this.model.get('name')
            });

            e.preventDefault();
        }
    });


    app.model = {};
    app.collection = {};
    app.view = {};


    app.initialize = function() {
        // инициализация коллекций
        $('.jsCollection').each(function(i, el) {
            var $el = $(el);
            var collectionName = $el.data('collection');

            if (!collectionName || !app.Collection[collectionName]) return; // continue

            console.info('.jsCollection:initialize', collectionName);

            app.collection[collectionName[0].toLowerCase() + collectionName.slice(1)] = new app.Collection[collectionName]($el.data('value'));
        });

        app.collection.product.each(function(model) {
            new app.View.Cart.BuyButton({model: model, el: model.get('buyButton').selector});
        });

        app.collection.cart = {};
        app.collection.cart.product = new app.Collection.Cart.Product;
    };


    $(function() {
        app.initialize();
    });

}(window.Enter = window.Enter || {}, window, window.jQuery, window._, window.Mustache, window.Backbone));
