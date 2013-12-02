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

	var pageConfig = ENTER.config.pageConfig,
		utils = ENTER.utils,
		catalog = utils.extendApp('ENTER.catalog'),
		bCatalogCount = $('#bCatalog').data('count');
	// end of vars
	

	catalog.enableHistoryAPI = ( typeof Mustache === 'object' ) && ( History.enabled );
	catalog.listingWrap = $('.bListing');
	catalog.liveScroll = false;
	catalog.pagesCount = null;
	catalog.productsCount = null;

	if ( 'undefined' !== typeof(bCatalogCount) ) {
		if ( 'undefined' !== typeof(bCatalogCount.pages) ) {
			catalog.pagesCount = bCatalog.pages;
		}

		if ( 'undefined' !== typeof(bCatalogCount.products) ) {
			catalog.productsCount = bCatalog.products;
		}
	}

	console.info('Mustache is '+ typeof Mustache + ' (Catalog main config)');
	console.info('enableHistoryAPI '+ catalog.enableHistoryAPI);

}(window.ENTER));