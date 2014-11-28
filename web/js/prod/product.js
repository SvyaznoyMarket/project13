/**
 * Кредит для карточки товара
 *
 * @author		Kotov Ivan, Zaytsev Alexandr
 * @requires	jQuery, printPrice, docCookies, JsHttpRequest.js
 */
;(function() {
	if( $('.creditbox').length ) {
		console.log('## Product has creditbox');
		var
			creditBoxNode = $('.creditbox'),
			priceNode = creditBoxNode.find('.creditbox__sum strong');
		// end of vars

		window.creditBox = {
			cookieTimeout: null,
			
			toggleCookie: function( state ) {
				clearTimeout( this.cookieTimeout );
				this.cookieTimeout = setTimeout( function(){
					window.docCookies.setItem('credit_on', state ? 1 : 0 , 60*60, '/');
				}, 200 );
			},

			init: function() {
				var
					self = this,
					creditd = $('input[name=dc_buy_on_credit]').data('model'),
					label = $('.creditbox label');
				// end of vars

				$('input[type=radio][name=price_or_credit]').change(function( e ) {
					e.preventDefault();

					if ( !label.length ) {
						return;
					}

					label.toggleClass('checked');
					self.toggleCookie( label.hasClass('checked') );
				});

				if ( this.getState() === 1 ) {
					$('.creditbox label').addClass('checked');
				}

				creditd.count = 1;
				creditd.cart = '/cart';

				dc_getCreditForTheProduct(
					4427,
					window.docCookies.getItem('enter_auth'),
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
			},
			
			getState: function() {
				if( ! window.docCookies.hasItem('credit_on') ) {
					return 0;
				}

				return window.docCookies.getItem('credit_on');
			}
		};
		
		creditBox.init();
	}
	else {
		console.log('## Product does not have creditbox');
		var
			productDesc = $('.bProductDesc');

		if ( productDesc.length && !productDesc.hasClass('mNoCredit') ) {
			productDesc.addClass('mNoCredit'); // добавим класс, дабы скрыть кредитный чекбокс
		}
	}
}());
/**
 * Открытие окна доставки
 *
 * @author		Zhukov Roman
 * @requires	jQuery, lightbox_me
 *
 */
(function() {

    $('.js-show-shops').on('click', function() {
        var popup = $('.shopsPopup'),
            buyButtons = $('.shopsPopup .jsBuyButton');

        popup.lightbox_me({
            centered: true,
            autofocus: true
        });

        buyButtons.on('click', function(){
            popup.trigger('close')
        });
    });

}());

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
		var slider = $('.bPhotoSlider');
		var fotoBox = slider.find('.bPhotoSliderGallery');
		var leftArr = slider.find('.bPhotoSlider__eBtn.mPrev');
		var rightArr = slider.find('.bPhotoSlider__eBtn.mNext');
		var photos = fotoBox.find('.bPhotoSliderGallery__eItem');

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
		if ( $('.bPhotoSlider').length){
			initFotoSlider();
		}
	});
})();
/**
 * 3D для мебели
 *
 * @requires jQuery, ENTER.utils.logError, ENTER.config
 */
;(function( global ) {
	var pageConfig = global.ENTER.config.pageConfig,
		utils = global.ENTER.utils;
	// end of vars
	
	var loadFurniture3D = function loadFurniture3D() {
		var furnitureAfterLoad = function furnitureAfterLoad() {

			var object = $('#3dModelImg'),
				data = object.data('value'),
				host = object.data('host'),

				AnimFramePlayer = null;
			// end of vars

			var furniture3dPopupShow = function furniture3dPopupShow() {
				$('#3dModelImg').lightbox_me({
					centered: true,
					closeSelector: '.close'
				});

				return false;
			};

			try {
				if ( !$('#3dImgContainer').length ) {
					AnimFramePlayer = new DAnimFramePlayer(document.getElementById('3dModelImg'), host);

					AnimFramePlayer.DoLoadModel(data);
					$('.mGrad360.3dimg').bind('click', furniture3dPopupShow);
				}
			}
			catch ( err ) {
				var dataToLog = {
						event: '3dimg',
						type: 'ошибка загрузки 3dimg для мебели',
						err: err
					};
				// end of vars

				utils.logError(dataToLog);
			}
		};

		$LAB.script( 'DAnimFramePlayer.min.js' ).wait(furnitureAfterLoad);
	};

	$(document).ready(function() {
		if ( pageConfig['product.img3d'] ) {
			loadFurniture3D();
		}
	});
}(this));
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
 * Планировщик шкафов купе
 *
 * @requires jQuery, ENTER.utils.logError
 */
