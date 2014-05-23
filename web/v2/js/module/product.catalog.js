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

                console.info('loadMoreProduct', $el.data('disabled'), $el, $container);

                if (url && (true !== $el.data('disabled'))) {
                    $.get(url, dataValue)
                        .done(function(response) {
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
                        })
                        .always(function() {
                            $el.data('disabled', false);
                        })
                    ;

                    $el.data('disabled', true);
                }

                e.preventDefault();
            }
        ;


        $body.on('click dblclick', '.js-productList-more', loadMoreProduct)
    }
);