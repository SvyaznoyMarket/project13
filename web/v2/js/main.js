window.Enter = {};

;(function(app, window, $, _, mustache, backbone) {

    app.Event = _.clone(Backbone.Events);

    app.Model = {};
    app.Collection = {};
    app.View = {};

    app.Model.Cart = backbone.Model.extend({});

    app.View.Cart = {};
    app.View.Cart.BuyButton = backbone.View.extend({
        template: '#tplCartBuyButton',

        render: function() {
            this.$el.html(mustache.render($(this.template), this.model.toJSON()));

            return this;
        }
    });

}(this.Enter, this, this.jQuery, this._, this.Mustache, this.Backbone));


$(function() {

});