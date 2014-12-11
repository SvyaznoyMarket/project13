/**
 * Catalog main config
 *
 * @requires jQuery, Mustache, ENTER.utils, ENTER.config
 * 
 * @author	Zaytsev Alexandr
 *
 * @param	{Object}	ENTER	Enter namespace
 */
;(function( ENTER ) {
	console.info('Catalog init: catalog.js');

	var //pageConfig = ENTER.config.pageConfig,
		utils = ENTER.utils,
		catalog = utils.extendApp('ENTER.catalog'),
		lastPage = $('#bCatalog').data('lastpage');
	// end of vars


	catalog.enableHistoryAPI = ( typeof Mustache === 'object' ) && ( History.enabled );
	catalog.listingWrap = $('.js-listing');
	catalog.liveScroll = false;
	catalog.lastPage = null;

	if ( lastPage ) {
		catalog.lastPage = lastPage;
	}

	console.info('Mustache is '+ typeof Mustache + ' (Catalog main config)');
	console.info('enableHistoryAPI '+ catalog.enableHistoryAPI);

    /**
     * Подключение слайдера товаров
     */
    $('.js-slider').goodsSlider({
        onLoad: function(goodsSlider) {
            ko.applyBindings(ENTER.UserModel, goodsSlider);
        }
    });

}(window.ENTER));