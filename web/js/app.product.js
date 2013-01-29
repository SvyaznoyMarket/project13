$(document).ready(function() {
	/*Вывод магазинов, когда товар доступен только в них
	*/
	if ($('#availableShops').length){
		vitrin = {
			shopStack: 0,
			initText:'',
			init: function(){
				vitrin.initText = $('#slideAvalShop').html()
				$('#slideAvalShop').bind('click', function(){
					$('#listAvalShop .hidden').toggle(150)
					$(this).toggleClass('showedShop')
					if ($(this).hasClass('showedShop')){
						$(this).html('Свернуть')
					}
					else{
						$(this).html(vitrin.initText)
					}
					return false
				})
				shopFromModel = $('#availableShops').data('shops')
				vitrin.shopStack = {}
				//render shops
				for (i in shopFromModel){
					// var shopTpl = tmpl('itemAvalShop_tmpl', shopFromModel[i])
					var shopMapTpl = tmpl('itemAvalShop_tmplPopup',shopFromModel[i])
					// $('#listAvalShop').append(shopTpl)
					$('#mapPopup_shopInfo').append(shopMapTpl)
					vitrin.shopStack[shopFromModel[i].id] = shopFromModel[i]
				}
				//delegate click on shop
				$('#listAvalShop').delegate('.shopLookAtMap', 'click', function(){
					$('#orderMapPopup').lightbox_me({ 
						centered: true,
						onLoad: function() {
							$('#mapPopup').empty()
							vitrin.showPopup()
						}
					})
					return false
				})
			},
			showPopup: function(){
				vitrin.loadMap()
				$('#mapPopup_shopInfo').delegate('li', 'hover', function() {
					//console.log('1')
			        var id = $(this).attr('ref')//$(this).data('id')
			        if( hoverTimer.timer ) {
			            clearTimeout( hoverTimer.timer )
			        }
			        if( id && id != hoverTimer.id) {
			            hoverTimer.id = id
			            hoverTimer.timer = setTimeout( function() {            
			                window.regionMap.showInfobox( id )
			            }, 350)
			        }
			    })
			},
			updateI: function ( marker ) {
				$('#map-info_window-container').html( tmpl( 'mapInfoBlock', marker ))
				hoverTimer.id = marker.id
			},
			loadMap: function (){
				MapInterface.ready( 'yandex', {
					yandex: $('#mapInfoBlock'), 
					google: $('#map-info_window-container')
				} )
				var mapCenter = calcMCenter( vitrin.shopStack )
				var mapCallback = function() {			
					window.regionMap.showMarkers( vitrin.shopStack )		
					//window.regionMap.addHandler( '.shopchoose', vitrin.ShopChoosed )
				}
				MapInterface.init( mapCenter, 'mapPopup', mapCallback, vitrin.updateI)
			}
		}
	 	vitrin.init()
	}

	/* вывод слайдера со схожими товарами, если товар доступен только на витрине*/
	if ( $('#similarGoodsSlider').length){

		// основные элементы
		var similarSlider = $('#similarGoodsSlider')
		var similarWrap = similarSlider.find('.bSimilarGoodsSlider_eWrap')
		var similarArrow = similarSlider.find('.bSimilarGoodsSlider_eArrow')

		var slidesW = 250
		var sliderW = 0
		var slidesCount = 0
		var wrapW = 0
		var left = 0
		// init
		$.getJSON( $('#similarGoodsSlider').data('url') , function(data){
        	for (var item in data){
        		similarWrap.append('<div class="bSimilarGoodsSlider_eGoods fl">
		          <a class="bSimilarGoodsSlider_eGoodsImg fl" href="'+data[item].link+'"><img width="83" height="83" src="'+data[item].image+'"/></a>
		          <div class="bSimilarGoodsSlider_eGoodsInfo fl">
		            <div class="goodsbox__rating rate'+data[item].rating+'"><div class="fill"></div></div>
		            <h3><a href="'+data[item].link+'">'+data[item].name+'</a></h3>
		            <div class="font18 pb10 mSmallBtns"><span class="price">'+data[item].price+'</span> <span class="rubl">p</span></div>
		          </div>
		        </div>')
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
			if ((left < sliderW-wrapW-50)){
				left = sliderW-wrapW-50
			}
			if (left > 0 ){
				left = 0
			}
			similarWrap.animate({'left':left})
			return false
		})

	}
	


	/* Delivery Bubble */
	if( $('.otherRegion').length ) {
		$('.expander').click( function() {
			$('.otherRegion').find('ul').toggle()
			return false
		})
	}

	/* Credit */
	if( $('.creditbox').length ) {
		window.creditBox = {
			cookieTimeout : null,
			
			toggleCookie : function( state ){
				var self = this
				clearTimeout( this.cookieTimeout )
				this.cookieTimeout = setTimeout( function(){
					docCookies.setItem(false, 'credit_on', state ? 1 : 0 , 60*60, '/')
				}, 200 )
			},

			init : function() {
				var self = this
				$('.creditbox label').click( function(e) {
					var target = $(e.target)
					e.stopPropagation()
					if (target.is('input')) {
						return false
					}
					
					$(this).toggleClass('checked')
					self.toggleCookie( $(this).hasClass('checked') )
				})
				if( this.getState() == 1) {
					$('.creditbox label').addClass('checked')
				}
				
				var creditd = $('input[name=dc_buy_on_credit]').data('model')
				creditd.count = 1
				creditd.cart = '/cart'
					dc_getCreditForTheProduct(
						4427, 
						docCookies.getItem('enter_auth'),
						'getPayment',
						{ price : creditd.price, count : creditd.count, type : creditd.product_type },
						function( result ) {
							if( ! 'payment' in result )
								return 
							if( result.payment > 0 ) {
								$('.creditboxinner .price').html( printPrice( Math.ceil(result.payment) ) )
								$('.creditbox').show()
							}
						}
					)

/*			
				JsHttpRequest.query(
					'http://direct-credit.ru/widget/payment.php',
					{
						'price'			:	creditd.price,
						'partner_id'	:	4427,
						'product_type'	:	creditd.product_type
					},
					function(result, errors) {
						$('.creditboxinner .price').html( printPrice( result.htmlcode.replace(/[^0-9]/g,'')) )
						$('.creditbox').show()
					},
					false
				)
*/				
			},
			
			getState : function() {
				if( ! docCookies.hasItem('credit_on') )
					return 0
				return docCookies.getItem('credit_on')
				//return $('.creditbox input:checked').length
			}
		}
		
		creditBox.init()
	}

	/* Rating */
	if( $('#rating').length ) {
		var iscore = $('#rating').next().html().replace(/[^\d\.]/g,'') * 1
		$('#rating img').remove()
        $('#rating span').remove()
        $('#rating').raty({
		  start: iscore,
		  showHalf: true,
		  path: '/css/skin/img/',
		  readOnly: $('#rating').data('readonly'),
		  starHalf: 'star_h.png',
		  starOn: 'star_a.png',
		  starOff: 'star_p.png',
		  hintList: ['плохо', 'удовлетворительно', 'нормально', 'хорошо', 'отлично'],
		  click: function( score ) {
		  		$.getJSON( $('#rating').attr('data-url').replace('score', score ) , function(data){
		  			if( data.success === true && data.data.rating ) {
		  				$.fn.raty.start( data.data.rating ,'#rating' )
		  				$('#rating').next().html( data.data.rating )
		  			}
		  		})
		  		$.fn.raty.readOnly(true, '#rating')
		  	}
		})
	}
	
	/* Product Counter */
	if( $('#page .bCountSet').length ) {
		var np = $('.goodsbarbig .bCountSet')
		var l1 = np.parent().find('.link1')
		var l1href = l1.attr('href')
		var l1cl = $('a.order1click-link')
		var l1clhref = l1cl.attr('href')
		np.data('hm', np.first().find('span').text().replace(/\D/g,'') )
		
		var tmp = $('.goodsbarbig:first').data('value')
		if (typeof(tmp) !== 'undefined')
			var max = ( 'jsstock' in tmp ) ? tmp.jsstock : 1
		
		np.bind('update', function() {
			var hm = $(this).data('hm')
			if( max < hm ) {
				$(this).data('hm', max)
				return
			}
			if( hm === max ) {
				$('.bCountSet__eP', np).addClass('disabled')
			} else {
				if( $('.bCountSet__eP', np).hasClass('disabled') )
					$('.bCountSet__eP', np).removeClass('disabled')
			}
			np.find('span').text( hm + '  шт.')
			l1.attr('href', l1href +  hm )
			l1cl.attr('href', l1clhref + '&quantity=' + hm )
		})
		
		$('.bCountSet__eP', np).click( function() {
			if( $(this).hasClass('disabled') )
				return false
			np.data('hm', np.data('hm')*1 + 1 )	
			np.trigger('update')
			return false
		})
		$('.bCountSet__eM', np).click( function() {	
			if( $(this).hasClass('disabled') )
				return false		
			var hm = np.data('hm')//how many
			if( hm == 1 )
				return false
			np.data('hm', np.data('hm')*1 - 1 )
			np.trigger('update')
			return false
		})		
	}
	
	/* Icons */
	$('.viewstock').bind( 'mouseover', function(){
		var trgtimg = $('#stock img[ref="'+$(this).attr('ref')+'"]')
		var isrc    = trgtimg.attr('src')
		var idu    = trgtimg.attr('data-url')
		if( trgtimg[0].complete ) {
			$('#goodsphoto img').attr('src', isrc)
			$('#goodsphoto img').attr('href', idu)
		}
	})

	/* Media library */
	//var lkmv = null
	var api = {
		'makeLite' : '#turnlite',
		'makeFull' : '#turnfull',
		'loadbar'  : '#percents',
		'zoomer'   : '#bigpopup .scale',
		'rollindex': '.scrollbox div b',
		'propriate': ['.versioncontrol','.scrollbox']
	}
	
	if( typeof( product_3d_small ) !== 'undefined' && typeof( product_3d_big ) !== 'undefined' )
		lkmv = new likemovie('#photobox', api, product_3d_small, product_3d_big )
	if( $('#bigpopup').length )
		var mLib = new mediaLib( $('#bigpopup') )

	$('.viewme').click( function(){
		if( mLib )
			mLib.show( $(this).attr('ref') , $(this).attr('href'))
		return false
	})
	    
	/* Some handlers */
    /*$('.bDropMenu').each( function() {
		var jspan  = $(this).find('span:first')
		var jdiv   = $(this).find('div')
		jspan.css('display','block')
		if( jspan.width() + 60 < jdiv.width() )
			jspan.width( jdiv.width() - 70)
		else
			jdiv.width( jspan.width() + 70)
	})*/
	
    $('.product_rating-form').live({
        'form.ajax-submit.prepare': function(e, result) {
            $(this).find('input:submit').attr('disabled', true)
        },
        'form.ajax-submit.success': function(e, result) {
            if (true == result.success) {
                $('.product_rating-form').effect('highlight', {}, 2000)
            }
        }
    })

    $('.product_comment-form').live({
        'form.ajax-submit.prepare': function(e, result) {
            $(this).find('input:submit').attr('disabled', true)
        },
        'form.ajax-submit.success': function(e, result) {
            $(this).find('input:submit').attr('disabled', false)
            if (true == result.success) {
                $($(this).data('listTarget')).replaceWith(result.data.list)
                $.scrollTo('.' + result.data.element_id, 500, {
                    onAfter: function() {
                        $('.' + result.data.element_id).effect('highlight', {}, 2000);
                    }
                })
            }
        }
    })

    $('.product_comment_response-link').live({
        'content.update.prepare': function(e) {
            $('.product_comment_response-block').html('')
        },
        'content.update.success': function(e) {
            $('.product_comment_response-block').find('textarea:first').focus()
        }
    })
    
   
});