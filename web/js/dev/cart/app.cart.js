$(document).ready(function() {

	/* F1 sale card*/
	if ( $('.bF1SaleCard').length ){
		var input = $('#F1SaleCard_number')
		var btn = $('#F1SaleCard_btn')
		var delBtn = $('.bF1SaleCard_eDel')
		btn.bind('click', function(){
			// var url = btn.data('url')
			var url = $('.bF1SaleCard_eRadio:checked').data('url')

			var authFromServer = function(response) {
				if ( response.success ) {
					window.location.reload()
				}
				else{
					$('#bF1SaleCard_eErr').html('Извините, карта с таким номером не найдена.')
				}
			}

			var data = {
				number: input.val()
			}

			$.ajax({
				type: 'POST',
				url: url,
				data: data,
				success: authFromServer
			})

		})


		delBtn.on('click',function(){
			var delUrl = $(this).data('url')
			var authFromServer = function(response) {
				if ( response.success ) {
					window.location.reload()
				}
			}
			$.ajax({
				type: 'POST',
				url: delUrl,
				success: authFromServer
			})
		})

		$(".bF1SaleCard_eRadio").bind('change', function(){
			if ( $('#cartCertificateAll').is(':checked')){
				input.attr('placeholder','Код скидки')
			}
			else if ($('#cartCertificateF1').is(':checked')){
				input.attr('placeholder', 'Номер карты «Под защитой F1»')
			}
		})
	}


	//KISS
	if ($('#_cartKiss').length){
		var data = $('#_cartKiss').data('cart')
		var toKISS = {
			'View Cart SKU Quantity':data.count,
			'View Cart SKU Total':data.price,
		}
		if (typeof(_kmq) !== 'undefined') {
			_kmq.push(['record', 'View Cart', toKISS])
		}
	}

	/* basket */
	var total = $('#total .price')
	var totalCash = 0
	var focusTrigger = false;

	if ($('.product_kit-data').length){
		$('.product_kit-data').bind('click', function(){
			var elems = $(this).data('value')
			$('.bKitPopup').empty()
			for (obj in elems){
				var kitLine = tmpl('bKitPopupLine_Tmpl', elems[obj])
				$('.bKitPopup').append(kitLine)
			}
			$('#kitPopup').lightbox_me({
				centered: true
			})
			return false
		})
	}

	function checkServWarranty() {
		// console.info('проверка наличия расширенных гарантий')
		warr = $('.bBacketServ.extWarr.mBig')
		$.each(warr, function(){
			service = $(this)
			if ( service.find('tr[ref]').length ){
				// есть добавленные услуги
				return true
			}
			else if ( service.is(':visible') ){
				// услуг добавленных нет, но блок большой
				var good = service.parents('.basketline') //текущий товар
				good.find('.bBacketServ.extWarr.mSmall').show()
				good.find('.bBacketServ.extWarr.mBig').hide()
				return false
			}
		})
	}

	var checkServF1 = function() {

		// console.info('проверка наличия услуг')
		var serv = $('.bBacketServ.F1.mBig')
		var res = false

		$.each(serv, function(){
			service = $(this)
			if ( service.find('tr[ref]').length ){
				// есть добавленные услуги
				res = true
			}
			else if ( service.is(':visible') ){
				// услуг добавленных нет, но блок большой
				var good = service.parents('.basketline') //текущий товар
				good.find('.bBacketServ.F1.mSmall').show()
				good.find('.bBacketServ.F1.mBig').hide()
				res = false
			}
		})
		return res
	}

	var checkForSaleCard = function() {
		var hasF1 = checkServF1();
		var allBlock = $('.bF1SaleCard');
		var hasCoupon = $('.bF1SaleCard_eComplete.mCoupon').length;
		var hasSertificate = $('.bF1SaleCard_eComplete.mSertificate').length;
		var config = $('#page-config').data('value');
		var f1Certificate = config.f1Certificate;
		var coupon = config.coupon;
		var form = $('.bF1SaleCard_eForm');
		var input = $('#F1SaleCard_number');

		
		// console.log('c '+coupon)
		// console.log('s '+f1Certificate)
		// console.log('hasC '+hasCoupon)
		// console.log('hasS '+hasSertificate)
		// console.log('hasF1 '+hasF1)


		if (coupon && !f1Certificate){ // купоны включены, сертификаты выключены
			form.removeClass('m2Coupon')
			input.attr('placeholder','Код скидки')
			$('.bF1SaleCard_eRadio').removeAttr('checked');
			$('#cartCertificateAll').attr('checked', 'checked');
			
			if (!hasCoupon){
				form.show();
			}
			if (hasCoupon){
				form.hide();
			}
		}
		else if (!coupon && f1Certificate){ // купоны выключены, сертификаты включены
			form.removeClass('m2Coupon')
			input.attr('placeholder', 'Номер карты «Под защитой F1»');
			$('.bF1SaleCard_eRadio').removeAttr('checked');
			$('#cartCertificateF1').attr('checked', 'checked');

			if (hasF1){
				allBlock.show();
				form.show();
			}
			if (!hasF1){
				allBlock.hide();
				form.hide();
			}
		}
		else if (coupon && f1Certificate){ // купоны и сертификаты включены

			if (!hasF1 && !hasCoupon){ // нет выбранных услуг F1 и нет оформленных купонов
				form.show().removeClass('m2Coupon')
				input.attr('placeholder','Код скидки')
				$('.bF1SaleCard_eRadio').removeAttr('checked');
				$('#cartCertificateAll').attr('checked', 'checked');
			}
			else if (hasF1 && !hasCoupon && !hasSertificate){ // есть выбранная услуга F1, нет оформленных купонов и сертификатов
				form.show().addClass('m2Coupon');
				input.attr('placeholder','Код скидки')
				$('.bF1SaleCard_eRadio').removeAttr('checked');
				$('#cartCertificateAll').attr('checked', 'checked');
			}
			else if (hasF1 && hasCoupon && !hasSertificate){ // есть выбранная услуга F1, нет оформленных сертификатов, есть оформленный купон
				form.show().removeClass('m2Coupon')
				input.attr('placeholder', 'Номер карты «Под защитой F1»');
				$('.bF1SaleCard_eRadio').removeAttr('checked');
				$('#cartCertificateF1').attr('checked', 'checked');
			}
			else if (hasCoupon && hasSertificate){ // есть оформленный сертификат и есть оформленный купон
				form.hide()
			}

		}
		else{ // все выключено
			form.hide()
		}

	}
	// init f1 sale card
	checkForSaleCard();

	function showOldPrice(oldPrice) {

		if ( $('.bF1SaleCard_eComplete').length === 1 && $('.bF1SaleCard_eComplete.mError').length){
			$('#commonSum .oldPrice').hide()
			return false
		}
		// скрытие отображение старой цены
		if ( $('.bF1SaleCard_eComplete').length){
			$('#commonSum .oldPrice').show()
		}
		else{
			$('#commonSum .oldPrice').hide()
		}

		$('#totalOldPrice').html(printPrice( oldPrice ))
	}

	function showPrice(price){
		if( !price ) {
			location.reload(true);
		}

		checkForSaleCard();

		total.html( printPrice( price ) );
		total.typewriter(800);
		totalCash = price;
	}

	function getTotal() {
		checkServWarranty()
		for(var i=0, tmp=0; i < basket.length; i++ ) {
			if( ! basket[i].noview && $.contains( document.body, basket[i].hasnodes[0] ) )
				tmp += basket[i].sum * 1
		}
		if( !tmp ) {
			location.reload(true)
		}
		total.html( printPrice( tmp ) )
		total.typewriter(800)
		totalCash = tmp
	}

	function basketline ( nodes, clearfunction ) {
		var self = this
		this.hasnodes = $(nodes.drop)
		
		$(nodes.less).data('run',false)
		$(nodes.more).data('run',false)
			var main = $(nodes.line)
		this.id      = main.attr('ref')	
		//var delurl   = $(nodes.less).parent().attr('href')
		var addurl   = $(nodes.more).parent().attr('href')
		// if( delurl === '#' )
		// 	delurl =  $(nodes.less).parent().attr('ref')
		// if( typeof(delurl)==='undefined' )
		// 	delurl = addurl + '-1'
		var drop     = $(nodes.drop).attr('href')
		var limit    = ( typeof(nodes.limit) !== 'undefined' ) ? nodes.limit : 1000
		if( $(nodes.quan).length )
			this.quantum = $(nodes.quan).val() * 1
		// var price    = ( self.sum* 1 / self.quantum *1 ).toFixed(2)
		// if( ( 'price' in nodes ) && $(nodes.price).length )
		//     price    = $(nodes.price).html().replace(/\s/,'')
		// this.price   = price
		this.noview  = false
		var dropflag = false

		if( !$(nodes.sum).length ) {
			this.price = $(nodes.price).html().replace(/\s/,'')
			this.sum = this.quantum * this.price
		} else {
			this.sum     = $(nodes.sum).html().replace(/\s/,'')
			this.price = ( self.sum* 1 / self.quantum *1 ).toFixed(2)
		}
		totalCash += this.sum * 1
		var totalCalcTO = null

		if( nodes.linked ) { // warranty only
			PubSub.subscribe('quantityChange', function( m, data ) {
//console.info( nodes.linked, data.id, self.sum )
				if( nodes.linked != data.id )
					return
				self.updateWrnt( data.q )
			})
		}

		this.updateWrnt = function( q ) {
			clearTimeout( totalCalcTO )
			self.quantum = q
			self.sum = q * self.price
			$(nodes.quan).val( self.quantum )
			totalCalcTO = setTimeout( function() {
                getTotal(), 1000
            })
		}

		this.calculate = function( q ) {
			clearTimeout( totalCalcTO )
			self.quantum = q
			self.sum = self.price * q
			$(nodes.sum).html( printPrice( self.sum ) )
			$(nodes.sum).typewriter(800, getTotal)
			totalCalcTO = setTimeout( function() {
                getTotal(), 1000
            })
		}

		this.clear = function() {
			main.remove()
			self.noview = true
			PubSub.publish( 'quantityChange', { q : 0, id : self.id } )
			if( clearfunction ) 
				clearfunction()
			
			$.when($.getJSON( drop , function( data ) {
			})).then( function(data){
				$(nodes.drop).data('run',false)
				if( !data.success ) {
					location.href = location.href
				}
				else{
					showOldPrice(data.data.old_price)
					showPrice(data.data.full_price)
					// getTotal()
				}
			})
		}

		this.update = function( minimax, delta ) {
			
			if( delta > 0 && ( limit < ( self.quantum + delta ) ) ) {
				$(minimax).data('run',false)
				return
			}
			//var tmpurl = (delta > 0) ? addurl : delurl
			//self.quantum += delta
            var tmpurl = addurl.slice(0, -1);
            
            if (minimax) {
            	self.quantum += delta
            }
            	else self.quantum = delta
            
            tmpurl += self.quantum

			//$(nodes.quan).html( self.quantum)
			// console.log($(nodes.quan))
			$(nodes.quan).val(self.quantum)
			self.calculate( self.quantum )
			totalCash += self.price * delta

			// if (self.quantum < nodes.line.find('.extWarr.mBig .ajaquant').val()){
				
			// }

			// PubSub.publish( 'quantityChange', { q : self.quantum, id : self.id } )
			// if( $('#selectCredit').length ) {
			// 	var sufx = ''
			// 	if( $('#selectCredit').val()*1 )
			// 		sufx = '/1'
			// 	else
			// 		sufx = '/0'
			// 	tmpurl += sufx
			// }
			$.when($.getJSON( tmpurl , function( data ) {
			})).then( function(data){
				$(minimax).data('run',false)
				//if( data.success && data.data.quantity ) {
					//$(nodes.quan).html( data.data.quantity + ' шт.' )
					//self.calculate( data.data.quantity )
					//var liteboxJSON = ltbx.restore()
					//liteboxJSON.vitems += delta
					//liteboxJSON.sum    += delta * price
					//ltbx.update( liteboxJSON )
				//}
				if( !data.success ) {
					location.href = location.href
				}
				showOldPrice(data.data.old_price)
				showPrice(data.data.full_price)
			})
		}

		this.checkNode = function(node, newQ){
			if (node.warranty !== 'undefined'){
				if (newQ>node.line.parents('.basketright').find('.ajaquant:first').val()){
					return false
				}
				else{
					return true
				}
			}
			else{
				return true
			}
		}

		$(nodes.drop).click( function() {
			if(! $(nodes.drop).data('run') ) {
				$(nodes.drop).data('run', true)
				dropflag = self.clear()
			}
			// console.log('удаление')
			return false
		})

		$(nodes.less).click( function() {
			var minus = this

			if( ! $(minus).data('run') ) {
				$(minus).data('run',true)
				if( self.quantum > 1 )
					self.update( minus, -1 )
				else
					self.clear()
			}
			return false
		})

		$(nodes.more).click( function() {
			var plus = this
			var nQuan = nodes.quan.val()+1
			if(self.checkNode(nodes, nQuan)){	
				if( ! $(plus).data('run') ) {
					$(plus).data('run',true)
					self.update( plus, 1 )
				}
			}
			return false
		})
		$(nodes.quan).focusin(function(){
			$(nodes.quan).bind('keyup',function(e){
				if (((e.which>=48)&&(e.which<=57))||(e.which==8)){//если это цифра или бэкспэйс
					var quan = self.quantum = $(nodes.quan).val().replace(/\D/g,'') * 1
					if (quan > 0){//если больше нуля, апдейтим
						focusTrigger = false
						if(self.checkNode(nodes, quan)){
							self.update( false, quan)
						}
					}
					else{ //если меньше, очищаем
						focusTrigger = true
					}
				}
				else{
					//если это не цифра
					var quan = self.quantum = $(nodes.quan).val().replace(/\D/g,'') * 1
					$(nodes.quan).val(self.quantum)
					focusTrigger = (quan > 0)? false : true
				}
			})
		})
		$(nodes.quan).focusout(function(){
			if (focusTrigger){
				focusTrigger = false
				self.clear()
			}
			$(nodes.quan).unbind('keyup')
		})

	} // object basketline
	
	var basket = [],
		popupIsOpened = false	

	$('.basketline').each( function(){
		var bline = $(this)
		var tmpline = new basketline({
						'line': bline,
						'less': bline.find('.ajaless:first'),
						'more': bline.find('.ajamore:first'),
						'quan': bline.find('.ajaquant:first'),
						'price': bline.find('.basketinfo .price:first'),
						'sum': bline.find('.basketinfo .sum:first'),
						'drop': bline.find('.basketinfo .whitelink:first'),
						'limit': bline.find('.numerbox').data('limit')
						})
		basket.push( tmpline )
				
		if( $('div.bBacketServ.mBig.F1', bline).length ) {
			$('div.bBacketServ.mBig.F1 tr', bline).each( function(){
				if( $('.ajaquant', $(this)).length ) {
					addLine( $(this), bline )
				}
			})
		}
		bline.find('a.link1').click( function(){
			if( popupIsOpened )
				return false
			popupIsOpened = true
			var f1popup = $('div.bF1Block', bline)
			f1popup.show()
			       .find('.close').click( function() {
				       	popupIsOpened = false
			       		f1popup.hide()
			       })
			f1popup.find('input.button').click( function() {
		   		if( $(this).hasClass('active') )
					return false
				$(this).val('В корзине').addClass('active')
				var f1item = $(this).data()
				$.getJSON( f1item.url, function(data) {
					if (data.success){
						f1item.f1price = data.data.sum
						// console.log(f1item)
						makeWide( bline, f1item)
						popupIsOpened = false
						f1popup.hide()
						showOldPrice(data.cart.old_price)
						showPrice(data.cart.full_price)
					}
				})
				
		   })
			return false
		})

		if( $('div.bBacketServ.mBig.extWarr', bline).length ) {
			$('div.bBacketServ.mBig.extWarr tr', bline).each( function(){
				if( $('.mPrice', $(this)).length ) {
					addLineWrnt( $(this), bline )
				}
			})
		}
		bline.find('a.link_extWarr').click( function(){
			if( popupIsOpened )
				return false
			popupIsOpened = true
			var wrntpopup = $('.extWarranty', bline)
			wrntpopup.show()
					 .find('.close').click( function() {
					 	popupIsOpened = false
			       		wrntpopup.hide()
			         })
			wrntpopup.find('input.button').click( function() {
		   		if( $(this).hasClass('active') )
					return false
				wrntpopup.find('input.button').val('Выбрать').removeClass('active')
				$(this).val('В корзине').addClass('active')
				var tmpitem = $(this).data()			
				$.getJSON( tmpitem.url, function(data) {
					showOldPrice(data.cart.old_price);
					showPrice(data.cart.full_price);
				})
				popupIsOpened = false
				wrntpopup.hide()
				makeWideWrnt( bline, tmpitem )
		   		
		   })
			return false
		})
	})

	function addLine( tr, bline ) {
	
		var checkWide = function () {
			var buttons = $('td.bF1Block_eBuy', bline)
			var mBig = $('div.bBacketServ.mBig.F1', bline)		
			for(var i=0, l = $(buttons).length; i < l; i++) {
				if( ! $('tr[ref=' + $(buttons[i]).attr('ref') + ']', mBig).length ) {
					$(buttons[i]).find('input').val('Купить услугу').removeClass('active')
					//break
				}	
			}	
						
			if ( !$('div.bBacketServ.mBig .ajaquant', bline).length ) {	
				$('div.bBacketServ.mBig.F1', bline).hide()							
				$('div.bBacketServ.mSmall.F1', bline).show()
			}	
		}	
		var tmpline = new basketline({
					'line': tr,
					'less': tr.find('.ajaless'),
					'more': tr.find('.ajamore'),
					'quan': tr.find('.ajaquant'),
					//'price': '.none',
					'sum': tr.find('.price'),
					'drop': tr.find('.whitelink')
					}, checkWide)
		basket.push( tmpline )
	}		
	
	function makeWide( bline, f1item ) {
		$('div.bBacketServ.mSmall.F1', bline).hide()
		var bBig = $('div.bBacketServ.mBig.F1', bline)
		bBig.show()
		var f1lineshead = $('tr:first', bBig)
		var f1linecart = tmpl('f1cartline', f1item)
		f1linecart = f1linecart.replace(/F1ID/g, f1item.fid ).replace(/F1TOKEN/g, f1item.f1token ).replace(/PRID/g, bline.attr('ref') )
		f1lineshead.after( f1linecart )
		addLine( $('tr:eq(1)', bBig) )
		getTotal()
	}

	function addLineWrnt( tr, bline ) {
		var checkWide = function () {
			var buttons = $('.extWarranty .bF1Block_eBuy', bline)
			var mBig = $('div.bBacketServ.mBig.extWarr', bline)	
			for(var i=0, l = $(buttons).length; i < l; i++) {
				if( ! $('tr[ref=' + $(buttons[i]).attr('ref') + ']', mBig).length ) {
					$(buttons[i]).find('input').val('Выбрать').removeClass('active')
					//break
				}	
			}	
						
			if ( !$('div.bBacketServ.mBig.extWarr .mPrice', bline).length ) {
				$('div.bBacketServ.mBig.extWarr', bline).hide()							
				$('div.bBacketServ.mSmall.extWarr', bline).show()
			}	
		}
		var tmpline = new basketline({
					'line': tr,
					'warranty':true,
					'less': tr.find('.ajaless'),
					'more': tr.find('.ajamore'),
					'quan': tr.find('.ajaquant'),
					// 'price': tr.find('.price'),
					'sum': tr.find('.price'),
					'drop': tr.find('.whitelink'),
					'linked': bline.attr('ref')
					}, checkWide)
		basket.push( tmpline )
	}	

	function makeWideWrnt( bline, f1item ) {
		$('div.bBacketServ.extWarr.mSmall', bline).hide()
		f1item.productQ = bline.find('.ajaquant:first').text().replace(/[^0-9]/g,'')
		var bBig = $('div.bBacketServ.extWarr.mBig', bline)
		bBig.show()	
		var f1lineshead = $('tr:first', bBig)
		var f1linecart = tmpl('wrntline', f1item)
		f1linecart = f1linecart.replace(/WID/g, f1item.ewid ).replace(/PRID/g, bline.attr('ref') )
		if ($('.ew_title', bBig).length){
			$($('tr:eq(1)', bBig)).remove();
		}
		f1lineshead.after( f1linecart )
		addLineWrnt( $('tr:eq(1)', bBig), bline )
		// getTotal()
	}
	
	/* credit */
	if( $('#selectCredit').length ) {
		var minsum = $('#creditSum').data('minsum')
		if( minsum )

		function anotherSum() {
			$('#creditSum').toggle()
		    $('#commonSum').toggle()
		}
		
		function checkFlag() {
			if( $('#selectCredit:checked').length ) {
				anotherSum()
			}
		}

		function toggleFlag() {
			$('#blockFromCreditAgent').fadeOut( 'slow' )	
			if( totalCash >= minsum ) {
				if( $('#creditFlag').is(':hidden') ) { // сумма превысила рубеж
					$('#creditFlag').show()
					checkFlag()
				}
			} else {
				if( $('#creditFlag').is(':visible') ) { // сумма стала ниже рубежа
					$('#creditFlag').hide()
					checkFlag()
				}
			}
		}

		function toggleCookie( name ) {
			if( !docCookies.hasItem( name ) ) {
				docCookies.setItem(false, name, 1, 60*60, '/')
				return
			}
			var curCook = docCookies.getItem( name )
			docCookies.setItem(false, name, Math.abs( curCook - 1 ), 60*60, '/')
		}

		toggleFlag()
		PubSub.subscribe( 'quantityChange', toggleFlag )
		PubSub.subscribe( 'bankAnswered', function() {
			$('#blockFromCreditAgent').fadeIn( 'slow' )
		} )
		//checkFlag()


		$('label.bigcheck').click( function(e) {
			var target = $(e.target)
			if (!target.is('input')) {
				return
			}
			$(this).toggleClass('checked')
			anotherSum()
			toggleCookie( 'credit_on' )
		})	
		
		DirectCredit.init( $('#tsCreditCart').data('value'), $('#creditPrice') )
		PubSub.subscribe( 'quantityChange', DirectCredit.change )
	} // credit 
    
});