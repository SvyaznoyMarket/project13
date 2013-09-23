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
		catalog = utils.extendApp('ENTER.catalog');
	// end of vars
	

	catalog.enableHistoryAPI = ( typeof Mustache === 'object' ) && ( History.enabled );
	catalog.listingWrap = $('.bListing');
	catalog.liveScroll = false;

	console.info('Mustache is '+ typeof Mustache);
	console.info('enableHistoryAPI '+ catalog.enableHistoryAPI);

}(window.ENTER));