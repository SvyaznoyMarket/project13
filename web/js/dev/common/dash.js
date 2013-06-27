$(document).ready(function(){
	
	// var carturl = $('.lightboxinner .point2').attr('href')


	/**
	 * Обработчик для кнопок купить
	 * 
	 * @param  {event} e
	 */
	var BuyButton = function(e){
		e.stopPropagation();

		var button = $(this);

		if (button.hasClass('disabled')) {
			return false;
		}
		if (button.hasClass('active')) {
			return false;
		}

		var url = button.attr('href');
		var productInfo = button.data('product');

		var addToCart = function(data) {
			if (data.success) {
				button.addClass('mBought');
				button.html('В корзине');
				kissAnalytics(data);
				sendAnalytics(button);
				
				if (blackBox) {
					var basket = data.data;
					var product = data.result.product;
					var tmpitem = {
						'id': productInfo.id,
						'title': product.name,
						'price' : product.price,
						'imgSrc': 'need image link',
						'totalQuan': basket.full_quantity,
						'totalSum': basket.full_price,
						'linkToOrder': basket.link,
					}
					blackBox.basket().add(tmpitem);
				}
			}
		}
		$.get(url, addToCart);
		return false;
	}
	$('.jsBuyButton').live('click', BuyButton);



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
		$('.bSimilarGoods.mProduct .bSimilarGoodsSlider_eGoods').live('click', kissSimilar)


		$('.bSimilarGoods.mCatalog .bSimilarGoodsSlider_eGoods a').live('click', sliderTracking)
	}


	var lboxCheckSubscribe = function(subscribe){
		var notNowShield = $('.bSubscribeLightboxPopupNotNow'),
			subPopup = $('.bSubscribeLightboxPopup'),
			input = $('.bSubscribeLightboxPopup__eInput'),
			submitBtn = $('.bSubscribeLightboxPopup__eBtn')
		
		input.placeholder()

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
		
		var subscribing = function(){
			if (submitBtn.hasClass('mDisabled'))
				return false

			var email = input.val(),
				url = $(this).data('url');

			$.post(url, {email: email}, function(res){
				if( !res.success )
					return false
				
				subPopup.html('<span class="bSubscribeLightboxPopup__eTitle mType">Спасибо! подтверждение подписки отправлено на указанный e-mail</span>')
				docCookies.setItem(false, 'subscribed', 1, 157680000, '/')
				if( typeof(_gaq) !== 'undefined' ){
					_gaq.push(['_trackEvent', 'Account', 'Emailing sign up', 'Page top'])
				}
				setTimeout(function(){
					subPopup.slideUp(300)
				}, 3000)
			})

			return false
		}

		var subscribeNow = function(){
			subPopup.slideDown(300)

			submitBtn.bind('click', subscribing)

			$('.bSubscribeLightboxPopup__eNotNow').bind('click', function(){
				var url = $(this).data('url')

				subPopup.slideUp(300, subscribeLater)
				docCookies.setItem(false, 'subscribed', 0, 157680000, '/')
				$.post(url)

				return false;
			})
		}

		var subscribeLater = function(){
			notNowShield.slideDown(300)
			notNowShield.bind('click', function(){
				$(this).slideUp(300)
				subscribeNow()
			})
		}

		if (!subscribe.show){
			if (!subscribe.agreed){
				subscribeLater()
			}
			return false
		}
		else{
			subscribeNow()
		}
	}
	
	var isInCart = false
	var changeButtons = function( lbox ){
		if(!lbox || !lbox.productsInCart ) return false
		for( var tokenP in lbox.productsInCart) { // Product Card
			var bx = $('div.boxhover[ref='+ tokenP +']')
			if( bx.length ) {
				var button = $('a.link1', bx)
				button.attr('href', $('.lightboxinner .point2').attr('href') )
				button.addClass('active')	//die('click') doesnt work
			}
			bx = $('div.goodsbarbig[ref='+ tokenP +']')
			if( bx.length ) {
				var button = $('a.link1', bx)
				button.attr('href', $('.lightboxinner .point2').attr('href') )
				$('body').addClass('bought')
				button.unbind('click')//.addClass('active')
				isInCart = true
				if( lbox.servicesInCart )
				for( var tokenS in lbox.servicesInCart ) {
					if( tokenP in lbox.servicesInCart[ tokenS ] ) {
						var button = $('div.mServ[ref='+ tokenS +'] a.link1')
						if( button.length ) {
							button.attr('href', $('.lightboxinner .point2').attr('href') ).text('В корзине')
						}
						button = $('td.bF1Block_eBuy[ref='+ tokenS +'] input.button')
						if( button.length ) {
							button.addClass('active').val('В корзине').attr( 'href', carturl )
						}
					}
				}
			}
		}
		if( lbox.servicesInCart )
		for( var tokenS in lbox.servicesInCart ) { // Service Card
			if( lbox.servicesInCart[ tokenS ][0] ) {
				var button = $('div.mServ[ref='+ tokenS +'] a.link1')
				if( button.length ) {
					button.attr('href', $('.lightboxinner .point2').attr('href') ).text('В корзине').addClass('active')
				}
				button = $('div.bServiceCard[ref='+ tokenS +'] input')
				if( button.length ) {
					button.val('В корзине').addClass('active').attr( 'href', carturl )
				}
			}
		}
		// if( lbox.is_credit )
		// 	if( $('#creditinput').length )
		// 		$('#creditinput').trigger('click')
	}
	/* ---- */

	// hover imitation for IE
	if (window.navigator.userAgent.indexOf ("MSIE") >= 0){
		$('.allpageinner').delegate( '.goodsbox__inner', 'hover', function() {
			$(this).toggleClass('hover');
		})
	}

	/* ---- */

	$('.goodsbox__inner').live('click', function(e) {
		if( $(this).attr('data-url') )
			window.location.href = $(this).attr('data-url')
	})

	/* GA tracks */
	var accessoriesMsg = {
		uri: window.location.pathname,
		atcl: $('.bGood__eArticle span:last').text().replace(/[^0-9\-]/g, '')
	}
	$('.bigcarousel').eq(0).bind('click', function(e) {
		if( typeof(_gaq) !== 'undefined' )
			_gaq.push(['_trackEvent', 'accessories_up', accessoriesMsg['atcl'], accessoriesMsg['uri'] ])
	})
	$('.bigcarousel').eq(1).bind('click', function(e) {
		if( typeof(_gaq) !== 'undefined' )
			_gaq.push(['_trackEvent', 'accessories_down', accessoriesMsg['atcl'], accessoriesMsg['uri'] ])
	})

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
	})

	/* F1 */
	if( $('div.bF1Info').length ) {
		var look    = $('div.bF1Info')
		var f1lines = $('div.bF1Block')
		// open popup
		$('.link1, .bF1Info_Logo', look).click( function(){
			if( $('div.hideblock.extWarranty').is(':visible') )
				$('div.hideblock.extWarranty').hide()
			f1lines.show()	
			return false
		})
		// close popup
		$('.close', f1lines).click( function(){
			f1lines.hide()
		})
		// add f1
		f1lines.find('input.button').bind ('click', function() {
			if( $(this).hasClass('active') ){
				window.location.href = $(this).attr('href')
				return false
			}
				
			$(this).val('В корзине').addClass('active').attr( 'href', carturl )
			var f1item = $(this).data()
			//credit case
// 			if( 'creditBox' in window ) {
// //				if( !f1item.url.match(/_quantity\/[0-9]+/) )
// //					f1item.url += '/1' //quantity
// 				if( creditBox.getState() )
// 					f1item.url += '1/1' //credit
// 				else 	
// 					f1item.url += '1/0' //no credit
// 			}			
			f1lines.fadeOut()
			$.getJSON( f1item.url, function(data) {
				if( !data.success )
					return true
				look.find('h3').text('Вы добавили услуги:')
				var f1line = tmpl('f1look', f1item)
				f1line = f1line.replace('F1ID', f1item.fid )
				look.find('.link1').before( f1line )


				// flybox
				var tmpitem = {
					'id'    : $('.goodsbarbig .link1').attr('href'),
					'title' : $('h1').html(),
					'vitems': data.data.full_quantity,
					'sum'   : data.data.full_price,
					'link'  : data.data.link,
					'price' : $('.goodsinfo .price').html(),
					'img'   : $('.goodsphoto img.mainImg').attr('src')
				}
				tmpitem.f1 = f1item
				if( isInCart )
					tmpitem.f1.only = 'yes'
				ltbx.getBasket( tmpitem )
				kissAnalytics(data)
				if( !isInCart ) {
					isInCart = true
					markPageButtons()
				}
			})
			return false
		})
		// remove f1
		$('a.bBacketServ__eMore', look).live('click', function(){
			var thislink = this
			$.getJSON( $(this).attr('href'), function(data) {
				if( !data.success )
					return true
				var line = $(thislink).parent()
				f1lines.find('td[ref='+ line.attr('ref') +']').find('input').val('Купить услугу').removeClass('active')
				line.remove()
				ltbx.update({ sum: data.data.full_price })

				if( !$('a.bBacketServ__eMore', look).length )
					look.find('h3').html('Выбирай услуги F1<br/>вместе с этим товаром')
			})
			return false
		})
	}

	/* draganddrop */
	var draganddrop = new DDforLB( $('.allpageinner'), ltbx )	
	$('.goodsbox__inner[ref] .mainImg').live('mousedown', function(e){
			e.stopPropagation()
			e.preventDefault()
			if(e.which == 1)
				draganddrop.prepare( e.pageX, e.pageY, parseItemNode(currentItem) ) // if delta then d&d
	})
	$('.goodsbox__inner[ref] .mainImg').live('mouseup', function(e){
		draganddrop.cancel()
	})

	function DDforLB( outer , ltbx ) {	
		if (! outer.length ) 
			return null

		var self     = this
		var lightbox = ltbx
		var isactive = false
		var icon     = null
		var margin   = 25 // gravitation parameter
		var wdiv2    = 30 // draged box halfwidth
		var containers = $('.dropbox')
		if (! containers.length ) 
			return null
		var abziss 	 = []		
		var ordinat  = 0
		var itemdata = null

		/* initia */
		var divicon = $('<div>').addClass('dragbox').css('display','none')
		var icon = $('<div>')
		divicon.append( icon )
		$(outer).append( divicon )
		var shtorka = $('<div>').addClass('graying')
				.css( {'display':'none', 'opacity': '0.5'} ) //ie special							
		$(outer).append( shtorka )
		var shtorkaoffset = 0

		this.cancel = function() {
			$(document).unbind('mousemove.dragitem')
		}

		$(document).bind('mouseup', function(e) {
		if(e.target.id == 'dragimg')
			self.cancel()
		})

		this.prepare = function( pageX, pageY, item ) {
			itemdata = item
			if(  $( '.goodsbox__inner[ref='+ itemdata.id +'] a.link1').hasClass('active') ) 
				return
			$(document).bind('mousemove.dragitem', function(e) {
				e.preventDefault()
				if(! isactive) {
					if( Math.abs(pageX - e.pageX) > margin || Math.abs(pageY - e.pageY) > margin ) {
						self.turnon(e.pageX, e.pageY)
						isactive = true
					}
				} else {		
					icon.css({'left':e.pageX - wdiv2, 'top':e.pageY - shtorkaoffset - wdiv2 })
					ordinat = $(containers[0]).offset().top

					if( e.pageY + wdiv2 > ordinat - margin &&
						e.pageX + wdiv2 > abziss[0] - margin && e.pageX - 30 < abziss[0] + 70 + margin ) { // mouse in HOT area
	//					e.pageX + wdiv2 > abziss[0] - margin && e.pageX - 30 < abziss[2] + 70 + margin ) { // mouse in HOT area
						/*var cindex = 3
						if( e.pageX  < abziss[0] + 70 + margin )
							cindex = 1
						else if( e.pageX < abziss[1]  + 70 + margin )
							cindex = 2*/

						lightbox.toFire( 3 ) // to burn the box !
					} else
						lightbox.putOutBoxes() // run checking is inside
				}
			})		
		}

		this.turnon = function( pageX, pageY ) {
			lightbox.clear()
			shtorka.show()
			shtorkaoffset = shtorka.offset().top
			icon.html( $('<img>').css({'width':60, 'height':60}).attr({'id':'dragimg','width':60, 'height':60, 'alt':'', 'src': itemdata.img }) )
			icon.css({'left':pageX - wdiv2, 'top':pageY - shtorkaoffset - wdiv2 })

			divicon.show()
			lightbox.getContainers()		
			for(var i=0; i < containers.length; i++) {
				abziss[i] = $(containers[i]).offset().left
			}	
			$(document).bind('mouseup.dragitem', function() {
				if( fbox = lightbox.gravitation( ) ) {
					//$(document).unbind('mousemove')
					$(document).unbind('.dragitem')
					icon.animate( {
	//						left: abziss[ fbox - 1 ] + 5,
							left: abziss[ 0 ] + 5,
							top: ordinat - shtorkaoffset + 5
						} , 400, 
						function() { self.finalize( fbox ) } )
				} else 
					self.turnoff()
			})		
		}

		this.turnoff = function() {
			isactive = false
			shtorka.fadeOut()
			divicon.hide()
			lightbox.hideContainers()
			//$(document).unbind('mousemove')
			$(document).unbind('.dragitem')
		}

		this.finalize = function( actioncode ) {
			setTimeout(function(){
				self.turnoff()
				switch( actioncode ) {
					case 1: //comparing
						lightbox.getComparing( itemdata )
						break
					case 2: //wishes 
						lightbox.getWishes( itemdata )
						break
					case 3: //basket
						$.getJSON( $( '.goodsbox__inner[ref='+ itemdata.id +'] a.link1').attr('href'), function(data) {
							if ( data.success && ltbx ) {
								var tmpitem = itemdata
								tmpitem.vitems = data.data.full_quantity
								tmpitem.sum = data.data.full_price
								ltbx.getBasket( tmpitem )
								kissAnalytics(data)
							}	
						})
						//lightbox.getBasket( itemdata )
						break
				}
			}, 400)
		}

	} // DDforLB object
	
	/* EXT WARRANTY */
	if ( ($('div.bBlueButton.extWarranty').length)&&($('div.bBlueButton.extWarranty').is(':visible')) ){
		var look_extWarr = $('div.bBlueButton.extWarranty')
		var f1lines_extWarr = $('div.hideblock.extWarranty')
		var ew_look = $("#ew_look")
		//open popup
		$('.link1',look_extWarr).click( function(){
			if( $('div.bF1Block').is(':visible') )
				$('div.bF1Block').hide()
			f1lines_extWarr.show()
			return false
		})
		//close popup
		$('.close', f1lines_extWarr).click( function(){
			f1lines_extWarr.hide()
		})
		//add warranty
		f1lines_extWarr.find('input.button').bind ('click', function() {
			if( $('input.button',f1lines_extWarr).hasClass('active') ){
				$('input.button',f1lines_extWarr).val('Выбрать').removeClass('active');
			}
			$(this).val('Выбрана').addClass('active')
			var extWarr_item = $(this).data()
			f1lines_extWarr.fadeOut()
			$.getJSON( extWarr_item.url, function(ext_data) {
				if( !ext_data.success )
					return true
				$('.link1',look_extWarr).text('Изменить гарантию')
				look_extWarr.find('h3').text('Вы выбрали гарантию:')

				$('.ew_title', ew_look).text(extWarr_item.f1title)
				$('.ew_price', ew_look).text(extWarr_item.f1price)
				$('.bBacketServ__eMore', ew_look).attr('href', extWarr_item.deleteurl)
				ew_look.show()
				var tmpitem = {
					'id'    : $('.goodsbarbig .link1').attr('href'),
					'title' : $('h1').html(),
					'vitems': ext_data.data.full_quantity,
					'sum'   : ext_data.data.full_price,
					'link'  : ext_data.data.link,
					'price' : $('.goodsinfo .price').html(),
					'img'   : $('.goodsphoto img.mainImg').attr('src')
				}
				tmpitem.f1 = extWarr_item
				if( isInCart )
					tmpitem.f1.only = 'yes'
				ltbx.getBasket( tmpitem )
				kissAnalytics(ext_data)
				if( !isInCart ) {
					isInCart = true
					markPageButtons()
				}
			})
			return false
		})
		$('.bBacketServ__eMore', ew_look).live('click', function(e){
			e.preventDefault()
			var thislink = this
			$.getJSON( $(this).attr('href'), function(ext_data) {
				if( !ext_data.success )
					return true
				var line = $(thislink).parent()
				$('input.button',f1lines_extWarr).val('Выбрать').removeClass('active');
				$('.link1',look_extWarr).text('Выбрать гарантию')
				line.hide()
				ltbx.update({ sum: ext_data.data.full_price })
				ew_look.hide()
				if( !$('a.bBacketServ__eMore', look_extWarr).length )
					look_extWarr.find('h3').html('Выбирай услуги F1<br/>вместе с этим товаром')
			})
			return false
		})
	}
	
	/* buy bottons */
	var markPageButtons = function(){
		var carturl = $('.lightboxinner .point2').attr('href')
		$('body').addClass('bought')
		$('.goodsbarbig .link1').attr('href', carturl )//.addClass('active')
		$('#bigpopup a.link1').attr('href', carturl )//.addClass('active')//.html('в корзине')
		$('.bSet__ePrice .link1').unbind('click')
		$('.goodsbar .link1').die('click')
		//$('.bCountSet__eP').addClass('disabled')
		//$('.bCountSet__eM').addClass('disabled')
	}
	
	/* stuff go to litebox */
	function parseItemNode( ref ){
		var jn = $( 'div[ref='+ ref +']')
		var item = {
			'id'   : $(jn).attr('ref'),
			'title': $('h3 a', jn).html(),
			'price': $('.price', jn).html(),
			'img'  : $('.photo img.mainImg', jn).attr('src')
		}
		return item
	}

	function sendAnalytics(item) {
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
	}

	var kissAnalytics = function(data){
		if (data.result.product){
			var productData = data.result.product
			var nowUrl = window.location.href
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
			}
			// console.log(toKISS_pr)
			if (typeof(_kmq) !== 'undefined') {
				_kmq.push(['record', 'Add to Cart', toKISS_pr ])
			}
		}
		if (data.result.service){
			var serviceData = data.result.service
			var productData = data.result.product
			var toKISS_serv = {
				'Add F1 F1 Name':serviceData.name,
				'Add F1 F1 Price':serviceData.price,
				'Add F1 SKU':productData.article,
				'Add F1 Product Name':productData.name,
				'Add F1 Root category':productData.category[0].name,
				'Add F1 Root ID':productData.category[0].id,
				'Add F1 Category name':productData.category[productData.category.length-1].name,
				'Add F1 Category ID':productData.category[productData.category.length-1].id,
			}
			// console.log(toKISS_serv)
			if (typeof(_kmq) !== 'undefined') {
				_kmq.push(['record', 'Add F1', toKISS_serv ])
			}
		}
		if (data.result.warranty){
			var warrantyData = data.result.warranty
			var productData = data.result.product
			var toKISS_wrnt = {
				'Add Warranty Warranty Name':warrantyData.name,
				'Add Warranty Warranty Price':warrantyData.price,
				'Add Warranty SKU':productData.article,
				'Add Warranty Product Name':productData.name,
				'Add Warranty Root category':productData.category[0].name,
				'Add Warranty Root ID':productData.category[0].id,
				'Add Warranty Category name':productData.category[productData.category.length-1].name,
				'Add Warranty Category ID':productData.category[productData.category.length-1].id,
			}
			// console.log(toKISS_wrnt)
			if (typeof(_kmq) !== 'undefined') {
				_kmq.push(['record', 'Add Warranty', toKISS_wrnt ])
			}
		}
	}

	// analytics HAS YOU
	if( 'ANALYTICS' in window ) {
		PubSub.subscribe( 'productBought', function() {
		})
	}


	// KISS view category
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

	// KISS Search
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

		$('.goodsbox__inner').live('click', KISSsearchClick)
		$('.goodsboxlink').live('click', KISSsearchClick)
	}
})

