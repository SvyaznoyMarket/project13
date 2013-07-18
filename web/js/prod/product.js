/**
 * Кредит для карточки товара
 *
 * @author		Kotov Ivan, Zaytsev Alexandr
 * @requires	jQuery, printPrice, docCookies
 */

;(function(){
	if( $('.creditbox').length ) {
		var creditBoxNode = $('.creditbox');
		var priceNode = creditBoxNode.find('.creditbox__sum strong');

		window.creditBox = {
			cookieTimeout : null,
			
			toggleCookie : function( state ){
				clearTimeout( this.cookieTimeout );
				this.cookieTimeout = setTimeout( function(){
					docCookies.setItem(false, 'credit_on', state ? 1 : 0 , 60*60, '/');
				}, 200 );
			},

			init : function() {
				var self = this;
				$('.creditbox label').click( function(e) {
					var target = $(e.target);
					e.stopPropagation();
					if (target.is('input')) {
						return false;
					}
					
					$(this).toggleClass('checked');
					self.toggleCookie( $(this).hasClass('checked') );
				});
				if( this.getState() === 1) {
					$('.creditbox label').addClass('checked');
				}
				
				var creditd = $('input[name=dc_buy_on_credit]').data('model');

				creditd.count = 1;
				creditd.cart = '/cart';
				dc_getCreditForTheProduct(
					4427, 
					docCookies.getItem('enter_auth'),
					'getPayment',
					{ price : creditd.price, count : creditd.count, type : creditd.product_type },
					function( result ) {
						if( ! 'payment' in result ){
							return;
						}
						if( result.payment > 0 ) {
							priceNode.html( printPrice( Math.ceil(result.payment) ) );
							creditBoxNode.show();
						}
					}
				);

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
				if( ! docCookies.hasItem('credit_on') ){
					return 0;
				}
				return docCookies.getItem('credit_on');
				//return $('.creditbox input:checked').length
			}
		};
		
		creditBox.init();
	}
}());
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Расчет доставки
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, simple_templating
 * @param		{Object}	widgetBox		Контейнер с доступными вариантами доставки
 * @param		{Object}	deliveryData	Данные необходимые для отображения доставки
 * @param		{String}	url				Адрес по которому необходимо запросить данные с расчитанной доставкой для текущего продукта
 * @param		{String}	deliveryShops	Список магазинов из которых можно забрать сегодня (только если товар не доступен для продажи)
 * @param		{Object}	productInfo		Данные о текущем продукте
 * @param		{Object}	dataToSend		Данные для отправки на сервер и получение расчитанной доставки
 */
