define(
    [
        'jquery', 'underscore', 'mustache'
    ],
    function (
        $, _, mustache
    ) {
        var $body = $('body'),

            loadMoreProduct = function(e) {
                e.stopPropagation();

                var $el = $(e.currentTarget),
                    $container = $($el.data('containerSelector')),
                    url = $container.data('url'),
                    dataValue = $container.data('value')
                ;

                console.info('loadMoreProduct', $el, $container);

                url && $.get(url, dataValue).done(function(response) {
                    if (_.isObject(response.result) && dataValue && $container.length) {
                        console.info(response.result);
                        dataValue.page = response.result.page;
                        dataValue.count = response.result.count;

                        if (dataValue.count <= dataValue.page * dataValue.limit) {
                            $el.hide();
                        }

                        _.each(response.result.productCards, function(content) {
                            $container.append(content);
                        });

                        if (_.isObject(response.result.widgets)) {
                            $body.data('widget', response.result.widgets);
                            $body.trigger('render');
                        }
                    }
                });

                e.preventDefault();
            }
        ;


        $body.on('click', '.js-productList-more', loadMoreProduct)
    }
);