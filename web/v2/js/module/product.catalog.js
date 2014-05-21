define(
    [
        'jquery', 'underscore', 'mustache'
    ],
    function (
        $, _, mustache
    ) {
        var $body = $('body'),

            loadMoreProduct = function(e) {
                var $el = $(e.currentTarget),
                    $container = $el.data('containerSelector')
                ;

                e.stopPropagation();

                console.info('loadMoreProduct', e);



                e.preventDefault();
            }
        ;


        $body.on('click', '.js-productList-more', loadMoreProduct)
    }
);