(function(){
	if (!$('#jsProductCard').length){
		return false;
	}

	var widgetBox = $('.bWidgetBuy__eDelivery'),
		deliveryData = widgetBox.data('value'),
		url = deliveryData.url,
		deliveryShops = (deliveryData.delivery.length) ? deliveryData.delivery[0].shop : [],
		productInfo = $('#jsProductCard').data('value'),
		dataToSend = {
			'product':[
				{'id': productInfo.id}
			]
		},

		/**
		 * Показ попапа с магазином
		 *
		 * @param	{Object}	popup		Контейнер с попапом
		 * @param	{Object}	button		Кнопка «перейти к магазину» в попапе
		 * @param	{Object}	position	Координаты магазина, зашитые в ссылку
		 * @param	{String}	url			Ссылка на магазин
		 * @return	{Boolean}
		 */
		showAvalShop = function (){
			var popup = $('#avalibleShop'),
				button = popup.find('.bOrangeButton'),
				position = {
					latitude: $(this).data('lat'),
					longitude: $(this).data('lng')
				},
				url = $(this).attr('href');
			// end of var

			button.attr('href', url);
			$('#ymaps-avalshops').css({'width':600, 'height':400});

			$.when(MapInterface.ready( 'yandex', {
				yandex: $('#infowindowtmpl'), 
				google: $('#infowindowtmpl')
			})).done(function(){
				MapInterface.onePoint( position, 'ymaps-avalshops' );
			});

			popup.css({'width':600, 'height':425});
			popup.lightbox_me({
				centered: true,
				onLoad: function() {
				},
				onClose: function(){
					$('#ymaps-avalshops').empty();
				}
			});

			return false;
		},

		/**
		 * Заполнение шаблона с доступными для самовывоза магазинами
		 * 
		 * @param	{Array}		shops			Массив магазинов
		 * @param	{Object}	nowBox			Контейнер для элементов текущего типа доставки
		 * @param	{Object}	toggleBtn		Кнопка переключения состояния листа магазинов открыто или закрыто
		 * @param	{Object}	shopList		Контейнер для вывода списка магазинов
		 * @param	{String}	templateNow		Готовый шаблон магазина
		 * @param	{Object}	shopInfo		Данные для подстановки в шаблон магазина
		 * @param	{Number}	shopLen			Количество магазинов
		 */
		fillAvalShopTmpl = function (shops){
			var nowBox = widgetBox.find('.bWidgetBuy__eDelivery-now'),
				toggleBtn = nowBox.find('.bWidgetBuy__eDelivery-nowClick'),
				shopList = nowBox.find('.bDeliveryFreeAddress'),
				templateNow = '',
				shopInfo = {},
				shopLen = shops.length,

				/**
				 * Обработчик переключения состояния листа магазинов открыто или закрыто
				 */
				shopToggle = function (){
					nowBox.toggleClass('mOpen');
					nowBox.toggleClass('mClose');
				};
			// end of var
			
			if (!shopLen){
				return;
			}

			for (var j = shopLen - 1; j >= 0; j--) {
				shopInfo = {
					name: shops[j].name,
					lat: shops[j].latitude,
					lng: shops[j].longitude,
					url: shops[j].url
				};

				templateNow = tmpl('widget_delivery_shop',shopInfo);
				shopList.append(templateNow);
			}

			nowBox.show();
			$('.bDeliveryFreeAddress__eLink').bind('click', showAvalShop);
			toggleBtn.bind('click', shopToggle);
		},

		/**
		 * Обработка данных с сервера
		 * 
		 * @param	{Object}	res	Ответ от сервера
		 */
		resFromSerever = function (res){
			if (!res.success){
				return false;
			}

			/**
			 * Полученнный с сервера массив вариантов доставок для текущего товара
			 * @type	{Array}
			 */
			var deliveryInfo = res.product[0].delivery;

			for (var i = deliveryInfo.length - 1; i >= 0; i--) {
				switch (deliveryInfo[i].token){
					case 'standart':
						var standartBox = widgetBox.find('.bWidgetBuy__eDelivery-price'),
							standartData = {
								price: deliveryInfo[i].price,
								dateString: deliveryInfo[i].date.name
							},
							templateStandart = tmpl('widget_delivery_standart', standartData);
						// end of var

						standartBox.html(templateStandart);
						break;

					case 'self':
						var selfBox = widgetBox.find('.bWidgetBuy__eDelivery-free'),
							selfData = {
								price: deliveryInfo[i].price,
								dateString: deliveryInfo[i].date.name
							},
							templateSelf = tmpl('widget_delivery_self', selfData);
						// end of var

						selfBox.html(templateSelf);
						break;

					case 'now':
						fillAvalShopTmpl(deliveryInfo[i].shop);
						break;
				}
			}
		};
	// end of var

	if (url === '') {
		fillAvalShopTmpl(deliveryShops);
	}
	else {
		$.ajax({
			type: 'POST',
			url: url,
			data: dataToSend,
			success: resFromSerever
		});
	}
}());
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Слайдер изображений товара
 *
 * @author	Zaytsev Alexandr
 * @requires jQuery
 */
