/**
 * Логирование данных с клиента на сервер
 * https://wiki.enter.ru/pages/viewpage.action?pageId=11239960
 * 
 * @param  {Object} data данные отсылаемы на сервер
 */
window.logError = function(data) {
	if (data.ajaxUrl === '/log-json') {
		return;
	}
	if (!pageConfig.jsonLog){
		return false;
	}
	$.ajax({
		type: 'POST',
		global: false,
		url: '/log-json',
		data: data
	});
};

/**
 * Общие настройки AJAX
 */
$.ajaxSetup({
	timeout: 10000,
	statusCode: {
		404: function() {
			var ajaxUrl = this.url;
			var pageID = $('body').data('id');
			var data = {
				event: 'ajax_error',
				type:'404 ошибка',
				pageID: pageID,
				ajaxUrl:ajaxUrl
			};

			logError(data);
			if( typeof(_gaq) !== 'undefined' ){
				_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '404 ошибка, страница не найдена']);
			}
		},
		401: function() {
			if( $('#auth-block').length ) {
				$('#auth-block').lightbox_me({
					centered: true,
					onLoad: function() {
						$('#auth-block').find('input:first').focus();
					}
				});
			}
			else{
				if( typeof(_gaq) !== 'undefined' ){
					_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '401 ошибка, авторизуйтесь заново']);
				}
			}
				
		},
		500: function() {
			var ajaxUrl = this.url;
			var pageID = $('body').data('id');
			var data = {
				event: 'ajax_error',
				type:'500 ошибка',
				pageID: pageID,
				ajaxUrl:ajaxUrl
			};

			logError(data);
			if( typeof(_gaq) !== 'undefined' ){
				_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '500 сервер перегружен']);
			}
		},
		503: function() {
			var ajaxUrl = this.url;
			var pageID = $('body').data('id');
			var data = {
				event: 'ajax_error',
				type:'503 ошибка',
				pageID: pageID,
				ajaxUrl:ajaxUrl
			};

			logError(data);
			if( typeof(_gaq) !== 'undefined' ){
				_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '503 ошибка, сервер перегружен']);
			}
		},
		504: function() {
			var ajaxUrl = this.url;
			var pageID = $('body').data('id');
			var data = {
				event: 'ajax_error',
				type:'504 ошибка',
				pageID: pageID,
				ajaxUrl:ajaxUrl
			};

			logError(data);
			if( typeof(_gaq) !== 'undefined' ){
				_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '504 ошибка, проверьте соединение с интернетом']);
			}
		}
	},
	error: function (jqXHR, textStatus, errorThrown) {
		var ajaxUrl = this.url;
		if( jqXHR.statusText === 'error' ){
			// console.error(' неизвестная ajax ошибка')
			if( typeof(_gaq) !== 'undefined' ){
				_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', 'неизвестная ajax ошибка']);
			}

			var pageID = $('body').data('id');
			var data = {
				event: 'ajax_error',
				type:'неизвестная ajax ошибка',
				pageID: pageID,
				ajaxUrl:ajaxUrl
			};

			logError(data);
		}
		else if (textStatus === 'timeout'){
			return;
		}
	}
});
 
 
/** 
 * NEW FILE!!! 
 */
 
 
(function(){
  $(function(){
    if($('.bCtg__eMore').length) {
      var expanded = false;
      $('.bCtg__eMore').click(function(){
        if(expanded) {
          $(this).siblings('.more_item').hide();
          $(this).find('a').html('еще...');
        } else {
          $(this).siblings('.more_item').show();
          $(this).find('a').html('скрыть');
        }
        expanded = !expanded;
        return false;
      });
    }

    /* Cards Carousel  */
    function cardsCarouselTag ( nodes, noajax ) {
      var current = 1;

      var wi  = nodes.width*1;
      var viswi = nodes.viswidth*1;

      if( !isNaN($(nodes.times).html()) )
        var max = $(nodes.times).html() * 1;
      else
        var max = Math.ceil(wi / viswi);

      if((noajax !== undefined) && (noajax === true)) {
        var buffer = 100;
      } else {
        var buffer = 2;
      }

      var ajaxflag = false;


      var notify = function() {
        $(nodes.crnt).html( current );
        if(refresh_max_page) {
          $(nodes.times).html( max );
        }
        if ( current == 1 )
          $(nodes.prev).addClass('disabled');
        else
          $(nodes.prev).removeClass('disabled');
        if ( current == max )
          $(nodes.next).addClass('disabled');
        else
          $(nodes.next).removeClass('disabled');
      }

      var shiftme = function() {  
        var boxes = $(nodes.wrap).find('.goodsbox')
        $(boxes).hide()
        var le = boxes.length
        for(var j = (current - 1) * viswi ; j < current  * viswi ; j++) {
          boxes.eq( j ).show()
        }
      }

      $(nodes.next).bind('click', function() {
        if( current < max && !ajaxflag ) {
          if( current + 1 == max ) { //the last pull is loaded , so special shift

            var boxes = $(nodes.wrap).find('.goodsbox')
            $(boxes).hide()
            var le = boxes.length
            var rest = ( wi % viswi ) ?  wi % viswi  : viswi
            for(var j = 1; j <= rest; j++)
              boxes.eq( le - j ).show()
            current++
          } else {
            if( current + 1 >= buffer ) { // we have to get new pull from server

              $(nodes.next).css('opacity','0.4') // addClass dont work ((
              ajaxflag = true
              var getData = []
              if( $('form.product_filter-block').length )
                getData = $('form.product_filter-block').serializeArray()
              getData.push( {name: 'page', value: buffer+1 } )  
              $.get( $(nodes.prev).attr('data-url') , getData, function(data) {
                buffer++
                $(nodes.next).css('opacity','1')
                ajaxflag = false
                var tr = $('<div>')
                $(tr).html( data )
                $(tr).find('.goodsbox').css('display','none')
                $(nodes.wrap).html( $(nodes.wrap).html() + tr.html() )
                tr = null
              })
              current++
              shiftme()
            } else { // we have new portion as already loaded one     
              current++
              shiftme() // TODO repair
            }
          }
          notify()
        }
        return false
      })

      $(nodes.prev).click( function() {
        if( current > 1 ) {
          current--
          shiftme()
          notify()
        }
        return false
      })

      var refresh_max_page = false
    } // cardsCarousel object

    $('.carouseltitle').each( function(){
      if($(this).find('.jshm').html()) {
        var width = $(this).find('.jshm').html().replace(/\D/g,'');
      } else {
        var width = 3;
      }
      cardsCarouselTag({
        'prev'  : $(this).find('.back'),
        'next'  : $(this).find('.forvard'),
        'crnt'  : $(this).find('.none'),
        'times' : $(this).find('span:eq(1)'),
        'width' : width,
        'wrap'  : $(this).find('~ .carousel').first(),
        'viswidth' : 3
      });
    })
  });
})();

 
 
/** 
 * NEW FILE!!! 
 */
 
 
;
/**
 * Обработчик для кнопок купить
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, BlackBox
 * @param		{event}		e
 * @param		{Boolean}	anyway Если true событие будет все равно выполнено
 */
(function(){

	/**
	 * Добавление в корзину на сервере. Получение данных о покупке и состоянии корзины. Маркировка кнопок.
	 * 
	 * @param  {Event}	e
	 */
	var buy = function(e){
		var button = $(this);
		var url = button.attr('href');

		var addToCart = function(data) {
			if (!data.success) {
				return false;
			}

			var groupBtn = button.data('group');

			$('.jsBuyButton[data-group="'+groupBtn+'"]').html('В корзине').addClass('mBought').attr('href','/cart');
			$("body").trigger("addtocart", [data]);
		};

		$.get(url, addToCart);
	};

	/**
	 * Хандлер кнопки купить
	 * 
	 * @param  {Event}	e
	 */
	var BuyButtonHandler = function(e){
		e.stopPropagation();

		var button = $(this);

		if (button.hasClass('mDisabled')) {
			return false;
		}
		if (button.hasClass('mBought')) {
			var url = button.attr('href');
			document.location.href(url);
			return false;
		}

		button.trigger('buy', buy);
		return false;
	};

	/**
	 * Маркировка кнопок «Купить»
	 * см.BlackBox startAction
	 * 
	 * @param	{event}		event          
	 * @param	{Object}	markActionInfo Данные полученые из Action
	 */
	var markCartButton = function(event, markActionInfo){
		for (var i = 0, len = markActionInfo.product.length; i < len; i++){
			$('.'+markActionInfo.product[i].id).html('В корзине').addClass('mBought').attr('href','/cart');
		}
	};
	$("body").bind('markcartbutton', markCartButton);
	
	$(document).ready(function() {
		$('.jsBuyButton').on('click', BuyButtonHandler);
		$('.jsBuyButton').on('buy', buy);
	});
}());


/**
 * Показ окна о совершенной покупке, парсинг данных от сервера, аналитика
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, printPrice, BlackBox
 * @param		{event}		event 
 * @param		{Object}	data	данные о том что кладется в корзину
 */
