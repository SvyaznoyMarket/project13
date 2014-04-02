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
            console.info('View.Cart.BuyButton:render', this.model.toJSON());
            this.$el.html(mustache.render(this.template, this.model.toJSON()));

            return this;
        }
    });


    app.initialize = function() {
        var buttons = new app.Collection.Cart.BuyButton;
        $('.jsBuyButton').each(function(i, el) {
            var $el = $(el);
            var button = new app.Model.Cart.BuyButton({id: $el.data('id'), value: $el.text(), dataValue: $el.find('>').attr('data-value')});
            buttons.add(button);
            new app.View.Cart.BuyButton({model: button, el: el});
        });
        console.info(buttons);

        buttons.at(1).set({'value': 'Недоступен', 'class': 'mDisabled'});

        //var button = new app.Model.Cart.BuyButton({});
        //var buttonView = new app.View.Cart.BuyButton({model: button, el: '.idCartProductButton141316'});
        //buttonView.render();
        //console.info(buttonView.el);
    };


    $(function() {
        app.initialize();
    });

}(window.Enter, window, window.jQuery, window._, window.Mustache, window.Backbone));
