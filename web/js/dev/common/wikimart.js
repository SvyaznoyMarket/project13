(function(){
    try {
        if (ENTER.config.pageConfig.wikimart.url) {
            var WikimartSettings = {
                // Формат ссылок на товары
                catalogGoodUrlPattern: ENTER.config.pageConfig.wikimart.productUrlPattern,
                // Ссылка на каталог
                catalogUrl: 'http://example.com#catalog',
                // Id города
                cityId: ENTER.config.pageConfig.wikimart.cityId
            };
            WikimartAffiliate = new WikimartAffiliateCore();
            WikimartAffiliate.init(WikimartSettings);
        }
    } catch (error) {
        console.error(error);
    }
})();