(function(){
	/**
	 * KISS Аналитика для добавления в корзину
	 */
	var kissAnalytics = function(data){
		if (data.product){
			var productData = data.product;
			var nowUrl = window.location.href;
			var toKISS_pr = {
				'Add to Cart SKU':productData.article,
				'Add to Cart SKU Quantity':productData.quantity,
				'Add to Cart Product Name':productData.name,
				'Add to Cart Root category':productData.category[0].name,
				'Add to Cart Root ID':productData.category[0].id,
				'Add to Cart Category name':productData.category[productData.category.length-1].name,
				'Add to Cart Category ID':productData.category[productData.category.length-1].id,
				'Add to Cart SKU Price':productData.price,
				'Add to Cart Page URL':nowUrl,
				'Add to Cart F1 Quantity':productData.serviceQuantity,
			};

			if (typeof(_kmq) !== 'undefined') {
				_kmq.push(['record', 'Add to Cart', toKISS_pr ]);
			}
		}
		if (data.service){
			var serviceData = data.service;
			var productData = data.product;
			var toKISS_serv = {
				'Add F1 F1 Name':serviceData.name,
				'Add F1 F1 Price':serviceData.price,
				'Add F1 SKU':productData.article,
				'Add F1 Product Name':productData.name,
				'Add F1 Root category':productData.category[0].name,
				'Add F1 Root ID':productData.category[0].id,
				'Add F1 Category name':productData.category[productData.category.length-1].name,
				'Add F1 Category ID':productData.category[productData.category.length-1].id,
			};

			if (typeof(_kmq) !== 'undefined') {
				_kmq.push(['record', 'Add F1', toKISS_serv ]);
			}
		}
		if (data.warranty){
			var warrantyData = data.warranty;
			var productData = data.product;
			var toKISS_wrnt = {
				'Add Warranty Warranty Name':warrantyData.name,
				'Add Warranty Warranty Price':warrantyData.price,
				'Add Warranty SKU':productData.article,
				'Add Warranty Product Name':productData.name,
				'Add Warranty Root category':productData.category[0].name,
				'Add Warranty Root ID':productData.category[0].id,
				'Add Warranty Category name':productData.category[productData.category.length-1].name,
				'Add Warranty Category ID':productData.category[productData.category.length-1].id,
			};

			if (typeof(_kmq) !== 'undefined') {
				_kmq.push(['record', 'Add Warranty', toKISS_wrnt ]);
			}
		}
	};


	var buyProcessing = function(event, data){
		kissAnalytics(data);
		// sendAnalytics();

		if (!blackBox) {
			return false;
		}

		var basket = data.cart;
		var product = data.product;
		var tmpitem = {
			'title': product.name,
			'price' : printPrice(product.price),
			'imgSrc': product.img,
			'productLink': product.link,
			'totalQuan': basket.full_quantity,
			'totalSum': printPrice(basket.full_price),
			'linkToOrder': basket.link,
		};
		
		blackBox.basket().add(tmpitem);
	};

	$(document).ready(function() {
		$("body").bind('addtocart', buyProcessing);
	});
}());
 
 
/** 
 * NEW FILE!!! 
 */
 
 
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
	if (window.navigator.userAgent.indexOf ("MSIE") >= 0){
		$('.allpageinner').delegate( '.goodsbox__inner', 'hover', function() {
			$(this).toggleClass('hover');
		})
	}

	/* ---- */
	$('.goodsbox__inner').on('click', function(e) {
		if( $(this).attr('data-url') )
			window.location.href = $(this).attr('data-url')
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
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Перемотка к Id
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery
 */
(function(){
	var goToId = function(){
		var to = $(this).data('goto');
		$(document).stop().scrollTo( $('#'+to), 800 );
		return false;
	};
	
	$(document).ready(function() {
		$('.jsGoToId').bind('click',goToId);
	});
}());
 
 
/** 
 * NEW FILE!!! 
 */
 
 
;
/**
 * JIRA
 */
(function(){
	$.ajax({
		url: "https://jira.enter.ru/s/ru_RU-istibo/773/3/1.2.4/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector.js?collectorId=2e17c5d6",
		type: "get",
		cache: true,
		dataType: "script"
	});
	
	window.ATL_JQ_PAGE_PROPS =  {
		"triggerFunction": function(showCollectorDialog) {
			$("#jira").click(function(e) {
				e.preventDefault();
				showCollectorDialog();
			});
		}
	};
}());
 
 
/** 
 * NEW FILE!!! 
 */
 
 
$(document).ready(function(){

	(function(){
		/*register e-mail check*/
		if (!$('#register_username').length){
			return false;
		}

		var chEmail = true; // проверяем ли как e-mail
		var register = false;
		var firstNameInput = $('#register_first_name');
		var mailPhoneInput = $('#register_username');
		var subscibe = mailPhoneInput.parents('#register-form').find('.bSubscibe');
		var regBtn = mailPhoneInput.parents('#register-form').find('.bigbutton');

		subscibe.show();

		/**
		 * переключение типов проверки
		 */
		$('.registerAnotherWayBtn').bind('click', function(){
			if (chEmail){
				chEmail = false;
				$('.registerAnotherWay').html('Ваш мобильный телефон');
				$('.registerAnotherWayBtn').html('Ввести e-mail');
				mailPhoneInput.attr('maxlength', 10);
				mailPhoneInput.addClass('registerPhone');
				$('.registerPhonePH').show();
				subscibe.hide();
			}
			else{
				chEmail = true;
				$('.registerAnotherWay').html('Ваш e-mail');
				$('.registerAnotherWayBtn').html('У меня нет e-mail');
				mailPhoneInput.removeAttr('maxlength');
				mailPhoneInput.removeClass('registerPhone');
				$('.registerPhonePH').hide();
				subscibe.show();
			}
			mailPhoneInput.val('');
			register = false;
			regBtn.addClass('mDisabled');
		});

		regBtn.bind('click', function(){
			if (!register){
				return false;
			}

			if ( typeof(_gaq) !== 'undefined' ){
				var type = (chEmail)?'email':'mobile';
				_gaq.push(['_trackEvent', 'Account', 'Create account', type]);
			}
		});

		/**
		 * проверка заполненности инпутов
		 * @param  {Event} e
		 */
		var checkInputs = function(e){
			if (chEmail){ 
				// проверяем как e-mail
				if ( ((mailPhoneInput.val().search('@')) != -1)&&(firstNameInput.val().length>0) ) {
					register = true;
					regBtn.removeClass('mDisabled');
				}
				else {
					register = false;
					regBtn.addClass('mDisabled');
				}
			}
			else { 
				// проверяем как телефон
				subscibe.hide();
				if ( ((e.which>=96)&&(e.which<=105))||((e.which>=48)&&(e.which<=57))||(e.which==8) ) {
					//если это цифра или бэкспэйс
					;
				}
				else{
					//если это не цифра
					var clearVal = mailPhoneInput.val().replace(/\D/g,'');
					mailPhoneInput.val(clearVal);
				}

				if ( (mailPhoneInput.val().length == 10)&&(firstNameInput.val().length>0) ) {
					regBtn.removeClass('mDisabled');
					register = true;
				}
				else {
					register = false;
					regBtn.addClass('mDisabled');
				}
			}
		}

		mailPhoneInput.bind('keyup',function(e){
			checkInputs(e);
		});

		firstNameInput.bind('keyup',function(){
			checkInputs();
		});
	}());
	

	/**
	 * Подписка
	 */
	$('.bSubscibe').on('click', function(){
		if ($(this).hasClass('checked')){
			$(this).removeClass('checked');
			$(this).find('.subscibe').removeAttr('checked');
		}
		else{
			$(this).addClass('checked');
			$(this).find('.subscibe').attr('checked','checked');
		}
		return false;
	});


	/* GA categories referrer */
	function categoriesSpy( e ) {
		if( typeof(_gaq) !== 'undefined' ){
			_gaq.push(['_trackEvent', 'CategoryClick', e.data, window.location.pathname ]);
		}
		return true;
	}
	$('.bMainMenuLevel-1__eLink').bind('click', 'Верхнее меню', categoriesSpy )
	$('.bMainMenuLevel-2__eLink').bind('click', 'Верхнее меню', categoriesSpy )
	$('.bMainMenuLevel-3__eLink').bind('click', 'Верхнее меню', categoriesSpy )
	$('.breadcrumbs').first().find('a').bind( 'click', 'Хлебные крошки сверху', categoriesSpy )
	$('.breadcrumbs-footer').find('a').bind( 'click', 'Хлебные крошки снизу', categoriesSpy )
	$('.extramenu').find('a').on('click', 'Верхнее меню', categoriesSpy )
	$('.bCtg').find('a').bind('click', 'Левое меню', categoriesSpy )
	$('.rubrictitle').find('a').bind('click', 'Заголовок карусели', categoriesSpy )
	$('a.srcoll_link').bind('click', 'Ссылка Посмотреть все', categoriesSpy )
    /* GA click counter */
    function gaClickCounter() {
        if( typeof(_gaq) !== 'undefined' ) {
            var title =  ($(this).data('title') !== 'undefined') ?  $(this).data('title') : 'без названия';
            var nowUrl = window.location.href
            nowUrl.replace('http://www.enter.ru','')
            var linkUrl = $(this).attr('href')
            if ( $(this).data('event') == 'accessorize'){
            	_gaq.push(['_trackEvent', 'AdvisedAccessorises', nowUrl, linkUrl]);
            }
            else if( $(this).data('event') == 'related'){
				_gaq.push(['_trackEvent', 'AdvisedAlsoBuy', nowUrl, linkUrl]);
			}
			else{
				_gaq.push(['_trackEvent', $(this).data('event'), title,,,false])	
			}
        }
        return true
    }
    $('.gaEvent').bind('click', gaClickCounter );



	/* Authorization process */
	$('.open_auth-link').bind('click', function(e) {
		e.preventDefault();
		
		var el = $(this);
		window.open(el.attr('href'), 'oauthWindow', 'status = 1, width = 540, height = 420').focus();
	});
		
	$('#auth-link').click(function() {
		$('#auth-block').lightbox_me({
			centered: true,
			autofocus: true,
			onLoad: function() {
				$('#auth-block').find('input:first').focus();
			}
		});
		return false;
	});

	;(function($) {
		$.fn.warnings = function() {
			var rwn = $('<strong id="ruschars" class="pswwarning">RUS</strong>')
			rwn.css({
				'border': '1px solid red',
				'color': 'red',
				'border-radius': '3px',
				'background-color':'#fff',
				'position': 'absolute',
				'height': '16px',
				'padding': '1px 3px',
				'margin-top': '2px'
			})
			var cln = rwn.clone().attr('id','capslock').html('CAPS LOCK').css('marginLeft', '-78px')

			$(this).keypress(function(e) {
				var s = String.fromCharCode( e.which )
				if ( s.toUpperCase() === s && s.toLowerCase() !== s && !e.shiftKey ) {
					if( !$('#capslock').length ) $(this).after(cln)
				} else {
					if( $('#capslock').length ) $('#capslock').remove()
				}
			})
			$(this).keyup(function(e) {
				if( /[а-яА-ЯёЁ]/.test( $(this).val() ) ) {
					if( !$('#ruschars').length ) {
						if( $('#capslock').length )
							rwn.css('marginLeft','-116px')
						else
							rwn.css('marginLeft','-36px')
						$(this).after(rwn)
					}
				} else {
					if( $('#ruschars').length ) $('#ruschars').remove()
				}
			})
		}
	})(jQuery);

	$('#signin_password').warnings();

	$('#bUserlogoutLink').on('click', function(){
		if (typeof(_kmq) !== 'undefined') {
			_kmq.push(['clearIdentity']);
		}
	})

	$('#login-form, #register-form').data('redirect', true).bind('submit', function(e, param) {
		e.preventDefault();

		var form = $(this); //$(e.target)
		var wholemessage = form.serializeArray();

		form.find('[type="submit"]:first').attr('disabled', true).val('login-form' == form.attr('id') ? 'Вхожу...' : 'Регистрируюсь...');
		wholemessage["redirect_to"] = form.find('[name="redirect_to"]:first').val();

		var authFromServer = function(response) {
			if ( !response.success ) {
				form.html( $(response.data.content).html() );
				regEmailValid();
				return false;
			}

			if ('login-form' == form.attr('id')) {
				if ( typeof(_gaq) !== 'undefined' ){
					var type = ((form.find('#signin_username').val().search('@')) != -1)?'email':'mobile';
					_gaq.push(['_trackEvent', 'Account', 'Log in', type, window.location.href]);
				}

				if (typeof(_kmq) !== 'undefined') {
					_kmq.push(['identify', form.find('#signin_username').val() ]);
				}
			}
			else {
				if (typeof(_kmq) !== 'undefined') {
					_kmq.push(['identify', form.find('#register_username').val() ]);
				}
			}
			if ( form.data('redirect') ) {
				if (response.data.link) {
					window.location = response.data.link;
				}
				else {
					form.unbind('submit');
					form.submit();
				}
			}
			else {
				$('#auth-block').trigger('close');
				PubSub.publish( 'authorize', response.user );
			}

			//for order page
			if ( $('#order-form').length ){
				$('#user-block').html('Привет, <strong><a href="'+response.data.link+'">'+response.data.user.first_name+'</a></strong>');
				$('#order_recipient_first_name').val( response.data.user.first_name );
				$('#order_recipient_last_name').val( response.data.user.last_name );
				$('#order_recipient_phonenumbers').val( response.data.user.mobile_phone.slice(1) );
			}
		};

		$.ajax({
			type: 'POST',
			url: form.attr('action'),
			data: wholemessage,
			success: authFromServer
		});
	});

	$('#forgot-pwd-trigger').on('click', function(){
		$('#reset-pwd-form').show();
		$('#reset-pwd-key-form').hide();
		$('#login-form').hide();
		return false;
	});

	$('#remember-pwd-trigger,#remember-pwd-trigger2').click(function(){
		$('#reset-pwd-form').hide();
		$('#reset-pwd-key-form').hide();
		$('#login-form').show();
		return false;
	});

	$('#reset-pwd-form, #auth_forgot-form').submit(function(){
		var form = $(this);
		form.find('.error_list').html('Запрос отправлен. Идет обработка...');
		form.find('.whitebutton').attr('disabled', 'disabled')
		$.post(form.prop('action'), form.serializeArray(), function(resp){
			if (resp.success === true) {
				if ( typeof(_gaq) !== 'undefined' ){
					var type = ((form.find('input.text').val().search('@')) != -1)?'email':'mobile'
					_gaq.push(['_trackEvent', 'Account', 'Forgot password', type])
				}
				//$('#reset-pwd-form').hide();
				//$('#login-form').show();
				//alert('Новый пароль был вам выслан по почте или смс');
				var resetForm = $('#reset-pwd-form > div')
				resetForm.find('input').remove()
				resetForm.find('.pb5').remove()
				resetForm.find('.error_list').html('Новый пароль был вам выслан по почте или смс!')
			} else {
				var txterr = ( resp.error !== '' ) ? resp.error : 'Вы ввели неправильные данные'
				form.find('.error_list').text( txterr );
			}
		}, 'json');

		return false;
	})

	
	/* Infinity scroll */
	var ableToLoad = true
	var compact = $("div.goodslist").length
	var custom_jewel = $('.items-section__list').length
	function liveScroll( lsURL, filters, pageid ) {
		var params = []
		/* RETIRED cause data-filter
		if( $('.bigfilter.form').length ) //&& ( location.href.match(/_filter/) || location.href.match(/_tag/) ) )
			params = $('.bigfilter.form').parent().serializeArray()
		*/
		// lsURL += '/' +pageid + '/' + (( compact ) ? 'compact' : 'expanded')
		var tmpnode = ( compact ) ? $('div.goodslist') : $('div.goodsline:last')

		if(custom_jewel) {
			tmpnode = $('.items-section__list')
		}

		var loader =
			"<div id='ajaxgoods' class='bNavLoader'>" +
				"<div class='bNavLoader__eIco'><img src='/images/ajar.gif'></div>" +
				"<div class='bNavLoader__eM'>" +
					"<p class='bNavLoader__eText'>Подождите немного</p>"+
					"<p class='bNavLoader__eText'>Идет загрузка</p>"+
				"</div>" +
			"</div>"
		tmpnode.after( loader )
		//'?' + filters + 
		if( lsURL.match(/\?/) )
			lsURL += '&page=' + pageid
		else
			lsURL += '?page=' + pageid
		// if( $("#sorting").length ) {
		// 	params.push( { name:'sort', value : $("#sorting").data('sort') })
		// }

		$.get( lsURL, params, function(data){
			if ( data != "" && !data.data ) { // JSON === error
				ableToLoad = true
				if( compact || custom_jewel )
					tmpnode.append(data)
				else
					tmpnode.after(data)
			}
			$('#ajaxgoods').remove()
			if( $('#dlvrlinks').length ) {
				var coreid = []
				var nodd = $('<div>').html( data )
				nodd.find('div.boxhover, div.goodsboxlink').each( function() {
					var cid = $(this).data('cid') || 0
					if( cid )
						coreid.push( cid )
				})
				dajax.post( dlvr_node.data('calclink'), coreid )
			}

		})
	}

	if( $('div.allpager').length ) {
		$('div.allpager').each(function() {
			var lsURL = $(this).data('url') 
			var filters = ''//$(this).data('filter')
			var vnext = ( $(this).data('page') !== '') ? $(this).data('page') * 1 + 1 : 2
			var vinit = vnext - 1
			var vlast = parseInt('0' + $(this).data('lastpage') , 10)
			function checkScroll(){
				if ( ableToLoad && $(window).scrollTop() + 800 > $(document).height() - $(window).height() ){
					ableToLoad = false
					if( vlast + vinit > vnext )
						liveScroll( lsURL, filters, ((vnext % vlast) ? (vnext % vlast) : vnext ))
					vnext += 1
				}
			}
			if( location.href.match(/sort=/) &&  location.href.match(/page=/) ) { // Redirect on first in sort case
				$(this).bind('click', function(){
					docCookies.setItem( false, 'infScroll', 1, 4*7*24*60*60, '/' );
					location.href = location.href.replace(/page=\d+/,'')
				})
			} else {
				$(this).bind('click', function(){
					docCookies.setItem( false, 'infScroll', 1, 4*7*24*60*60, '/' );
					var next = $('div.pageslist:first li:first')
					if( next.hasClass('current') )
						next = next.next()
					var next_a = next.find('a')
									.html('<span>123</span>')
									.addClass('borderedR')
					next_a.attr('href', next_a.attr('href').replace(/page=\d+/,'') );
	
					$('div.pageslist li').remove()
					$('div.pageslist ul').append( next )
										 .find('a')
										 .bind('click', function(){
											docCookies.removeItem( 'infScroll' );
										  })
					$('div.allpager').addClass('mChecked')
					checkScroll()
					$(window).scroll( checkScroll )
				})
			}
		});

		if( docCookies.hasItem( 'infScroll' ) ){
			$('div.allpager:first').trigger('click');
		}
	}


	$('#jscity').autocomplete( {
		autoFocus: true,
		appendTo: '#jscities',
		source: function( request, response ) {
			$.ajax({
				url: $('#jscity').data('url-autocomplete'),
				dataType: "json",
				data: {
					q: request.term
				},
				success: function( data ) {
					var res = data.data.slice(0, 15)
					response( $.map( res, function( item ) {
						return {
							label: item.name,
							value: item.name,
							url: item.url
						}
					}));
				}
			});
		},
		minLength: 2,
		select: function( event, ui ) {
			$('#jschangecity').data('url', ui.item.url );
			$('#jschangecity').removeClass('mDisabled');
		},
		open: function() {
			$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
		},
		close: function() {
			$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
		}
	});

	// function paintRegions() {
	// 	$('.bCityPopupWrap').lightbox_me({ centered: true })
	// }
	
	function getRegions() {
		$('.popupRegion').lightbox_me({
			autofocus: true,
			onLoad: function(){
				if ($('#jscity').val().length){
					$('#jscity').putCursorAtEnd();
					$('#jschangecity').removeClass('mDisabled');
				}
			},
			onClose: function() {			
				if( !docCookies.hasItem('geoshop') ) {
					var id = $('#jsregion').data('region-id');
					docCookies.setItem( false, "geoshop", id, 31536e3, "/")
					// document.location.reload()
				}
			}
		});
	}

	$('.cityItem .moreCity').bind('click',function(){
		$(this).toggleClass('mExpand');
		$('.regionSlidesWrap').slideToggle(300);
	});

	$('#jsregion, .jsChangeRegion').click( function() {
		var authFromServer = function(res){
			if (!res.data.length){
				$('.popupRegion .mAutoresolve').html('');
				return false;
			}

			var url = res.data[0].url;
			var name = res.data[0].name;
			var id = res.data[0].id;

			if (id === 14974 || id === 108136){
				return false;
			}
			
			if ($('.popupRegion .mAutoresolve').length){
				$('.popupRegion .mAutoresolve').html('<a href="'+url+'">'+name+'</a>');
			}
			else{
				$('.popupRegion .cityInline').prepend('<div class="cityItem mAutoresolve"><a href="'+url+'">'+name+'</a></div>');
			}
			
		}

		var autoResolve = $(this).data("autoresolve-url");
		if (autoResolve !=='undefined'){
			$.ajax({
				type: 'GET',
				url: autoResolve,
				success: authFromServer
			});
		}
		
		getRegions();
		return false;
	});
	
	$('body').delegate('#jschangecity', 'click', function(e) {
		e.preventDefault();
		if( $(this).data('url') ){
			window.location = $(this).data('url');
		}
		else{
			$('.popupRegion').trigger('close');
		}
	});
	
	$('.inputClear').bind('click', function(e) {
		e.preventDefault();
		$('#jscity').val('');
  	});

  	$('.popupRegion .rightArr').bind('click', function(){
  		var regionSlideW = $('.popupRegion .regionSlides_slide').width() *1;
  		var sliderW = $('.popupRegion .regionSlides').width() *1;
  		var sliderLeft = parseInt($('.popupRegion .regionSlides').css('left'));
  		$('.popupRegion .leftArr').show();
  		$('.popupRegion .regionSlides').animate({'left':sliderLeft-regionSlideW});
		if ((sliderLeft-(regionSlideW*2)) <= -sliderW){
  			$('.popupRegion .rightArr').hide();
  		}
  	});
  	$('.popupRegion .leftArr').bind('click', function(){
  		var regionSlideW = $('.popupRegion .regionSlides_slide').width() *1;
  		var sliderW = $('.popupRegion .regionSlides').width() *1;
  		var sliderLeft = parseInt($('.popupRegion .regionSlides').css('left'));
  		$('.popupRegion .rightArr').show();
  		$('.popupRegion .regionSlides').animate({'left':sliderLeft+regionSlideW});
  		if (sliderLeft+(regionSlideW*2) >= 0){
  			$('.popupRegion .leftArr').hide();
  		}
  	});
   
	/* GEOIP fix */
	if( !docCookies.hasItem('geoshop') ) {
		getRegions();
	}
	
	/* Services Toggler */
	if( $('.serviceblock').length ) {
		$('.info h3').css('cursor', 'pointer').click( function() {
			$(this).parent().find('> div').toggle();
		});
		if( $('.info h3').length === 1 ){
			$('.info h3').trigger('click');
		}
	}
	
	/* prettyCheckboxes */
    $('.form input[type=checkbox],.form input[type=radio]').prettyCheckboxes();

	/* Rotator */
	// if ($('#rotator').length) {
	// 	$('#rotator').jshowoff({ speed:8000, controls:false })
	// 	$('.jshowoff-slidelinks a').wrapInner('<span/>')
	// }
	
	/* tags */
	$('.fm').toggle( 
		function(){
			$(this).parent().find('.hf').slideDown()
			$(this).html('скрыть')
		},
		function(){
			$(this).parent().find('.hf').slideUp()
			$(this).html('еще...')
		}
	);


	$('.bCtg__eMore').bind('click', function(e) {
		e.preventDefault();
		var el = $(this);
		el.parent().find('li.hf').slideToggle();
		var link = el.find('a');
		link.text('еще...' == link.text() ? 'скрыть' : 'еще...');
	});

	$('.product_filter-block input:submit').addClass('mDisabled');
	$('.product_filter-block input:submit').click( function(e) {
		if ( $(this).hasClass('mDisabled') ){
			e.preventDefault();
		}
	});
  
	/* Side Filter Block handlers */
	$(".bigfilter dt").click(function(){
		if ( $(this).hasClass('submit') ){
			return true;
		}
		$(this).next(".bigfilter dd").slideToggle(200)
		$(this).toggleClass("current");
		return false;
	});
	
	$(".f1list dt B").click(function(){
		$(this).parent("dt").next(".f1list dd").slideToggle(200)
		$(this).toggleClass("current");
		return false;
	});

	$(".tagslist dt").click(function(){
		$(this).next(".tagslist dd").slideToggle(200)
		$(this).toggleClass("current");
		return false;
	});
	
	var launch = false;
	var activateForm = function() {
		if ( !launch ) {
			$('.product_filter-block input:submit').removeClass('mDisabled')
			launch = true;
		}
	}
	$('.product_filter-block').change(function(){
		activateForm();
	})
	
	/* Side Filters */
    var filterlink = $('.filter .filterlink:first');
	var filterlist = $('.filter .filterlist');
	var clientBrowser = new brwsr();
	if( clientBrowser.isTouch ) {
		filterlink.click(function(){
			filterlink.hide();
			filterlist.show();
			return false;
		})
	}
	else {
		filterlink.mouseenter(function(){
			filterlink.hide();
			filterlist.show();
		});
		filterlist.mouseleave(function(){
			filterlist.hide();
			filterlink.show();
		});
	}	
	
	var ajaxFilterCounter = 0;
	
	$('.product_filter-block')
    .bind('change', function(e) {
        var el = $(e.target);

        if (el.is('input') && (-1 != $.inArray(el.attr('type'), ['radio', 'checkbox']))) {
            el.trigger('preview');
        }
    })
    .bind('preview', function(e) {
        var el = $(e.target)
        var form = $(this)
        var flRes = $('.filterresult');
        ajaxFilterCounter++
		var getFiltersResult = function(result) {
			ajaxFilterCounter--;
			if( ajaxFilterCounter > 0 ){
				return;
			}
			if( result.success ) {
                flRes.hide();
                switch (result.data % 10) {
                  case 1:
                    ending = 'ь';
                    break
                  case 2: case 3: case 4:
                    ending = 'и';
                    break
                  default:
                    ending = 'ей';
                    break
                }
                switch (result.data % 100) {
                  case 11: case 12: case 13: case 14:
                    ending = 'ей';
                    break
                }
                var firstli = null
                if ( el.is("div") ) //triggered from filter slider !
                	firstli = el
                else
	                firstli = el.parent().find('> label').first()
                	$('.result', flRes).text(result.data);
                	$('.ending', flRes).text(ending);
                	flRes.css('top',firstli.offset().top-$('.product_filter-block').offset().top).show();
                	
                var localTimeout = null
                $('.product_count-block')
					.hover(
						function() {
							if( localTimeout )
								clearTimeout( localTimeout )
						},
						function() {
							localTimeout = setTimeout( function() {
								flRes.hide();
							}, 4000  )
						}
						)
					.click(function() {
						form.submit()
					})
					.trigger('mouseout')
            }
        }

		var wholemessage = form.serializeArray();
		wholemessage["redirect_to"] = form.find('[name="redirect_to"]:first').val();
		$.ajax({
			type: 'GET',
			url: form.data('action-count'),
			data: wholemessage,
			success: getFiltersResult
		});
    });
    
	/* Sliders */
	$('.sliderbox').each( function(){
		var sliderRange = $('.filter-range', this);
		var filterrange = $(this);
		var papa = filterrange.parent();
		var mini = $('.slider-from',  $(this).next() ).val() * 1;
		var maxi = $('.slider-to',  $(this).next() ).val() * 1;
		var informator = $('.slider-interval', $(this).next());
		var from = papa.find('input:first');
		var to   = papa.find('input:eq(1)');
		informator.html( printPrice( from.val() ) + ' - ' + printPrice( to.val() ) );
		// var stepf = (/price/.test( from.attr('id') ) ) ?  10 : 1;
		var stepf = papa.find('.slider-interval').data('step');
		if (typeof(stepf)== undefined){
			var stepf = (/price/.test( from.attr('id') ) ) ?  10 : 1;
		}
		
		// if( maxi - mini <= 3 && stepf != 10 )
		// 	stepf = 0.1
		sliderRange.slider({
			range: true,
			step: stepf,
			min: mini,
			max: maxi,
			values: [ from.val()  ,  to.val() ],
			slide: function( e, ui ) {
				informator.html( printPrice( ui.values[ 0 ] ) + ' - ' + printPrice( ui.values[ 1 ] ) );
				from.val( ui.values[ 0 ] );
				to.val( ui.values[ 1 ] );
			},
			change: function(e, ui) {
				if ( parseFloat(to.val()) > 0 ){
					from.parent().trigger('preview');
					activateForm();
				}
			}
		});

	});


	/* ---- */
	if( $('.error_list').length && $('.basketheader').length ) {
		$.scrollTo( $('.error_list:first'), 300 )
	}

	/* Cards Carousel  */
	function cardsCarousel ( nodes, noajax ) {
		var self = this
		var current = 1

		var wi  = nodes.width*1
		var viswi = nodes.viswidth*1

		if( !isNaN($(nodes.times).html()) )
			var max = $(nodes.times).html() * 1
		else
			var max = Math.ceil(wi / viswi)			

		if((noajax !== undefined) && (noajax === true)) {
			var buffer = 100
		} else {
			var buffer = ($(nodes.times).parent().parent().hasClass('accessories')) ? 6 : 2
		}

		var ajaxflag = false

		this.notify = function() {
			$(nodes.crnt).html( current )
			if(refresh_max_page) {
				$(nodes.times).html( max )
			}
			if ( current == 1 )
				$(nodes.prev).addClass('disabled')
			else
				$(nodes.prev).removeClass('disabled')
			if ( current == max )
				$(nodes.next).addClass('disabled')
			else
				$(nodes.next).removeClass('disabled')
		}

		var shiftme = function() {	
			var boxes = $(nodes.wrap).find('.goodsbox')
			$(boxes).hide()
			var le = boxes.length
			for(var j = (current - 1) * viswi ; j < current  * viswi ; j++) {
				boxes.eq( j ).show()
			}
		}

		$(nodes.next).bind('click', function() {
			if(grouped_accessories[current_accessory_category]) {
				buffer = grouped_accessories[current_accessory_category]['buffer']
				if(!isNaN(grouped_accessories[current_accessory_category]['quantity'])) {
					wi = grouped_accessories[current_accessory_category]['quantity']
				}
			}
			if( current < max && !ajaxflag ) {
				if( current + 1 == max ) { //the last pull is loaded , so special shift

					var boxes = $(nodes.wrap).find('.goodsbox')
					$(boxes).hide()
					var le = boxes.length
					var rest = ( wi % viswi ) ?  wi % viswi  : viswi
					for(var j = 1; j <= rest; j++)
						boxes.eq( le - j ).show()
					current++
				} else {
					if( current + 1 >= buffer ) { // we have to get new pull from server

						$(nodes.next).css('opacity','0.4') // addClass dont work ((
						ajaxflag = true
						var getData = []
						if( $('form.product_filter-block').length )
							getData = $('form.product_filter-block').serializeArray()
						getData.push( {name: 'page', value: buffer+1 } )	
						getData.push( {name: 'categoryToken', value: current_accessory_category } )	
						$.get( $(nodes.prev).attr('data-url') , getData, function(data) {
							buffer++
							$(nodes.next).css('opacity','1')
							ajaxflag = false
							var tr = $('<div>')
							$(tr).html( data )
							$(tr).find('.goodsbox').css('display','none')
							$(nodes.wrap).html( $(nodes.wrap).html() + tr.html() )
							if(grouped_accessories[current_accessory_category]) {
								grouped_accessories[current_accessory_category]['accessories'] = $(nodes.wrap).html()
								grouped_accessories[current_accessory_category]['buffer']++
							}
							tr = null
				  		handle_custom_items()
						})
						current++
						shiftme()
					} else { // we have new portion as already loaded one			
						current++
						shiftme() // TODO repair
					}
				}
				self.notify()
			}
			return false
		})

		$(nodes.prev).click( function() {
			if( current > 1 ) {
				current--
				shiftme()
				self.notify()
			}
			return false
		})

		var refresh_max_page = false
		var current_accessory_category = '';
		var grouped_accessories = {
			'':{
				'quantity':parseInt($($(nodes.wrap).find('.goodsbox')[0]).attr('data-quantity')),
				'totalpages':parseInt($($(nodes.wrap).find('.goodsbox')[0]).attr('data-total-pages')),
				'accessories':$(nodes.wrap).html(),
				'buffer':buffer
			}
		}

		$('.categoriesmenuitem').click(function(){
			refresh_max_page = true
			var menuitem = $(this)
			if( !$(this).hasClass('active') ) {
				$(this).siblings('.active').addClass('link')
				$(this).siblings('.active').removeClass('active')
				$(this).addClass('active')
				$(this).removeClass('link')

				current_accessory_category = $(this).attr('data-category-token');
				if (current_accessory_category == undefined) {
					current_accessory_category = ''
				}

				if(grouped_accessories[current_accessory_category]) {
					$(nodes.wrap).html(grouped_accessories[current_accessory_category]['accessories'])
					if(!isNaN(grouped_accessories[current_accessory_category]['totalpages'])) {
						max = grouped_accessories[current_accessory_category]['totalpages']
					}
					if(!isNaN(grouped_accessories[current_accessory_category]['quantity'])) {
						width = grouped_accessories[current_accessory_category]['quantity']
					}

					current = 1
					shiftme()
					self.notify()
				} else {
					ajaxflag = true
					var getData = []
					getData.push( {name: 'page', value: 1 } )	
					getData.push( {name: 'categoryToken', value: current_accessory_category } )	
					$.get( $(this).attr('data-url') , getData, function(data) {
						buffer = 2
						$(nodes.wrap).html(data)
						width = parseInt($($(nodes.wrap).find('.goodsbox')[0]).attr('data-quantity'))
						max = parseInt($($(nodes.wrap).find('.goodsbox')[0]).attr('data-total-pages'))
						var xhr_category = $($(nodes.wrap).find('.goodsbox')[0]).attr('data-category')
						grouped_accessories[xhr_category] = {
							'quantity':width,
							'totalpages':max,
							'accessories':data,
							'buffer':buffer
						}
						current = 1
						shiftme()
						self.notify()
					}).done(function(data) {
						ajaxflag = false
				  })
				}
			}
			return false
		});

	} // cardsCarousel object

	$('.carouseltitle').each( function(){
		if( $(this).hasClass('carbig') && !$(this).hasClass('accessories') ) {
			var tmpline = new cardsCarousel ({
					'prev'  : $(this).find('.back'),
					'next'  : $(this).find('.forvard'),
					'crnt'  : $(this).find('span:first'),
					'times' : $(this).find('span:eq(1)'),
					'width' : $(this).find('.scroll').data('quantity'),
					'wrap'  : $(this).find('~ .bigcarousel').first(),
					'viswidth' : 5
				})		
		} else if( $(this).hasClass('carbig') && $(this).hasClass('accessories') ) {
			var tmpline = new cardsCarousel ({
					'prev'  : $(this).find('.back'),
					'next'  : $(this).find('.forvard'),
					'crnt'  : $(this).find('span:first'),
					'times' : $(this).find('span:eq(1)'),
					'width' : $(this).find('.scroll').data('quantity'),
					'wrap'  : $(this).find('~ .bigcarousel').first(),
					'viswidth' : 4
				})		
		} else {
			if( $(this).find('.jshm').length ) {
				var tmpline = new cardsCarousel ({
					'prev'  : $(this).find('.back'),
					'next'  : $(this).find('.forvard'),
					'crnt'  : $(this).find('.none'),
					'times' : $(this).find('span:eq(1)'),
					'width' : $(this).find('.jshm').html().replace(/\D/g,''),
//					'width' : $(this).find('.rubrictitle strong').html().replace(/\D/g,''),
					'wrap'  : $(this).find('~ .carousel').first(),
					'viswidth' : 3
				})
			}		
		}			
	})

	loadProductRelatedContainer($('#product_view-container'))
	loadProductRelatedContainer($('#product_also_bought-container'))
    loadProductRelatedContainer($('#product_user-also_viewed-container'))
    //loadProductRelatedContainer($('#product_buy-container')); // no such element
    //loadProductRelatedContainer($('#product_user-recommendation-container'));

    function loadProductRelatedContainer(container) {
        if (container.length) {
            $.ajax({
                url: container.data('url'),
                timeout: 20000
            }).success(function(result) {
                    container.html(result)
                    // console.log(111)
								    handle_custom_items()
                    container.fadeIn()      
                    var tmpline = new cardsCarousel ({
                            'prev'  : container.find('.back'),
                            'next'  : container.find('.forvard'),
                            'crnt'  : container.find('span:first'),
                            'times' : container.find('span:eq(1)'),
                            'width' : container.find('.scroll').data('quantity'),
                            'wrap'  : container.find('.bigcarousel'),
                            'viswidth' : 5
                        }, true )  // true === noajax for carousel                                       
            })
        }
    }

    $('.product_buy-container').each(function() {
        order = $(this).data('order')
        if (typeof(order) == 'object' && !$.isEmptyObject(order)) {
            $.ajax({
                url: ($(this).data('url')),
                data: order,
                type: 'POST',
                timeout: 20000
            })
        }
    })

	/* Delivery Ajax */
	function dlvrajax() {
		var that = this
		this.self = ''
		this.other = []
		this.node = null

		this.formatPrice = function(price) {
			if (typeof price === 'undefined' || price === null)
				return ''
			if (price > 0) 
				return ', '+price+' <span class="rubl">p</span>'
			else
				return ', бесплатно'
		}

		this.printError = function() {
			if( this.node )
				$(this.node).html( 'Стоимость доставки Вы можете уточнить в Контакт-сENTER 8&nbsp;(800)&nbsp;700-00-09' )
		}

		this.post = function( url, coreid ) {
			$.post( url, {ids:coreid}, function(data) {
				if( !('success' in data ) ) {
					that.printError()
					return false
				}
				if( !data.success || data.data.length === 0 ) {
					// that.printError()
					if( that.node )
						$(that.node).html('')
					return false					
				}
					
				for(var i=0; i < coreid.length; i++) {
					if( !data.data[ coreid[i] ] )
						continue
					for( var j in data.data[ coreid[i] ] ) {
						var dlvr = data.data[ coreid[i] ][ j ]			
						switch ( dlvr.token ) {
							case 'self':
								that.self = dlvr.date
								break
							default:
								that.other.push( { date: dlvr.date, price: dlvr.price, tc: ( typeof(dlvr.transportCompany) !== 'undefined') ? dlvr.transportCompany : false, days: dlvr.days, origin_date:dlvr.origin_date } )
						}
					}
					that.processHTML( coreid[i] )
					that.self = ''
					that.other = []					
				}
			})
		}
	} // dlvrajax object

	if( $('#dlvrlinks').length ) { // Extended List
		var dlvr_node = $('#dlvrlinks')
		dlvrajax.prototype.processHTML = function( id ) {
			var self = this.self,
				other = this.other
			var pnode = $( 'div[data-cid='+id+']' ).parent()
			var ul = $('<ul>')
			if(self)
				$('<li>').html( 'Возможен самовывоз ' + self ).appendTo( ul )
			for(var i=0; i < other.length; i++) {
				var tmp = 'Доставка ' + other[i].date
				tmp += ( other[i].price ) ? this.formatPrice( other[i].price ) : ''
				$('<li>').html( tmp ).appendTo( ul )
			}
			var uls = pnode.find( 'div.extrainfo ul' )
			uls.html( uls.html() + ul.html() )		
		}
		var coreid = []
		$('div.boxhover, div.goodsboxlink').each( function(){
			var cid = $(this).data('cid') || 0
			if( cid )
				coreid.push( cid )
		})
		var dajax = new dlvrajax()
		dajax.post( dlvr_node.data('calclink'), coreid )
	}
	
    if ( $('.delivery-info').length ) { // Product Card
    	var dlvr_node = $('.delivery-info')
    	var dajax = new dlvrajax()
    	var isSupplied = false
    	if ($('#productInfo').length){
    		var prData = $('#productInfo').data('value')
    		isSupplied = prData.isSupplied
    	}
    	dajax.node = dlvr_node
    	dlvrajax.prototype.processHTML = function( id ) {
			var self = this.self,
				other = this.other    	
			var html = '<h4>Как получить заказ?</h4><ul>'
			if( self )
				html += '<li><h5>Можно заказать сейчас и самостоятельно забрать в магазине ' +
						self + '</h5><div>&mdash; <a target="blank" href="' +
						dlvr_node.data('shoplink') + '">В каких магазинах ENTER можно забрать?</a></div></li>'	
			// console.log(other.length)
			if( other.length > 0 ){
				html += '<li><h5>Можно заказать сейчас с доставкой</h5>'
			}
			for(var i in other) {
				// console.info(other[i].date)
				// console.info(this.formatPrice(other[i].price))
				if (other[i].date !== undefined){
					html += '<div>&mdash; Можем доставить '+ other[i].date + this.formatPrice(other[i].price) +'</div>'
				}
				if( other[i].tc ) {
					html += '<div>&mdash; <a href="/how_get_order">Доставка осуществляется партнерскими транспортными компаниями</a></div>'
				}
			}
			if( other.length > 0 && isSupplied){
				html = '<h4>Доставка</h4><p>Через ~'+other[0].days+' дней<br/>планируемая дата поставки '+other[0].origin_date+'</p><p>Оператор контакт-cENTER согласует точную дату за 2-3 дня</p>'
				if (other[i].price === 0){
					html += '<p class="price">Бесплатно</p>'
				}
				else{
					html += '<p class="price">'+other[i].price+' <span class="rubl">p</span></p>'
				}
			}
			else{
				html += '</ul>'	
			}
			
			dlvr_node.html(html)
		}
    
		var coreid = [ dlvr_node.attr('id').replace('product-id-', '') ]
		
		dajax.post( dlvr_node.data('calclink'), coreid )
    }


	// if ( $('.searchtextClear').length ){
	// 	$('.searchtextClear').each(function(){
	// 		if(!$(this).val().length) {
	// 			$(this).addClass('vh');
	// 		} else {
	// 			$(this).removeClass('vh');
	// 		}
	// 	});

	// 	$('.searchtextClear').click(function(){
	// 		$(this).siblings('.searchtext').val('');
	// 		$(this).addClass('vh');
	// 		if($('#searchAutocomplete').length) {
	// 			$('#searchAutocomplete').html('');
	// 		}
	// 	});
	// }

	(function(){
		$(".items-section__list .item").hover(
		function() {
			$(this).addClass('hover')
		},
		function() {
			$(this).removeClass('hover')
		});

		$(".bigcarousel-brand .goodsbox").hover(
		function() {
			$(this).addClass('hover');
		},
		function() {
			$(this).removeClass('hover');
		});
	}());
});
 
 
/** 
 * NEW FILE!!! 
 */
 
 
;
/**
 * Всплывающая синяя плашка с предложением о подписке
 * Срабатывает при возникновении события showsubscribe.
 * см.BlackBox startAction
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, jQuery.emailValidate, docCookies
 * 
 * @param		{event}		event
 * @param		{Object}	subscribe			Информация о подписке
 * @param		{Boolean}	subscribe.agreed	Было ли дано согласие на подписку в прошлый раз
 * @param		{Boolean}	subscribe.show		Показывали ли пользователю плашку с предложением о подписке
 */
(function(){
	var lboxCheckSubscribe = function(event, subscribe){

		var notNowShield = $('.bSubscribeLightboxPopupNotNow'),
			subPopup = $('.bSubscribeLightboxPopup'),
			input = $('.bSubscribeLightboxPopup__eInput'),
			submitBtn = $('.bSubscribeLightboxPopup__eBtn'),
		
			subscribing = function(){
				if (submitBtn.hasClass('mDisabled')){
					return false;
				}

				var email = input.val(),
					url = $(this).data('url');
				//end of vars

				$.post(url, {email: email}, function(res){
					if( !res.success ){
						return false;
					}
					
					subPopup.html('<span class="bSubscribeLightboxPopup__eTitle mType">Спасибо! подтверждение подписки отправлено на указанный e-mail</span>');
					docCookies.setItem(false, 'subscribed', 1, 157680000, '/');
					if( typeof(_gaq) !== 'undefined' ){
						_gaq.push(['_trackEvent', 'Account', 'Emailing sign up', 'Page top']);
					}
					setTimeout(function(){
						subPopup.slideUp(300);
					}, 3000);
				});

				return false;
			},

			subscribeNow = function(){
				subPopup.slideDown(300);

				submitBtn.bind('click', subscribing);

				$('.bSubscribeLightboxPopup__eNotNow').bind('click', function(){
					var url = $(this).data('url');

					subPopup.slideUp(300, subscribeLater);
					docCookies.setItem(false, 'subscribed', 0, 157680000, '/');
					$.post(url);

					return false;
				});
			},

			subscribeLater = function(){
				notNowShield.slideDown(300);
				notNowShield.bind('click', function(){
					$(this).slideUp(300);
					subscribeNow();
				});
			};
		//end of vars

		input.placeholder();

		input.emailValidate({
			onValid: function(){
				input.removeClass('mError');
				submitBtn.removeClass('mDisabled');
			},
			onInvalid: function(){
				submitBtn.addClass('mDisabled');
				input.addClass('mError');
			}
		});

		if (!subscribe.show){
			if (!subscribe.agreed){
				subscribeLater();
			}
			return false;
		}
		else{
			subscribeNow();
		}
	};

	$("body").bind('showsubscribe', lboxCheckSubscribe);
}());
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Саджест для поля поиска
 * Нужен рефакторинг
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery
 */
;(function(){
	var nowSelectSuggest = -1;
	var suggestLen = 0;

	/**
	 * Хандлер на поднятие клавиши в поле поиска
	 * @param  {event} e
	 */
	var suggestUp = function(e){
        var text = $(this).attr('value');

        if (!text.length){
            if($(this).siblings('.searchtextClear').length) {
                $(this).siblings('.searchtextClear').addClass('vh');
            }
        }
        else {
            if($(this).siblings('.searchtextClear').length) {
                $(this).siblings('.searchtextClear').removeClass('vh');
            }
        }

		var authFromServer = function(response){
			$('#searchAutocomplete').html(response);
			suggestLen = $('.bSearchSuggest__eRes').length;
		};

        if ((e.which < 37 || e.which>40) && (nowSelectSuggest = -1)){
            if (!text.length){ 
                return false;
            }

            if($(this).siblings('.searchtextClear').length) {
                $(this).siblings('.searchtextClear').removeClass('vh');
            }
			
			$('.bSearchSuggest__eRes').removeClass('hover');
			nowSelectSuggest = -1;

			var url = '/search/autocomplete?q='+encodeURI(text);

			$.ajax({
				type: 'GET',
				url: url,
				success: authFromServer
			});
		}
	};

	/**
	 * Хандлер на нажатие клавиши в поле поиска
	 * @param  {event} e
	 */
	var suggestDown = function(e){
		/**
		 * маркировка пункта
		 */
		var markSuggest = function(){
			$('.bSearchSuggest__eRes').removeClass('hover').eq(nowSelectSuggest).addClass('hover');
		};

		/**
		 * стрелка вверх
		 */
		var upSuggestItem = function(){
			if (nowSelectSuggest-1 >= 0){
				nowSelectSuggest--;
				markSuggest();
			}
			else{
				nowSelectSuggest = -1;
				$('.bSearchSuggest__eRes').removeClass('hover');
				$(this).focus();
			}
			
		};

		/**
		 * стрелка вниз
		 */
		var downSuggestItem = function(){
			if (nowSelectSuggest+1 <= suggestLen-1){
				nowSelectSuggest++;
				markSuggest();
			}			
		};

		/**
		 * нажатие клавиши 'enter'
		 */
		var enterSuggest = function(){
			// suggest analitycs
			var link = $('.bSearchSuggest__eRes').eq(nowSelectSuggest).attr('href');
			var type = ($('.bSearchSuggest__eRes').eq(nowSelectSuggest).hasClass('bSearchSuggest__eCategoryRes')) ? 'suggest_category' : 'suggest_product';
			
			if ( typeof(_gaq) !== 'undefined' ){	
				_gaq.push(['_trackEvent', 'Search', type, link]);
			}
			document.location.href = link;
		};

		if (e.which === 38){
			upSuggestItem();
		}
		else if (e.which === 40){
			downSuggestItem();
		}
		else if (e.which === 13 && nowSelectSuggest !== -1){
			e.preventDefault();
			enterSuggest();
		}
		// console.log(nowSelectSuggest)
	};

	var suggestInputFocus = function(){
		nowSelectSuggest = -1;
		$('.bSearchSuggest__eRes').removeClass('hover');
	};

	var suggestInputClick = function(){
		$('#searchAutocomplete').show();
	};

	$(document).ready(function() {
		/**
		 * навешивание хандлеров на поле поиска
		 */
		$('.searchbox .searchtext').keydown(suggestDown).keyup(suggestUp).mouseenter(suggestInputFocus).focus(suggestInputFocus).click(suggestInputClick).placeholder();

		$('.searchbox .search-form').submit(function(){
			var text = $('.searchbox .searchtext').attr('value');
			if (!text.length){
				return false;
			}
		});

		$('.bSearchSuggest__eRes').on('mouseover', function(){
			$('.bSearchSuggest__eRes').removeClass('hover');
			var index = $(this).addClass('hover').index();
			nowSelectSuggest = index - 1;
		});

		$('body').click(function(e){		
			var targ = e.target.className;
			if (!(targ.indexOf('bSearchSuggest')+1 || targ.indexOf('searchtext')+1)) {
				$('#searchAutocomplete').hide();
			}
		});

		/**
		 * suggest analitycs
		 */
		$('.bSearchSuggest__eRes').on('click', function(){
			if ( typeof(_gaq) !== 'undefined' ){
				var type = ($(this).hasClass('bSearchSuggest__eCategoryRes')) ? 'suggest_category' : 'suggest_product';
				var url = $(this).attr('href');

				_gaq.push(['_trackEvent', 'Search', type, url]);
			}
		});
	});
}());
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/* Top Menu */
var menuDelayLvl1 = 300 //ms
var menuDelayLvl2 = 500 //ms
var triangleOffset = 15 //px

var lastHoverLvl1 = null
var checkedItemLvl1 = null
var hoverNowLvl1 = false

var lastHoverLvl2 = null
var checkedItemLvl2 = null

var currentMenuItemDimensions = null
var menuLevel2Dimensions = null
var menuLevel3Dimensions = null
var pointA = {x: 0,	y: 0}
var pointB = {x: 0,	y: 0}
var pointC = {x: 0,	y: 0}
var cursorNow = {x: 0, y: 0}

/**
 * Активируем элемент меню 1-го уровня
 *
 * @param  {element} el
 */
activateItemLvl1 = function(el){
	lastHoverLvl1 = new Date()
	checkedItemLvl1 = el
	$('.bMainMenuLevel-2__eItem').removeClass('hover')
	el.addClass('hover')
}

/**
 * Обработчик наведения на элемент меню 1-го уровня
 */
menuHoverInLvl1 = function(){
	var el = $(this)
	lastHoverLvl1 = new Date()
	hoverNowLvl1 = true

	setTimeout(function(){
		if(hoverNowLvl1 && (new Date() - lastHoverLvl1 > menuDelayLvl1)) {
			activateItemLvl1(el)
		}
	}, menuDelayLvl1 + 20)
}

/**
 * Обработчик ухода мыши из элемента меню 1-го уровня
 */
menuMouseLeaveLvl1 = function(){
	var el = $(this)
	el.removeClass('hover')
	hoverNowLvl1 = false
}

/**
 * Непосредственно построение треугольника. Требуется предвариательно получить нужные координаты и размеры
 */
createTriangle = function(){
	// левый угол - текущее положение курсора
	pointA = {
		x: cursorNow.x,
		y: cursorNow.y - $(window).scrollTop()
	}

	// верхний угол - левый верх меню 3го уровня минус triangleOffset
	pointB = {
		x: menuLevel3Dimensions.left - triangleOffset,
		y: menuLevel3Dimensions.top - $(window).scrollTop()
	}

	// нижний угол - левый низ меню 3го уровня минус triangleOffset
	pointC = {
		x: menuLevel3Dimensions.left - triangleOffset,
		y: menuLevel3Dimensions.top + menuLevel3Dimensions.height - $(window).scrollTop()
	}
}

/**
 * Проверка входит ли точка в треугольник.
 * Соединяем точку со всеми вершинами и считаем площадь маленьких треугольников.
 * Если она равна площади большого треугольника, то точка входит в треугольник. Иначе не входит.
 * Также точка входит в область задержки, если она попадает в прямоугольник, формируемый сдвигом треугольника
 * 
 * @param  {object} now    координаты точки, которую необходимо проверить
 * 
 * @param  {object} A      левая вершина большого треугольника
 * @param  {object} A.x    координата по оси x левой вершины
 * @param  {object} A.y    координата по оси y левой вершины
 * 
 * @param  {object} B      верхняя вершина большого треугольника
 * @param  {object} B.x    координата по оси x верхней вершины
 * @param  {object} B.y    координата по оси y верхней вершины
 * 
 * @param  {object} C      нижняя вершина большого треугольника
 * @param  {object} C.x    координата по оси x нижней вершины
 * @param  {object} C.y    координата по оси y нижней вершины
 * 
 * @return {boolean}       true - входит, false - не входит
 */
menuCheckTriangle = function(){
	var res1 = (pointA.x-cursorNow.x)*(pointB.y-pointA.y)-(pointB.x-pointA.x)*(pointA.y-cursorNow.y)
	var res2 = (pointB.x-cursorNow.x)*(pointC.y-pointB.y)-(pointC.x-pointB.x)*(pointB.y-cursorNow.y)
	var res3 = (pointC.x-cursorNow.x)*(pointA.y-pointC.y)-(pointA.x-pointC.x)*(pointC.y-cursorNow.y)

	if ((res1 >= 0 && res2 >= 0 && res3 >= 0) || (res1 <= 0 && res2 <= 0 && res3 <= 0) || (cursorNow.x >= pointB.x && cursorNow.x <= (pointB.x + triangleOffset) && cursorNow.y >= pointB.y && cursorNow.y <= pointC.y)){
		// console.info('принадлежит')
		return true
	}
	else{
		// console.info('не принадлежит')
		return false
	}
}

/**
 * Отслеживание перемещения мыши по меню 2-го уровня
 *
 * @param  {event} e
 */
menuMoveLvl2 = function(e){
	cursorNow = {
		x: e.pageX,
		y: e.pageY - $(window).scrollTop()
	}
	var el = $(this)
	if(el.attr('class') == checkedItemLvl2.attr('class')) {
		buildTriangle(el)
		lastHoverLvl2 = new Date()
	}
	checkHoverLvl2(el)
}

/**
 * Активируем элемент меню 2-го уровня, строим треугольник
 *
 * @param  {element} el
 */
activateItemLvl2 = function(el){
	checkedItemLvl2 = el
	el.addClass('hover')
	lastHoverLvl2 = new Date()
	buildTriangle(el)
}

/**
 * Обработчик наведения на элемент меню 2-го уровня
 */
menuHoverInLvl2 = function(){
	var el = $(this)
	checkHoverLvl2(el)
	el.addClass('hoverNowLvl2')

	if(lastHoverLvl2 && (new Date() - lastHoverLvl2 <= menuDelayLvl2) && menuCheckTriangle()) {
		setTimeout(function(){
			if(el.hasClass('hoverNowLvl2') && (new Date() - lastHoverLvl2 > menuDelayLvl2)) {
				checkHoverLvl2(el)
			}
		}, menuDelayLvl2 + 20)
	}
}

/**
 * Обработчик ухода мыши из элемента меню 1-го уровня
 */
menuMouseLeaveLvl2 = function(){
	var el = $(this)
	el.removeClass('hoverNowLvl2')
}

/**
 * Меню 2-го уровня
 * Если первое наведение - просто активируем
 * Иначе - проверяем условия по которым активировать
 *
 * @param  {element} el
 */
checkHoverLvl2 = function(el) {
	if (!lastHoverLvl2) {
		activateItemLvl2(el)
	} else if(!menuCheckTriangle() || (lastHoverLvl2 && (new Date() - lastHoverLvl2 > menuDelayLvl2) && menuCheckTriangle())) {
		checkedItemLvl2.removeClass('hover')
		activateItemLvl2(el)
	}
}

/**
 * Получаем все нужные координаты и размеры и строим треугольник, попадание курсора в который
 * будет определять нужна ли задержка до переключения на другой пункт меню
 *
 * @param  {element} el
 */
buildTriangle = function(el) {
	currentMenuItemDimensions = getDimensions(el)
	menuLevel2Dimensions = getDimensions(el.find('.bMainMenuLevel-3'))
	var dropMenuWidth = el.find('.bMainMenuLevel-2__eTitle')[0].offsetWidth
	menuLevel3Dimensions = {
		top: menuLevel2Dimensions.top,
		left: menuLevel2Dimensions.left + dropMenuWidth,
		width: menuLevel2Dimensions.width - dropMenuWidth,
		height: menuLevel2Dimensions.height
	}
	createTriangle()
}

/**
 * Получение абсолютных координат элемента и его размеров
 *
 * @param  {element} el
 */
getDimensions = function(el) {
		var width = $(el).width()
		var height = $(el).height()
		el = el[0]
    var x = 0
    var y = 0
    while(el && !isNaN(el.offsetLeft) && !isNaN(el.offsetTop)) {
        x += el.offsetLeft - el.scrollLeft
        y += el.offsetTop - el.scrollTop
        el = el.offsetParent
    }
    return { top: y, left: x, width: width, height: height }
}


$('.bMainMenuLevel-1__eItem').mouseenter(menuHoverInLvl1)
$('.bMainMenuLevel-1__eItem').mouseleave(menuMouseLeaveLvl1)

$('.bMainMenuLevel-2__eItem').mouseenter(menuHoverInLvl2)
$('.bMainMenuLevel-2__eItem').mousemove(menuMoveLvl2)
$('.bMainMenuLevel-2__eItem').mouseleave(menuMouseLeaveLvl2)





/* код ниже был закомментирован в main.js, перенес его сюда чтобы код, касающийся меню, был в одном месте */

// header_v2
// $('.bMainMenuLevel-1__eItem').bind('mouseenter', function(){
//  var menuLeft = $(this).offset().left
//  var cornerLeft = menuLeft - $('#header').offset().left + ($(this).find('.bMainMenuLevel-1__eTitle').width()/2) - 11
//  $(this).find('.bCorner').css({'left':cornerLeft})
// })

// header_v1
// if( $('.topmenu').length && !$('body#mainPage').length) {
//  $.get('/category/main_menu', function(data){
//    $('#header').append( data )
//  })
// }

// var idcm          = null // setTimeout
// var currentMenu = 0 // ref= product ID
// function showList( self ) {  
//  if( $(self).data('run') ) {
//    var dmenu = $(self).position().left*1 + $(self).width()*1 / 2 + 5
//    var punkt = $( '#extramenu-root-'+ $(self).attr('id').replace(/\D+/,'') )
//    if( punkt.length && punkt.find('dl').html().replace(/\s/g,'') != '' )
//      punkt.show()//.find('.corner').css('left', dmenu)
//  }
// }
// if( clientBrowser.isTouch ) {
//  $('#header .bToplink').bind ('click', function(){
//    if( $(this).data('run') )
//      return true
//    $('.extramenu').hide()  
//    $('.topmenu a.bToplink').each( function() { $(this).data('run', false) } )
//    $(this).data('run', true)
//    showList( this )
//    return false
//  })
// } else { 
//  $('#header .bToplink').bind( {
//    'mouseenter': function() {
//      $('.extramenu').hide()
//      var self = this       
//      $(self).data('run', true)
//      currentMenu = $(self).attr('id').replace(/\D+/,'')
//      var menuLeft = $(self).offset().left
//      var cornerLeft = menuLeft-$('#header').offset().left+($('#topmenu-root-'+currentMenu+'').width()/2)-13
//      $('#extramenu-root-'+currentMenu+' .corner').css({'left':cornerLeft})
//      idcm = setTimeout( function() { showList( self ) }, 300)
//    },
//    'mouseleave': function() {
//      var self = this

//      if( $(self).data('run') ) {
//        clearTimeout( idcm )
//        $(self).data('run',false)
//      }
//      //currentMenu = 0
//    }
//  })
// }

// $(document).click( function(e){
//  if (currentMenu) {
//    if( e.which == 1 )
//      $( '#extramenu-root-'+currentMenu+'').data('run', false).hide()
//  }
// })

// $('.extramenu').click( function(e){
//  e.stopPropagation()
// })

 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Кнопка наверх
 *
 * @requires	jQuery
 * @author		Zaytsev Alexandr
 */
;(function(){
	var upper = $('#upper');
		trigger = false,	//сработало ли появление языка

		pageScrolling = function(){
			if (($(window).scrollTop() > 600)&&(!trigger)){
				//появление языка
				trigger = true;
				upper.animate({'marginTop':'0'},400);
			}
			else if (($(window).scrollTop() < 600)&&(trigger)){
				//исчезновение
				trigger = false;
				upper.animate({'marginTop':'-30px'},400);
			}
		},

		goUp = function(){
			$(window).scrollTo('0px',400);
			return false;
		};
	//end of vars

	$(window).scroll(pageScrolling);
	upper.bind('click',goUp);
}());