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
                e.stopPropagation();

                var $el = $(e.currentTarget),
                    dataValue = $listContainer.data('value')
                ;

                console.info('setFilter', $el);

                dataValue.page = 1;

                if ($el.is(':radio, :checkbox')) {
                    if ($el.is(':checked')) {
                        dataValue[$el.attr('name')] = $el.val();
                    } else {
                        delete dataValue[$el.attr('name')];
                    }
                } else if ($el.is(':text')) {
                    dataValue[$el.attr('name')] = $el.val();
                }

                loadProducts(e, {clear: true});
            },

            deleteFilter = function(e) {
                e.stopPropagation();

                var $el = $(e.currentTarget),
                    currentName = $el.data('name'),
                    dataValue = $listContainer.data('value')
                ;

                console.info('deleteFilter', e, dataValue);

                if (currentName) {
                    dataValue.page = 1;

                    _.each(dataValue, function(value, name) {
                        if (name == currentName) {
                            delete dataValue[currentName];
                            return true;
                        }
                    });

                    var $filter = $('.js-productFilter-set').filter('[data-name="' + currentName + '"]');
                    if ($filter.length) {
                        if ($filter.is(':radio, :checkbox')) {
                            $filter.removeAttr('checked');
                        }
                    }

                    loadProducts(e, {clear: true});

                    e.preventDefault();
                }
            },

            clearFilter = function(e) {
                e.stopPropagation();

                var dataValue = $listContainer.data('value'),
                    dataReset = $listContainer.data('reset')
                ;

                console.info('clearFilter', e, dataValue);

                $listContainer.data('value', _.extend({}, dataReset));
                console.info(dataValue);

                dataValue.page = 1;

                loadProducts(e, {clear: true});

                e.preventDefault();
            },

            loadMoreProducts = function(e) {
                e.stopPropagation();

                console.info('loadMoreProducts', e);

                loadProducts(e, {clear: false});

                e.preventDefault();
            },

            loadProducts = function(e, options) {
                var $moreLink = $('.js-productList-more'),
                    //$container = $($el.data('containerSelector')),
                    url = $listContainer.data('url'),
                    dataValue = $listContainer.data('value')
                ;

                options = _.extend({clear: false}, options);

                console.info('loadProduct', $moreLink, $listContainer, dataValue);

                if (url && (true !== $moreLink.data('disabled'))) {
                    $.get(url, dataValue)
                        .done(function(response) {
                            if (_.isObject(response.result) && dataValue && $listContainer.length) {
                                if (true === options.clear) {
                                    $listContainer.empty();
                                }


                                dataValue.page = response.result.page;
                                dataValue.count = response.result.count;

                                if (dataValue.count <= dataValue.page * dataValue.limit) {
                                    $moreLink.hide();
                                } else {
                                    $moreLink.show();
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
                            $moreLink.data('disabled', false);
                        })
                    ;

                    $moreLink.data('disabled', true);
                }
            }
        ;


        $body
            .on('click dblclick', '.js-productList-more', loadMoreProducts)
            .on('change', '.js-productFilter-set', setFilter)
            .on('click', '.js-productFilter-delete', deleteFilter)
            .on('click', '.js-productFilter-clear', clearFilter)
    }
);