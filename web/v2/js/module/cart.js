define(
    [
        'require', 'jquery', 'underscore', 'mustache',
    ],
    function (
        require, $, _, mustache
    ) {
        var $body = $('body');


        // direct-credit
        $creditPayment = $('.js-creditPayment');
        console.info('creditPayment', $creditPayment);
        var dataValue = $creditPayment.data('value');
        _.isObject(dataValue) && require(['module/direct-credit', 'direct-credit'], function(directCredit) {
            dataValue.product.quantity = 1;

            directCredit.getPayment(
                { partnerId: dataValue.partnerId },
                $body.data('user'),
                dataValue.product,
                function (result) {
                    var $template = $($creditPayment.data('templateSelector')),
                        $price = $($creditPayment.data('priceSelector')),
                        price = Math.ceil(result.payment);

                    $price.html(mustache.render($template.html(), {
                        shownPrice: price
                    }));

                    $creditPayment.show();
                }
            );
        });
    }
);