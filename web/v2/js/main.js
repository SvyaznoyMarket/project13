;(function (app, window, $, _, mustache, undefined) {
    app.initialize = function () {
        var $document = $(document);
        var $body = $('body');

        $document.ajaxSuccess(function(e, xhr, settings) {
            //var response = JSON.parse(xhr.responseText);
        });

        $body.on('click', '.js-buyButton', function(e) {
            var $el = $(e.currentTarget);
            var data = $el.data();

            console.info('click:js-buyButton', $el, data);

            if (data.url) {
                $.post(data.url, data.value, function(response) {
                    if (_.isObject(response.result.buyButton)) {
                        $el.trigger('render', response.result.buyButton);
                    }
                });

                e.preventDefault();
            }
        });

        $body.on('render', '.js-buyButton', function(e, data) {
            var idSelector = $(e.currentTarget).data('idSelector');
            var $el = $(idSelector);

            console.info('render:js-buyButton', $el, data);

            $el.replaceWith(mustache.render($('#tpl-cart-buyButton').html(), data));
        });
    };


    $(function () {
        app.initialize();
    });

}(window.Enter = window.Enter || {}, window, window.jQuery, window._, window.Mustache));