/**
 * Catalog infinity scroll
 *
 * @requires jQuery, Mustache, docCookies, ENTER.utils, ENTER.config, ENTER.catalog.history
 * 
 * @author	Zaytsev Alexandr
 *
 * @param	{Object}	ENTER	Enter namespace
 */
;(function( ENTER ) {
	console.info('Catalog init: catalog_infinityScroll.js');

	var pageConfig = ENTER.config.pageConfig,
		utils = ENTER.utils,
		catalog = utils.extendApp('ENTER.catalog'),

		viewParamPanel = $('.bSortingLine');
	// end of vars

	
	catalog.infScroll = {
		load: function() {

		},

		enable: function() {
			window.docCookies.setItem('infScroll', 1, 4*7*24*60*60, '/' );

			console.info('infinity scroll enable');
		},

		disable: function() {
			window.docCookies.setItem('infScroll', 0, 0, '/' );
		}
	};

	var infBtnHandler = function infBtnHandler() {
		catalog.infScroll.enable();

		return false;
	}

	if ( window.docCookies.getItem( 'infScroll' ) === '1' ) {
		catalog.infScroll.enable();
	}

	viewParamPanel.on('click', '.jsInfinity', infBtnHandler);

}(window.ENTER));