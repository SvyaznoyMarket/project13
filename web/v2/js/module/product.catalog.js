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

                });

                e.preventDefault();
            }
        ;


        $body.on('click', '.js-productList-more', loadMoreProduct)
    }
);