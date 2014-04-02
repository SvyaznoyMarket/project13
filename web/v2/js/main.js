;

window.Enter = {};

(function(app, window, $, _, mustache, backbone) {

    app.Event = _.clone(Backbone.Events);

    app.Model = {};
    app.Collection = {};
    app.View = {};

    app.Model.Cart = {};
    app.Model.Cart.BuyButton = backbone.Model.extend({});

    app.Collection.Cart = {};
    app.Collection.Cart.BuyButton = backbone.Collection.extend({
        model: app.Model.Cart.BuyButton
    });

    app.View.Cart = {};
    app.View.Cart.BuyButton = backbone.View.extend({
        initialize: function() {
            this.template = $('#tplCartBuyButton').html();

            this.model.on('change', this.render, this);

            //this.render();
        },
        render: function() {
            console.info('View.Cart.BuyButton:render');
            this.$el.html(mustache.render(this.template, this.model.toJSON()));
            console.info(this.model.toJSON());

            return this;
        }
    });
    app.View.Cart.BuyButtonCollection = backbone.View.extend({
        render: function() {
            this.collection.each(this.addOne, this);

            return this;
        },
        addOne: function(model) {
            var view = new app.View.Cart.BuyButton({model: model, el: '.idCartProductButton' + model.get('id')});

            view.render();
        }
    });


    app.initialize = function() {
        var buttons = new app.Collection.Cart.BuyButton;
        $('.jsBuyButton').each(function(i, el) {
            buttons.add({id: $(el).data('id')});
        });
        //console.info(buttons);

        var buttonCollectionView = new app.View.Cart.BuyButtonCollection({collection: buttons});
        //buttonCollectionView.render();

        setTimeout(function() {
            buttons.at(1).set('value', 'Buy');
            console.info(buttons.at(1));
        }, 4000);

        //var button = new app.Model.Cart.BuyButton({});
        //var buttonView = new app.View.Cart.BuyButton({model: button, el: '.idCartProductButton141316'});
        //buttonView.render();
        //console.info(buttonView.el);
    };


    $(function() {
        app.initialize();
    });

}(window.Enter, window, window.jQuery, window._, window.Mustache, window.Backbone));
