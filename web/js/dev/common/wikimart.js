(function(){
    try {
        if (ENTER.config.pageConfig.wikimart.url) {
            var WikimartSettings = {
                // Формат ссылок на товары
                catalogGoodUrlPattern: 'http://example.com#modelDetail?goodId=%GOOD_ID%',
                // Ссылка на каталог
                catalogUrl: 'http://example.com#catalog',
                // Id города
                cityId: $("#cityId").val()
            };
            WikimartAffiliate = new WikimartAffiliateCore();
            WikimartAffiliate.init(WikimartSettings);
        }
    } catch (error) {
        console.error(error);
    }
})();