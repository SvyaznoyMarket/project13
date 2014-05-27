define(
    [
        'jquery', 'underscore', 'mustache'
    ],
    function (
        $, _, mustache
    ) {
        var $body = $('body'),
            $listContainer = $('.js-productList-container'), // FIXME: хардкод

            setFilter = function(e) {
                var $el = $(e.currentTarget),
                    dataValue = $listContainer.data('value')
                ;

                console.info('setFilter', $el);

                if ($el.is(':radio, :checkbox')) {
                    if ($el.is(':checked')) {
                        dataValue[$el.attr('name')] = $el.val();
                    } else {
                        delete dataValue[$el.attr('name')];
                    }

                    dataValue.page = 1;
                }
            },

            loadMoreProduct = function(e) {
                e.stopPropagation();

                var $el = $(e.currentTarget),
                    //$container = $($el.data('containerSelector')),
                    url = $listContainer.data('url'),
                    dataValue = $listContainer.data('value')
                ;

                console.info('loadMoreProduct', $el.data('disabled'), $el, $listContainer);

                if (url && (true !== $el.data('disabled'))) {
                    $.get(url, dataValue)
                        .done(function(response) {
                            if (_.isObject(response.result) && dataValue && $listContainer.length) {
                                console.info(response.result);
                                dataValue.page = response.result.page;
                                dataValue.count = response.result.count;

                                if (dataValue.count <= dataValue.page * dataValue.limit) {
                                    $el.hide();
                                }

                                _.each(response.result.productCards, function(content) {
                                    $listContainer.append(content);
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


        $body
            .on('click dblclick', '.js-productList-more', loadMoreProduct)
            .on('change', '.js-productFilter', setFilter)
    }
);