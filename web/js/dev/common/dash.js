$(document).ready(function(){
	// var carturl = $('.lightboxinner .point2').attr('href')


	/* вывод слайдера со схожими товарами, если товар доступен только на витрине*/
	if ( $('#similarGoodsSlider').length ) {

		// основные элементы
		var similarSlider = $('#similarGoodsSlider'),
			similarWrap = similarSlider.find('.bSimilarGoodsSlider_eWrap'),
			similarArrow = similarSlider.find('.bSimilarGoodsSlider_eArrow'),

			slidesW = 0,

			sliderW = 0,
			slidesCount = 0,
			wrapW = 0,
			left = 0;
		// end of vars
		
		var sliderTracking = function sliderTracking() {
				var nowUrl = document.location,
					toUrl = $(this).attr('href');
				// end of vars
				
				if( typeof(_gaq) !== 'undefined' ){
					_gaq.push(['_trackEvent', 'AdvisedCrossss', nowUrl, toUrl]);
				}
			},

			kissSimilar = function kissSimilar() {
				var clicked = $(this),
					toKISS = {
						'Recommended Item Clicked Similar Recommendation Place':'product',
						'Recommended Item Clicked Similar Clicked SKU':clicked.data('article'),
						'Recommended Item Clicked Similar Clicked Product Name':clicked.data('name'),
						'Recommended Item Clicked Similar Product Position':clicked.data('pos')
					};
				// end of vars
				
				if (typeof(_kmq) !== 'undefined') {
					_kmq.push(['record', 'Recommended Item Clicked Similar', toKISS]);
				}
			},

			// init
			init = function init( data ) {
				var similarGoods = similarSlider.find('.bSimilarGoodsSlider_eGoods');

				for ( var item in data ) {
					var similarGood = tmpl('similarGoodTmpl',data[item]);
					similarWrap.append(similarGood);
				}

				slidesW = similarGoods.width() + parseInt(similarGoods.css('paddingLeft'), 10) * 2;
				slidesCount = similarGoods.length;
				wrapW = slidesW * slidesCount;
				similarWrap.width(wrapW);

				if ( slidesCount > 0 ) {
					$('.bSimilarGoods').fadeIn(300, function() {
						sliderW = similarSlider.width();
					});
				}

				if ( slidesCount < 4 ){
					$('.bSimilarGoodsSlider_eArrow.mRight').hide();
				}
			};

		$.getJSON( $('#similarGoodsSlider').data('url') , function( data ) {
			if ( !($.isEmptyObject(data)) ){
				var initData = data;

				init(initData);
			}
		}).done(function() {
			var similarGoods = similarSlider.find('.bSimilarGoodsSlider_eGoods');

			slidesCount = similarGoods.length;
			wrapW = slidesW * slidesCount;
			similarWrap.width(wrapW);
			if ( slidesCount > 0 ) {
				$('.bSimilarGoods').fadeIn(300, function(){
					sliderW = similarSlider.width();
				});
			}
		});
		
		similarArrow.bind('click', function() {
			if ( $(this).hasClass('mLeft') ) {
				left += (slidesW * 2);
			}
			else {
				left -= (slidesW * 2);
			}
			// left *= ($(this).hasClass('mLeft'))?-1:1
			if ( (left <= sliderW-wrapW) ) {
				left = sliderW - wrapW;
				$('.bSimilarGoodsSlider_eArrow.mRight').hide();
				$('.bSimilarGoodsSlider_eArrow.mLeft').show();
			} 
			else if ( left >= 0 ) {
				left = 0;
				$('.bSimilarGoodsSlider_eArrow.mLeft').hide();
				$('.bSimilarGoodsSlider_eArrow.mRight').show();
			}
			else {
				similarArrow.show();
			}

			similarWrap.animate({'left':left});
			return false;
		});


		// KISS
		$('.bSimilarGoods.mProduct .bSimilarGoodsSlider_eGoods').on('click', kissSimilar);
		$('.bSimilarGoods.mCatalog .bSimilarGoodsSlider_eGoods a').on('click', sliderTracking);
	}



	// hover imitation for IE
	if ( window.navigator.userAgent.indexOf("MSIE") >= 0 ) {
		$('.allpageinner').on( 'hover', '.goodsbox__inner', function() {
			$(this).toggleClass('hover');
		});
	}

	/* ---- */
	$('body').on('click', '.goodsbox__inner', function(e) {
		if ( $(this).attr('data-url') ) {
			window.location.href = $(this).attr('data-url');
		}
	});


	/* GA tracks */
	var accessoriesMsg = {
		uri: window.location.pathname,
		atcl: $('.bGood__eArticle span:last').text().replace(/[^0-9\-]/g, '')
	};
	
	$('.bigcarousel').eq(0).bind('click', function(e) {
		if ( typeof(_gaq) !== 'undefined' ) {
			_gaq.push(['_trackEvent', 'accessories_up', accessoriesMsg['atcl'], accessoriesMsg['uri'] ]);
		}
	});
	$('.bigcarousel').eq(1).bind('click', function(e) {
		if ( typeof(_gaq) !== 'undefined' ) {
			_gaq.push(['_trackEvent', 'accessories_down', accessoriesMsg['atcl'], accessoriesMsg['uri'] ]);
		}
	});


	//KISS
	$('.bigcarousel .goodsbox__inner').bind('click', function(){
		var data = $(this).data('product'),
			toKISS = {};
		// end of vars
		
		switch ( data.type ) {
			case 'Accessorize':
				toKISS = {
					'Recommended Item Clicked Accessorize Recommendation Place':'product',
					'Recommended Item Clicked Accessorize Clicked SKU':data.article,
					'Recommended Item Clicked Accessorize Clicked Product Name':data.name,
					'Recommended Item Clicked Accessorize Product Position':data.position
				};

				if ( typeof(_kmq) !== 'undefined' ) {
					_kmq.push(['record', 'Recommended Item Clicked Accessorize', toKISS]);
				}
				break;
			case 'Also Bought':
				toKISS = {
					'Recommended Item Clicked Also Bought Recommendation Place':'product',
					'Recommended Item Clicked Also Bought Clicked SKU':data.article,
					'Recommended Item Clicked Also Bought Clicked Product Name':data.name,
					'Recommended Item Clicked Also Bought Product Position':data.position
				};

				if ( typeof(_kmq) !== 'undefined' ) {
					_kmq.push(['record', 'Recommended Item Clicked Also Bought', toKISS]);
				}
				break;
			case 'Also Viewed':
				toKISS = {
					'Recommended Item Clicked Also Viewed Recommendation Place':'product',
					'Recommended Item Clicked Also Viewed Clicked SKU':data.article,
					'Recommended Item Clicked Also Viewed Clicked Product Name':data.name,
					'Recommended Item Clicked Also Viewed Product Position':data.position
				};

				if ( typeof(_kmq) !== 'undefined' ) {
					_kmq.push(['record', 'Recommended Item Clicked Also Viewed', toKISS]);
				}
				break;
		}
	});



	/**
	 * KISS view category
	 */
	var kissForCategory = function kissForCategory() {
		var data = $('#_categoryData').data('category'),
			toKISS = {
				'Viewed Category Category Type':data.type,
				'Viewed Category Category Level':data.level,
				'Viewed Category Parent category':data.parent_category,
				'Viewed Category Category name':data.category,
				'Viewed Category Category ID':data.id
			};
		// end of vars
		
		if ( typeof(_kmq) !== 'undefined' ) {
			_kmq.push(['record', 'Viewed Category', toKISS]);
		}
	};

	if ( $('#_categoryData').length ) {
		kissForCategory();
	}

	/**
	 * KISS Search
	 */
	var kissForSearchResultPage = function kissForSearchResultPage() {
		var data = $('#_searchKiss').data('search'),
			toKISS = {
				'Search String':data.query,
				'Search Page URL':data.url,
				'Search Items Found':data.count
			};
		// end of vars

		if ( typeof(_kmq) !== 'undefined' ) {
			_kmq.push(['record', 'Search', toKISS]);
		}

		var KISSsearchClick = function() {
			var productData = $(this).data('add'),
				prToKISS = {
					'Search Results Clicked Search String':data.query,
					'Search Results Clicked SKU':productData.article,
					'Search Results Clicked Product Name':productData.name,
					'Search Results Clicked Page Number':productData.page,
					'Search Results Clicked Product Position':productData.position
				};
			// end of vars

			if ( typeof(_kmq) !== 'undefined' ) {
				_kmq.push(['record', 'Search Results Clicked',  prToKISS]);
			}
		};

		$('.goodsbox__inner').on('click', KISSsearchClick);
		$('.goodsboxlink').on('click', KISSsearchClick);
	};

	if ( $('#_searchKiss').length ) {
		kissForSearchResultPage();
	}

});