;(function(){

	/**
	 * Инициализация слайдера
	 *
	 * @param	{Object}	slider		Элемент слайдера
	 * @param	{Object}	fotoBox		Элемент контейнера с фотографиями
	 * @param	{Object}	leftArr		Стрелка влево
	 * @param	{Object}	rightArr	Стрелка вправо
	 * @param	{Object}	photos		Карточки фотографий
	 * @param	{Number}	itemW		Ширина одной карточки с фотографией
	 * @param	{Number}	nowLeft		Текущий отступ слева
	 */
	var initFotoSlider = function(){
		var slider = $('.bPhotoActionOtherPhoto');
		var fotoBox = slider.find('.bPhotoActionOtherPhotoList');
		var leftArr = slider.find('.bPhotoActionOtherPhoto__eBtn.mPrev');
		var rightArr = slider.find('.bPhotoActionOtherPhoto__eBtn.mNext');
		var photos = fotoBox.find('.bPhotoActionOtherPhotoItem');

		if (!photos.length){
			return false;
		}

		var itemW = photos.width() + parseInt(photos.css('marginLeft'),10) + parseInt(photos.css('marginRight'),10);
		var nowLeft = 0;

		fotoBox.css({'width': photos.length*itemW, 'left':nowLeft});
		/**
		 * Проверка стрелок
		 */
		var checkArrow = function(){
			if (nowLeft > 0){
				leftArr.show();
			}
			else {
				leftArr.hide();	
			}

			if (nowLeft < fotoBox.width()-slider.width()){
				rightArr.show();
			}
			else {
				rightArr.hide();
			}
		};

		/**
		 * Предыдущее фото
		 */
		var prevFoto = function(){
			nowLeft = nowLeft - itemW;
			fotoBox.animate({'left':-nowLeft});
			checkArrow();
			return false;
		};

		/**
		 * Следущее фото
		 */
		var nextFoto = function(){
			nowLeft = nowLeft + itemW;
			fotoBox.animate({'left':-nowLeft});
			checkArrow();
			return false;
		};

		checkArrow();

		leftArr.bind('click', prevFoto);
		rightArr.bind('click', nextFoto);
	};

	$(document).ready(function() {
		if ( $('.bPhotoActionOtherPhoto').length){
			initFotoSlider();
		}
	});
})();
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * 3D для мебели
 */
;(function(){
	var loadFurniture3D = function(){
		var furnitureAfterLoad = function(){

			var object = $('#3dModelImg');
			var data = object.data('value');
			var host = object.data('host');

			var furniture3dPopupShow = function(){
				$('#3dModelImg').lightbox_me({
					centered: true,
					closeSelector: ".close"
				});
				return false;
			};

			try {
				if (!$('#3dImgContainer').length) {
					var AnimFramePlayer = new DAnimFramePlayer(document.getElementById('3dModelImg'), host);
					AnimFramePlayer.DoLoadModel(data);
					$('.bPhotoActionOtherAction__eGrad360.3dimg').bind('click', furniture3dPopupShow);
				}
			}
			catch (err){
				var pageID = $('body').data('id');
				var dataToLog = {
					event: '3dimg',
					type:'ошибка загрузки 3dimg для мебели',
					pageID: pageID,
					err: err
				};
				logError(dataToLog);
			}
		};
		$LAB.script( 'DAnimFramePlayer.min.js' ).wait(furnitureAfterLoad);
	};

	$(document).ready(function() {
		if (pageConfig['product.img3d']){
			loadFurniture3D();
		}
	});
})();
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Слайдер товаров
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery
 */