;(function( global ) {
	/**
	 * Имя объекта для конструктора шкафов купе
	 *
	 * ВНИМАНИЕ
	 * Имя переменной менять нельзя. Захардкожено в файле KupeConstructorScript.js
	 * Переменная должна находится в глобальной области видимости
	 */
	global.Planner3dKupeConstructor = null;


	/**
	 * Callback Инициализации конструктора шкафов
	 *
	 * ВНИМАНИЕ
	 * Название функции менять нельзя. Захардкожено в файле KupeConstructorScript.js
	 * Функция должна находится в глобальной области видимости
	 */
	global.Planner3d_Init = function ( ApiIds ) {
		// console.info(ApiIds)
	};


	/**
	 * Callback изменений в конструкторе шкафов
	 * 
	 * ВНИМАНИЕ
	 * Название функции менять нельзя. Захардкожено в файле KupeConstructorScript.js
	 * Функция должна находится в глобальной области видимости
	 */
	global.Planner3d_UpdatePrice = function ( IdsWithInfo ) {
		var url = $('#planner3D').data('cart-sum-url'),
			product = {};
		// end of vars

		product.product = {};

		var authFromServer = function( res ) {
			if ( !res.success ) {
				return false;
			}

			$('.jsPrice').html(printPrice(res.sum));
		};

		for ( var i = 0, len = IdsWithInfo.length; i < len; i++ ) {
			var prodID = IdsWithInfo[i].id;

			if ( IdsWithInfo[i].error !== '' ) {
				$('.jsBuyButton').addClass('mDisabled');
				$('#coupeError').html('Вставки продаются только парами!').show();

				return false;
			}

			$('.jsBuyButton').removeClass('mDisabled');
			$('#coupeError').hide();

			if ( product.product[prodID+''] !== undefined ) {
				product.product[prodID+''].quantity++;
			}
			else {
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
	var kupe2basket = function() {
		if ( $(this).hasClass('mDisabled') ) {
			return false;
		}

		var structure = global.Planner3dKupeConstructor.GetBasketContent(),
			url = $(this).attr('href'),
			product = {};
		// end of vars

		var resFromServer = function( res ) {
			if ( !res.success ) {
				return false;
			}

			$('.jsBuyButton').html('В корзине').addClass('mBought').attr('href','/cart');

			/* костыль */
			res.product.name = $('.bMainContainer__eHeader-title').html();
			res.product.price = $('.jsPrice').eq('1').html();
			res.product.article = $('.bMainContainer__eHeader-article').html();
			/* */
			
			$('body').trigger('addtocart', [res]);
		};

		product.product = structure;

		$.ajax({
			type: 'POST',
			url: url,
			data: product,
			success: resFromServer
		});

		return false;
	};

	var initPlanner = function() {
		try {
			var coupeInfo = $('#planner3D').data('product');
			
			global.Planner3dKupeConstructor = new DKupe3dConstructor(document.getElementById('planner3D'),'/css/item/coupe_img/','/css/item/coupe_tex/', '/css/item/test_coupe_icons/');
			global.Planner3dKupeConstructor.Initialize('/js/KupeConstructorData.json', coupeInfo.id);
		}
		catch ( err ) {
			var dataToLog = {
					event: 'Kupe3dConstructor error',
					type:'ошибка загрузки Kupe3dConstructor',
					err: err
				};
			// end of vars
			
			global.ENTER.utils.logError(dataToLog);
		}

		$('.jsBuyButton').off();
		$('.jsBuyButton').bind('click', kupe2basket);
	};


	$(document).ready(function() {
		if ( $('#planner3D').length ) {
			$LAB.script( 'KupeConstructorScript.min.js' ).script( 'three.min.js' ).wait(initPlanner);
		}
	});
}(this));
/**
 * Подписка на снижение цены
 *
 * @author	Zaytsev Alexandr
 * @requires jQuery, jQuery.placeholder plugin
 */
;(function() {
	var lowPriceNotifer = function lowPriceNotifer() {
		var
			notiferWrapper = $('.priceSale'),
			notiferButton = $('.jsLowPriceNotifer'),
			submitBtn = $('.bLowPriceNotiferPopup__eSubmitEmail'),
			input = $('.bLowPriceNotiferPopup__eInputEmail'),
			notiferPopup = $('.bLowPriceNotiferPopup'),
			error = $('.bLowPriceNotiferPopup__eError'),
			subscribe = $('.jsSubscribe');
		// end of vars

		var
			/**
			 * Скрыть окно подписки на снижение цены
			 */
			lowPriceNitiferHide = function lowPriceNitiferHide() {
				notiferPopup.fadeOut(300);

				return false;
			},

			/**
			 * Авторизованность пользователя
			 * Вызывается событием «userLogged» у body
			 *
			 * @param event
			 * @param userInfo — данные пользователя (если существуют)
			 */
			userLogged = function userLogin( event, userInfo ) {
				if ( userInfo ) {
					if( userInfo.name ) {
						// Если существует имя, значит юзер точно зарегистрирован и его данные получены
						notiferWrapper.show();
					}
					if( userInfo.email ) {
						input.val(userInfo.email);
					}
				}
			},

			/**
			 * Показать окно подписки на снижение цены
			 */
			lowPriceNitiferShow = function lowPriceNitiferShow() {
				notiferPopup.fadeIn(300);
				notiferPopup.find('.close').bind('click', lowPriceNitiferHide);

				return false;
			},

			/**
			 * Обработка ответа от сервера
			 * 
			 * @param	{Object}	res	Ответ от сервера
			 */
			resFromServer = function resFromServer( res ) {
				if ( !res.success ) {
					input.addClass('red');

					if ( res.error.message ) {
						error.show().html(res.error.message);
					}

					return false;
				}

				if (subscribe[0] && subscribe[0].checked && typeof _gaq != 'undefined') {
					_gaq.push(['_trackEvent', 'subscription', 'subscribe_price_alert', input.val()]);
				}

				lowPriceNitiferHide();
				notiferPopup.remove();
				notiferButton.remove();
			},

			/**
			 * Проверка чекбокса "Акции и суперпредложения"
			 */
			checkSubscribe = function checkSubscribe() {
				if ( subscribe.length && subscribe.is(':checked') ) {
					return true;
				}

				return false;
			},

			/**
			 * Отправка данных на сервер
			 */
			lowPriceNotiferSubmit = function lowPriceNotiferSubmit() {
				var
					submitUrl = submitBtn.data('url');
				// end of vars
				
				submitUrl += encodeURI('?email=' + input.val() + '&subscribe=' + (checkSubscribe() ? 1 : 0));
				$.get( submitUrl, resFromServer);

				return false;
			};
		// end of functions

		
		submitBtn.bind('click', lowPriceNotiferSubmit);
		notiferButton.bind('click', lowPriceNitiferShow);
		$('body').bind('userLogged', userLogged);
	};


	$(document).ready(function() {
		if ( $('.jsLowPriceNotifer').length ){
			lowPriceNotifer();
		}
	});
}());
/**
 * Maybe3D
 *
 * @requires jQuery, ENTER.utils.logError, ENTER.config
 */
;(function( global ) {
	var pageConfig = global.ENTER.config.pageConfig,
		utils = global.ENTER.utils,
		swfobjectLoaded = false;
	// end of vars

	var
		loadWithSWF = function( functionName ) {
			if ( 'function' !== typeof(functionName) ) return false;
			if ( !swfobjectLoaded ) {
				$LAB.script('swfobject.min.js').wait(functionName);
				swfobjectLoaded = true;
			}
			else{
				functionName();
			}
			return true;
		},
		loadMaybe3D = function() {
		var
			data = $('#maybe3dModelPopup').data('value');

		var
			afterLoad = function() {
			var
				maybe3dPopupShow = function( e ) {
				e.stopPropagation();
				try {
					if ( !$('#maybe3dModel').length ) {
						$('#maybe3dModelPopup_inner').append('<div id="maybe3dModel"></div>');
					}

					swfobject.embedSWF(data.init.swf, data.init.container, data.init.width, data.init.height, data.init.version, data.init.install, data.flashvars, data.params, data.attributes);
					$('#maybe3dModelPopup').lightbox_me({
						centered: true,
						closeSelector: '.close',
						onClose: function() {
							swfobject.removeSWF(data.attributes.id);
						}
					});
				}
				catch ( err ) {
					var
						dataToLog = {
							event: 'swfobject_error',
							type:'ошибка загрузки swf maybe3d',
							err: err
						};
					// end of vars

					utils.logError(dataToLog);
				}
				return false;
			};

			$('.mGrad360.maybe3d').bind('click', maybe3dPopupShow);
		};

		loadWithSWF(afterLoad);
	},
	loadFitting = function loadFitting() {
		console.log('### LoadFitting BEGIN');
		var
			f_afterLoad = function f_afterLoad()
			{
				var
					ARPluginLoad = function ARPluginLoad() {
						if ( 'undefined' === typeof(/*utils.*/ARPlugin) ) {
							console.warn('ARPlugin is not defined');
							$('li.vFitting' ).hide();
							return false;
						}
						console.log('ARPlugin is defined');
						/*utils.*/ARPlugin.init({
							//type:"advanced",
							type:"simple",
							js:"/js/prod/",
							css:"/styles/ARPlugin/",
							img:"/styles/ARPlugin/img/",
							swf:"/styles/ARPlugin/swf/",
							resources:		pageConfig['product.resources'],
							meshes_path:	pageConfig['product.meshes'],
							textures_path:	pageConfig['product.textures'],
							marker_path:	pageConfig['product.marker']
						});

						fittingPopupShow = function( e ) {
							e.preventDefault();
							if ( typeof _gaq !== 'undefined' ) {
								_gaq.push(['_trackEvent', '3D-primerochnaya', pageConfig['product.name'], 'click']);
							}
							/*utils.*/ARPlugin.show(
								pageConfig['product.article'] + '.obj',
								pageConfig['product.article'] + '.png'
							);
						};

						$('.vFitting').bind('click', fittingPopupShow);
					};

				$LAB.script('ARPluginOrigin.js').wait(ARPluginLoad);

			};

		loadWithSWF(f_afterLoad);
	};

	$(document).ready(function() {
		if ( pageConfig['product.maybe3d'] ) {
			loadMaybe3D();
		}

		if ( pageConfig['product.vFitting'] ) {
			loadFitting();
		}
	});
}(this));
/**
 * Kit JS
 *
 * @requires jQuery, knockout, printPrice
 * @author	Zhukov Roman
 * @param	{Object}	ENTER	Enter namespace
 */
;$(document).ready(function( ENTER ) {

    if ($('.packageSet').length === 0) return; // выходим из функции

	var product = $('#jsProductCard').data('value'),
        packageSetBtn = $('.jsChangePackageSet'),
		packageSetWindow = $('.jsPackageSetPopup'),
        packageProducts = $('.mPackageSetEdit').data('value');

	/**
	 * Показ окна с изменение комплекта 
	 */
	var showPackageSetPopup = function showPackageSetPopup(event) {
        event.preventDefault();
        $('.bCountSection').goodsCounter('destroy');
			packageSetWindow.lightbox_me({
				autofocus: true
			});
        // Google Analytics
        if (typeof _gaq !== 'undefined' && typeof product !== 'undefined') {
            console.log('_gaq:_trackEvent addedCollection collection %s', product.article);
            _gaq.push(['_trackEvent', 'addedCollection', 'collection', product.article]);
        }
    };

	packageSetBtn.on('click', showPackageSetPopup);

    /**
     * Закрытие окна
     */
    $('body').on('addtocart', function(){
        packageSetWindow.trigger('close.lme');
    });

    init();

    function init() {
        var ko = window.ko;

        function ProductModel(product){
            var self = this;

            self.id = product.id;
            self.url = product.url;
            self.name = product.name;
            self.line = product.lineName;
            self.price = product.price;
            self.image = product.image;
            self.height = product.height;
            self.width = product.width;
            self.depth = product.depth;
            self.count = ko.observable(product.count);
            self.maxCount = ko.observable(Infinity);
            self.prettyPrice = ko.computed(function(){
                return window.printPrice(parseInt(self.price) * parseInt(self.count()));
            });
            self.prettyItemPrice = ko.computed(function(){
                return window.printPrice(parseInt(self.price));
            });
            self.deliveryDate = ko.observable(product.deliveryDate);

            self.plusClick = function() {
                if (self.maxCount() > self.count() && self.count() < 99) {
                    self.count(parseInt(self.count()) + 1);
                    $.post('/ajax/product/delivery', {product: [
                        {id: self.id, quantity: self.count()}
                    ] }, function (data) {
                        if (data.success) {
                            self.deliveryDate(data.product[0].delivery[0].date.value);
                            console.log('Delivery: id=', self.id, ' quantity=', self.count(), ' date: ', data.product[0].delivery)
                        } else {
                            self.count(self.count() - 1);
                            self.maxCount(self.count());
                        }
                    })
                }
            };

            self.minusClick = function() {
                if (self.count() > 0) self.count(self.count()-1);
            };

            self.countKeydown = function(item, e) {
                e.stopPropagation();
                var isTextSelected = e.target.selectionStart - e.target.selectionEnd != 0;

                if ( e.which === 38 ) { // up arrow
                    item.plusClick();
                    return false;
                }
                else if ( e.which === 40 ) { // down arrow
                    item.minusClick();
                    return false;
                }
                else if ( e.which === 39 || e.which === 37 ) return true;
                else if ( (( (e.which >= 48) && (e.which <= 57) ) ||  //num keys
                    ( (e.which >= 96) && (e.which <= 105) ) || //numpad keys
                    (e.which === 8) ||
                    (e.which === 46))
                    ) {
                    if (!isTextSelected) { // если текст не выделен
                        if (item.count().toString().length < 2 && (e.which == 8 || e.which == 46)) return false; // предотвращаем пустую строку ввода
                        if (item.count().toString().length > 1 && !(e.which == 8 || e.which == 46)) return false;
                    }
                    return true;
                }
                return false;
            };

            self.countKeyUp = function(item, e) {
                // TODO-zra сделать проверку доставки
                if (self.count() === "") self.count(1); // если поле ввода вдруг окажется пустым
                return false;
            }
        }

        function ProductList(){
            var self = this;

            self.products = ko.observableArray([]);

            self.isBaseKit = ko.computed(function(){
                var isEqual = true;
                ko.utils.arrayForEach(self.products(), function(item){
                    if (packageProducts[item.id].count != item.count()) isEqual = false;
                });
                return isEqual;
            });

            self.totalPrice = ko.computed(function(){
                var total = 0;
                ko.utils.arrayForEach(self.products(), function(item) {
                    total += parseInt(item.count()) * parseInt(item.price)
                });
                return window.printPrice(total);
            });

            self.totalCount = ko.computed(function(){
                var total = 0;
                ko.utils.arrayForEach(self.products(), function(item) {
                    total += parseInt(item.count())
                });
                return total;
            });

            self.buyLink = ko.computed(function(){
                var link = '/cart/set-products?',
                    id = 0;
                ko.utils.arrayForEach(self.products(), function(item){
                    if (item.count() > 0 ) {
                        link += 'product['+id+'][id]=' + item.id + '&product['+id+'][quantity]=' + item.count() + '&';
                        id += 1;
                    }
                });
                return link;
            });

            self.dataUpsale = function(mainId){
                var url = '/ajax/upsale/' + mainId;
                return ko.toJSON({url : url, fromUpsale: false});
            };

            self.addProduct = function(product){
                self.products.push(new ProductModel(product))
            };

            self.populateWithObj = function(obj) {
                // Заполняем Модель продуктами
                self.products($.map(obj, function (item) {
                    return new ProductModel(item)
                }));

                // Сортируем по line
                self.products.sort(function(a, b){
                    return a.line == b.line ? 0 : ( a.line < b.line ? -1 : 1)
                });
            };

            self.resetToBaseKit = function() {
                self.populateWithObj(packageProducts);
            };

            self.populateWithObj(packageProducts);

        }

		ko.cleanNode(document.querySelector('.jsPackageSetPopup')); // на всякий случай
        ko.applyBindings(new ProductList(), document.querySelector('.jsPackageSetPopup'));

    }

}(window.ENTER));
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
            docCookies.setItem('product_viewed', viewed.join(','), 7 * 24 * 60 * 60, '/');
        }
    } catch (e) {
        console.error(e);
    }
});
/**
 * Аналитика просмотра карточки товара
 *
 * @requires jQuery
 */
