$(document).ready(function() {


	/**
	 * Подключение нового зумера
	 *
	 * @requires jQuery, jQuery.elevateZoom
	 */
	$('.bZoomedImg').elevateZoom({
		gallery: 'productImgGallery',
		galleryActiveClass: 'mActive',
		zoomWindowOffety: -15,
		zoomWindowOffetx: 19,
		zoomWindowWidth: 518,
		borderSize: 1,
		borderColour: '#C7C7C7'
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

			// добавление в корзину после обновления спиннера
			// if (bindButton.hasClass('mBought')){
			// 	bindButton.eq('0').trigger('buy');
			// }
		}
	});


	/**
	 * Аналитика для карточки товара
	 *
	 * @requires jQuery
	 */
	(function(){
		if (!$('#jsProductCard').length){
			return false;
		}
		
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
	

	/**
	 * Затемнение всех контролов после добавления в корзину
	 */
	(function(){
		var afterBuy = function(){
			$('.bCountSection').addClass('mDisabled').find('input').attr('disabled','disabled');
			$('.jsOrder1click').addClass('mDisabled');
		};

		$("body").bind('addtocart', afterBuy);
	})();


	/**
	 * Custom select
	 */
	(function($){
		$.fn.customDropDown = function(params) {
			return this.each(function() {
				var options = $.extend(
								{},
								$.fn.customDropDown.defaults,
								params);
				var $self = $(this);

				var select = $self.find(options.selectSelector);
				var value = $self.find(options.valueSelector);

				var selectChangeHandler = function(){
					var selectedOption = select.find('option:selected');

					value.html(selectedOption.val());
					options.changeHandler(selectedOption);
				};

				select.on('change', selectChangeHandler)
			});
		};
				
		$.fn.customDropDown.defaults = {
			valueSelector: '.bDescSelectItem__eValue',
			selectSelector: '.bDescSelectItem__eSelect',
			changeHandler: function(){}
		};

	})(jQuery);

	(function(){
		$('.bDescSelectItem').customDropDown({
			changeHandler: function(option){
				var url = option.data('url');

				document.location.href = url;
			}
		});
	})();
	


	/**
	 * Media library
	 *
	 * Для вызова нашего старого лампового 3D
	 */
	//var lkmv = null
	// var api = {
	// 	'makeLite' : '#turnlite',
	// 	'makeFull' : '#turnfull',
	// 	'loadbar'  : '#percents',
	// 	'zoomer'   : '#bigpopup .scale',
	// 	'rollindex': '.scrollbox div b',
	// 	'propriate': ['.versioncontrol','.scrollbox']
	// }
	
	// if( typeof( product_3d_small ) !== 'undefined' && typeof( product_3d_big ) !== 'undefined' )
	// 	lkmv = new likemovie('#photobox', api, product_3d_small, product_3d_big )
	// if( $('#bigpopup').length )
	// 	var mLib = new mediaLib( $('#bigpopup') )

	// $('.viewme').click( function(){
	// 	if ($(this).hasClass('maybe3d')){
			
	// 		return false
	// 	}
	// 	if ($(this).hasClass('3dimg')){

	// 	}
		
	// 	if( mLib )
	// 		mLib.show( $(this).attr('ref') , $(this).attr('href'))
	// 	return false
	// });


	
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