;(function($){
	$.fn.goodsSlider = function(params) {
		return this.each(function() {
			var options = $.extend(
							{},
							$.fn.goodsSlider.defaults,
							params),
				$self = $(this),
				hasCategory = $self.hasClass('mWithCategory'),
				leftBtn = $self.find(options.leftArrowSelector),
				rightBtn = $self.find(options.rightArrowSelector),
				wrap = $self.find(options.sliderWrapperSelector),
				slider = $self.find(options.sliderSelector),
				item = $self.find(options.itemSelector),
				catItem = $self.find(options.categoryItemselector),
				itemW = item.width() + parseInt(item.css('marginLeft'),10) + parseInt(item.css('marginRight'),10),
				elementOnSlide = wrap.width()/itemW,
				nowLeft = 0,

				nextSlide = function(){
					if ($(this).hasClass('mDisabled')){
						return false;
					}

					leftBtn.removeClass('mDisabled');

					if (nowLeft + elementOnSlide * itemW >= slider.width()-elementOnSlide * itemW){
						nowLeft = slider.width()-elementOnSlide * itemW
						rightBtn.addClass('mDisabled');
					}
					else{
						nowLeft = nowLeft + elementOnSlide * itemW;
						rightBtn.removeClass('mDisabled');
					}

					slider.animate({'left': -nowLeft });

					return false;
				},

				prevSlide = function(){
					if ($(this).hasClass('mDisabled')){
						return false;
					}

					rightBtn.removeClass('mDisabled');

					if (nowLeft - elementOnSlide * itemW <= 0){
						nowLeft = 0;
						leftBtn.addClass('mDisabled');
					}
					else{
						nowLeft = nowLeft - elementOnSlide * itemW;
						leftBtn.removeClass('mDisabled');
					}

					slider.animate({'left': -nowLeft });

					return false;
				},

				reWidthSlider = function(nowItems){
					leftBtn.addClass('mDisabled');
					rightBtn.addClass('mDisabled');

					if (nowItems.length > elementOnSlide) {
						rightBtn.removeClass('mDisabled');
					}

					slider.width(nowItems.length * itemW);
					nowLeft = 0;
					leftBtn.addClass('mDisabled');
					slider.css({'left':nowLeft});
					nowItems.show();
				},

				showCategoryGoods = function(){
					var nowCategoryId = catItem.filter('.mActive').attr('id'),
						showAll = (catItem.filter('.mActive').data('product') === 'all'),
						nowShowItem = (showAll) ? item : item.filter('[data-category="'+nowCategoryId+'"]');
					//end of vars
					
					item.hide();
					reWidthSlider(nowShowItem);
				},

				selectCategory = function(){
					catItem.removeClass('mActive');
					$(this).addClass('mActive');
					showCategoryGoods();
				};
		//end of vars

			if (hasCategory) {
				showCategoryGoods();
			}
			else {
				reWidthSlider(item);
			}

			rightBtn.bind('click', nextSlide);
			leftBtn.bind('click', prevSlide);
			catItem.bind('click', selectCategory)
		});
	};

	$.fn.goodsSlider.defaults = {
		leftArrowSelector: '.bSliderAction__eBtn.mPrev',
		rightArrowSelector: '.bSliderAction__eBtn.mNext',
		sliderWrapperSelector: '.bSliderAction__eInner',
		sliderSelector: '.bSliderAction__eList',
		itemSelector: '.bSliderAction__eItem',
		categoryItemselector: '.bGoodsSlider__eCatItem'
	};

})(jQuery);

$(document).ready(function() {
	if ($('.bGoodsSlider').length) {
		$('.bGoodsSlider').goodsSlider();
	}
});
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Подсказки к характеристикам
 *
 * @author	Zaytsev Alexandr
 * @requires jQuery
 */
