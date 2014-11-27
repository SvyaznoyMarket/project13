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

		var
			image = $('.bZoomedImg'),
			zoomDisable = ( image.data('zoom-disable') !== undefined ) ? image.data('zoom-disable') : true,
			zoomConfig = {
				gallery: 'productImgGallery',
				galleryActiveClass: 'mActive',
				zoomWindowOffety: 0,
				zoomWindowOffetx: 19,
				zoomWindowWidth: 519,
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
	$('.bGoodsSlider').goodsSlider({
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
            },

            handleOneClick = function() {
                console.info('show one click form');

                var button = $(this),
                    $target = $(button.data('target')),
                    $orderContent = $('#js-order-content')
                ; // end of vars

                $('.shopsPopup').find('.close').trigger('click'); // закрыть выбор магазинов
                $('.jsOneClickCompletePage').remove(); // удалить ранее созданный контент с оформленным заказом
                $('#jsOneClickContentPage').show();

                // mask
                $.mask.definitions['x']='[0-9]';
                $.mask.placeholder= "_";
                $.mask.autoclear= false;
                $.map($('#jsOneClickContent').find('input'), function(elem, i) {
                    if (typeof $(elem).data('mask') !== 'undefined') $(elem).mask($(elem).data('mask'));
                });

                console.warn($target.length);
                if ($target.length) {
                    var data = $.parseJSON($orderContent.data('param'));
                    data.quantity = button.data('quantity');
                    data.shopId = button.data('shop');
                    $orderContent.data('shop', data.shopId);

                    if (button.data('title')) {
                        $target.find('.jsOneClickTitle').text(button.data('title'));
                    }

                    $target.lightbox_me({
                        centered: true,
						sticky: '#jsOneClickContent' != button.data('target'),
                        closeSelector: '.close',
                        removeOtherOnCreate: false,
                        closeClick: false,
                        closeEsc: false,
                        onLoad: function() {
                            $('#OrderV3ErrorBlock').empty().hide();
							$('.jsOrderV3PhoneField').focus();
                        }
                    });

                    $.ajax({
                        url: $orderContent.data('url'),
                        type: 'POST',
                        data: data,
                        dataType: 'json',
                        beforeSend: function() {
                            $orderContent.fadeOut(500);
                            //if (spinner) spinner.spin(body)
                        },
                        closeClick: false
                    }).fail(function(jqXHR){
                        var response = $.parseJSON(jqXHR.responseText);

                        if (response.result && response.result.errorContent) {
                            $('#OrderV3ErrorBlock').html($(response.result.errorContent).html()).show();
                        }
                    }).done(function(data) {
                        console.log("Query: %s", data.result.OrderDeliveryRequest);
                        console.log("Model:", data.result.OrderDeliveryModel);
                        $orderContent.empty().html($(data.result.page).html());

                        ENTER.OrderV3.constructors.smartAddress();
                        $orderContent.find('input[name=address]').focus();
                    }).always(function(){
                        $orderContent.stop(true, true).fadeIn(200);
                        //if (spinner) spinner.stop();

                        $('body').trigger('trackUserAction', ['0 Вход']);
                    });

                    return false;
                }
            },

            toggleOneClickDelivery = function toggleOneClickDelivery() {
            	var button = $(this),
            		$toggleNote = $('.js-order-oneclick-delivery-toggle-btn-note'),
            		$toggleBox = $('.js-order-oneclick-delivery-toggle');

            		button.toggleClass('orderU_lgnd-tggl-cur');
            		$toggleBox.toggle();
            		$toggleNote.toggleClass('orderU_lgnd_tgglnote-cur')

                $('body').trigger('trackUserAction', ['2 Способ получения']);
            };
		// end of functions

		$('.jsPayPalButton').bind('click', buyOneClickAndRedirect);
		$('.jsLifeGiftButton').bind('click', buyOneClickAndRedirect);
		$('.jsOneClickButton').bind('click', buyOneClickAndRedirect);
        $('.jsOneClickButton-new').bind('click', handleOneClick);
		$('.js-order-oneclick-delivery-toggle-btn').on('click', toggleOneClickDelivery);
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
            docCookies.setItem('product_viewed', viewed.slice(-50).join(','), 7 * 24 * 60 * 60, '/');
        }
    } catch (e) {
        console.error(e);
    }
});