(function() {
	var
		productInfo = $('#jsProductCard').data('value') || {},
		toKISS = {
			'Viewed Product SKU': productInfo.article,
			'Viewed Product Product Name': productInfo.name,
			'Viewed Product Product Status': productInfo.stockState
		},
	// end of vars

		reviewsYandexClick = function ( e ) {
			console.log('reviewsYandexClick');
			var
				link = this //, url = link.href
			;

			if ( 'undefined' !==  productInfo.article ) {
				_gaq.push(['_trackEvent', 'YM_link', productInfo.article]);
				e.preventDefault();
				if ( 'undefined' !== link ) {
					setTimeout(function () {
						//document.location.href = url; // не подходит, нужно в новом окне открывать
						link.click(); // эмулируем клик по ссылке
					}, 500);
				}
			}
		};
	// end of functions and vars
	
	if ( !$('#jsProductCard').length ) {
		return false;
	}

	if ( typeof _kmq !== 'undefined' ) {
		_kmq.push(['record', 'Viewed Product', toKISS]);
	}
	if ( typeof _gaq !== 'undefined' ) {
		// GoogleAnalitycs for review click
		$( 'a.reviewLink.yandex' ).each(function() {
			$(this).one( "click", reviewsYandexClick); // переопределяем только первый клик
		});
	}
})();