(function(){
	var hintShower = function(){
		var hintPopup = $('.bHint_ePopup');
		var hintLnk = $('.bHint_eLink');
		var hintCloseLnk = $('.bHint_ePopup .close');

		var hintAnalytics = function(data){
			if (typeof(_gaq) !== 'undefined') {
				_gaq.push(['_trackEvent', 'Hints', data.hintTitle, data.url]);
			}
		};

		var hintShow = function(){
			hintPopup.hide();
			$(this).parent().find('.bHint_ePopup').fadeIn(150);

			var analyticsData = {
				hintTitle: $(this).html(),
				url: window.location.href
			};
			hintAnalytics(analyticsData);

			return false;
		};

		var hintClose = function(){
			hintPopup.fadeOut(150);
			return false;
		};


		hintLnk.bind('click', hintShow);

		hintCloseLnk.bind('click', hintClose);
	};


	$(document).ready(function() {
		if ($('.bHint').length){
			hintShower();
		}
	});
}());
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Планировщик шкафов купе
 *
 * @requires jQuery
 */
;(function(){
		/**
		 * Имя объекта для конструктора шкафов купе
		 *
		 * ВНИМАНИЕ
		 * Имя переменной менять нельзя. Захардкожено в файле KupeConstructorScript.js
		 * Переменная должна находится в глобальной области видимости
		 */
		Planner3dKupeConstructor = null;


		/**
		 * Callback Инициализации конструктора шкафов
		 *
		 * ВНИМАНИЕ
		 * Название функции менять нельзя. Захардкожено в файле KupeConstructorScript.js
		 * Функция должна находится в глобальной области видимости
		 */
		Planner3d_Init = function (ApiIds){
			// console.info(ApiIds)
		};


		/**
		 * Callback изменений в конструкторе шкафов
		 * 
		 * ВНИМАНИЕ
		 * Название функции менять нельзя. Захардкожено в файле KupeConstructorScript.js
		 * Функция должна находится в глобальной области видимости
		 */
		Planner3d_UpdatePrice = function (IdsWithInfo) {
			var url = $('#planner3D').data('cart-sum-url');
			var product = {};
			product.product = {};

			var authFromServer = function(res){
				if (!res.success){
					return false;
				}

				$('.jsPrice').html(printPrice(res.sum));
			};

			for (var i = 0, len = IdsWithInfo.length; i < len; i++){
				var prodID = IdsWithInfo[i].id;

				if (IdsWithInfo[i].error !== ''){
					$('.jsBuyButton').addClass('mDisabled');
					$('#coupeError').html('Вставки продаются только парами!').show();
					return false;
				}
				$('.jsBuyButton').removeClass('mDisabled');
				$('#coupeError').hide();

				if (product.product[prodID+''] !== undefined){
					product.product[prodID+''].quantity++;
				}
				else{
					product.product[prodID+''] = {
						id : prodID,
						quantity : 1
					};
				}
			}

			$.ajax({
				type: 'POST',
				url: url,
				data: product,
				success: authFromServer
			});
		};


		/**
		 * Добавление шкафа купе в корзину
		 */
		var kupe2basket = function(){
			if ($(this).hasClass('mDisabled')){
				return false;
			}

			var structure = Planner3dKupeConstructor.GetBasketContent();
			var url = $(this).attr('href');

			var resFromServer = function(res){
				if ( !res.success ) {
					return false;
				}
				$('.jsBuyButton').html('В корзине').addClass('mBought').attr('href','/cart');

				/* костыль */
				res.product.name = $('.bMainContainer__eHeader-title').html();
				res.product.price = $('.jsPrice').eq('1').html();
				res.product.article = $('.bMainContainer__eHeader-article').html();
				/* */
				
				$("body").trigger("addtocart", [res]);
			};

			var product = {};

			product.product = structure;
			$.ajax({
				type: 'POST',
				url: url,
				data: product,
				success: resFromServer
			});
			return false;
		};

		var initPlanner = function(){
			try {
				var coupeInfo = $('#planner3D').data('product');
				
				Planner3dKupeConstructor = new DKupe3dConstructor(document.getElementById('planner3D'),'/css/item/coupe_img/','/css/item/coupe_tex/', '/css/item/test_coupe_icons/');
				Planner3dKupeConstructor.Initialize('/js/KupeConstructorData.json', coupeInfo.id);
			}
			catch (err){
				var pageID = $('body').data('id');
				var dataToLog = {
					event: 'Kupe3dConstructor error',
					type:'ошибка загрузки Kupe3dConstructor',
					pageID: pageID,
					err: err
				};
				logError(dataToLog);
			}

			$('.jsBuyButton').off();
			$('.jsBuyButton').bind('click', kupe2basket);
		};


	$(document).ready(function() {
		if ($('#planner3D').length){
			$LAB.script( 'KupeConstructorScript.min.js' ).script( 'three.min.js' ).wait(initPlanner);
		}
	});
})();
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Подписка на снижение цены
 *
 * @author	Zaytsev Alexandr
 * @requires jQuery, jQuery.placeholder plugin, jQuery.emailValidate plugin
 */
