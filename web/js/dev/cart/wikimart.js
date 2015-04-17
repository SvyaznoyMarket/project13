(function(){
    try {
        if (WikimartAffiliate) {
            WikimartAffiliate.createCheckoutIFrame('wikimart-checkout-block');
        }
    } catch (error) {
        console.error(error);
    }
})();