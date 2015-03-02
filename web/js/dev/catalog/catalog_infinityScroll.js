/**
 * Catalog infinity scroll
 *
 * @requires jQuery, jquery.visible, Mustache, docCookies, ENTER.utils, ENTER.config, ENTER.catalog.history
 *
 * @author	Zaytsev Alexandr
 *
 * @param	{Object}	ENTER	Enter namespace
 */
;(function( ENTER ) {
	console.info('[Catalog] Init catalog_infinityScroll.js');

	var
		$body = $('body'),
		utils = ENTER.utils,
		catalog = utils.extendApp('ENTER.catalog'),
		viewParamPanel = $('.js-category-sortingAndPagination'),
        bottomInfButton = $('.jsInfinityEnable').last();
	// end of vars


	catalog.infScroll = {
		loading: false,

		nowPage: 1,

		checkInfinity: function() {
			console.info('Infinity scroll cookie = '+ window.docCookies.getItem( 'infScroll' ));
			if ( window.docCookies.getItem( 'infScroll' ) === '1' ) {
				catalog.infScroll.enable();
			}
		},

		checkScroll: function() {

			var w = $(window),
				d = $(document);
			// end of vars

			if ( !catalog.infScroll.loading && bottomInfButton.visible() &&
				( catalog.lastPage - catalog.infScroll.nowPage > 0 || null === catalog.lastPage ) ) {
				console.warn('checkscroll true. load');
				catalog.infScroll.nowPage += 1;
				catalog.infScroll.load();
				$body.trigger('loadInfinityPage', [catalog.infScroll.nowPage]);
			}
		},

		load: function() {
			console.info('load page');

			var url = catalog.filter.getFilterUrl();

			var resHandler = function resHandler( res ) {
				var html;

				html = catalog.filter.render['list']( res['list'] );
				catalog.infScroll.loading = false;

				catalog.listingWrap.append(html);
			};

			catalog.liveScroll = true;
			catalog.infScroll.loading = true;
			url = url.addParameterToUrl('page', catalog.infScroll.nowPage);
			url = url.addParameterToUrl('ajax', 'true');

			catalog.history.getDataFromServer(url, resHandler);
		},

		enable: function() {

			var activeClass = 'mActive act',
				infBtn = viewParamPanel.find('.js-category-pagination-infinity'),
				pagingBtn = viewParamPanel.find('.js-category-pagination-paging'),
				pageBtn = viewParamPanel.find('.js-category-pagination-page'),
				url = catalog.filter.getFilterUrl(),
				hasPaging = document.location.search.match('page=');
			// end of vars

			pagingBtn.css({'display':'inline-block'});
			pageBtn.hide();
			infBtn.addClass(activeClass);

			catalog.infScroll.nowPage = 1;
			catalog.infScroll.loading = false;

			window.docCookies.setItem('infScroll', 1, 4*7*24*60*60, '/' );

			catalog.infScroll.checkScroll();
			$(window).on('scroll', catalog.infScroll.checkScroll);

			console.info(hasPaging);

			if ( catalog.enableHistoryAPI && hasPaging ) {
				catalog.history.gotoUrl(url);
			}

			bottomInfButton = $('.jsInfinityEnable').last();

			if (bottomInfButton.visible() && catalog.lastPage > 1) {
				catalog.infScroll.nowPage += 1;
				this.load();
			}

			console.log('Infinity scroll enabled');
		},

		disable: function() {
			console.info('Infinity scroll disabling');

			var url = catalog.filter.getFilterUrl();
			// end of vars

			catalog.liveScroll = false;
			url = url.addParameterToUrl('ajax', 'true');

			window.docCookies.setItem('infScroll', 0, 4*7*24*60*60, '/' );
			$(window).off('scroll', catalog.infScroll.checkScroll);
			catalog.history.getDataFromServer(url, catalog.filter.renderCatalogPage);

			console.log('infinity scroll disable '+window.docCookies.getItem( 'infScroll' ));
		}
	};

	var
		infBtnHandler = function(e) {
			var activeClass = 'mActive',
				infBtn = viewParamPanel.find('.js-category-pagination-infinity'),
				isActiveTab = infBtn.hasClass(activeClass);
			// end of vars

			e.preventDefault();

			if (isActiveTab) {
				return;
			}

			catalog.infScroll.enable();
		},

		paginationBtnHandler = function(e) {
			console.info('paginationBtnHandler');
			var activeClass = 'mActive',
				infBtn = viewParamPanel.find('.js-category-pagination-infinity'),
				isActiveTab = infBtn.hasClass(activeClass);
			// end of vars

			e.preventDefault();

			if (!isActiveTab) {
				return;
			}

			catalog.infScroll.disable();
		};
	// end of functions

	catalog.infScroll.checkInfinity();

	viewParamPanel.on('click', '.jsPaginationEnable', paginationBtnHandler);
	viewParamPanel.on('click', '.jsInfinityEnable', infBtnHandler);

}(window.ENTER));