;(function(){
	var lowPriceNotifer = function(){
		var notiferButton = $('.jsLowPriceNotifer');
		var submitBtn = $('.bLowPriceNotiferPopup__eSubmitEmail');
		var input = $('.bLowPriceNotiferPopup__eInputEmail');
		var notiferPopup = $('.bLowPriceNotiferPopup');
		var error = $('.bLowPriceNotiferPopup__eError');

		var lowPriceNitiferHide = function(){
			notiferPopup.fadeOut(300);
			return false;
		};

		var lowPriceNitiferShow = function(){
			notiferPopup.fadeIn(300);
			notiferPopup.find('.close').bind('click', lowPriceNitiferHide);
			return false;
		};

		var lowPriceNitiferSubmit = function(){
			if (submitBtn.hasClass('mDisabled')){
				error.show().html('Неправильный email');
				return false;
			}

			var submitUrl = submitBtn.data('url');
			submitUrl += encodeURI('?email='+input.val());

			var resFromServer = function(res){
				if (!res.success){
					input.addClass('red');
					if (res.error.message){
						error.show().html(res.error.message);
					}
					return false;
				}

				lowPriceNitiferHide();
				notiferPopup.remove();
				notiferButton.remove();
			};
			$.get( submitUrl, resFromServer);

			return false;
		};

		input.placeholder().emailValidate({
			onValid: function(){
				submitBtn.removeClass('mDisabled');
				error.hide();
			},
			onInvalid: function(){
				submitBtn.addClass('mDisabled');
			}
		});
		submitBtn.bind('click', lowPriceNitiferSubmit);
		notiferButton.bind('click', lowPriceNitiferShow);
	};

	$(document).ready(function() {
		if ($('.jsLowPriceNotifer').length){
			lowPriceNotifer();
		}
	});
}());
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Maybe3D
 */
;(function(){
	var loadMaybe3D = function(){
		var data = $('#maybe3dModelPopup').data('value');
		var afterLoad = function(){
			var maybe3dPopupShow = function(e){
				e.stopPropagation();
				try {
					if (!$('#maybe3dModel').length){
						$('#maybe3dModelPopup_inner').append('<div id="maybe3dModel"></div>');
					}
					swfobject.embedSWF(data.init.swf, data.init.container, data.init.width, data.init.height, data.init.version, data.init.install, data.flashvars, data.params, data.attributes);
					$('#maybe3dModelPopup').lightbox_me({
						centered: true,
						closeSelector: ".close",
						onClose: function() {
							swfobject.removeSWF(data.attributes.id);
						}
					});
				}
				catch (err){
					var pageID = $('body').data('id');
					var dataToLog = {
						event: 'swfobject_error',
						type:'ошибка загрузки swf maybe3d',
						pageID: pageID,
						err: err
					};
					logError(dataToLog);
				}
				return false;
			};
			$('.bPhotoActionOtherAction__eGrad360.maybe3d').bind('click', maybe3dPopupShow);
		};
		$LAB.script('swfobject.min.js').wait(afterLoad);
	};

	$(document).ready(function() {
		if (pageConfig['product.maybe3d']){
			loadMaybe3D();
		}
	});
}());
 
 
/** 
 * NEW FILE!!! 
 */
 
 
