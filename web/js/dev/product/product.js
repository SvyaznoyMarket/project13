$(document).ready(function() {


	/**
	 * Подключение нового зумера
	 *
	 * @requires jQuery, jQuery.elevateZoom
	 */
	(function () {
		if ( !$('.bZoomedImg').length ) {
			console.warn('Нет изображения для elevateZoom');

			return;
		}

		var zoomDisable = ( $('.bZoomedImg').data('zoom-disable') ) ? $('.bZoomedImg').data('zoom-disable') : false;

		$('.bZoomedImg').elevateZoom({
			gallery: 'productImgGallery',
			galleryActiveClass: 'mActive',
			zoomWindowOffety: 0,
			zoomWindowOffetx: 19,
			zoomWindowWidth: 519,
			borderSize: 1,
			borderColour: '#C7C7C7',
			disableZoom: zoomDisable
		});
	})();


	/**
	 * Каутер товара
	 *
	 * @requires	jQuery, jQuery.goodsCounter
	 * @param		{Number} count Возвращает текущее значение каунтера
	 */
	$('.bCountSection').goodsCounter({
		onChange:function( count ){
			var spinnerFor = $('.bCountSection').attr('data-spinner-for'),
				bindButton = $('.'+spinnerFor),
				newHref = bindButton.attr('href');
			// end of vars

			bindButton.attr('href',newHref.addParameterToUrl('quantity',count));

			// добавление в корзину после обновления спиннера
			// if (bindButton.hasClass('mBought')){
			// 	bindButton.eq('0').trigger('buy');
			// }
		}
	});


	/**
	 * Подключение слайдера товаров
	 */
	$('.bGoodsSlider').goodsSlider();


	/**
	 * Подключение кастомных дропдаунов
	 */
	$('.bDescSelectItem').customDropDown({
		changeHandler: function( option ) {
			var url = option.data('url');

			document.location.href = url;
		}
	});


	/**
	 * Обработчик кнопки PayPal в карточке товара
	 */
	(function() {
		if ( !$('.jsPayPalButton').length ) {
			console.warn('Нет кнопки paypal');

			return;
		}

		console.info('Кнопка paypal существует');

		var payPalResHandler = function payPalResHandler( res ) {
				console.info('payPal ajax complete');

				if ( !res.success || !res.redirect ) {
					window.ENTER.utils.blockScreen.unblock();

					return;
				}

				document.location.href = res.redirect;
			},

			payPalEcsHandler = function payPalEcsHandler() {
				console.info('payPal click');

				var button = $(this),
					url = button.attr('href');
				// end of vars

				window.ENTER.utils.blockScreen.block('Загрузка');

				$.get(url, payPalResHandler);

				return false;
			};
		// end of functions

		$('.jsPayPalButton').bind('click', payPalEcsHandler);
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
	if ( $('#productDescriptionToggle').length ) {
		$('#productDescriptionToggle').toggle(
			function( e ) {
				e.preventDefault();
				$(this).parent().parent().find('.descriptionlist:not(.short)').show();
				$(this).html('Скрыть все характеристики');
			},
			function( e ) {
				e.preventDefault();
				$(this).parent().parent().find('.descriptionlist:not(.short)').hide();
				$(this).html('Показать все характеристики');
			}
		);
	}
});