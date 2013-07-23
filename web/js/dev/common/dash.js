$(document).ready(function(){
	// var carturl = $('.lightboxinner .point2').attr('href')


	/* вывод слайдера со схожими товарами, если товар доступен только на витрине*/
	if ( $('#similarGoodsSlider').length){

		// основные элементы
		var similarSlider = $('#similarGoodsSlider')
		var similarWrap = similarSlider.find('.bSimilarGoodsSlider_eWrap')
		var similarArrow = similarSlider.find('.bSimilarGoodsSlider_eArrow')

		var slidesW = 0

		var sliderW = 0
		var slidesCount = 0
		var wrapW = 0
		var left = 0
		
		var sliderTracking = function(){
			var nowUrl = document.location
			var toUrl = $(this).attr('href')
			
			if( typeof(_gaq) !== 'undefined' ){
				_gaq.push(['_trackEvent', 'AdvisedCrossss', nowUrl, toUrl])
			}
		}

		var kissSimilar = function(){
			var clicked = $(this)
			var toKISS = {
				'Recommended Item Clicked Similar Recommendation Place':'product',
				'Recommended Item Clicked Similar Clicked SKU':clicked.data('article'),
				'Recommended Item Clicked Similar Clicked Product Name':clicked.data('name'),
				'Recommended Item Clicked Similar Product Position':clicked.data('pos'),
			}

			if (typeof(_kmq) !== 'undefined') {
				_kmq.push(['record', 'Recommended Item Clicked Similar', toKISS])
			}
		}

		// init
		var init = function(data){
			for (var item in data){
				var similarGood = tmpl('similarGoodTmpl',data[item])
				similarWrap.append(similarGood)
			}
			var similarGoods = similarSlider.find('.bSimilarGoodsSlider_eGoods')

			slidesW = similarGoods.width() + parseInt(similarGoods.css('paddingLeft'))*2
			slidesCount = similarGoods.length
			wrapW = slidesW * slidesCount
			similarWrap.width(wrapW)

			if (slidesCount > 0){
				$('.bSimilarGoods').fadeIn(300, function(){
					sliderW = similarSlider.width()
				})
			}

			if (slidesCount < 4){
				$('.bSimilarGoodsSlider_eArrow.mRight').hide()
			}
		}

		$.getJSON( $('#similarGoodsSlider').data('url') , function(data){
			if (!($.isEmptyObject(data))){
				var initData = data
				init(initData)
			}
		}).done(function(){
			var similarGoods = similarSlider.find('.bSimilarGoodsSlider_eGoods')
			slidesCount = similarGoods.length
			wrapW = slidesW * slidesCount
			similarWrap.width(wrapW)
			if (slidesCount > 0){
				$('.bSimilarGoods').fadeIn(300, function(){
					sliderW = similarSlider.width()
				})
			}
		})
		
		similarArrow.bind('click', function(){
			if ($(this).hasClass('mLeft')){
				left += (slidesW * 2)
			}
			else{
				left -= (slidesW * 2)
			}
			// left *= ($(this).hasClass('mLeft'))?-1:1
			if ((left <= sliderW-wrapW)){
				left = sliderW-wrapW
				$('.bSimilarGoodsSlider_eArrow.mRight').hide()
				$('.bSimilarGoodsSlider_eArrow.mLeft').show()
			} 
			else if (left >= 0 ){
				left = 0
				$('.bSimilarGoodsSlider_eArrow.mLeft').hide()
				$('.bSimilarGoodsSlider_eArrow.mRight').show()
			}
			else{
				similarArrow.show()
			}
			similarWrap.animate({'left':left})
			return false
		})


		// KISS
		$('.bSimilarGoods.mProduct .bSimilarGoodsSlider_eGoods').on('click', kissSimilar)


		$('.bSimilarGoods.mCatalog .bSimilarGoodsSlider_eGoods a').on('click', sliderTracking)
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
	})


	/* GA tracks */
	var accessoriesMsg = {
		uri: window.location.pathname,
		atcl: $('.bGood__eArticle span:last').text().replace(/[^0-9\-]/g, '')
	};
	
	$('.bigcarousel').eq(0).bind('click', function(e) {
		if( typeof(_gaq) !== 'undefined' )
			_gaq.push(['_trackEvent', 'accessories_up', accessoriesMsg['atcl'], accessoriesMsg['uri'] ])
	});
	$('.bigcarousel').eq(1).bind('click', function(e) {
		if( typeof(_gaq) !== 'undefined' )
			_gaq.push(['_trackEvent', 'accessories_down', accessoriesMsg['atcl'], accessoriesMsg['uri'] ])
	});


	//KISS
	$('.bigcarousel .goodsbox__inner').bind('click', function(){
		var data = $(this).data('product')
		switch (data.type) {
			case 'Accessorize':
				var toKISS = {
					'Recommended Item Clicked Accessorize Recommendation Place':'product',
					'Recommended Item Clicked Accessorize Clicked SKU':data.article,
					'Recommended Item Clicked Accessorize Clicked Product Name':data.name,
					'Recommended Item Clicked Accessorize Product Position':data.position
				}
				if (typeof(_kmq) !== 'undefined') {
					_kmq.push(['record', 'Recommended Item Clicked Accessorize', toKISS])
				}
				break
			case 'Also Bought':
				var toKISS = {
					'Recommended Item Clicked Also Bought Recommendation Place':'product',
					'Recommended Item Clicked Also Bought Clicked SKU':data.article,
					'Recommended Item Clicked Also Bought Clicked Product Name':data.name,
					'Recommended Item Clicked Also Bought Product Position':data.position
				}
				if (typeof(_kmq) !== 'undefined') {
					_kmq.push(['record', 'Recommended Item Clicked Also Bought', toKISS])
				}
				break
			case 'Also Viewed':
				var toKISS = {
					'Recommended Item Clicked Also Viewed Recommendation Place':'product',
					'Recommended Item Clicked Also Viewed Clicked SKU':data.article,
					'Recommended Item Clicked Also Viewed Clicked Product Name':data.name,
					'Recommended Item Clicked Also Viewed Product Position':data.position
				}
				if (typeof(_kmq) !== 'undefined') {
					_kmq.push(['record', 'Recommended Item Clicked Also Viewed', toKISS])
				}
				break
		}
	});



	var sendAnalytics = function(item) {
		if (typeof(MyThings) != "undefined") {
			//matches = item.match("\/cart\/add\/(\\d+)/_quantity")
			if (item.data('product') != "undefined") {
			//    productId = matches[1]

				MyThings.Track({
					EventType: MyThings.Event.Visit,
					Action: "1013",
					ProductId: item.data('product')
				})
			}
		}

		if (($('#adriverProduct').length || $('#adriverCommon').length) && (item.data('product') != "undefined")){
			 (function(s){
				var d = document, i = d.createElement('IMG'), b = d.body;
				s = s.replace(/![rnd]/, Math.round(Math.random()*9999999)) + '&tail256=' + escape(d.referrer || 'unknown');
				i.style.position = 'absolute'; i.style.width = i.style.height = '0px';
				i.onload = i.onerror = function()
				{b.removeChild(i); i = b = null}
				i.src = s;
				b.insertBefore(i, b.firstChild);
			})('http://ad.adriver.ru/cgi-bin/rle.cgi?sid=182615&sz=add_basket&custom=10='+item.data('product')+';11='+item.data('category')+'&bt=55&pz=0&rnd=![rnd]');
		}
	};


	// analytics HAS YOU
	if( 'ANALYTICS' in window ) {
		PubSub.subscribe( 'productBought', function() {
		})
	}

	/**
	 * KISS view category
	 */
	if ($('#_categoryData').length){
		var data = $('#_categoryData').data('category')
		var toKISS = {
			'Viewed Category Category Type':data.type,
			'Viewed Category Category Level':data.level,
			'Viewed Category Parent category':data.parent_category,
			'Viewed Category Category name':data.category,
			'Viewed Category Category ID':data.id
		}
		if (typeof(_kmq) !== 'undefined') {
			_kmq.push(['record', 'Viewed Category', toKISS]);
		}
	}

	/**
	 * KISS Search
	 */
	if ( $('#_searchKiss').length){
		var data = $('#_searchKiss').data('search')
		var toKISS = {
			'Search String':data.query,
			'Search Page URL':data.url,
			'Search Items Found':data.count
		}
		if (typeof(_kmq) !== 'undefined') {
			_kmq.push(['record', 'Search', toKISS]);
		}

		var KISSsearchClick = function(){
			var productData = $(this).data('add')
			var prToKISS = {
				'Search Results Clicked Search String':data.query,
				'Search Results Clicked SKU':productData.article,
				'Search Results Clicked Product Name':productData.name,
				'Search Results Clicked Page Number':productData.page,
				'Search Results Clicked Product Position':productData.position,
			}
			if (typeof(_kmq) !== 'undefined') {
				_kmq.push(['record', 'Search Results Clicked',  toKISS]);
			}
		}

		$('.goodsbox__inner').on('click', KISSsearchClick);
		$('.goodsboxlink').on('click', KISSsearchClick);
	}
});