$(document).ready(function() {


	/**
	 * Подключение нового зумера
	 *
	 * @requires jQuery, jQuery.elevateZoom
	 */
	$('.bZoomedImg').elevateZoom({
		gallery: 'productImgGallery',
		galleryActiveClass: 'mActive',
		zoomWindowOffety: 0,
		zoomWindowOffetx: 19,
		zoomWindowWidth: 519,
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
				e.preventDefault();
				$(this).parent().parent().find('.descriptionlist:not(.short)').show();
				$(this).html('Скрыть все характеристики');
			},
			function(e){
				e.preventDefault();
				$(this).parent().parent().find('.descriptionlist:not(.short)').hide();
				$(this).html('Показать все характеристики');
			}
		);
	}
});
 
 
/** 
 * NEW FILE!!! 
 */
 
 
;(function(){
	// текущая страница для каждой вкладки
	var reviewCurrentPage = {
		user: -1,
		pro: -1
	};
	// количество страниц для каждой вкладки
	var reviewPageCount = {
		user: 0,
		pro: 0
	};
	var reviewsProductId = null;
	var reviewsType = null;
	var reviewsContainerClass = null;

	//nodes
	var moreReviewsButton = $('.jsGetReviews');
	var reviewTab = $('.bReviewsTabs__eTab');
	var reviewWrap = $('.bReviewsWrapper');
	var reviewContent = $('.bReviewsContent');
	// получение отзывов
	var getReviews = function(productId, type, containerClass) {
		var page = reviewCurrentPage[type] + 1;
		
		var layout = false;
		if($('body').hasClass('jewel')) {
			layout = 'jewel';
		}

		$.get('/product-reviews/'+productId, {
			page: page,
			type: type,
			layout: layout
		}, 
		function(data){
			$('.'+containerClass).html($('.'+containerClass).html() + data.content);
			reviewCurrentPage[type]++;
			reviewPageCount[type] = data.pageCount;
			if(reviewCurrentPage[type] + 1 >= reviewPageCount[type]) {
				moreReviewsButton.hide();
			}
			else {
				moreReviewsButton.show();
			}
		});
	};

	// карточка товара - отзывы - переключение по табам
	if(reviewTab.length) {
		// начальная инициализация
		var initialType = reviewWrap.attr('data-reviews-type');

		reviewCurrentPage[initialType]++;
		reviewPageCount[initialType] = reviewWrap.attr('data-page-count');

		if(reviewPageCount[initialType] > 1) {
			moreReviewsButton.show();
		}
		reviewsProductId = reviewWrap.attr('data-product-id');
		reviewsType = reviewWrap.attr('data-reviews-type');
		reviewsContainerClass = reviewWrap.attr('data-container');

		reviewTab.click(function(){
			reviewsContainerClass = $(this).attr('data-container');
			if (reviewsContainerClass === undefined){
				return;
			}

			reviewsType = $(this).attr('data-reviews-type');
			reviewTab.removeClass('active');
			$(this).addClass('active');
			reviewContent.hide();
			$('.'+reviewsContainerClass).show();

			moreReviewsButton.hide();
			if (reviewsType === 'user') {
				moreReviewsButton.html('Показать ещё отзывы');
			} else if(reviewsType === 'pro') {
				moreReviewsButton.html('Показать ещё обзоры');
			}

			if(!$('.'+reviewsContainerClass).html()) {
				getReviews(reviewsProductId, reviewsType, reviewsContainerClass);
			} else {
				// проверяем что делать с кнопкой "показать еще" - скрыть/показать
				if(reviewCurrentPage[reviewsType] + 1 >= reviewPageCount[reviewsType]) {
					moreReviewsButton.hide();
				} else {
					moreReviewsButton.show();
				}
			}
		});

		moreReviewsButton.click(function(){
			getReviews(reviewsProductId, reviewsType, reviewsContainerClass);
		});
	}

	var leaveReview = function(){
		if (!$('#jsProductCard').length){
			return false;
		}
		
		var productInfo = $('#jsProductCard').data('value');
		var pid = $(this).data('pid');
		var name = productInfo.name;
		var src = "http://reviews.testfreaks.com/reviews/new?client_id=enter.ru&" + $.param({key: pid, name: name});

		$(".reviewPopup").lightbox_me({
			onLoad: function() {
				$("#rframe").attr("src", src);
			}
		});
		return false;
	};

	$('.jsLeaveReview').on('click', leaveReview);

}());
 
 
/** 
 * NEW FILE!!! 
 */
 
 