/**
 * Аналитика для слайдеров
 */
(function() {
		/**
		 * Аналитика по типу слайдера
		 *
		 * @param	{Object}	productData	Данные о продукте на который произошел клик
		 */
	var trackAs = {
			accessorize: function( productData ) {
				console.log(productData);

				var toKISS = {
					'Recommended Item Clicked Accessorize Recommendation Place': 'product',
					'Recommended Item Clicked Accessorize Clicked SKU': productData.article,
					'Recommended Item Clicked Accessorize Clicked Product Name': productData.name,
					'Recommended Item Clicked Accessorize Product Position': productData.position
				};

				if ( typeof _kmq !== 'undefined' ) {
					_kmq.push(['record', 'Recommended Item Clicked Accessorize', toKISS]);
				}
			},

			alsoBought: function( productData ) {
				console.log(productData);

				var toKISS = {
					'Recommended Item Clicked Also Bought Recommendation Place': 'product',
					'Recommended Item Clicked Also Bought Clicked SKU': productData.article,
					'Recommended Item Clicked Also Bought Clicked Product Name': productData.name,
					'Recommended Item Clicked Also Bought Product Position': productData.position
				};

				if ( typeof _kmq !== 'undefined' ) {
					_kmq.push(['record', 'Recommended Item Clicked Also Bought', toKISS]);
				}

			},

			alsoViewed: function( productData ) {
				console.log(productData);

				var toKISS = {
					'Recommended Item Clicked Also Viewed Recommendation Place': 'product',
					'Recommended Item Clicked Also Viewed Clicked SKU': productData.article,
					'Recommended Item Clicked Also Viewed Clicked Product Name': productData.name,
					'Recommended Item Clicked Also Viewed Product Position': productData.position
				};

				if ( typeof _kmq !== 'undefined' ) {
					_kmq.push(['record', 'Recommended Item Clicked Also Viewed', toKISS]);
				}
			}
		},

		sliderAnalytics = function sliderAnalytics() {
			console.info('click!');

			var sliderData = $(this).parents('.js-slider').data('slider'),
				sliderType = sliderData.type,

				productData = $(this).data('product');
			// end of vars
			
			console.log(sliderType);
			productData.position = $(this).index();

			if ( trackAs.hasOwnProperty(sliderType) ) {
				trackAs[sliderType](productData);
			}
		};
	// end of functions

	$('.js-slider').on('click', '.bSliderAction__eItem', sliderAnalytics);
}());
;(function() {
	// текущая страница для каждой вкладки
	var reviewCurrentPage = {
			user: -1,
			pro: -1
		},
		// количество страниц для каждой вкладки
		reviewPageCount = {
			user: 0,
			pro: 0
		},
		reviewsProductUi = null,
		reviewsType = null,
		reviewsContainerClass = null,

		//nodes
		moreReviewsButton = $('.jsGetReviews'),
		reviewTab = $('.bReviewsTabs__eTab'),
		reviewWrap = $('.bReviewsWrapper'),
		reviewContent = $('.bReviewsContent');
	// end of vars

	/**
	 * Получение отзывов
	 * @param	{String}	productId
	 * @param	{String}	type
	 * @param	{String}	containerClass
	 */
	var getReviews = function( productId, type, containerClass ) {
		var page = reviewCurrentPage[type] + 1,
			layout = false,
			url = '/product-reviews/'+productId,
			dataToSend;
		// end of vars
		
		var reviewsResponse = function reviewsResponse( data ) {
			$('.'+containerClass).html($('.'+containerClass).html() + data.content);
			reviewCurrentPage[type]++;
			reviewPageCount[type] = data.pageCount;

			if ( reviewCurrentPage[type] + 1 >= reviewPageCount[type] ) {
				moreReviewsButton.hide();
			}
			else {
				moreReviewsButton.show();
			}
		};
		// end of functions

		if ( $('body').hasClass('jewel') ) {
			layout = 'jewel';
		}

		dataToSend = {
			page: page,
			type: type,
			layout: layout
		};

		$.ajax({
			type: 'GET',
			data: dataToSend,
			url: url,
			success: reviewsResponse
		});
	};

	// карточка товара - отзывы - переключение по табам
	if ( reviewTab.length ) {
		// начальная инициализация
		var initialType = reviewWrap.attr('data-reviews-type');

		reviewCurrentPage[initialType]++;
		reviewPageCount[initialType] = reviewWrap.attr('data-page-count');

		if ( reviewPageCount[initialType] > 1 ) {
			moreReviewsButton.show();
		}

		reviewsProductUi = reviewWrap.attr('data-product-ui');
		reviewsType = reviewWrap.attr('data-reviews-type');
		reviewsContainerClass = reviewWrap.attr('data-container');

		reviewTab.click(function() {
			reviewsContainerClass = $(this).attr('data-container');

			if ( reviewsContainerClass === undefined ) {
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
			}

			if ( !$('.'+reviewsContainerClass).html() ) {
				getReviews(reviewsProductUi, reviewsType, reviewsContainerClass);
			}
			else {
				// проверяем что делать с кнопкой "показать еще" - скрыть/показать
				if ( reviewCurrentPage[reviewsType] + 1 >= reviewPageCount[reviewsType] ) {
					moreReviewsButton.hide();
				}
				else {
					moreReviewsButton.show();
				}
			}
		});

		moreReviewsButton.click(function() {
			getReviews(reviewsProductUi, reviewsType, reviewsContainerClass);
		});
	}

//	var leaveReview = function() {
//		if ( !$('#jsProductCard').length ) {
//			return false;
//		}
//
//		var productInfo = $('#jsProductCard').data('value'),
//			pid = $(this).data('pid'),
//			name = productInfo.name,
//			src = 'http://reviews.testfreaks.com/reviews/new?client_id=enter.ru&' + $.param({key: pid, name: name});
//		// end of vars
//
//		$('.reviewPopup').lightbox_me({
//			onLoad: function() {
//				$('#rframe').attr('src', src);
//			}
//		});
//
//		return false;
//	};
//
//	$('.jsLeaveReview').on('click', leaveReview);

}());



