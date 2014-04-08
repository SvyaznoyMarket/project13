;(function (app, window, $, _, mustache, undefined) {
    app.initialize = function () {
        var $document = $(document);
        var $body = $('body');

        $document.ajaxSuccess(function(e, xhr, settings) {
            //var response = JSON.parse(xhr.responseText);
        });

        $body.on('click', '.js-cart-buyButton', function(e) {
            var $el = $(e.currentTarget);
            var data = $el.find('.js-link').data();

            console.info('click:js-cart-buyButton', $el, data);

            if (data.url) {
                $.post(data.url, data.value, function(response) {
                    $el.trigger('render', response.result)
                });

                e.preventDefault();
            }
        });

        $body.on('render', '.js-cart-buyButton', function(e, data) {
            var $el = $(e.currentTarget);

            console.info('render:js-cart-buyButton', $el, data);

            $el.html($(mustache.render($('#tpl-cart-buyButton').html(), data)).find('.js-link'));
        });
    };


    $(function () {
        app.initialize();
    });

}(window.Enter = window.Enter || {}, window, window.jQuery, window._, window.Mustache));