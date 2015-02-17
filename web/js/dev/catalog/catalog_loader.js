/**
 * Catalog loader
 *
 * @requires jQuery, Mustache, ENTER.utils, ENTER.config, ENTER.catalog.history
 * 
 * @author	Zaytsev Alexandr
 *
 * @param	{Object}	ENTER	Enter namespace
 */
;(function( ENTER ) {
	console.info('New catalog init: loader.js');

	var
		pageConfig = ENTER.config.pageConfig,
		utils = ENTER.utils,
		catalog = utils.extendApp('ENTER.catalog'),
		filterSubminBtn = $('.js-category-filter-submit', '.js-category-filter');
	// end of vars

	console.info('Mustache is '+ typeof Mustache);
	console.info('enableHistoryAPI '+ catalog.enableHistoryAPI);

	catalog.loader = {
		_loader: null,
		loading: function() {
			if ( catalog.loader._loader ) {
				return;
			}

			catalog.loader._loader = $('<li>').addClass('mLoader');
			filterSubminBtn.addClass('mButLoader').text('Подобрать');

			if ( catalog.liveScroll ) {
				catalog.listingWrap.append(catalog.loader._loader);
			}
			else {
				catalog.listingWrap.empty();
				catalog.listingWrap.append(catalog.loader._loader);
			}
		},

		complete: function() {
			if ( catalog.loader._loader ) {
				catalog.loader._loader.remove();
				catalog.loader._loader = null;
			}
			filterSubminBtn.removeClass('mButLoader');
			$('body').trigger('catalogLoadingComplete');
		},

		error: function() {
			console.warn('error');

			if ( catalog.loader._loader ) {
				catalog.loader._loader.remove();
				catalog.loader._loader = null;
			}
		}
	};

}(window.ENTER));