/**
 * Обработчик для формы "Отзыв о товаре"
 *
 * @author		Shaposhnik Vitaly
 */
(function() {
	var body = $('body'),
		reviewPopup = $('.jsReviewPopup'),
		form = reviewPopup.find('.jsReviewForm'),
		submitReviewButton = $('.jsFormSubmit'),
		submitReviewButtonText = submitReviewButton.val(),

		reviewStar = form.find('.starsList__item'),
		reviewStarCount = form.find('.jsReviewStarsCount'),
		starStateClass = {
			fill: 'mFill',
			empty: 'mEmpty'
		},

		advantageField = $('.jsAdvantage'),
		disadvantageField = $('.jsDisadvantage'),
		extractField = $('.jsExtract'),
		authorNameField = $('.jsAuthorName'),
		authorEmailField = $('.jsAuthorEmail'),

		/**
		 * Конфигурация валидатора для формы "Отзыв о товаре"
		 *
		 * @type {Object}
		 */
		validationConfig = {
			fields: [
				{
					fieldNode: advantageField,
					require: true,
					customErr: 'Не указаны достоинства'
				},
				{
					fieldNode: disadvantageField,
					require: true,
					customErr: 'Не указаны недостатки'
				},
				{
					fieldNode: extractField,
					require: true,
					customErr: 'Не указан комментарий'
				},
				{
					fieldNode: authorNameField,
					require: true,
					customErr: 'Не указано имя'
				},
				{
					fieldNode: authorEmailField,
					require: true,
					customErr: 'Не указан e-mail',
					validBy: 'isEmail'
				}
			]
		},
		validator = new FormValidator(validationConfig);
	//end of vars

	var 
		/**
		 * Открытие окна с отзывами
		 */
		openPopup = function openPopup() {
			reviewPopup.lightbox_me({
				centered: true,
				autofocus: true,
				onLoad: function() {}
			});

			return false;
		},

		/**
		 * Обработка ошибок формы
		 *
		 * @param   {Object}    formError   Объект с полем содержащим ошибки
		 */
		formErrorHandler = function formErrorHandler( formError ) {
			var field = $('[name="review[' + formError.field + ']"]');
			// end of vars

			var clearError = function clearError() {
				validator._unmarkFieldError($(this));
			};
			// end of functions

			console.warn('Ошибка в поле');

			validator._markFieldError(field, formError.message);
			field.bind('focus', clearError);

			return false;
		},

		/**
		 * Показ глобальных сообщений об ошибках
		 *
		 * @param   {String}    msg     Сообщение которое необходимо показать пользователю
		 */
		showError = function showError( msg ) {
			var error = $('ul.error_list', form);
			// end of vars

			if ( error.length ) {
				error.html('<li>' + msg + '</li>');
			}
			else {
				$('.bFormLogin__ePlaceTitle', form).after($('<ul class="error_list" />').append('<li>' + msg + '</li>'));
				$( form ).prepend( $('<ul class="error_list" />').append('<li>' + msg + '</li>') );
			}

			return false;
		},

		/**
		 * Обработка ошибок из ответа сервера
		 *
		 * @param {Object} res Ответ сервера
		 */
		serverErrorHandler = function serverErrorHandler( res ) {
			var formError = null;
			// end of vars

			console.warn('Обработка ошибок формы');

			for ( var i = res.form.error.length - 1; i >= 0; i-- ) {
				formError = res.form.error[i];
				console.warn(formError);

				if ( formError.field !== 'global' && formError.message !== null ) {
					formErrorHandler(formError);
				}
				else if ( formError.field === 'global' && formError.message !== null ) {
					showError(formError.message);
				}
			}

			return false;
		},

		/**
		 * Обработчик ответа от сервера
		 *
		 * @param	{Object}	response	Ответ сервера
		 */
		responseFromServer = function responseFromServer( response ) {
			console.log('Ответ от сервера');

			if ( response.error ) {
				console.warn('Form has error');
				serverErrorHandler(response);

				return false;
			}

			if ( response.success ) {
				if (response.notice.message) {
					form.before(response.notice.message);
				}
				form.hide();
			}

			return false;
		},

		/**
		 * Сабмит формы "Отзыв о товаре"
		 */
		formSubmit = function formSubmit() {
			if (form.data('disabled')) {
				return false;
			}

			form.data('disabled', true);
			submitReviewButton.attr('disabled', 'disabled');
			submitReviewButton.addClass('mDisabled');
			submitReviewButton.val('Сохраняю…');

			// очищаем блок с глобальными ошибками
			if ( $('ul.error_list', form).length ) {
				$('ul.error_list', form).html('');
			}

			function enableSubmitReviewButton() {
				form.data('disabled', false);
				submitReviewButton.removeAttr('disabled');
				submitReviewButton.removeClass('mDisabled');
				submitReviewButton.val(submitReviewButtonText);
			}

			validator.validate({
				onInvalid: function( err ) {
					console.warn('invalid');
					console.log(err);

					enableSubmitReviewButton();
				},
				onValid: function() {
					$.ajax({
						type: 'post',
						url: form.attr('action'),
						data: form.serializeArray(),
						dataType: 'json',
						success: responseFromServer,
						complete: function() {
							enableSubmitReviewButton();
						}
					});
					console.log('Сабмит формы "Отзыв о товаре"');

					return false;
				}
			});

			return false;
		},

		/**
		 * Закрашивание необходимого количества звезд
		 * 
		 * @param	{Number}	count	Количество звезд которое необходимо закрасить
		 */
		fillStars = function fillStars( count ) {
			reviewStar.removeClass(starStateClass['fill']).removeClass(starStateClass['empty']);

			reviewStar.each(function( index ) {
				if ( index + 1 <= count ) {
					$(this).addClass(starStateClass['fill']);
				}
				else {
					$(this).addClass(starStateClass['empty']);
				}
			});
		},

		/**
		 * Наведение на звезду курсора
		 */
		hoverStar = function hoverStar() {
			var nowStar = $(this),
				starIndex = nowStar.index() + 1;
			// end of vars

			fillStars(starIndex);			
		},

		/**
		 * Событие сведения курсора со звезды
		 */
		unhoverStar = function unhoverStar() {
			var nowStarCount = reviewStarCount.val();
			// end of vars
			
			fillStars(nowStarCount);
		},

		/**
		 * Нажатие на звезду
		 */
		markStar = function markStar() {
			var nowStar = $(this),
				starIndex = nowStar.index() + 1;
			// end of vars
			
			reviewStarCount.val(starIndex);
			fillStars(starIndex);
		},

		/**
		 * Заполнение данных пользователя в форме (поля "Ваше имя" и "Ваш e-mail") и скрытие полей.
		 *
		 * @param  {Event} e
		 * @param  {Object} userInfo
		 */
		fillUserData = function fillUserData( e, userInfo ) {
			if ( userInfo ) {
				// если присутствует имя пользователя
				if ( userInfo.name ) {
					authorNameField.val(userInfo.name);
					authorNameField.parent('.jsPlace2Col').hide();
				}
				// если присутствует email пользователя
				if ( userInfo.email ) {
					authorEmailField.val(userInfo.email);
					authorEmailField.parent('.jsPlace2Col').hide();
				}
				// если присутствует и имя и email пользователя, то скрываем весь fieldset
				if ( userInfo.name && userInfo.email ) {
					authorNameField.parents('.jsFormFieldset').hide();
				}
			}
		};
	//end of functions


	body.on('click', '.jsReviewSend', openPopup);
	body.on('submit', '.jsReviewForm', formSubmit);
	body.on('userLogged', fillUserData);

	reviewStar.hover(hoverStar, unhoverStar);
	reviewStar.on('unhover', unhoverStar);
	reviewStar.on('click', markStar);

    if ('#add-review' == location.hash) {
        openPopup();
    }
}());

