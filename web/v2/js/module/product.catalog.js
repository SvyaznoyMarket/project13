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

                var $el = $(e.target),
                    dataValue = $listContainer.data('value')
                ;

                console.info('setFilter', $el);

                if ($el.is(':radio, :checkbox')) {
                    if ($el.is(':checked')) {
                        dataValue[$el.attr('name')] = $el.val();
                    } else {
                        delete dataValue[$el.attr('name')];
                    }
                } else if ($el.is(':text')) {
                    dataValue[$el.attr('name')] = $el.val();
                }

                dataValue.page = 1;
                loadProducts(e, {clear: true});
            },

            deleteFilter = function(e) {
                e.stopPropagation();

                var $el = $(e.target),
                    currentName = $el.data('name'),
                    dataValue = $listContainer.data('value')
                ;

                console.info('deleteFilter', e, dataValue, currentName);

                if (currentName) {
                    _.each(dataValue, function(value, name) {
                        if (name == currentName) {
                            delete dataValue[currentName];
                            return true;
                        }
                    });

                    var $filter = $('.js-productFilter-set').filter('[name="' + currentName + '"]');
                    if ($filter.length) {
                        if ($filter.is(':radio, :checkbox')) {
                            $filter.removeAttr('checked');
                        } else if ($filter.is(':text')) {
                            $filter.val($filter.data('value'));
                            $filter.trigger('change');
                        }
                    }

                    dataValue.page = 1;
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

                // FIXME
                dataReset.sort = dataValue.sort;

                $listContainer.data('value', _.extend({}, dataReset));

                $('.js-productFilter-set').each(function(i, el) {
                    var $el = $(el);

                    if ($el.is(':radio, :checkbox')) {
                        $el.removeAttr('checked');
                    } else if ($el.is(':text')) {
                        $el.val($el.data('value'));
                        $el.trigger('change');
                    }
                });

                dataValue.page = 1;
                loadProducts(e, {clear: true});

                e.preventDefault();
            },

            setSorting = function(e) {
                e.stopPropagation();

                var $el = $(e.target),
                    sortingValue = $el.data('value'),
                    dataValue = $listContainer.data('value')
                ;

                console.info('setSorting', $el);

                if (_.isObject(sortingValue)) {
                    _.each(sortingValue, function(value, name) {
                        dataValue[name] = value;
                    });

                    dataValue.page = 1;
                    loadProducts(e, {clear: true});

                    e.preventDefault();
                }
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
            .on('click', '.js-productFilter-sort', setSorting)
    }
);