/**
 * Видео в карточке товара
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, jQuery.lightbox_me
 */
;(function(){
	var initVideo = function(){
		if (!$('#productVideo').length){
			return false;
		}

		var videoStartTime = 0;
		var videoEndTime = 0;
		var productUrl = document.location.href;
		var shield = $('.bPhotoActionOtherAction__eVideo');
		var iframe = $('#productVideo .productVideo_iframe').html();

		var openVideo = function(){
			$('#productVideo .productVideo_iframe').append(iframe);
			$(".productVideo_iframe iframe").attr("src", $(".productVideo_iframe iframe").attr("src")+"?autoplay=1");
			$('#productVideo').lightbox_me({ 
				centered: true,
				onLoad: function(){
					videoStartTime = new Date().getTime();

					if (typeof(_gaq) !== 'undefined') {
						_gaq.push(['_trackEvent', 'Video', 'Play', productUrl]);
					}
				},
				onClose: function(){
					$('#productVideo .productVideo_iframe').empty();
					videoEndTime = new Date().getTime();
					var videoSpent = videoEndTime - videoStartTime;

					if (typeof(_gaq) !== 'undefined') {
						_gaq.push(['_trackEvent', 'Video', 'Stop', productUrl, videoSpent]);
					}
				}
			});
			return false;
		};

		$('#productVideo .productVideo_iframe').empty();

		shield.bind('click', openVideo);
	};

	$(document).ready(function() {
		if ($('.bPhotoActionOtherAction__eVideo').length){
			initVideo();
		}
	});
}());
 
 
/** 
 * NEW FILE!!! 
 */
 
 
;(function(){
	var addWarranty = function(el){
		var url = el.data('set-url');
		var resFromServer = function(res){
			if (!res.success){
				return false;
			}
			console.log(res);
			// if (blackBox) {
			// 	var basket = data.cart;
			// 	var product = data.product;
			// 	var tmpitem = {
			// 		'title': product.name,
			// 		'price' : printPrice(product.price),
			// 		'imgSrc': product.img,
			// 		'productLink': product.link,
			// 		'totalQuan': basket.full_quantity,
			// 		'totalSum': printPrice(basket.full_price),
			// 		'linkToOrder': basket.link,
			// 	};
			// 	blackBox.basket().add(tmpitem);
			// }
		};
		$.ajax({
			type: 'GET',
			url: url,
			success: resFromServer
		});
	};

	var delWarranty = function(el){
		var url = el.data('delete-url');
		var resFromServer = function(res){
			if (!res.success){
				return false;
			}
			console.log(res);
			
			if (blackBox) {
				var basket = res.cart;
				var tmpitem = {
					'cartQ': basket.full_quantity,
					'cartSum' : printPrice(basket.full_price)
				};
				blackBox.basket().update(tmpitem);
			}
		};
		$.ajax({
			type: 'GET',
			url: url,
			success: resFromServer
		});
	};


	$('.jsCustomRadio').customRadio({
		onChecked: addWarranty,
		onUncheckedGroup: delWarranty
	});
}());