(function (window, document, modules) {
    var moduleRealization = function (provide, app) {
        var module = (function () {
            var
                init = function (config) {
                    //app.Model = {};
                    //app.Collection = {};
                    //app.View = {};
                }

            return {
                init: init
            };
        }());

        provide(module);
    };

    modules.define(
        'Cart.Common',
        ['Enter'],
        moduleRealization
    );
}(
    this,
    this.document,
    this.modules
));


