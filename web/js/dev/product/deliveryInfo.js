/**
 * Расчет доставки
 *
 * @author		Zaytsev Alexandr
 * 
 * @requires	jQuery, simple_templating
 * 
 * @param		{Object}	widgetBox		Контейнер с доступными вариантами доставки
 * @param		{Object}	deliveryData	Данные необходимые для отображения доставки
 * @param		{String}	url				Адрес по которому необходимо запросить данные с расчитанной доставкой для текущего продукта
 * @param		{String}	deliveryShops	Список магазинов из которых можно забрать сегодня (только если товар не доступен для продажи)
 * @param		{Object}	productInfo		Данные о текущем продукте
 * @param		{Object}	dataToSend		Данные для отправки на сервер и получение расчитанной доставки
 */
(function() {
	if ( $('.bDeliveryNowClick').length ) {
		/**
		 * Обработчик переключения состояния листа магазинов открыто или закрыто (блок способов доставки)
		 */
		var shopToggle = function shopToggle() {
			var nowBox = $(this).parent('.mDeliveryNow');
			//end of vars

			nowBox.toggleClass('mOpen');
			nowBox.toggleClass('mClose');
		};

		$('.bDeliveryNowClick').on('click', shopToggle);
	}

//	console.info('Расчет доставки');
//
//	var widgetBox = $('.bDelivery'),
//		deliveryData = widgetBox.data('value'),
//		url = deliveryData.url ? deliveryData.url : '',
//		response = deliveryData.response ? deliveryData.response : null,
//		loadShops = deliveryData.loadShops,
//		deliveryShops = ( deliveryData.delivery.length ) ? deliveryData.delivery[0].shop : [],
//		productInfo = $('#jsProductCard'),
//		productInfoVal = ( productInfo ) ? productInfo.data('value') : null,
//		dataToSend = {};
//	// end of vars
//
//	if ( !productInfo || !productInfoVal ) {
//		console.warn('Недостаточно данных для расчета доставки');
//		console.log(productInfo);
//		console.log(productInfoVal);
//
//		widgetBox.removeClass('mLoader');
//
//		return false;
//	}
//
//		/**
//		 * Показ попапа с магазином
//		 *
//		 * @param	{Object}	popup		Контейнер с попапом
//		 * @param	{Object}	button		Кнопка «перейти к магазину» в попапе
//		 * @param	{Object}	position	Координаты магазина, зашитые в ссылку
//		 * @param	{String}	url			Ссылка на магазин
//		 * @return	{Boolean}
//		 */
//	var showAvalShop = function showAvalShop() {
//			var popup = $('#avalibleShop'),
//				button = popup.find('.bOrangeButton'),
//				position = {
//					latitude: $(this).data('lat'),
//					longitude: $(this).data('lng')
//				},
//				url = $(this).attr('href');
//			// end of vars
//
//			button.attr('href', url);
//			$('#ymaps-avalshops').css({'width':600, 'height':400});
//
//			$.when(MapInterface.ready( 'yandex', {
//				yandex: $('#infowindowtmpl'),
//				google: $('#infowindowtmpl')
//			})).done(function(){
//				MapInterface.onePoint( position, 'ymaps-avalshops' );
//			});
//
//			popup.css({'width':600, 'height':425});
//			popup.lightbox_me({
//				centered: true,
//				onLoad: function() {
//				},
//				onClose: function(){
//					$('#ymaps-avalshops').empty();
//				}
//			});
//
//			return false;
//		},
//
//		/**
//		 * Заполнение шаблона с доступными для самовывоза магазинами
//		 *
//		 * @param	{Array}		shops			Массив магазинов
//		 * @param	{Object}	nowBox			Контейнер для элементов текущего типа доставки
//		 * @param	{Object}	toggleBtn		Кнопка переключения состояния листа магазинов открыто или закрыто
//		 * @param	{Object}	shopList		Контейнер для вывода списка магазинов
//		 * @param	{String}	templateNow		Готовый шаблон магазина
//		 * @param	{Object}	shopInfo		Данные для подстановки в шаблон магазина
//		 * @param	{Number}	shopLen			Количество магазинов
//		 */
//		fillAvalShopTmpl = function fillAvalShopTmpl( shops ) {
//			var nowBox = widgetBox.find('.mDeliveryNow'),
//				toggleBtn = nowBox.find('.bDeliveryNowClick'),
//				shopList = nowBox.find('.bDeliveryFreeAddress'),
//				templateNow = '',
//				shopInfo = {},
//				shopLen = shops.length;
//			// end of var
//
//			/**
//			 * Обработчик переключения состояния листа магазинов открыто или закрыто
//			 */
//			var shopToggle = function shopToggle() {
//				nowBox.toggleClass('mOpen');
//				nowBox.toggleClass('mClose');
//			};
//
//			if ( !shopLen ) {
//				return;
//			}
//
//			for ( var j = shopLen - 1; j >= 0; j-- ) {
//				shopInfo = {
//					name: shops[j].name,
//					lat: shops[j].latitude,
//					lng: shops[j].longitude,
//					url: shops[j].url
//				};
//
//				templateNow = tmpl('widget_delivery_shop',shopInfo);
//				shopList.append(templateNow);
//			}
//
//			widgetBox.removeClass('mLoader');
//			nowBox.show();
//			$('.bDeliveryFreeAddress__eLink').bind('click', showAvalShop);
//			toggleBtn.bind('click', shopToggle);
//		},
//
//		/**
//		 * Обработка ошибки получения списка магазинов с сервера
//		 *
//		 * @param	{Object}	res	Ответ от сервера
//		 */
//		errorHandler = function errorHandler() {
//			widgetBox.removeClass('mLoader');
//			widgetBox.remove();
//		},
//
//		/**
//		 * Обработка данных с сервера
//		 *
//		 * @param	{Object}	res	Ответ от сервера
//		 */
//		resFromServer = function resFromServer( res ) {
//			if ( !res.success ) {
//				errorHandler();
//
//				return false;
//			}
//
//			/**
//			 * Полученнный с сервера массив вариантов доставок для текущего товара
//			 * @type	{Array}
//			 */
//			var deliveryInfo = res.product[0].delivery,
//				i,
//				selfBox = widgetBox.find('.mDeliveryFree'),
//				templateSelf = false,
//				hasPickpoint = false,
//				hasSelf = false;
//
//			if ( !res.success ) {
//				errorHandler();
//				return false;
//			}
//
//			for ( var i = deliveryInfo.length - 1; i >= 0; i-- ) {
//				switch (deliveryInfo[i].token){
//					case 'standart':
//						var standartBox = widgetBox.find('.mDeliveryPrice'),
//							standartData = {
//								price: deliveryInfo[i].price,
//								dateString: deliveryInfo[i].date.name
//							},
//							templateStandart = tmpl('widget_delivery_standart', standartData);
//						// end of var
//
//						standartBox.html(templateStandart);
//						break;
//
//					case 'self':
//						var selfData = {
//								price: deliveryInfo[i].price,
//								dateString: deliveryInfo[i].date.name
//							};
//
//						hasSelf = true;
//						break;
//
//					case 'now':
//						fillAvalShopTmpl( deliveryInfo[i].shop );
//						break;
//
//					case 'pickpoint':
//						var pickpointData = {
//								price: 'PickPoint',
//								dateString: deliveryInfo[i].date.name
//							};
//
//						hasPickpoint = true;
//						break;
//				}
//			}//end for
//
//			if ( hasSelf ) {
//				console.log('Есть тип доставки -self-');
//				templateSelf = tmpl('widget_delivery_self', selfData);
//			}
//			else if ( hasPickpoint ) {
//				console.log('Есть тип доставки -pickpoint- и нету типа доставки -self-');
//				templateSelf = tmpl('widget_delivery_self', pickpointData);
//			}
//
//			if ( templateSelf ) {
//				selfBox.html(templateSelf);
//			}
//
//			widgetBox.removeClass('mLoader');
//		};
//	// end of functions
//
//	dataToSend = {
//		'product':[{
//			'id': productInfoVal.id
//		}]
//	};
//
//	if ( loadShops ) {
//		if ( deliveryShops.length === 0 ) {
//			console.warn('URL отсутствует. Список магазинов пуст.');
//
//			widgetBox.removeClass('mLoader');
//		}
//		else {
//			fillAvalShopTmpl( deliveryShops );
//		}
//	}
//
//	if ( url !== '' ) {
//		$.ajax({
//			type: 'POST',
//			url: url,
//			data: dataToSend,
//			success: resFromServer,
//			statusCode: {
//				500: errorHandler,
//				502: errorHandler,
//				503: errorHandler,
//				504: errorHandler
//			},
//			error: errorHandler
//		});
//	}
//	else if ( response ) {
//		resFromServer( response );
//	}
//
}());
