$(document).ready(function() {


	/**
	 * Подключение нового зумера
	 *
	 * @requires jQuery, jQuery.elevateZoom
	 */
	(function () {
		var image = $('.js-photo-zoomedImg');

		if ( !image.length ) {
			console.warn('Нет изображения для elevateZoom');

			return;
		}

		var
			zoomDisable = ( image.data('zoom-disable') !== undefined ) ? image.data('zoom-disable') : true,
			zoomConfig = {
				gallery: 'productImgGallery',
				galleryActiveClass: 'prod-photoslider__gal__link--active',
				zoomWindowOffety: 0,
				zoomWindowOffetx: 19,
				zoomWindowWidth: image.data('is-slot') ? 344 : 519,
				borderSize: 1,
				borderColour: '#C7C7C7',
				disableZoom: zoomDisable
			};
		// end of vars

		var
			/**
			 * Обработчик клика на изображение в галерее.
			 * Нужен для инициализации/удаления зумера
			 */
			photoGalleryLinkClick = function() {
				if ( $(this).data("zoom-disable") == undefined ) {
					return;
				}

				if ( $(this).data("zoom-disable") == zoomDisable ) {
					return;
				}

				zoomDisable = $(this).data("zoom-disable");

				// инициализация зумера
				if( !zoomDisable ) {
					zoomConfig.disableZoom = zoomDisable;
					image.elevateZoom(zoomConfig);
				}
				else { // удаления зумера
					$.removeData(image, 'elevateZoom');//remove zoom instance from image
					$('.zoomContainer').remove();//remove zoom container from DOM
				}

				return false;
			};
		// end of functions

		image.elevateZoom(zoomConfig);
		$('.jsPhotoGalleryLink').on('click', photoGalleryLinkClick);
	})();


	/**
	 * Каутер товара
	 *
	 * @requires	jQuery, jQuery.goodsCounter
	 * @param		{Number} count Возвращает текущее значение каунтера
	 */
	$('.bCountSection').goodsCounter({
		onChange:function( count ){
			var spinnerFor = this.attr('data-spinner-for'),
				bindButton = $('.'+spinnerFor),
                bindOneClickButton = $('.' + spinnerFor + '-oneClick')
				newHref = bindButton.attr('href') || '';
			// end of vars

			console.log('counter change');
			console.log(bindButton);

			bindButton.attr('href',newHref.addParameterToUrl('quantity',count));
            bindOneClickButton.data('quantity', count);

			// добавление в корзину после обновления спиннера
			// if (bindButton.hasClass('mBought')){
			// 	bindButton.eq('0').trigger('buy');
			// }
		}
	});


	/**
	 * Подключение слайдера товаров
	 */
	$('.js-slider').goodsSlider({
		onLoad: function(goodsSlider) {
			ko.applyBindings(ENTER.UserModel, goodsSlider);
		}
	});

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
		var
			oneClickAnalytics = function oneClickAnalytics( data ) {
				var
					product = data.product,
					regionId = data.regionId,
					result,
					_rutarget = window._rutarget || [];
				// end of vars

				if ( !product || !regionId ) {
					return;
				}

				result = {'event': 'buyNow', 'sku': product.id, 'qty': product.quantity,'regionId': regionId};

				console.info('RuTarget buyNow');
				console.log(result);
				_rutarget.push(result);
			},

			successHandler = function successHandler( res ) {
				console.info('payPal ajax complete');

				if ( !res.success || !res.redirect ) {
					window.ENTER.utils.blockScreen.unblock();

					return;
				}

				// analytics
				oneClickAnalytics(res);

				document.location.href = res.redirect;
			},

			buyOneClickAndRedirect = function buyOneClickAndRedirect() {
				console.info('payPal click');

				var button = $(this),
					url = button.attr('href'),
					quantityBlock = $('.bCountSection__eNum'),
					data = {};
				// end of vars

				window.ENTER.utils.blockScreen.block('Загрузка');

				// если количество товаров > 1, то передаем его на сервер
				if ( quantityBlock.length && $.isNumeric(quantityBlock.val()) && quantityBlock.val() * 1 > 1 ) {
					data.quantity = quantityBlock.val();
				}

				$.get(url, data, successHandler);

				return false;
            };
		// end of functions

		$('.jsPayPalButton').bind('click', buyOneClickAndRedirect);
		$('.jsLifeGiftButton').bind('click', buyOneClickAndRedirect);
		$('.jsOneClickButton').bind('click', buyOneClickAndRedirect);
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

    try {
        var
            productId =ENTER.config.pageConfig.product ? ENTER.config.pageConfig.product.id : null,
            cookieValue = docCookies.getItem('product_viewed') || '',
            viewed = []
        ;

        if (productId) {
            viewed = cookieValue ? ENTER.utils.arrayUnique(cookieValue.split(',')) : [];
            viewed.push(productId);
            docCookies.setItem('product_viewed', viewed.slice(-20).join(','), 7 * 24 * 60 * 60, '/');
        }
    } catch (e) {
        console.error(e);
    }
});