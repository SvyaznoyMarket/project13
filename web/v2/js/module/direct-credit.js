define(
    ['direct-credit'],
    function () {
        return {
            getPayment: function(config, user, product, done) {
                dc_getCreditForTheProduct (
                    config.partnerId,
                    user.sessionId,
                    'getPayment',
                    {
                        price: product.price,
                        count: 1,
                        type: product.type
                    },
                    function(result) {
                        console.info('dc_getCreditForTheProduct', result);
                        if (!'payment' in result || (result.payment <= 0)) {
                            return;
                        }

                        done(result);
                    }
                );
            }
        }
    }
);