/**
 * Обработчик для sprosikupi
 */
$(function() {
	if (!$('#spk-widget-reviews').length) {
		return;
	}

    if ('#add-review' == location.hash) {
        location.href = '?spkPreState=addReview';
    }

	$('.sprosikupiRating .spk-good-rating a').live('click', function(e) {
		$(document).stop().scrollTo($(e.currentTarget).attr('href'), 800);
		return false;
	});

	$.getScript("//static.sprosikupi.ru/js/widget/sprosikupi.bootstrap.js");
});

/**
 * Обработчик для shoppilot
 */
$(function() {
	var reviewsContainer = $('#shoppilot-reviews-container');
	if (!reviewsContainer.length) {
		return;
	}

	_shoppilot = window._shoppilot || [];
	_shoppilot.push(['_setStoreId', '535a852cec8d830a890000a6']);
	_shoppilot.push(['_setOnReady', function (Shoppilot) {
		(new Shoppilot.ProductWidget({
			name: 'product-rating',
			styles: 'product-reviews',
			product_id: reviewsContainer.data('productId')
		})).appendTo('#shoppilot-rating-container');

		(new Shoppilot.ProductWidget({
			name: 'product-reviews',
			styles: 'product-reviews',
			product_id: reviewsContainer.data('productId')
		})).appendTo(reviewsContainer[0]);

        if ('#add-review' == location.hash) {
            (new Shoppilot.Surveybox({
                product_id: reviewsContainer.data('productId')
            })).open();
        }
	}]);

	(function() {
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.async = true;
		script.src = '//ugc.shoppilot.ru/javascripts/require.js';
		script.setAttribute('data-main',
			'//ugc.shoppilot.ru/javascripts/social-apps.js');
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(script, s);
	})();

    $('#shoppilot-rating-container').on('click', 'a.sp-product-inline-rating-label', function(e) {
		$(document).stop().scrollTo($(e.currentTarget).attr('href').replace(/^.*?#/, '#'), 800);
		return false;
	});
});
/**
 * Видео в карточке товара
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, jQuery.lightbox_me
 */
;(function() {
	var initVideo = function() {
		if ( !$('#productVideo').length ) {
			return false;
		}

		var videoStartTime = 0,
			videoEndTime = 0,
			productUrl = document.location.href,
			shield = $('.mVideo'),
			iframe = $('#productVideo .productVideo_iframe').html();
		// end of vars

		var openVideo = function() {
			$('#productVideo .productVideo_iframe').append(iframe);
			$('.productVideo_iframe iframe').attr('src', $('.productVideo_iframe iframe').attr('src')+'?autoplay=1');
			$('#productVideo').lightbox_me({ 
				centered: true,
				onLoad: function() {
					videoStartTime = new Date().getTime();

					if (typeof(_gaq) !== 'undefined') {
						_gaq.push(['_trackEvent', 'Video', 'Play', productUrl]);
					}
				},
				onClose: function() {
					$('#productVideo .productVideo_iframe').empty();
					videoEndTime = new Date().getTime();
					var videoSpent = videoEndTime - videoStartTime;

					if ( typeof _gaq !== 'undefined' ) {
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
		if ( $('.mVideo').length ) {
			initVideo();
		}
	});
}());