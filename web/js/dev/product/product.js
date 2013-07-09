$(document).ready(function() {


	/**
	 * Подключение нового зумера
	 *
	 * @requires jQuery, jQuery.elevateZoom
	 */
	$('.bZoomedImg').elevateZoom({
		gallery: 'productImgGallery',
		galleryActiveClass: 'mActive',
		zoomWindowOffety: 5,
		zoomWindowOffetx: 18,
		zoomWindowWidth: 290
	});


	/**
	 * Каутер товара
	 *
	 * @requires	jQuery, jQuery.goodsCounter
	 * @param		{Number} count Возвращает текущее значение каунтера
	 */
	$('.bCountSection').goodsCounter({
		onChange:function(count){
			var spinnerFor = $('.bCountSection').attr('data-spinner-for');
			var bindButton = $('.'+spinnerFor);
			var newHref = bindButton.attr('href');

			bindButton.attr('href',newHref.addParameterToUrl('quantity',count));
			if (bindButton.hasClass('mBought')){
				bindButton.eq('0').trigger('buy');
			}
		}
	});


	/**
	 * Аналитика для карточки товара
	 *
	 * @requires jQuery
	 */
	(function(){
		var productInfo = $('#jsProductCard').data('value');
		var toKISS = {
			'Viewed Product SKU':productInfo.article,
			'Viewed Product Product Name':productInfo.name,
			'Viewed Product Product Status':productInfo.stockState,
		};
		if (typeof(_kmq) !== 'undefined'){
			_kmq.push(['record', 'Viewed Product',toKISS]);
		}
	})();
	


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
			
			return false
		}
		if ($(this).hasClass('3dimg')){

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
	
	$('.product_rating-form').on({
		'form.ajax-submit.prepare': function(e, result) {
			$(this).find('input:submit').attr('disabled', true)
		},
		'form.ajax-submit.success': function(e, result) {
			if (true == result.success) {
				$('.product_rating-form').effect('highlight', {}, 2000)
			}
		}
	})

	$('.product_comment-form').on({
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

	$('.product_comment_response-link').on({
		'content.update.prepare': function(e) {
			$('.product_comment_response-block').html('')
		},
		'content.update.success': function(e) {
			$('.product_comment_response-block').find('textarea:first').focus()
		}
	})

	
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