define(
    [
        'require', 'jquery', 'underscore', 'mustache', 'module/util'
    ],
    function (
        require, $, _, mustache, util
    ) {
        var $body = $('body'),

            getCreditPayment = function() {
                // direct-credit
                $creditPayment = $('.js-creditPayment');
                var dataValue = $creditPayment.data('value');
                console.info('creditPayment', $creditPayment, dataValue);
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
                                shownPrice: util.formatCurrency(price)
                            }));

                            $creditPayment.show();
                        }
                    );
                });
            }
        ;

        $body
            .on('render', '.js-cart-total', getCreditPayment)

        getCreditPayment();

    }
);