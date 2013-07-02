/**
 * Имя объекта для конструктора шкафов купе
 *
 * ВНИМАНИЕ
 * Имя переменной менять нельзя. Захардкожено в файле KupeConstructorScript.js
 * Переменная должна находится в глобальной области видимости
 */
var Planner3dKupeConstructor = null;

$(document).ready(function() {

	$('.bZoomedImg').elevateZoom({
		zoomWindowOffety: 5,
		zoomWindowOffetx: 18,
		zoomWindowWidth: 290,
	});

	$('.bCountSection').goodsCounter({
		onChange:function(count){
			var spinnerData = $('.bCountSection').data('spinner');
			var bindButton = $('.'+spinnerData.button);
			var newHref = bindButton.attr('href');

			bindButton.attr('href',newHref.addParameterToUrl('quantity',count));
			if (bindButton.hasClass('mBought')){
				bindButton.trigger('click',[true]);
			}
		}
	});

	


	/**
	 * Планировщик шкафов купе
	 */
	if ($('#planner3D').length){
		try {
			var coupeInfo = $('#planner3D').data('product')
			Planner3dKupeConstructor = new DKupe3dConstructor(document.getElementById('planner3D'),'/css/item/coupe_img/','/css/item/coupe_tex/', '/css/item/test_coupe_icons/');

			Planner3dKupeConstructor.Initialize('/js/KupeConstructorData.json', coupeInfo.id);
		}
		catch (err){
			var pageID = $(body).data(id)
			var dataToLog = {
				event: 'Kupe3dConstructor error',
				type:'ошибка загрузки Kupe3dConstructor',
				pageID: pageID,
				err: err,
			}
			logError(dataToLog)
		}

		/**
		 * Callback Инициализации конструктора шкафов
		 *
		 * ВНИМАНИЕ
		 * Название функции менять нельзя. Захардкожено в файле KupeConstructorScript.js
		 * Функция должна находится в глобальной области видимости
		 */
		Planner3d_Init = function (ApiIds){
			// console.info(ApiIds)
		}

		/**
		 * Callback изменений в конструкторе шкафов
		 * 
		 * ВНИМАНИЕ
		 * Название функции менять нельзя. Захардкожено в файле KupeConstructorScript.js
		 * Функция должна находится в глобальной области видимости
		 */
		Planner3d_UpdatePrice = function (IdsWithInfo) {
			var url = $('#planner3D').data('cart-sum-url')
			var product = {}
			product.product = {}

			var authFromServer = function(res){
				if (!res.success)
					return false

				$('.bProductCardRightCol__ePrice').html(res.sum)
			}

			for (var i = 0, len = IdsWithInfo.length; i < len; i++){
				var prodID = IdsWithInfo[i].id

				if (IdsWithInfo[i].error !== ''){
					$('.cart-add').addClass('disabled')
					$('#coupeError').html('Вставки продаются только парами!').show()
					return false
				}
				$('.cart-add').removeClass('disabled')
				$('#coupeError').hide()

				if (product.product[prodID+''] !== undefined){
					product.product[prodID+''].quantity++;
				}
				else{
					product.product[prodID+''] = {
						id : prodID,
						quantity : 1,
					}
				}
			}

			$.ajax({
				type: 'POST',
				url: url,
				data: product,
				success: authFromServer
			})
		}

		/**
		 * Добавление шкафа купе в корзину
		 */
		var kupe2basket = function(){
			if ($(this).hasClass('disabled')){
				return false
			}

			var structure = Planner3dKupeConstructor.GetBasketContent()
			var url = $(this).attr('href')

			var resFromServer = function(res){
				if ( res.success && ltbx ) {
					var tmpitem = {
						'id'    : data.id,
						'title' : data.name,
						'price' : res.data.sum,
						'img'   : '/images/logo.png',
						'vitems': res.data.full_quantity,
						'sum'   : res.data.full_price,
						'link'  : res.data.link
					}
					ltbx.getBasket( tmpitem )
					// kissAnalytics(data)
					// PubSub.publish( 'productBought', tmpitem )
					// sendAnalytics($(button))
				}
			}

			var product = {}

			product.product = structure
			$.ajax({
				type: 'POST',
				url: url,
				data: product,
				success: resFromServer
			})
			return false
		}

		$('.goodsbarbig .link1').unbind();
		$('.goodsbarbig .link1').bind('click', kupe2basket)
	}


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

	// видео в карточке товара
	if ($('.goodsphoto_eVideoShield').length){
		var videoStartTime = 0
		var videoEndTime = 0
		var productUrl = document.location.href
		var shield = $('.goodsphoto_eVideoShield')
		var iframe = $('#productVideo .productVideo_iframe').html()
		$('#productVideo .productVideo_iframe').empty()
		shield.bind('click', function(){
			$('#productVideo .productVideo_iframe').append(iframe)
			$(".productVideo_iframe iframe").attr("src", $(".productVideo_iframe iframe").attr("src")+"?autoplay=1")
			$('#productVideo').lightbox_me({ 
				centered: true,
				onLoad: function(){
					videoStartTime = new Date().getTime()
					if (typeof(_gaq) !== 'undefined') 
						_gaq.push(['_trackEvent', 'Video', 'Play', productUrl]);
				},
				onClose: function(){
					$('#productVideo .productVideo_iframe').empty()
					videoEndTime = new Date().getTime()
					var videoSpent = videoEndTime - videoStartTime
					if (typeof(_gaq) !== 'undefined') 
						_gaq.push(['_trackEvent', 'Video', 'Stop', productUrl, videoSpent]);
				}
			})
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
		if ($(this).hasClass('maybe3d')){
			var data = $('#maybe3dModelPopup').data('value')
			try {
				if (!$('#maybe3dModel').length){
					$('#maybe3dModelPopup_inner').append('<div id="maybe3dModel"></div>')
				}
				swfobject.embedSWF(data.init.swf, data.init.container, data.init.width, data.init.height, data.init.version, data.init.install, data.flashvars, data.params, data.attributes);
				$('#maybe3dModelPopup').lightbox_me({
					centered: true,
					closeSelector: ".close",
					onClose: function() {
						swfobject.removeSWF(data.attributes.id)
					}
				})
			}
			catch (err){
				var pageID = $(body).data(id)
				var dataToLog = {
					event: 'swfobject_error',
					type:'ошибка загрузки swf maybe3d',
					pageID: pageID,
					err: err,
				}
				logError(dataToLog)
			}
			return false
		}
        if ($(this).hasClass('3dimg')){
            var object = $('#3dModelImg')
            var data = object.data('value')
            var host = object.data('host')
            try {
                if (!$('#3dImgContainer').length) {
                    var AnimFramePlayer = new DAnimFramePlayer(document.getElementById('3dModelImg'), host)
                    AnimFramePlayer.DoLoadModel(data)
                }
                $('#3dModelImg').lightbox_me({
                    centered: true,
                    closeSelector: ".close",
                })
            }
            catch (err){
            	var pageID = $(body).data(id)
				var dataToLog = {
					event: '3dimg',
					type:'ошибка загрузки 3dimg для мебели',
					pageID: pageID,
					err: err,
				}
				logError(dataToLog)
            }
            return false
        }
		
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

    // KISS
    if ($('#productInfo').length){
    	var data = $('#productInfo').data('value')
    	var toKISS = {
			'Viewed Product SKU':data.article,
			'Viewed Product Product Name':data.name,
			'Viewed Product Product Status':data.stockState,
		}
		if (typeof(_kmq) !== 'undefined'){
			_kmq.push(['record', 'Viewed Product',toKISS]);
		}
    }
    
  	// карточка товара - характеристики товара краткие/полные
    if($('#productDescriptionToggle').length) {
        $('#productDescriptionToggle').toggle(
            function(e){
                e.preventDefault()
                $(this).parent().parent().find('.descriptionlist:not(.short)').show()
                $(this).html('Скрыть все характеристики')
            },
            function(e){
                e.preventDefault()
                $(this).parent().parent().find('.descriptionlist:not(.short)').hide()
                $(this).html('Показать все характеристики')
            }
        );
    }


    //Класс для аксессуаров по категориям
    if ($('.categoriesmenu').length) {
        $('.acess-box-section').addClass('acess-box');
    }


    function handle_jewel_items() {
        if($('body.jewel').length) {
            $(".link1.link1active").attr('href', '/cart')
            $(".link1").bind( 'click', function()   {
                if($(this).parent().hasClass('goodsbarbig')) {
                    $('.goodsbarbig .link1').html("В корзине")
                    $('.goodsbarbig .link1').addClass("link1active")
                } else {
                    $(this).html("В корзине")
                    $(this).addClass("link1active")
                }
            })
        }
    }
    handle_jewel_items()

});