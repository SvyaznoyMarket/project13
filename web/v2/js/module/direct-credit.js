define(
    ['direct-credit'],
    function () {
        return {
            getPayment: function(config, user, products, done) {
                dc_getCreditForTheProduct (
                    config.partnerId,
                    user.sessionId,
                    'getPayment',
                    {
                        products: products
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