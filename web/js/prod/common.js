/**
 *  Модель для точек самовывоза
 **/

;(function($, ko){

    var balloonTemplate =
        '<table class="pickup-list"><tbody><tr class="pickup-item clearfix" ><td class="pickup-item__logo">'+
        '<img src="{{ icon }}" class="pickup-item__img" >'+
        '<span class="pickup-item__name">{{ listName }}</span>'+
        '</td><td class="pickup-item__addr">'+
        '{{# subway }}' +
        '<div class="pickup-item__metro" style="background: {{ subway.line.color }};">'+
        '<div class="pickup-item__metro-inn">{{ subway.name }}</div></div>'+
        '{{/ subway }}'+
        '<div class="pickup-item__addr-name">{{ address }}</div>'+
        '<div class="pickup-item__time">{{ regtime }}</div></td>'+
        '<td class="pickup-item__info pickup-item__info--nobtn">'+
        '<div class="pickup-item__date" data-bind="text: humanNearestDay">{{ humanNearestDay }}</div>'+
        '<div class="pickup-item__price"><span >{{ humanCost }}</span> {{# showRubles }}<span class="rubl">p</span></div>{{/ showRubles }}'+
        '</td></tr></tbody></table>';

    ENTER.DeliveryPoints = function DeliveryPointsF (points, mapParam) {

        var self = this,
            pointsBounds,
            map = ENTER.OrderV3 ? ENTER.OrderV3.map : ENTER.OrderV31Click.map;

        if (mapParam) map = mapParam;

        self.searchInput = ko.observable();
        self.searchAutocompleteList = ko.observableArray();
        self.searchAutocompleteListVisible = ko.observable(false);
        self.enableAutocompleteListVisible = function(){self.searchAutocompleteListVisible(true)};
        self.disableAutocompleteListVisible = function(){self.searchAutocompleteListVisible(false)};
        self.limitedSearchInput = ko.computed(self.searchInput).extend({throttle: 500});
        self.limitedSearchInput.subscribe(function(text) {

            var extendValue = 0.5,
                extendedBounds = [[pointsBounds[0][0] - extendValue, pointsBounds[0][1] - extendValue],[pointsBounds[1][0] + extendValue, pointsBounds[1][1] + extendValue]];

            if (typeof window.ymaps == 'undefined' || text.length == 0) return;

            self.searchAutocompleteList.removeAll();
            self.searchAutocompleteListVisible(false);

            ymaps.geocode(text, { boundedBy: extendedBounds, strictBounds: true }).then(
                function(res){
                    res.geoObjects.each(function(obj){
                        self.searchAutocompleteList.push({
                            'name' : obj.properties.get('name') + ', ' + obj.properties.get('description'),
                            'bounds' : obj.geometry.getBounds()
                        })
                    });
                    self.searchAutocompleteListVisible(true);
                },
                function(err){
                    console.warn('Geocode error', err)
                }
            )
        });
        self.clearSearchInput = function(){
            self.searchInput('');
            self.searchAutocompleteList.removeAll();
        };
        self.setMapCenter = function(val) {
            map.setCenter(val.bounds[0], 14);
            self.searchAutocompleteListVisible(false);
        };

        /* Полный список точек */
        self.availablePoints = ko.observableArray([]);
        /* Список выбранных типов точек */
        self.choosenTokens = ko.observableArray([]);
        /* Список выбранной цены доставки */
        self.choosenCosts = ko.observableArray([]);
        /* Список выбранных дат */
        self.choosenDates = ko.observableArray([]);
        /* Координаты границ карты */
        self.latitudeMin = ko.observable();
        self.latitudeMax = ko.observable();
        self.longitudeMin = ko.observable();
        self.longitudeMax = ko.observable();

        /* Текст для дропдауна с точками самовывоза */
        self.pointsText = ko.computed(function(){
            switch (self.choosenTokens().length) {
                case 0:
                    return 'Все точки';
                case 1:
                    return $.grep(self.availablePoints(), function(point){ return self.choosenTokens()[0] == point['token'] })[0]['dropdownName'];
                case 2: case 3: case 4:
                    return self.choosenTokens().length + ' точки';
                default:
                    return self.choosenTokens().length + ' точек';
            }
        });

        /* Текст для дропдауна со стоимостью */
        self.costsText = ko.computed(function(){
            if (self.choosenCosts().length == 1) {
                return self.choosenCosts()[0] == 0 ? 'Бесплатно' : self.choosenCosts()[0] + '&nbsp;<span class="rubl">p</span>';
            }
            return 'Стоимость';
        });

        /* Текст для дропдауна с датой */
        self.datesText = ko.computed(function(){
            return self.choosenDates().length == 1
                ? $.grep(self.availablePoints(), function(point){ return self.choosenDates()[0] == point['nearestDay'] })[0]['humanNearestDay']
                : 'Дата';
        });

        /* Список точек с учетом фильтрации */
        self.points = ko.computed(function(){

            var tokens = self.choosenTokens(),
                costs = self.choosenCosts(),
                dates = self.choosenDates(),
                arr;

            /* Фильтруем */
            arr = $.grep( self.availablePoints(), function(point) {
                /* Если не попадает в список выбранных токенов */
                if (tokens.length && tokens.indexOf(point.token) == -1) return false;
                /* Если не попадает в список выбранной цены доставки */
                if (costs.length && costs.indexOf(point.cost) == -1) return false;
                /* Если не попадает в список выбранных дат */
                if (dates.length && dates.indexOf(point.nearestDay) == -1) return false;
                /* В итоге проверяем на попадание в видимые границы карты */
                return self.isPointInBounds(point);
            });

            /* Сортируем */
            return arr.sort(function(a,b) {
                // сначала дата
                if (a.nearestDay != b.nearestDay) {
                    return a.nearestDay < b.nearestDay ? -1 : 1;
                }
                // потом цена
                if (a.cost != b.cost ) {
                    return a.cost - b.cost;
                }
                // TODO сначала Enter, потом Связной, потом по алфавиту
                if (a.blockName != b.blockName) {
                    return 0;
                }
                return 0;
            })
        });

        /**
         * Функция определения нахождения точки в границах карты
         */
        self.isPointInBounds = function(point){
            return self.latitudeMin() < point.latitude && self.longitudeMin() < point.longitude && self.latitudeMax() > point.latitude && self.longitudeMax() > point.longitude;
        };

        /**
         * Отображаем на карте только те точки, которые были выбраны в первом дропдауне
         */
        self.choosenTokens.subscribe(function(arr){
            map.geoObjects.each(function(geoObject){
                if (arr.length == 0) {
                    geoObject.options.set('visible', true)
                } else {
                    geoObject.options.set('visible', $.inArray(geoObject.properties.get('enterToken'), arr) !== -1)
                }
            });
        });

        /* INIT */

        console.log('Init DeliveryPointsModel with ', {points: points, mapParam: mapParam});

        $.each(points, function(index, point) {
            self.availablePoints.push(point);
            if (typeof pointsBounds == 'undefined') pointsBounds = [[point.latitude, point.longitude], [point.latitude, point.longitude]];
            else {
                if (point.latitude < pointsBounds[0][0]) pointsBounds[0][0] = point.latitude;
                if (point.latitude > pointsBounds[1][0]) pointsBounds[1][0] = point.latitude;
                if (point.longitude < pointsBounds[0][1]) pointsBounds[0][1] = point.longitude;
                if (point.longitude > pointsBounds[1][1]) pointsBounds[1][1] = point.longitude;
            }
        });

        window.map = self;

        return self;

    };

    ENTER.Placemark = function(point, visible, buyButtonClass) {

        var visibility = typeof visible == 'undefined' ? true : visible,
            balloonContent, placemark;

        if (!buyButtonClass) buyButtonClass = 'jsChangePoint';

        // Для шаблона
        if (point.cost == 0) {
            point.humanCost = 'Бесплатно';
            point.showRubles = false;
        } else {
            point.humanCost = point.cost;
            point.showRubles = true;
        }

        if (!point.latitude || !point.longitude) throw 'Не указаны координаты точки';

        balloonContent = Mustache.render(balloonTemplate, point);

        // кнопка "Выбрать магазин"
        balloonContent += $('<button />', {
                'text':'Купить',
                'class': 'btn-type btn-type--buy ' + buyButtonClass,
                'style': 'display: block',
                'data-shop': point.id,
                'data-token': point.token,
                'data-blockname': point.orderToken
            }
        )[0].outerHTML;

        placemark = new ymaps.Placemark([point.latitude, point.longitude], {
            // balloonContentHeader: point.name,
            balloonContentBody: balloonContent,
            hintContent: point.name,
            enterToken: point.token // Дополняем собственными свойствами
        }, {
            balloonMaxWidth: 428,
            iconLayout: 'default#image',
            iconImageHref: point.marker.iconImageHref,
            iconImageSize: point.marker.iconImageSize,
            iconImageOffset: point.marker.iconImageOffset,
            visible: visibility
        });

        // Максимальная ширина балуна
        //placemark.balloon.set('maxWidth', 100);

        return placemark;
    };

})(jQuery, ko);

;$(function($){
	var $body = $(document.body),
		searchUrl = '/search/autocomplete',
		switchCookie = JSON.parse(docCookies.getItem('switch')),
		advSearchEnabled = switchCookie && switchCookie.adv_search == 'on';

	function SearchModel(){
		var self = this;
		self.searchInput = ko.observable('');
		self.searchFocus = ko.observable(false);
		self.searchResults = ko.observableArray();

		self.advancedSearch = ko.observable(advSearchEnabled);
		self.searchCategoryVisible = ko.observable(false);
		self.currentCategory = ko.observable(null);
		self.previousCategory = ko.observable(null);

		self.searchResultCategories = ko.observableArray();
		self.searchResultProducts = ko.observableArray();

		self.isNoSearchResult = ko.computed(function(){
			return self.searchResultCategories().length == 0 && self.searchResultProducts().length == 0
		});

		self.toggleCategoryVisibility = function(){
			self.searchCategoryVisible(!self.searchCategoryVisible());
		};

		self.searchResultNavigation = function(data, e) {
			var keycode = e.which,
				$links = $('.jsSearchbarResults a'),
				activeClass = 'searchdd_lk_iact',
				index = $links.index($links.filter('.'+activeClass));

            console.log(keycode, index);

			if (!self.isNoSearchResult()) {
				$links.removeClass(activeClass);
				switch (keycode) {
					case 13: // Enter key
						if (index > -1) {
							window.location.href = $links.eq(index).attr('href');
							return false;
						}
						break;
					case 38: // up key
                        if (index == -1) index = self.searchResults.length;
						$links.eq(index - 1).addClass(activeClass);
						break;
					case 40: // down key
						$links.eq(index + 1).addClass(activeClass);
						break
				}
			}

			return true;
		};

		self.categoryClick = function(data, event){
			var category = $(event.target).data('value');
			self.currentCategory(category);
			self.toggleCategoryVisibility();
		};
		self.categoryReset = function(){
			self.currentCategory(null);
			self.toggleCategoryVisibility();
		};

		// задержка для скрытия результатов поиска
		self.searchResultsVisibility = ko.computed(function(){
			return self.searchFocus()
		}).extend({throttle: 200});

		// Throttled ajax query
		ko.computed(function(){
			var val = self.searchInput();
			var params = {q: val, sender: 'knockout'};

			if (self.currentCategory() != null) params.catId = self.currentCategory().id;

			if (val.length < 3) return;

			// assuming jQuery
			$.get(searchUrl, params)
				.done(function (data) {
					self.searchResultCategories(data.result.categories);
					self.searchResultProducts(data.result.products);
				})
				.fail(function () {
					console.error("could not retrieve value from server");
				});
		}).extend({ throttle: 200 });

		// АНАЛИТИКА
		// Предыдущее значение category
		self.currentCategory.subscribe(function(val){
			self.previousCategory(val);
		}, self, 'beforeChange');

		self.currentCategory.subscribe(function(val){
			var previous = self.previousCategory() === null ? '' : self.previousCategory().name;

			if (val == null) {
				$body.trigger('trackGoogleEvent',['search_scope', 'clear', previous])
			} else {
				if (self.previousCategory() == null) {
					$body.trigger('trackGoogleEvent',['search_scope', 'change', val.name + '_' + 'Все товары'])
				} else {
					$body.trigger('trackGoogleEvent',['search_scope', 'change', val.name + '_' + previous])
				}
			}
		});

		return self;
	}

	// Биндинги на нужные элементы
	$body.find('.jsKnockoutSearch').each(function(){
		ko.applyBindings(new SearchModel(), this);
	});

	// Аналитика на фокусе строки поиска
	$body.on('focus', '.jsSearchInput', function(){
		$body.trigger('trackGoogleEvent',['search_string', 'string'])
	});

	// Клик по категории в подсказке
	$body.on('click', '.jsSearchSuggestCategory', function(e){
		e.preventDefault();
		$body.trigger('trackGoogleEvent', [{
			category: 'search_string',
			action: 'suggest',
			label: 'category',
			hitCallback: $(this).attr('href')
		}])
	});

	// Клик по продукте в подсказке
	$body.on('click', '.jsSearchSuggestProduct', function(e){
		e.preventDefault();
		$body.trigger('trackGoogleEvent', [{
			category: 'search_string',
			action: 'suggest',
			label: 'item',
			hitCallback: $(this).attr('href')
		}])
	});

	// Клик по значку Enterprize в строке поиска
	$body.on('click', '.jsEnterprizeInSearchBarButton', function(e){
		e.preventDefault();
		$body.trigger('trackGoogleEvent', [{
			category: 'search_enterprize',
			action: 'click',
			hitCallback: $(this).attr('href')
		}])
	});

	// Клик по значку "Выбери подарки" в строке поиска
	$body.on('click', '.jsGiftInSearchBarButton', function(e){
		e.preventDefault();
		$body.trigger('trackGoogleEvent', [{
			category: 'search_present',
			action: 'click',
			hitCallback: $(this).attr('href')
		}])
	});

}(jQuery));

;(function($) {
	ko.bindingHandlers.buyButtonBinding = {
		update: function(element, valueAccessor) {
			var cart = ko.unwrap(valueAccessor()),
				$elem = $(element),
				productId = $elem.data('product-id'),
				inShopStockOnly = $elem.data('in-shop-stock-only'),
				inShopShowroomOnly = $elem.data('in-shop-showroom-only'),
				isBuyable = $elem.data('is-buyable'),
				statusId = $elem.data('status-id'),
                noUpdate = $elem.data('noUpdate'),
				buyUrl = $elem.data('buy-url'),
				isSlot = $elem.data('is-slot'),
				colorClass = $elem.data('color-class') || '',
                sender = $elem.data('sender') || {}
            ;

			if (typeof isBuyable != 'undefined' && !isBuyable) {
				$elem
					.text(typeof inShopShowroomOnly != 'undefined' && inShopShowroomOnly ? 'На витрине' : 'Нет')
					.addClass('mDisabled')
					.removeClass('mShopsOnly')
					.removeClass('mBought')
					.addClass('js-orderButton jsBuyButton')
					.attr('href', '#');
			} else if (typeof statusId != 'undefined' && 5 == statusId) { // SITE-2924
				$elem
					.text('Нет')
					.addClass('mDisabled')
					.removeClass('mShopsOnly')
					.removeClass('mBought')
					.addClass('js-orderButton jsBuyButton')
					.attr('href', '#');
			} else if (typeof isSlot != 'undefined' && isSlot) {
				$elem
					.text('Как купить?')
					.removeClass('mDisabled')
					.removeClass('mShopsOnly')
					.removeClass('mBought')
					.addClass('btn btn--slot js-orderButton js-slotButton')
					.attr('href', '#');
			} else if (typeof inShopStockOnly != 'undefined' && inShopStockOnly && ENTER.config.pageConfig.user.region.forceDefaultBuy) { // Резерв товара
				$elem
					.text('Купить')
					.removeClass('mDisabled')
					.removeClass('mShopsOnly')
					.removeClass('mBought')
					.addClass('js-orderButton jsOneClickButton-new')
					.addClass(colorClass)
					.removeClass('jsBuyButton')
					.attr('href', ENTER.utils.generateUrl('cart.oneClick.product.set', $.extend({productId: productId}, sender)));
			} else if (ENTER.utils.getObjectWithElement(cart, 'id', productId) && !noUpdate) {
				$elem
					.text('В корзине')
					.removeClass('mDisabled')
					.removeClass('mShopsOnly')
					.addClass('mBought')
					.removeClass('js-orderButton')
					.removeClass('jsBuyButton')
					.attr('href', ENTER.utils.generateUrl('cart'));
			} else if ($elem.hasClass('mBought')) {
				$elem
					.text('Купить')
					.removeClass('mDisabled')
					.removeClass('mShopsOnly')
					.removeClass('mBought')
					.addClass('js-orderButton jsBuyButton')
					.addClass(colorClass)
					.attr('href', buyUrl ? buyUrl : ENTER.utils.generateUrl('cart.product.set', $.extend({productId: productId}, sender)));
			}
		}
	};

	ko.bindingHandlers.buySpinnerBinding = {
		update: function(element, valueAccessor) {
			var cart = ko.unwrap(valueAccessor()),
				$elem = $(element);
			
			$elem.removeClass('mDisabled').find('input').attr('disabled', false);
			$.each(cart, function(key, value){
				if (this.id == $elem.data('product-id')) {
					$elem.addClass('mDisabled');
					$elem.find('input').val(value.quantity()).attr('disabled', true);
				}
			})
		}
	};

	ko.bindingHandlers.compareButtonBinding = {
		update: function(element, valueAccessor) {
			var compare = ko.unwrap(valueAccessor()),
				$elem = $(element),
				productId = $elem.data('id'),
				typeId = $elem.data('type-id'),
                activeLinkClass = 'btnCmpr_lk-act',
                buttonText = 'Добавить к сравнению',
				comparableProducts;

			var location = '';
			if (ENTER.config.pageConfig.location.indexOf('listing') != -1) {
				location = 'listing';
			} else if (ENTER.config.pageConfig.location.indexOf('product') != -1) {
				location = 'product';
			}

            if (ENTER.config.pageConfig.newProductPage) {
                activeLinkClass = 'product-card-tools__lk--active';
                buttonText = 'Сравнить';
            }
			
			if (ENTER.utils.getObjectWithElement(compare, 'id', productId)) {
				$elem
					.addClass('btnCmpr-act')
					.find('.jsCompareLink').addClass(activeLinkClass).attr('href', ENTER.utils.generateUrl('compare.delete', {productId: productId}))
					.find('span').text('Убрать из сравнения');
			} else {
				$elem
					.removeClass('btnCmpr-act')
					.find('.jsCompareLink').removeClass(activeLinkClass).attr('href', ENTER.utils.generateUrl('compare.add', {productId: productId, location: location}))
					.find('span').text(buttonText);
			}
	
			// массив продуктов, которые можно сравнить с данным продуктом
			comparableProducts = $.grep(compare, function(val){ return typeId == val.typeId; });
	
			if (comparableProducts.length > 1) {
				$elem.find('.btnCmpr_more').show().find('.btnCmpr_more_qn').text(comparableProducts.length);
			} else {
				$elem.find('.btnCmpr_more').hide();
			}
		}
	};
	
	ko.bindingHandlers.compareListBinding = {
		update: function(element, valueAccessor) {
			var compare = ko.unwrap(valueAccessor()),
				$elem = $(element),
				productId = $elem.data('id');

			var location = '';
			if (ENTER.config.pageConfig.location.indexOf('listing') != -1) {
				location = 'listing';
			} else if (ENTER.config.pageConfig.location.indexOf('product') != -1) {
				location = 'product';
			}

			if (ENTER.utils.getObjectWithElement(compare, 'id', productId)) {
				$elem.addClass('btnCmprb-act').attr('href', ENTER.utils.generateUrl('compare.delete', {productId: productId}));
			} else {
				$elem.removeClass('btnCmprb-act').attr('href', ENTER.utils.generateUrl('compare.add', {productId: productId, location: location}));
			}
		}
	};
}(jQuery));
;$(function(){
	var $body = $(document.body),
		region = ENTER.config.pageConfig.user.region.name,
		userInfoURL = ENTER.config.pageConfig.userUrl.addParameterToUrl('ts', new Date().getTime() + Math.floor(Math.random() * 1000)),
		authorized_cookie = '_authorized',
		startTime, endTime, spendTime;

	/* Модель продукта в корзине */
	function createCartModel(cart) {
		var model = {};
		$.each(cart, function(key, value){
			model[key] = value;
		});

		model.quantity = ko.observable(cart.quantity);
		return model;
	}

	function createUserModel(){
		var model = {};

		model.name = ko.observable();
		model.firstName = ko.observable();
		model.lastName = ko.observable();
		model.link = ko.observable();
		model.isEnterprizeMember = ko.observable();
		/* была ли модель обновлена данными от /ajax/userinfo */
		/* чтобы предотвратить моргание элементов, видимость которых зависит от суммы корзины, например */
		model.isUpdated = ko.observable(false);

		model.cart = ko.observableArray();
		model.cartSum = ko.computed(function(){
			var sum = 0;
			$.each(model.cart(), function(i,val){ sum += val.price * val.quantity()});
			return sum;
		});
		model.compare = ko.observableArray();

		model.isProductInCompare = function(elem){
			console.log('isProductInCompare', elem);
			return $.grep(model.compare, function(val){return val.id == $(elem).data('id')}).length == 0
		};

		model.update = function(data) {
			if (data.user) {
				if (data.user.name) model.name(data.user.name);
				if (data.user.firstName) model.firstName(data.user.firstName);
				if (data.user.lastName) model.lastName(data.user.lastName);
				if (data.user.link) model.link(data.user.link);
				if (data.user.isEnterprizeMember) model.isEnterprizeMember(data.user.isEnterprizeMember);
			}
			if (data.cartProducts && $.isArray(data.cartProducts)) {
				$.each(data.cartProducts, function(i,val){ model.cart.unshift(createCartModel(val)) });
			}
			if (data.compare) {
				$.each(data.compare, function(i,val){ model.compare.push(val) })
			}
			model.isUpdated(true);
			$body.trigger('userModelUpdate')
		};

		/* Обновление количества продукта */
		model.productQuantityUpdate = function(product_id, count) {
			$.each(model.cart(), function(i,val){
				if (product_id == val.id) val.quantity(count)
			})
		};

		/* Удаление продукта по ID */
		model.removeProductByID = function(product_id) {
			model.cart.remove(function(item) { return item.id == product_id });
		};

		/* АБ-тест платного самовывоза */
		model.infoIconVisible = ko.observable(false);
		model.infoBlock_1Visible = ko.computed(function(){
			return ENTER.config.pageConfig.selfDeliveryTest && ENTER.config.pageConfig.selfDeliveryLimit > model.cartSum();
		});
		model.infoBlock_2Visible = ko.computed(function(){
			return ENTER.config.pageConfig.selfDeliveryTest && ENTER.config.pageConfig.selfDeliveryLimit <= model.cartSum() && docCookies.hasItem('enter_ab_self_delivery_view_info');
		});

		return model;
	}

	ENTER.UserModel = createUserModel();

	// Биндинги на нужные элементы
	// Топбар, кнопка Купить на странице продукта, листинги, слайдер аксессуаров
	$('.js-topbarfix, .js-topbarfixBuy, .js-WidgetBuy, .js-listing, .js-jewelListing, .js-gridListing, .js-lineListing, .js-slider, .jsKnockoutCart, .js-compareProduct').each(function(){
		ko.applyBindings(ENTER.UserModel, this);
	});

	// Обновление данных о пользователе и корзине
	$.ajax({
		url: userInfoURL,
		beforeSend: function(){
			startTime = new Date().getTime();
		},
		success: function(data){
			endTime = new Date().getTime();
			spendTime = endTime - startTime;
			ENTER.UserModel.update(data);
			if (typeof ga == 'function') {
				ga('send', 'timing', 'userInfo', 'Load User Info', spendTime);
				console.log('[Google Analytics] Send user/info timing: %s ms', spendTime)
			}

			ENTER.config.userInfo = data;

			if (!docCookies.hasItem(authorized_cookie)) {
				if (data && data.user && typeof data.user.id != 'undefined') {
					docCookies.setItem(authorized_cookie, 1, 60*60, '/'); // on
				} else {
					docCookies.setItem(authorized_cookie, 0, 60*60, '/'); // off
				}
			}

			$body.trigger('userLogged', [data]);
		}
	});

	$body.on('catalogLoadingComplete', function(){
		$('.js-listing, .js-jewelListing').each(function(){
			ko.cleanNode(this);
			ko.applyBindings(ENTER.UserModel, this);
		});
	});

	$body.on('addtocart', function(event, data) {
		if ( data.redirect ) {
			console.warn('redirect');
			document.location.href = data.redirect;
		} else {

			ENTER.UserModel.cart.removeAll();
			$.each(data.cart.products, function(key, value){
				ENTER.UserModel.cart.unshift(createCartModel(value));
			});
		}
	});

    // Удаление товара из корзины (RetailRocket, etc)
    $body.on('removeFromCart', function(e, product) {
        if (!product.id) return;
        console.info('RetailRocket removeFromCart id = %s', product.id);
        if (window.rrApiOnReady) window.rrApiOnReady.push(function(){ window.rrApi.removeFromBasket(product.id) });
    });

	/* SITE-4472 Аналитика по АБ-тесту платного самовывоза и рекомендаций из корзины */
	$body.on('mouseover', '.btnBuy-inf', function(){
		if (!docCookies.hasItem('enter_ab_self_delivery_view_info')) {
			docCookies.setItem('enter_ab_self_delivery_view_info', true);
			if (ENTER.UserModel.cartSum() < ENTER.config.pageConfig.selfDeliveryLimit) $body.trigger('trackGoogleEvent', ['Платный_самовывоз_' + region, 'увидел всплывашку платный самовывоз', 'всплывающая корзина']);
			if (ENTER.UserModel.cartSum() >= ENTER.config.pageConfig.selfDeliveryLimit) $body.trigger('trackGoogleEvent', ['Платный_самовывоз_' + region, 'самовывоз бесплатно', 'всплывающая корзина']);
		}
		ENTER.UserModel.infoIconVisible(false);
	});

	$body.on('showUserCart', function(e){
		var $target = $(e.target);

		if (ENTER.config.pageConfig.selfDeliveryTest && ENTER.UserModel.infoIconVisible()) $body.trigger('trackGoogleEvent', ['Платный_самовывоз_' + region, 'увидел подсказку', 'всплывающая корзина']);
		else if (ENTER.config.pageConfig.selfDeliveryTest && !ENTER.UserModel.infoIconVisible()) $body.trigger('trackGoogleEvent', ['Платный_самовывоз_' + region, 'не увидел подсказку', 'всплывающая корзина']);

		/* Если человек еще не наводил на иконку в всплывающей корзине */
		if (ENTER.config.pageConfig.selfDeliveryTest) {
			if (!docCookies.hasItem('enter_ab_self_delivery_view_info') && ENTER.UserModel.cartSum() < ENTER.config.pageConfig.selfDeliveryLimit) {
				ENTER.UserModel.infoIconVisible(true);
			}
		}

		if (ENTER.config.pageConfig.selfDeliveryTest && ENTER.UserModel.infoBlock_2Visible() && !ENTER.UserModel.infoIconVisible()) {
			$body.trigger('trackGoogleEvent', ['Платный_самовывоз_' + region, 'самовывоз бесплатно', 'всплывающая корзина']);
		}
	});

	$body.on('userModelUpdate', function(e) {
		if (ENTER.config.pageConfig.selfDeliveryTest) {
			if (!docCookies.hasItem('enter_ab_self_delivery_view_info') && ENTER.UserModel.cartSum() < ENTER.config.pageConfig.selfDeliveryLimit) {
				ENTER.UserModel.infoIconVisible(true);
			}
		}
	});

	$body.on('click', '.jsAbSelfDeliveryLink', function(e){
		var href = e.target.href;
		if (href) {
			e.preventDefault();
			$body.trigger('trackGoogleEvent',
				{	category: 'Платный_самовывоз_' + region,
					action:'добрать товар',
					label:'всплывающая корзина',
					hitCallback: function(){
						window.location.href = href;
					}
				})
		}
	});

});

;(function (window, document, $, ENTER) {
	
	/**
	 * Общие настройки AJAX
	 *
	 * @requires	jQuery, ENTER.utils.logError
	 */
	$.ajaxSetup({
		timeout: 20000,
		statusCode: {
			404: function() { 
				var ajaxUrl = this.url,
					data = {
						event: 'ajax_error',
						type: '404 ошибка',
						ajaxUrl: ajaxUrl
					};
				// end of vars

				if ( typeof _gaq !== 'undefined' ) {
					_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '404 ошибка, страница не найдена']);
				}
			},
			401: function() {
				if ( $('#auth-block').length ) {
					$('#auth-block').lightbox_me({
						centered: true,
						onLoad: function() {
							$('#auth-block').find('input:first').focus();
						}
					});
				}
				else {
					if ( typeof _gaq !== 'undefined' ) {
						_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '401 ошибка, авторизуйтесь заново']);
					}
				}
					
			},
			500: function() {
				var ajaxUrl = this.url,
					data = {
						event: 'ajax_error',
						type: '500 ошибка',
						ajaxUrl: ajaxUrl
					};
				// end of vars

				if ( typeof _gaq !== 'undefined' ) {
					_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '500 сервер перегружен']);
				}
			},
			503: function() {
				var ajaxUrl = this.url,
					data = {
						event: 'ajax_error',
						type: '503 ошибка',
						ajaxUrl: ajaxUrl
					};
				// end of vars

				if ( typeof _gaq !== 'undefined' ) {
					_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '503 ошибка, сервер перегружен']);
				}
			},
			504: function() {
				var ajaxUrl = this.url,
					data = {
						event: 'ajax_error',
						type: '504 ошибка',
						ajaxUrl: ajaxUrl
					};
				// end of vars

				if ( typeof _gaq !== 'undefined' ) {
					_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', '504 ошибка, проверьте соединение с интернетом']);
				}
			}
		},
		error: function ( jqXHR, textStatus, errorThrown ) {
			var ajaxUrl = this.url,
				data = {
					event: 'ajax_error',
					type: 'неизвестная ajax ошибка',
					ajaxUrl: ajaxUrl
				};
			// end of vars
			
			if ( jqXHR.statusText === 'error' ) {

				if ( typeof _gaq !== 'undefined' ) {
					_gaq.push(['_trackEvent', 'Errors', 'Ajax Errors', 'неизвестная ajax ошибка']);
				}
			}
			else if ( textStatus === 'timeout' ) {
				return;
			}
		}
	});
}(this, this.document, this.jQuery, this.ENTER));
;(function($) {

    var body = $(document.body),
        ga = this.ga,       // Universal
        _gaq = this._gaq,   // Classic

        isUniversalAvailable = function isUniversalAvailableF (){
            return typeof ga === 'function' && typeof ga.getAll == 'function' && ga.getAll().length != 0;
        },
        isClassicAvailable = function isClassicAvailableF() {
            return typeof _gaq === 'object';
        },
        /**
         * Логирование просмотра страницы в Google Analytics (Classical + Universal)
         * @link 'https://developers.google.com/analytics/devguides/collection/analyticsjs/pages'
         * @link 'https://developers.google.com/analytics/devguides/collection/gajs/methods/gaJSApiBasicConfiguration#_gat.GA_Tracker_._trackPageview'
         * @param jQueryEvent event, который автоматически передается от jQuery.trigger()
         * @param eventObject Параметры в следующем порядке: 'page', 'title'
         */
        trackGooglePageview = function trackGooglePageView (jQueryEvent, eventObject) {
            var data = {};
            if (arguments.length >= 2 && typeof eventObject == 'string') {
                data.page = arguments[1];
                if (typeof data.page == 'string' && data.page.substr(0,1) != '/') data.page = '/' + data.page;
                if (arguments[2]) data.title = arguments[2]
            }
            if (isUniversalAvailable()) {
                ga('send', 'pageview', data);
            }
            if (isClassicAvailable()) {
                _gaq.push(['_trackPageview', data.page])
            }
        },

        /**
         * Логирование события в Google Analytics (Classical + Universal)
         * @link 'https://developers.google.com/analytics/devguides/collection/analyticsjs/field-reference#events'
         * @param jQueryEvent event, который автоматически передается от jQuery.trigger()
         * @param eventObject либо объект, либо параметры в следующем порядке: 'category', 'action', 'label', 'value', 'nonInteraction', 'hitCallback'
         */
        trackGoogleEvent = function trackGoogleEventF (jQueryEvent, eventObject) {

            var e = {},
                universalEvent = { hitType: 'event' },
                classicEvent = ['_trackEvent'],
                props = ['category', 'action', 'label', 'value', 'nonInteraction', 'hitCallback'];

            // Формируем event
            if (arguments.length == 2 && typeof eventObject == 'object') {
                $.each(props, function(i,elem){
                    if (eventObject.hasOwnProperty(elem)) e[elem] = eventObject[elem];
                })
            } else if (arguments.length > 2 && typeof eventObject == 'string') {
                for (var i = 1, l = arguments.length; i < l; i++) {
                    e[props[i - 1]] = arguments[i];
                }
            }

            // Форматируем event
            $.each(props, function(i,elem){
                if (e.hasOwnProperty(elem)) {
                    switch (elem) {
                        case 'category':
                            e[elem] = e[elem].slice(0, 150);
                            break;
                        case 'action':
                        case 'label':
                            e[elem] = e[elem].slice(0, 500);
                            break;
                        case 'value':
                            e[elem] = parseInt(e[elem].slice, 10);
                            break;
                        case 'nonInteraction':
                            e[elem] = Boolean(e[elem]);
                            break;
                    }
                }
            });

            // Classic Tracking Code
            if (typeof _gaq === 'object') {
                classicEvent.push(e.category, e.action);
                classicEvent.push(e.label ? e.label: null);
                classicEvent.push(e.value ? e.value: null);
                if (e.nonInteraction) classicEvent.push(e.nonInteraction);
                _gaq.push(classicEvent);
            } else {
                console.warn('No Google Analytics object found')
            }

            // Universal Tracking Code
            // TODO refactor if statement
            if (typeof ga === 'function' && typeof ga.getAll == 'function' && ga.getAll().length != 0) {
                universalEvent.eventCategory = e.category;
                universalEvent.eventAction = e.action;
                if (e.label) universalEvent.eventLabel = e.label;
                if (e.value) universalEvent.eventValue = e.value;
                if (typeof e.hitCallback == 'function') universalEvent.hitCallback = e.hitCallback;
                else if (typeof e.hitCallback == 'string') universalEvent.hitCallback = function(){ window.location.href = e.hitCallback };
                if (e.nonInteraction) ga('set', 'nonInteraction', true);
                ga('send', universalEvent);
                console.info('[Google Analytics] Send event:', e);
            } else {
                console.warn('No Universal Google Analytics function found', typeof universalEvent.hitCallback, e.hitCallback);
                if (typeof e.hitCallback == 'function') e.hitCallback(); // если не удалось отправить, но callback необходим
                else if (typeof e.hitCallback == 'string') window.location.href = e.hitCallback;
            }

        },
        /**
         * Объект транзакции
         * @param data Object {id,affiliation,total,shipping,tax,city}
         * @returns Object
         * @constructor
         */
        GoogleTransaction = function GoogleTransactionF(data) {
            this.id = data.id ? data.id : false;
            this.affiliation = data.affiliation;
            this.total = data.total;
            this.shipping = data.shipping ? data.shipping : '0';
            this.tax = data.tax ? data.tax : '0';
            this.city = data.city ? data.city : '';

            // checking values
            if (!this.id) throw 'Некорректный ID транзакции';
            if (!this.total) throw 'Некорректная сумма заказа';

            this.toObject = function toObjectF() {
                return {
                    'id': this.id,
                    'affiliation': this.affiliation,
                    'revenue': this.total,
                    'shipping': this.shipping,
                    'tax': this.tax
                }
            };

            this.toArray = function toArrayF() {
                return [this.id, this.affiliation, this.total, this.tax, this.shipping, this.city]
            };

            return this;
        },
        /**
         * Объект продукта
         * @param data Object {id,name,category,sku,price,quantity}
         * @param transaction_id String
         * @returns Object
         * @constructor
         */
        GoogleProduct = function GoogleProductF(data, transaction_id) {

            this.id = transaction_id ? String(transaction_id) : '';
            this.name = data.name ? String(data.name) : '';
            this.category = data.category ? String(data.category) : '';
            this.sku = data.sku ? String(data.sku) : '';
            this.price = data.price ? String(data.price) : '';
            this.quantity = data.quantity ? String(data.quantity) : '';

            if (!this.id) throw 'Некорректный ID товара';
            if (!this.name) throw 'Некорректное название товара';
            if (!this.price) throw 'Некорректная цена товара';
            if (!this.quantity) throw 'Некорректное количество товара';

            this.toObject = function toObjectF(){
                return {
                    'id': this.id,
                    'name': this.name,
                    'sku': this.sku,
                    'category': this.category,
                    'price': this.price,
                    'quantity': this.quantity
                }
            };

            this.toArray = function toArrayF(){
                return [this.id, this.sku, this.name, this.category, this.price, this.quantity];
            };

            return this;
        },
        /**
         * Логирование транзакции в Google Analytics (Classical + Universal)
		 * Если в action передаётся несколько меток, то для удобства фильтрации по ним в аналитеке нужно заключать каждую метку в скобки, например: RR_покупка (marketplace)(gift)
         * @link 'https://developers.google.com/analytics/devguides/collection/analyticsjs/ecommerce'
         * @link 'https://developers.google.com/analytics/devguides/collection/gajs/gaTrackingEcommerce'
         * @param jQueryEvent event, который автоматически передается от jQuery.trigger()
         * @param eventObject '{ transaction: {}, products: [] }'
         */
        trackGoogleTransaction = function trackGoogleTransactionF (jQueryEvent, eventObject) {

            var googleTrans, googleProducts;

            try {

                googleTrans = new GoogleTransaction(eventObject.transaction);
                googleProducts = $.map(eventObject.products, function(elem){ return new GoogleProduct(elem, googleTrans.id)});

                // Classic Tracking Code
                if (typeof _gaq === 'object') {
                    _gaq.push(['_addTrans'].concat(googleTrans.toArray()));
                    $.each(googleProducts, function(i, product){
                        _gaq.push(['_addItem'].concat(product.toArray()))
                    });
                    _gaq.push(['_trackTrans']);
                } else {
                    console.warn('No Google Analytics object found')
                }

                // Universal Tracking Code
                if (typeof ga === 'function' && ga.getAll().length != 0) {
                    ga('require', 'ecommerce', 'ecommerce.js');
                    ga('ecommerce:addTransaction', googleTrans.toObject());
                    $.each(googleProducts, function(i, product){
                        ga('ecommerce:addItem',product.toObject())
                    });
                    ga('ecommerce:send');
                } else {
                    console.warn('No Universal Google Analytics function found');
                }

            } catch (exception) {
                console.error('[Google Analytics Ecommerce] %s', exception)
            }

		};

    if (typeof ga === 'undefined') ga = window[window['GoogleAnalyticsObject']]; // try to assign ga

    // common listener for triggering from another files or functions
    body.on('trackGooglePageview', trackGooglePageview);
    body.on('trackGoogleEvent', trackGoogleEvent);
    body.on('trackGoogleTransaction', trackGoogleTransaction);

    // TODO вынести инициализацию трекера из ports.js
    try {
        if (typeof ga === 'function' && typeof ga.getAll == 'function' && ga.getAll().length == 0) {
			console.warn('Creating ga tracker');
            ga( 'create', 'UA-25485956-5', 'enter.ru' );
        }
    } catch (e) {
        console.error(e);
    }

})(jQuery);
/**
 * Обработчик для личного кабинета
 *
 * @author    Trushkevich Anton
 * @requires  jQuery
 */
(function(){
  var checkedSms = false;
  var checkedEmail = false;

  var handleSubscribeSms = function() {
    if ( checkedSms ) {
      $('#mobilePhoneWrapper').hide();
      $('#mobilePhoneWrapper').parent().find('.red').html('');
      checkedSms = false;
    } else {
      $('#mobilePhoneWrapper').show();
      checkedSms = true;
    }
  };

  var handleSubscribeEmail = function() {
    if ( checkedEmail ) {
      $('#emailWrapper').hide();
      $('#emailWrapper').parent().find('.red').html('');
      checkedEmail = false;
    } else {
      $('#emailWrapper').show();
      checkedEmail = true;
    }
  };

  $(document).ready(function(){
    checkedSms = $('.smsCheckbox').hasClass('checked');
    if ( !$('#user_mobile_phone').val() ) {
      $('.smsCheckbox').bind('click', handleSubscribeSms);
    }
    checkedEmail = $('.emailCheckbox').hasClass('checked');
    if ( !$('#user_email').val() ) {
      $('.emailCheckbox').bind('click', handleSubscribeEmail);
    }
  });
}());



// (function(){
//   $(function(){
//     if($('.bCtg__eMore').length) {
//       var expanded = false;
//       $('.bCtg__eMore').click(function(){
//         if(expanded) {
//           $(this).siblings('.more_item').hide();
//           $(this).find('a').html('еще...');
//         } else {
//           $(this).siblings('.more_item').show();
//           $(this).find('a').html('скрыть');
//         }
//         expanded = !expanded;
//         return false;
//       });
//     }

//     /* Cards Carousel  */
//     function cardsCarouselTag ( nodes, noajax ) {
//       var current = 1;

//       var wi  = nodes.width*1;
//       var viswi = nodes.viswidth*1;

//       if( !isNaN($(nodes.times).html()) )
//         var max = $(nodes.times).html() * 1;
//       else
//         var max = Math.ceil(wi / viswi);

//       if((noajax !== undefined) && (noajax === true)) {
//         var buffer = 100;
//       } else {
//         var buffer = 2;
//       }

//       var ajaxflag = false;


//       var notify = function() {
//         $(nodes.crnt).html( current );
//         if(refresh_max_page) {
//           $(nodes.times).html( max );
//         }
//         if ( current == 1 )
//           $(nodes.prev).addClass('disabled');
//         else
//           $(nodes.prev).removeClass('disabled');
//         if ( current == max )
//           $(nodes.next).addClass('disabled');
//         else
//           $(nodes.next).removeClass('disabled');
//       }

//       var shiftme = function() {  
//         var boxes = $(nodes.wrap).find('.goodsbox')
//         $(boxes).hide()
//         var le = boxes.length
//         for(var j = (current - 1) * viswi ; j < current  * viswi ; j++) {
//           boxes.eq( j ).show()
//         }
//       }

//       $(nodes.next).bind('click', function() {
//         if( current < max && !ajaxflag ) {
//           if( current + 1 == max ) { //the last pull is loaded , so special shift

//             var boxes = $(nodes.wrap).find('.goodsbox')
//             $(boxes).hide()
//             var le = boxes.length
//             var rest = ( wi % viswi ) ?  wi % viswi  : viswi
//             for(var j = 1; j <= rest; j++)
//               boxes.eq( le - j ).show()
//             current++
//           } else {
//             if( current + 1 >= buffer ) { // we have to get new pull from server

//               $(nodes.next).css('opacity','0.4') // addClass dont work ((
//               ajaxflag = true
//               var getData = []
//               if( $('form.product_filter-block').length )
//                 getData = $('form.product_filter-block').serializeArray()
//               getData.push( {name: 'page', value: buffer+1 } )  
//               $.get( $(nodes.prev).attr('data-url') , getData, function(data) {
//                 buffer++
//                 $(nodes.next).css('opacity','1')
//                 ajaxflag = false
//                 var tr = $('<div>')
//                 $(tr).html( data )
//                 $(tr).find('.goodsbox').css('display','none')
//                 $(nodes.wrap).html( $(nodes.wrap).html() + tr.html() )
//                 tr = null
//               })
//               current++
//               shiftme()
//             } else { // we have new portion as already loaded one     
//               current++
//               shiftme() // TODO repair
//             }
//           }
//           notify()
//         }
//         return false
//       })

//       $(nodes.prev).click( function() {
//         if( current > 1 ) {
//           current--
//           shiftme()
//           notify()
//         }
//         return false
//       })

//       var refresh_max_page = false
//     } // cardsCarousel object

//     $('.carouseltitle').each( function(){
//       if($(this).find('.jshm').html()) {
//         var width = $(this).find('.jshm').html().replace(/\D/g,'');
//       } else {
//         var width = 3;
//       }
//       cardsCarouselTag({
//         'prev'  : $(this).find('.back'),
//         'next'  : $(this).find('.forvard'),
//         'crnt'  : $(this).find('.none'),
//         'times' : $(this).find('span:eq(1)'),
//         'width' : width,
//         'wrap'  : $(this).find('~ .carousel').first(),
//         'viswidth' : 3
//       });
//     })
//   });
// })();

/**
 * @author		Zaytsev Alexandr
 */
(function(ENTER) {
	var $body = $('body');

	// Обработчик для кнопок купить
	$body.on('click', '.jsBuyButton', function(e) {
		var $button = $(e.currentTarget);

        $body.trigger('TL_buyButton_clicked');

		if ( $button.hasClass('mDisabled') ) {
			//return false;
            e.preventDefault();
		}

		if ( $button.hasClass('mBought') ) {
			document.location.href($button.attr('href'));
			//return false;
            e.preventDefault();
		}

		$button.addClass('mLoading');

		// Добавление в корзину на сервере. Получение данных о покупке и состоянии корзины. Маркировка кнопок.
		$.ajax({
			url: $button.attr('href'),
			type: 'GET',
			success: function(data) {
				var
					upsale = $button.data('upsale') ? $button.data('upsale') : null,
					product = $button.parents('.jsSliderItem').data('product');

				if (!data.success) {
					return;
				}

				$button.removeClass('mLoading');

				if (data.product) {
					data.product.isUpsale = product && product.isUpsale ? true : false;
					data.product.fromUpsale = upsale && upsale.fromUpsale ? true : false;
				}

				data.location = $button.data('location');

				$body.trigger('addtocart', [data, upsale]);
			},
			error: function() {
				$button.removeClass('mLoading');
			}
		});

		//return false;
        e.preventDefault();
	});

	// analytics
	$body.on('addtocart', function(event, data){
		var
			/**
			 * Google Analytics аналитика добавления в корзину
			 */
				googleAnalytics = function googleAnalytics( event, data ) {
				var productData = data.product;

				var
					tchiboGA = function() {
						if (typeof window.ga === "undefined" || !productData.hasOwnProperty("isTchiboProduct") || !productData.isTchiboProduct) {
							return;
						}

						console.log("TchiboGA: tchiboTracker.send event Add2Basket product [%s, %s]", productData.name, productData.article);
						ga("tchiboTracker.send", "event", "Add2Basket", productData.name, productData.article);
					};
				// end of functions

				if ( !productData || typeof _gaq === 'undefined' ) {
					return;
				}

				tchiboGA();

				ENTER.utils.sendAdd2BasketGaEvent(productData.article, productData.price, productData.isOnlyFromPartner, productData.isSlot, data.sender ? data.sender.name : '');

				productData.isUpsale && _gaq.push(['_trackEvent', 'cart_recommendation', 'cart_rec_added_from_rec', productData.article]);
				productData.fromUpsale && _gaq.push(['_trackEvent', 'cart_recommendation', 'cart_rec_added_to_cart', productData.article]);

                try {
                    var sender = data.sender;
                    console.info({sender: sender});
                    if (sender && ('retailrocket' == sender.name)) {
						var rrEventLabel = '';
						if (ENTER.config.pageConfig.product) {
							if (ENTER.config.pageConfig.product.isSlot) {
								rrEventLabel = ' (marketplace-slot)';
							} else if (ENTER.config.pageConfig.product.isOnlyFromPartner) {
								rrEventLabel = ' (marketplace)';
							}
						}

                        $body.trigger('trackGoogleEvent',['RR_Взаимодействие' + rrEventLabel, 'Добавил в корзину', sender.position]);
                    }
                } catch (e) {
                    console.error(e);
                }
			},

			/**
			 * Обработчик добавления товаров в корзину. Рекомендации от RetailRocket
			 */
				addToRetailRocket = function addToRetailRocket( event, data ) {
				var product = data.product;


				if ( typeof rcApi === 'object' ) {
					try {
						rcApi.addToBasket(product.id);
					}
					catch ( err ) {}
				}
			};
		//end of functions

		try{
			if (data.product) {
				googleAnalytics(event, data);
				addToRetailRocket(event, data);
			}

			if (data.products) {
				console.groupCollapsed('Аналитика для набора продуктов');
				for (var i in data.products) {
					/* Google Analytics */
					googleAnalytics(event, $.extend({}, data, {product: data.products[i]}));
				}
				console.groupEnd();
			}
		}
		catch( e ) {
			console.warn('addtocartAnalytics error');
			console.log(e);
		}
	});
}(window.ENTER));

$(function() {
	var
		compareNoticeShowClass = 'topbarfix_cmpr_popup-show',
		$comparePopup,
		compareNoticeTimeout;

	$('body').on('click', '.jsCompareLink, .jsCompareListLink', function(e){
		var
			url = e.currentTarget.href,
			$button = $(e.currentTarget),
			productId = $button.data('id'),
			inCompare = $button.hasClass('btnCmprb-act'),
			isSlot = $button.data('is-slot'),
			isOnlyFromPartner = $button.data('is-only-from-partner');

		var location = '';
		if (ENTER.config.pageConfig.location.indexOf('listing') != -1) {
			location = 'listing';
		} else if (ENTER.config.pageConfig.location.indexOf('product') != -1) {
			location = 'product';
		}

		if ($(this).hasClass('jsCompareListLink')) {
			url = inCompare ? ENTER.utils.generateUrl('compare.delete', {productId: productId}) : ENTER.utils.generateUrl('compare.add', {productId: productId, location: location});
		}

		e.preventDefault();

		$.ajax({
			url: url,
			success: function(data) {
				if (data.compare) {
					ENTER.UserModel.compare.removeAll();
					$.each(data.compare, function(i,val){ ENTER.UserModel.compare.push(val) });

					if (!inCompare) {
						if (!$comparePopup) {
							var $userbar = ENTER.userBar.userBarFixed;
							$comparePopup = $('.js-compare-addPopup', $userbar);

							$('.js-compare-addPopup-closer', $comparePopup).click(function() {
								$comparePopup.removeClass(compareNoticeShowClass);
							});

							$('.js-topbarfixLogin, .js-topbarfixNotEmptyCart', $userbar).mouseover(function() {
								$comparePopup.removeClass(compareNoticeShowClass);
							});

							$('html').click(function() {
								$comparePopup.removeClass(compareNoticeShowClass);
							});

							$($comparePopup).click(function(e) {
								e.stopPropagation();
							});

							$(document).keyup(function(e) {
								if (e.keyCode == 27) {
									$comparePopup.removeClass(compareNoticeShowClass);
								}
							});
						}

						if (compareNoticeTimeout) {
							clearTimeout(compareNoticeTimeout);
						}

						compareNoticeTimeout = setTimeout(function() {
							$comparePopup.removeClass(compareNoticeShowClass);
						}, 2000);

						$('.js-compare-addPopup-image', $comparePopup).attr('src', data.product.imageUrl);
						$('.js-compare-addPopup-prefix', $comparePopup).text(data.product.prefix);
						$('.js-compare-addPopup-webName', $comparePopup).text(data.product.webName);

						ENTER.userBar.show(true, function(){
							$comparePopup.addClass(compareNoticeShowClass)
						});

						(function() {
							var action;
							if (isSlot) {
								action = 'marketplace-slot';
							} else if (isOnlyFromPartner) {
								action = 'marketplace';
							} else {
								action = 'enter';
							}

							if (location) {
								$('body').trigger('trackGoogleEvent', ['Compare_добавление', action, location]);
							}
						})();
					}
				}
			}
		})
	});
});
(function() {
	ENTER.counters = {
		callGetIntentCounter: function(data) {
			if (typeof __GetI === "undefined") {
				__GetI = [];
			}

			(function () {
				var p = {
					type: data.type,
					site_id: "267"
				};

				if (data.productId !== undefined) {
					p.product_id = data.productId;
				}

				if (data.productPrice !== undefined) {
					p.product_price = data.productPrice;
				}

				if (data.categoryId !== undefined) {
					p.category_id = data.categoryId;
				}

				if (data.orderId !== undefined) {
					p.transaction_id = data.orderId;
				}

				if (data.orderProducts !== undefined) {
					p.order = data.orderProducts;
				}

				if (data.orderRevenue !== undefined) {
					p.revenue = data.orderRevenue;
				}

				console.log('Вызов счётчика GetIntent', p);

				__GetI.push(p);
				var domain = (typeof __GetI_domain) == "undefined" ? "px.adhigh.net" : __GetI_domain;
				var src = ('https:' == document.location.protocol ? 'https://' : 'http://') + domain + '/p.js';
				var script = document.createElement( 'script' );
				script.type = 'text/javascript';
				script.src = src;
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(script, s);
			})();
		},

		callRetailRocketCounter: function(routeName, data) {
			var actions = {
				'product': function (data, userData) {
					console.info('RetailRocketJS product');

					var rcAsyncInit = function() {
						try {
							rcApi.view(data, userData.userId ? userData : undefined);
						}
						catch (err) {}
						console.log('Вызов счётчика RetailRocket', routeName, data, userData);
					};

					rrApiOnReady.push(rcAsyncInit);
				},

				'product.category': function (data, userData) {
					console.info('RetailRocketJS product.category');

					var rcAsyncInit = function() {
						try {
							rcApi.categoryView(data, userData.userId ? userData : undefined);
						}
						catch (err) {}
						console.log('Вызов счётчика RetailRocket', routeName, data, userData);
					};

					rrApiOnReady.push(rcAsyncInit);
				},

				'order.complete': function (data, userData) {
					console.info('RetailRocketJS order.complete');

					if (userData.userId) {
						data.userId = userData.userId;
						data.hasUserEmail = userData.hasUserEmail;
					}

					var rcAsyncInit = function() {
						try {
							rcApi.order(data);
						}
						catch (err) {}
						console.log('Вызов счётчика RetailRocket', routeName, data, userData);
					};

					rrApiOnReady.push(rcAsyncInit);
				}
			};

			function callCounter(userInfo) {
				try {
					console.info('RetailRocketJS action');

					if (userInfo && userInfo.id) {
						rrPartnerUserId = userInfo.id; // rrPartnerUserId — по ТЗ должна быть глобальной
					}

					if (actions.hasOwnProperty(routeName)) {
						var userData = {
							userId: userInfo ? userInfo.id || false : null,
							hasUserEmail: userInfo && userInfo.email ? true : false
						};

						actions[routeName](data, userData);
					}
				} catch (err) {}
			}

			if (ENTER.config.userInfo === false) {
				callCounter();
			} else if (!ENTER.config.userInfo) {
				setTimeout(function() {
					if (ENTER.config.userInfo) {
						callCounter(ENTER.config.userInfo.user);
					} else {
						setTimeout(arguments.callee, 100);
					}
				}, 100);
			} else {
				console.warn(ENTER.config.userInfo);
				callCounter(ENTER.config.userInfo.user);
			}
		}
	};
})();
/**
 * Custom inputs
 *
 * @requires jQuery
 *
 * @author	Zaytsev Alexandr
 */
;$(function() {
	var inputs = $('input.bCustomInput, .js-customInput'),
		body = $('body');
	// end of vars

	function updateInput($input) {
		if ( !$input.is('[type=checkbox]') && !$input.is('[type=radio]') ) {
			return;
		}

		var id = $input.attr('id'),
			type = ( $input.is('[type=checkbox]') ) ? 'checkbox' : 'radio',
			groupName = $input.attr('name') || '',
			label = $('label[for="'+id+'"]');
		// end of vars

		if (!label.length) {
			label = $input.closest('label');
		}

		if ( type === 'checkbox' ) {

			if ( $input.is(':checked') ) {
				label.addClass('mChecked');
			}
			else {
				label.removeClass('mChecked');
			}
		}


		if ( type === 'radio' ) {
			if ( $input.is(':checked') ) {
				$('input[name="'+groupName+'"]').each(function() {
					var currElement = $(this),
						currId = currElement.attr('id'),
						currLabel = $('label[for="'+currId+'"]');

					if (!currLabel.length) {
						currLabel = currElement.closest('label');
					}

					currLabel.removeClass('mChecked');
				});

				label.addClass('mChecked');
			} else {
				label.removeClass('mChecked');
			}
		}
	}


	body.on('change', '.bCustomInput, .js-customInput', function(e) {
		updateInput($(e.currentTarget));
	});

	inputs.each(function(index, input) {
		updateInput($(input));
	});
});
;$(function(){
    var
        $body = $('body')
    ;

    $('.jsEvent_documentReady').each(function(i, el) {
        var
            event = $(el).data('value')
        ;

        if (!event.name) return;
        
        $body.trigger(event.name, event.data || []);
        console.info('event', event.name, event.data);
    });
});
$(function() {
    $('body').on('click', '.jsFavoriteLink', function(e){
        var
            $el = $(e.currentTarget),
            xhr = $el.data('xhr')
        ;

        console.info({'.jsFavoriteLink click': $el});

        try {
            if (xhr)  xhr.abort();
        } catch (error) { console.error(error); }

        xhr = $.post($el.attr('href'))
            .done(function(response) {
                // TODO: добавить модификатор к кнопке
            })
            .always(function() {
                $el.data('xhr', null);
            })
        ;
        $el.data('xhr', xhr);

        e.preventDefault();
    });
});
/**
 * Перемотка к Id
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery
 */
(function() {
	var goToId = function goToId() {
		var to = $(this).data('goto');

		$(document).stop().scrollTo( $('#'+to), 800 );
		
		return false;
	};
	
	$(document).ready(function() {
		$('.jsGoToId').bind('click',goToId);
	});
}());
/**
 * Обработчик горячих ссылок
 *
 * @author    Trushkevich Anton
 * @requires  jQuery
 */
(function(){
  var handleHotLinksToggle = function() {
    var toggle = $(this);
    if(toggle.hasClass('expanded')) {
      toggle.parent().parent().find('.toHide').hide();
      toggle.html('Все метки');
      toggle.removeClass('expanded');
    } else {
      toggle.parent().parent().find('.toHide').show();
      toggle.html('Основные метки');
      toggle.addClass('expanded');
    }
    return false;
  };


  $(document).ready(function(){
    $('.hotlinksToggle').bind('click', handleHotLinksToggle);
  });
}());



/**
 * JIRA
 */
;(function() {
	$.ajax({
		url: 'https://jira.enter.ru/s/ru_RU-istibo/773/3/1.2.4/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector.js?collectorId=2e17c5d6',
		type: 'get',
		cache: true,
		dataType: 'script'
	});
	
	window.ATL_JQ_PAGE_PROPS = {
		'triggerFunction': function( showCollectorDialog ) {
			$('#jira').click(function( e ) {
				e.preventDefault();
				showCollectorDialog();
			});
		}
	};
}());
;$(function() {
	var
		$body = $('body'),
		isOpening = false;

	$body.on('click', '.js-kitButton', function(e) {
		e.preventDefault();

		if (isOpening) {
			return;
		}

		var $button = $(e.currentTarget);

		isOpening = true;
		$.ajax({
			url: ENTER.utils.generateUrl('product.kit', {productUi: $button.data('product-ui')}),
			type: 'POST',
			dataType: 'json',
			closeClick: false,
			success: function(result) {
				$('.bCountSection').goodsCounter('destroy');

				var $popup = $(Mustache.render($('#tpl-cart-kitForm').html()));

				$popup.lightbox_me({
					autofocus: true,
					destroyOnClose: true
				});

				ko.applyBindings(new PopupModel(result.product, $button.data('sender'), $button.data('sender2') || ''), $popup[0]);

				// Закрытие окна
				$body.one('addtocart', function(){
					$popup.trigger('close.lme');
				});

				// Google Analytics
				if (typeof _gaq !== 'undefined') {
					_gaq.push(['_trackEvent', 'addedCollection', 'collection', result.product.article]);
				}
			},
			complete: function() {
				isOpening = false;
			}
		});
	});

	function PopupModel(product, sender, sender2) {
		var self = this;

		self.productId = product.id;
		self.productPrefix = product.prefix;
		self.productWebname = product.webname;
		self.productImageUrl = product.imageUrl;
		self.products = ko.observableArray([]);

		self.isBaseKit = ko.computed(function(){
			var isEqual = true;
			ko.utils.arrayForEach(self.products(), function(item){
				if (product.kitProducts[item.id].count != item.count()) isEqual = false;
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

			link += $.param({sender: sender});

			if (sender2) {
				link += '&' + $.param({sender2: sender2});
			}

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
			self.populateWithObj(product.kitProducts);
		};

		self.populateWithObj(product.kitProducts);
	}

	function ProductModel(product) {
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
});
;$(document).ready(function(){
	// при любом клике на странице
	$(document.body).on('click', function(){
		var last_p = window.last_partner_second_click;
		// ставим куку last_partner на 30 дней, если есть переменная window.last_partner_second_click
		if (typeof last_p != 'undefined') {
			docCookies.setItem(
				'last_partner',
				last_p,
				60 * 60 *24 *30,
				'/'
			);
		}
	})
});
;(function( ENTER ) {
	function changeSocnetLinks(isSubscribe) {
		$('.js-registerForm-socnetLink').each(function(index, link) {
			var $link = $(link);
			$link.attr('href', ENTER.utils.setURLParam('subscribe', isSubscribe ? '1' : null, $link.attr('href')));
		});
	}

	var $subscribe = $('.js-registerForm-subscribe');
	changeSocnetLinks($subscribe.length && $subscribe[0].checked);

	var
		$authBlock = $('#auth-block'),

        init = function() {
			$('.js-registerForm-subscribe').change(function(e) {
				changeSocnetLinks(e.currentTarget.checked);
			});

            // изменение состояния блока авторизации
            $authBlock.on('changeState', function(e, state) {
                var
                    $el = $(this)
                ;

                console.info({'message': 'authBlock.changeState', 'state': state});

                if (state) {
                    var
                        oldClass = $el.attr('data-state') ? ('state_' + $el.attr('data-state')) : null,
                        newClass = 'state_' + state
                    ;

                    oldClass && $el.removeClass(oldClass);
                    $el.addClass(newClass);
                    $el.attr('data-state', state);
                }

                $('.js-resetForm, .js-authForm, .js-registerForm').trigger('clearError');
            });

            // клик по ссылкам
            $authBlock.find('.js-link').on('click', function(e) {
                var
                    $el = $(e.target),
                    $target = $($el.data('value').target),
                    state = $el.data('value').state
                ;

                console.info({'$target': $target, 'state': state});
                $target.trigger('changeState', [state]);
            });

            // формы
            $('.js-resetForm, .js-authForm, .js-registerForm')
                // отправка форм
                .on('submit', function(e) {
                    var
                        $el = $(e.target),
                        data = $el.serializeArray()
                    ;

                    $.post($el.attr('action'), data).done(function(response) {
						function getFieldValue(fieldName) {
							for (var i = 0; i < data.length; i++) {
								if (data[i]['name'] == fieldName) {
									return data[i]['value'];
								}
							}

							return null;
						}

						if ($el.hasClass('js-registerForm') && getFieldValue('subscribe') && typeof _gaq != 'undefined') {
							_gaq.push(['_trackEvent', 'subscription', 'subscribe_registration', getFieldValue('register[email]')]);
						}

                        if (response.data && response.data.link) {
                            window.location.href = response.data.link ? response.data.link : window.location.href;

                            return true;
                        }

                        $el.trigger('clearError');

                        var message = response.message;
                        if (!message && response.notice && response.notice.message) {
                            message = response.notice.message;
                        }

                        if (message) {
                            $el.find('.js-message').html(message);
                        }

                        response.form && response.form.error && $.each(response.form.error, function(i, error) {
                            console.warn(error);

                            $el.trigger('fieldError', [error]);
                        });
                    });

                    e.preventDefault();
                })

                .on('fieldError', function(e, error) {
                    var
                        $el = $(e.target),
                        $field = $el.find('[name*="' + error.field + '"]')
                    ;

                    if ($field.length) {
                        $field.prev('.js-fieldError').remove();
                        if (error.message) {
                            $field.before('<div class="js-fieldError bErrorText"><div class="bErrorText__eInner">' + error.message + '</div></div>');
                        }
                    }
                })

                // очистить ошибки
                .on('clearError', function() {
                    var $el = $(this);

                    $el.find('.js-message').html('');

                    $el.find('input').each(function(i, el) {
                        $el.trigger('fieldError', [{field: $(el).attr('name')}]);
                    });
                })

                .on('focus', 'input', function() {
                    var $el = $(this);

                    $el.closest('form').trigger('fieldError', [{field: $el.attr('name')}])
                })
            ;

			$.mask.definitions['n'] = '[0-9]';
			$('.js-registerForm .js-phoneField').mask('+7 (nnn) nnn-nn-nn');
        };
    ;

	$(document).ready(function() {
		init();
	});

}(window.ENTER));
;(function( ENTER ) {
	var constructors = ENTER.constructors,
		body = $('body'),
		authBlock = $('#auth-block'),
		registerMailPhoneField = $('.jsRegisterUsername'),
		resetPwdForm = $('.jsResetPwdForm'),
		registerForm = $('.jsRegisterForm'),
		loginForm = $('.jsLoginForm'),
		completeRegister = $('.jsRegisterFormComplete'),
		showLoginFormLink = $('.jsShowLoginForm'),

		/**
		 * Конфигурация валидатора для формы логина
		 * @type {Object}
		 */
		signinValidationConfig = {
			fields: [
				{
					fieldNode: $('.jsSigninUsername', authBlock),
					require: true,
					customErr: 'Не указан логин'
				},
				{
					fieldNode: $('.jsSigninPassword', authBlock),
					require: true,
					customErr: 'Не указан пароль'
				}
			]
		},
		signinValidator = new FormValidator(signinValidationConfig),

		/**
		 * Конфигурация валидатора для формы регистрации
		 * @type {Object}
		 */
		registerValidationConfig = {
			fields: [
				{
					fieldNode: $('.jsRegisterFirstName', authBlock),
					require: true,
					customErr: 'Не указано имя'
				},
				{
					fieldNode: registerMailPhoneField,
					validBy: 'isEmail',
					require: true,
					customErr: 'Некорректно введен e-mail'
				}
			]
		},
		registerValidator = new FormValidator(registerValidationConfig),

		/**
		 * Конфигурация валидатора для формы регистрации
		 * @type {Object}
		 */
		forgotPwdValidationConfig = {
			fields: [
				{
					fieldNode: $('.jsForgotPwdLogin', authBlock),
					require: true,
					customErr: 'Не указан email или мобильный телефон',
					validateOnChange: true
				}
			]
		},
		forgotValidator = new FormValidator(forgotPwdValidationConfig);
	// end of vars

	var
		/**
		 * Задаем настройки валидаторов.
		 * Глобальные настройки позволяют навешивать кастомные валидаторы на различные авторизационные формы.
		 */
		setValidatorSettings = function() {
			ENTER.utils.signinValidationConfig = signinValidationConfig;
			ENTER.utils.signinValidator = signinValidator;
			ENTER.utils.registerValidationConfig = registerValidationConfig;
			ENTER.utils.registerValidator = registerValidator;
			ENTER.utils.forgotPwdValidationConfig = forgotPwdValidationConfig;
			ENTER.utils.forgotValidator = forgotValidator;
		};
	// end of functions

	setValidatorSettings();

	/**
	 * Класс по работе с окном входа на сайт
	 *
	 * @author  Shaposhnik Vitaly
	 *
	 * @this    {Login}
	 *
	 * @constructor
	 */
	constructors.Login = (function() {
		'use strict';

		function Login() {
			// enforces new
			if ( !(this instanceof Login) ) {
				return new Login();
			}
			// constructor body

			this.form = null; // текущая форма
			this.redirect_to = null;

			body.on('click', '.registerAnotherWayBtn', $.proxy(this.registerAnotherWay, this));
			body.on('click', '.bAuthLink', this.openAuth);
			$('.jsLoginForm, .jsRegisterForm, .jsResetPwdForm').data('redirect', true).on('submit', $.proxy(this.formSubmit, this));
			body.on('click', '.jsForgotPwdTrigger, .jsRememberPwdTrigger', this.forgotFormToggle);
			body.on('click', '#bUserlogoutLink', this.logoutLinkClickLog);

			if ( showLoginFormLink.length ) {
				loginForm.hide();
				body.on('click', '.jsShowLoginForm', this.showLoginForm);
			}
		}


		/**
		 * Показ сообщений об ошибках
		 *
		 * @param   {String}    msg     Сообщение которое необходимо показать пользователю
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.showError = function( msg, callback ) {
			var error = $('ul.error_list', this.form);
			// end of vars

			if ( callback !== undefined ) {
				callback();
			}

			if ( error.length ) {
				error.html('<li>' + msg + '</li>');
			}
			else {
				$('.bFormLogin__ePlaceTitle', this.form).after($('<ul class="error_list" />').append('<li>' + msg + '</li>'));
			}

			return false;
		};

		/**
		 * Обработка ошибок формы
		 *
		 * @param   {Object}    formError   Объект с полем содержащим ошибки
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.formErrorHandler = function( formError ) {
			var validator = this.getFormValidator(),
				field = $('[name="' + this.getFormName() + '[' + formError.field + ']"]');
			// end of vars

			var clearError = function clearError() {
				validator._unmarkFieldError($(this));
			};
			// end of functions

			console.warn('Ошибка в поле');

			validator._markFieldError(field, formError.message);
			field.bind('focus', clearError);

			return false;
		};

		/**
		 * Обработка ошибок из ответа сервера
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.serverErrorHandler = {
			'default': function( res ) {
				console.log('Обработчик ошибки');

				if ( !res.redirect ) {
					res.redirect = window.location.href;
				}

				if ( res.error && res.error.message ) {
					this.showError(res.error.message, function() {
						document.location.href = res.redirect;
					});

					return false;
				}

				document.location.href = res.redirect;
			},

			0: function( res ) {
				var formError = null;
				// end of vars

				console.warn('Обработка ошибок формы');

				if ( res.redirect ) {
					this.showError(res.error.message, function() {
						document.location.href = res.redirect;
					});

					return;
				}

				// очищаем блок с глобальными ошибками
				if ( $('ul.error_list', this.form).length ) {
					$('ul.error_list', this.form).html('');
				}
				//this.showError(res.error.message);

				for ( var i = res.form.error.length - 1; i >= 0; i-- ) {
					formError = res.form.error[i];
					console.warn(formError);

					if ( formError.field !== 'global' && formError.message !== null ) {
						$.proxy(this.formErrorHandler, this)(formError);
					}
					else if ( formError.field === 'global' && formError.message !== null ) {
						this.showError(formError.message);
					}
				}

				return false;
			}
		};

		/**
		 * Проверяем как e-mail
		 *
		 * @return  {Boolean}   Выбрано ли поле e-mail в качестве регистрационных данных
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.checkEmail = function() {
			return registerMailPhoneField.hasClass('jsRegisterPhone') ? false : true;
		};

		/**
		 * Переключение типов проверки
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.registerAnotherWay = function() {
			var label = $('.registerAnotherWay'),
				btn = $('.registerAnotherWayBtn');
			// end of vars

			registerMailPhoneField.val('');

			if ( this.checkEmail() ) {
				label.html('Ваш мобильный телефон:');
				btn.html('Ввести e-mail');
				registerMailPhoneField.addClass('jsRegisterPhone');
				registerValidator.setValidate( registerMailPhoneField, {validBy: 'isPhone', customErr: 'Некорректно введен телефон'} );

				// устанавливаем маску для поля "Ваш мобильный телефон"
				$.mask.definitions['n'] = '[0-9]';
				registerMailPhoneField.mask('+7 (nnn) nnn-nn-nn');
			}
			else {
				label.html('Ваш e-mail:');
				btn.html('У меня нет e-mail');
				registerMailPhoneField.removeClass('jsRegisterPhone');
				registerValidator.setValidate( registerMailPhoneField, {validBy: 'isEmail', customErr: 'Некорректно введен e-mail'} );

				// убераем маску с поля "Ваш мобильный телефон"
				registerMailPhoneField.unmask();
			}

			return false;
		};

		/**
		 * Authorization process
		 *
		 * @public
		 */
		Login.prototype.openAuth = function() {
			var
				/**
				 * При закрытии попапа убераем ошибки с полей
				 */
				removeErrors = function() {
					var
						validators = ['signin', 'register', 'forgot'],
						validator,
						config,
						self,
						i, j;
					// end of vars

					for (j in validators) {
						validator = eval('ENTER.utils.' + validators[j] + 'Validator');
						config = eval('ENTER.utils.' + validators[j] + 'ValidationConfig');

						if ( !config || !config.fields || !validator ) {
							continue;
						}

						for (i in config.fields) {
							self = config.fields[i].fieldNode;
							self && validator._unmarkFieldError(self);
						}
					}
				};
			// end of functions

			setValidatorSettings();

			authBlock.lightbox_me({
				centered: true,
				autofocus: true,
				onLoad: function() {
					authBlock.find('input:first').focus();
				},
                onClose: function() {
                    removeErrors();
                    authBlock.trigger('changeState', ['default']);
                }
			});

			return false;
		};

		/**
		 * Изменение значения кнопки сабмита при отправке ajax запроса
		 *
		 * @param btn Кнопка сабмита
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.submitBtnLoadingDisplay = function( btn ) {
			if ( btn.length ) {
				var value1 = btn.val(),
					value2 = btn.data('loading-value');
				// end of vars

				btn.attr('disabled', (btn.attr('disabled') === 'disabled' ? false : true)).val(value2).data('loading-value', value1);
			}

			return false;
		};

		/**
		 * Валидатор формы
		 *
		 * @return  {Object}   Валидатор для текущей формы
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.getFormValidator = function() {
			return eval('ENTER.utils.' + this.getFormName() + 'Validator');
		};

		/**
		 * Получить название формы
		 *
		 * @return {string} Название текущей формы
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.getFormName = function() {
			return ( this.form.hasClass('jsLoginForm') ) ? 'signin' : (this.form.hasClass('jsRegisterForm') ? 'register' : (this.form.hasClass('jsResetPwdForm') ? 'forgot' : ''));
		};

		/**
		 * Сабмит формы регистрации или авторизации
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.formSubmit = function( e, param ) {
			e.preventDefault();
			this.form = $(e.target);

			var formData = this.form.serializeArray(),
				validator = this.getFormValidator(),
				formSubmit = $('.jsSubmit', this.form),
				forgotPwdLogin = $('.jsForgotPwdLogin', this.form),
				urlParams = this.getUrlParams(),
				timeout;
			// end of vars

			// устанавливаем редирект
			this.redirect_to = window.location.href;
			if ( urlParams['redirect_to'] ) {
				this.redirect_to = urlParams['redirect_to'];
			}

			var responseFromServer = function( response ) {
					// когда пришел ответ с сервера, очищаем timeout
					clearTimeout(timeout);

					if ( response.error ) {
						console.warn('Form has error');

						if ( Login.serverErrorHandler.hasOwnProperty(response.error.code) ) {
							console.log('Есть обработчик');
							$.proxy(Login.serverErrorHandler[response.error.code], this)(response);
						}
						else {
							console.log('Стандартный обработчик');
							Login.serverErrorHandler['default'](response);
						}

						this.submitBtnLoadingDisplay( formSubmit );

						return false;
					}

					$.proxy(this.formSubmitLog, this);

					// если форма "Восстановление пароля" то скрываем елементы и выводим сообщение
					if ( forgotPwdLogin.length && forgotPwdLogin.is(':visible') ) {
						this.submitBtnLoadingDisplay( formSubmit );
						forgotPwdLogin.hide();
						$('.jsForgotPwdLoginLabel', this.form).hide();
						formSubmit.hide();
						this.showError(response.notice.message);
					}

					console.log(this.form.data('redirect'));
					console.log(response.data.link);
					if ( typeof(gaRun) != 'undefined' && typeof(gaRun.register) === 'function' ) {
						gaRun.register();
					}

					if ( this.form.data('redirect') ) {
						if ( typeof (response.data.link) !== 'undefined' ) {
							console.info('try to redirect to2 ' + response.data.link);
							console.log(typeof response.data.link);

							document.location.href = response.data.link.replace(/#.*$/, '');

							return false;
						}
						else {
							// this.form.unbind('submit');
							// this.form.submit();

							completeRegister.html(response.message);
							completeRegister.show();
							registerForm.hide();
							this.showLoginForm();

							// Закомментил следующую строку так как изза нее возникает баг SITE-3389
							// document.location.href = window.location.href;
						}
					}
					else {
						authBlock.trigger('close');
					}

					//for order page
					if ( $('#order-form').length ) {
						$('#user-block').html('Привет, <strong><a href="' + response.data.link + '">' + response.data.user.first_name + '</a></strong>');
						$('#order_recipient_first_name').val(response.data.user.first_name);
						$('#order_recipient_last_name').val(response.data.user.last_name);
						$('#order_recipient_phonenumbers').val(response.data.user.mobile_phone.slice(1));
						$('#qiwi_phone').val(response.data.user.mobile_phone.slice(1));
					}
				},

				requestToServer = function() {
					this.submitBtnLoadingDisplay( formSubmit );
					formData.push({name: 'redirect_to', value: this.redirect_to});
					$.post(this.form.attr('action'), formData, $.proxy(responseFromServer, this), 'json');

					/*
					 SITE-3174 Ошибка авторизации.
					 Принято решение перезагружать страничку через 5 сек, после отправки запроса на логин.
					 */
					timeout = setTimeout($.proxy(function() {document.location.href = this.redirect_to;}, this), 5000);
				};
			// end of functions

			validator.validate({
				onInvalid: function( err ) {
					console.warn('invalid');
					console.log(err);
				},
				onValid: $.proxy(requestToServer, this)
			});

			return false;
		};

		/**
		 * Показать форму логина на странице /login
		 */
		Login.prototype.showLoginForm = function() {
			showLoginFormLink.hide();
			loginForm.slideDown(300);
			$.scrollTo(loginForm, 500);
		};


		/**
		 * Отображение формы "Забыли пароль"
		 *
		 * @public
		 */
		Login.prototype.forgotFormToggle = function() {
			if ( resetPwdForm.is(':visible') ) {
				resetPwdForm.hide();
				loginForm.show();
			}
			else {
				resetPwdForm.show();
				loginForm.hide();
			}

			return false;
		};

		/**
		 * Логирование при сабмите формы регистрации или авторизации
		 *
		 * @this   {Login}
		 * @public
		 */
		Login.prototype.formSubmitLog = function() {
			var type = '';
			// end of vars
			if ( typeof(gaRun) && typeof(gaRun.login) === 'function' ) {
				gaRun.login();
			}
			if ( 'signin' === this.getFormName() ) {
				if ( typeof(_gaq) !== 'undefined' ) {
					type = ( (this.form.find('.jsSigninUsername').val().search('@')) !== -1 ) ? 'email' : 'mobile';
					_gaq.push(['_trackEvent', 'Account', 'Log in', type, window.location.href]);
				}

			}
			else if ( 'register' === this.getFormName() ) {
				if ( typeof(_gaq) !== 'undefined' ) {
					type = ( this.checkEmail() ) ? 'email' : 'mobile';
					_gaq.push(['_trackEvent', 'Account', 'Create account', type]);
				}

			}
			else if ( 'forgot' === this.getFormName() ) {
				if ( typeof(_gaq) !== 'undefined' ) {
					type = ( (this.form.find('.jsForgotPwdLogin').val().search('@')) !== -1 ) ? 'email' : 'mobile';
					_gaq.push(['_trackEvent', 'Account', 'Forgot password', type]);
				}
			}
		};

		/**
		 * Логирование при клике на ссылку выхода
		 *
		 * @public
		 */
		Login.prototype.logoutLinkClickLog = function() {
		};

		/**
		 * Получение get параметров текущей страницы
		 */
		Login.prototype.getUrlParams = function() {
			var $_GET = {},
				__GET = window.location.search.substring(1).split('&'),
				getVar,
				i;
			// end of vars

			for ( i = 0; i < __GET.length; i++ ) {
				getVar = __GET[i].split('=');
				$_GET[getVar[0]] = typeof( getVar[1] ) === 'undefined' ? '' : getVar[1];
			}

			return $_GET;
		};

		return Login;
	}());


	$(document).ready(function() {
		var login = new ENTER.constructors.Login();
	});

}(window.ENTER));
$(document).ready(function() {
	/**
	 * Подписка
	 */
	$('body').on('click', '.bSubscibe', function() {
		if ( $(this).hasClass('checked') ) {
			$(this).removeClass('checked');
			$(this).find('.subscibe').removeAttr('checked');
			$(this).find('input[name="subscribe"]').val(0);
		} else {
			$(this).addClass('checked');
			$(this).find('.subscibe').attr('checked','checked');
			$(this).find('input[name="subscribe"]').val(1);
		}

		return false;
	});


	/* GA categories referrer */
	function categoriesSpy( e ) {
		if ( typeof(_gaq) !== 'undefined' ) {
			_gaq.push(['_trackEvent', 'CategoryClick', e.data, window.location.pathname ]);
		}

		return true;
	}

	$('.bMainMenuLevel-1__eLink').bind('click', 'Верхнее меню', categoriesSpy );
	$('.bMainMenuLevel-2__eLink').bind('click', 'Верхнее меню', categoriesSpy );
	$('.bMainMenuLevel-3__eLink').bind('click', 'Верхнее меню', categoriesSpy );
	$('.breadcrumbs').first().find('a').bind( 'click', 'Хлебные крошки сверху', categoriesSpy );
	$('.breadcrumbs-footer').find('a').bind( 'click', 'Хлебные крошки снизу', categoriesSpy );

	$('.bCtg__eMore').bind('click', function(e) {
		e.preventDefault();
		var el = $(this);
		el.parent().find('li.hf').slideToggle();
		var link = el.find('a');
		link.text('еще...' == link.text() ? 'скрыть' : 'еще...');
	});

	$('.product_buy-container').each(function() {
		var order = $(this).data('order');

		if ( typeof(order) == 'object' && !$.isEmptyObject(order) ) {
			$.ajax({
				url: ($(this).data('url')),
				data: order,
				type: 'POST',
				timeout: 20000
			});
		}
	});
});
;(function($){

	var $body = $(document.body),
		$nav = $('nav'),
        $hamburgerIcon = $('.jsHamburgerIcon'),
		MenuStorage, storage, fillRecommendBlocks, hideMenuTimeoutId;

	/**
	 * Конструктор хранилища данных. Возвращает либо lscache, либо объект с необходимыми свойствами (функциями)
	 * @return {*}
	 * @link https://github.com/pamelafox/lscache
	 * @constructor
	 */
	MenuStorage = function MenuStorageF() {
		var cKey = 'cachedData';
		if (lscache && typeof lscache.supported == 'function' && lscache.supported()) {
			return lscache
		} else {
			return {
				'set': function(key, value, time, $el){
					$el.data(cKey, value);
				},
				'get': function(key, $el) {
					return $el.data(cKey) ? $el.data(cKey) : false;
				},
				'remove': function(key, $el) {
					$el.data(cKey, false);
				}
			}
		}
	};

	/**
	 * Заполнение блоков меню "товарами дня"
	 * @param $el
	 * @param blocks
	 */
	fillRecommendBlocks = function fillRecommendBlocksF($el, blocks) {

		var $containers = $el.find('.jsMenuRecommendation');

		$.each(blocks, function(i, block) {
			try {
				if (!block.categoryId) return;
				var $container = $containers.filter('[data-parent-category-id="' + block.categoryId + '"]');
				$container.html(block.content);
			} catch (e) {
				console.error(e);
			}
		});
	};

	// объект универсального хранилища для данных "товар дня"
	storage = new MenuStorage();

	// Simple lazy loading
	$nav.on('mouseenter', '.navsite2_i', function(){
		$(this).find('.menuImgLazy').each(function(){
			$(this).attr('src', $(this).data('src'))
		});
	});

	// Товар дня
	$nav.on('mouseenter', '.navsite_i', function(){

		var	$el = $(this),
			url = $el.data('recommendUrl'),
			lKey = 'xhrLoading', // ключ для предотвращения дополнительного запроса на загрузку данных
			cacheTime = 10, // время кэширования в localstorage (в минутах)
			key, xhr;

		if (typeof url == 'string' && !$el.data(lKey) === true) {

			// отрезаем от url параметры для ключа в localstorage
			key = url.substring(0, url.indexOf('?'));

			if (!storage.get(key, $el)) {

				xhr = $.get(url);
				$el.data(lKey, true);

				xhr.done(function(response) {
					var data = response.productBlocks;
					if (!data) return;
					storage.set(key, data, cacheTime, $el);
					fillRecommendBlocks($el, data);
				}).fail(function() {
					storage.remove(key, $el);
				}).always(function(){
					$el.data(lKey, false)
				});
			} else {
				fillRecommendBlocks($el, storage.get(key, $el));
			}

		}
	});

	// аналитика
	$body.on('click', '.jsRecommendedItemInMenu', function(event) {

		event.stopPropagation();

		try {

			var $el = $(this),
				link = $el.attr('href'),
				isNewWindow = $el.attr('target') == '_blank',
				sender = $el.data('sender');

			$body.trigger('TLT_processDOMEvent', [event]);

			$body.trigger('trackGoogleEvent', {
				category: 'RR_взаимодействие',
				action: 'Перешел на карточку товара',
				label: sender ? sender.position : null,
				hitCallback: isNewWindow ? null : function(){

					if (link) {
						setTimeout(function() { window.location.href = link; }, 90);
					}
				}
			});

			$el.trigger('TL_recommendation_clicked');

		} catch (e) { console.error(e); }
	});

    $body.on('click', '.jsHamburgerIcon', function(){
        $nav.toggleClass('show');
    });

    // if ($hamburgerIcon.length > 0) {
    //     $hamburgerIcon.hover(function(){
    //         clearTimeout(hideMenuTimeoutId);
    //         hideMenuTimeoutId = null;
    //         $nav.show();
    //     });
    //     $body.on('hover', 'div', function(e){
    //         var $target;
    //         if ($nav.is(':visible') && !hideMenuTimeoutId) {
    //             $target = $(e.target);
    //             if ($target.closest('nav').length == 0
    //                 && $target.prop('nodeName') != 'NAV'
    //                 && !$target.hasClass('jsHamburgerIcon') ) {
    //                 hideMenuTimeoutId = setTimeout( function(){ $nav.hide() }, 2000 );
    //             }
    //         }
    //     })
    // }

})(jQuery);
(function() {
	var oneClickOpening = false;
	$('body').on('click', '.jsOneClickButton-new', function(e) {
		console.info('show one click form');

		e.preventDefault();

		if (oneClickOpening) {
			return;
		}

		var
			button = $(e.currentTarget),
			$target = $('#jsOneClickContent'),
            $productInfo = $('#product-info'),
            productUi;

        productUi = $productInfo.length > 0 ? $productInfo.data('ui') : button.data('product-ui');

        if (!productUi) throw 'Не обнаружен ui продукта';

		if ($target.length) {
			openPopup(false);
			init();
		} else {
			oneClickOpening = true;
			$.ajax({
				url: ENTER.utils.generateUrl('orderV3OneClick.form', {productUid: productUi, sender: button.data('sender'), sender2: button.data('sender2')}),
				type: 'POST',
				dataType: 'json',
				closeClick: false,
				success: function(result) {
					$('body').append(result.form);
					$target = $('#jsOneClickContent');
					openPopup(true);
					init();
				},
				complete: function() {
					oneClickOpening = false;
				}
			})
		}

		function init() {
			ENTER.OrderV31Click.functions.initAddress();
			ENTER.OrderV31Click.functions.initYandexMaps();
			ENTER.OrderV31Click.functions.initDelivery();
			ENTER.OrderV31Click.functions.initValidate();
		}

		function openPopup(removeOnClose) {
			$('.js-order-oneclick-delivery-toggle-btn').on('click', function(e) {
				var button = $(e.currentTarget),
					$toggleNote = $('.js-order-oneclick-delivery-toggle-btn-note'),
					$toggleBox = $('.js-order-oneclick-delivery-toggle');

				button.toggleClass('orderU_lgnd-tggl-cur');
				$toggleBox.toggle();
				$toggleNote.toggleClass('orderU_lgnd_tgglnote-cur');

				$('body').trigger('trackUserAction', ['2 Способ получения']);
			});

			var $orderContent = $('#js-order-content');

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

			if ($target.length) {
				var data = $.parseJSON($orderContent.data('param'));
				data.quantity = button.data('quantity');
				data.shopId = button.data('shop');
				$orderContent.data('shop', data.shopId);

				$target.lightbox_me({
					centered: true,
					sticky: false,
					closeSelector: '.close',
					removeOtherOnCreate: false,
					closeClick: false,
					closeEsc: false,
					onLoad: function() {
						$('#OrderV3ErrorBlock').empty().hide();
						$('.jsOrderV3PhoneField').focus();
					},
					onClose: function() {
						if (removeOnClose) {
							$target.remove();
							$('.jsOneClickForm').remove();
						}
                        $('.jsNewPoints').remove();            // удалить ранее созданные карты
                        ENTER.OrderV31Click.koModels = [];
                        ENTER.OrderV31Click.map.destroy();
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
					//console.log("Query: %s", data.result.OrderDeliveryRequest);
					//console.log("Model:", data.result.OrderDeliveryModel);

                    var $data = $(data.result.page);

                    $orderContent.empty().html($data.html());

                    var E = ENTER.OrderV31Click,
                        pointData = JSON.parse($data.find('script.jsMapData').html()),
                        points = new ENTER.DeliveryPoints(pointData.points, E.map);

                    E.koModels.push(points);
                    ko.applyBindings(points, $orderContent[0]);

					ENTER.OrderV31Click.functions.initAddress();
					$orderContent.find('input[name=address]').focus();
				}).always(function(){
					$orderContent.stop(true, true).fadeIn(200);
					//if (spinner) spinner.stop();

					$('body').trigger('trackUserAction', ['0 Вход']);
				});
			}
		}
	});
})();
;(function($){	
	/*paginator*/
	var EnterPaginator = function( domID,totalPages, visPages, activePage ) {
		
		var self = this;

		self.inputVars = {
			domID: domID, // id элемента для пагинатора
			totalPages:totalPages, //общее количество страниц
			visPages:visPages?visPages:10, // количество видимых сраниц
			activePage:activePage?activePage:1 // текущая активная страница
		};

		var pag = $('#'+self.inputVars.domID), // пагинатор
			pagW = pag.width(), // ширина пагинатора
			eSliderFillW = (pagW*self.inputVars.visPages)/self.inputVars.totalPages, // ширина закрашенной области слайдера
			onePageOnSlider = eSliderFillW / self.inputVars.visPages, // ширина соответствующая одной странице на слайдере
			onePage = pagW / self.inputVars.visPages, // ширина одной цифры на пагинаторе
			center = Math.round(self.inputVars.visPages/2);
		// end of vars

		var scrollingByBar = function scrollingByBar ( left ) {
			var pagLeft = Math.round(left/onePageOnSlider);

			$('.bPaginator_eWrap', pag).css('left', -(onePage * pagLeft));
		};

		var enableHandlers = function enableHandlers() {
			// биндим хандлеры
			var clicked = false,
				startX = 0,
				nowLeft = 0;
			// end of vars
			
			$('.bPaginatorSlider', pag).bind('mousedown', function(e){
				startX = e.pageX;
				nowLeft = parseInt($('.bPaginatorSlider_eFill', pag).css('left'), 10);
				clicked = true;
			});

			$('.bPaginatorSlider', pag).bind('mouseup', function(){
				clicked = false;
			});

			pag.bind('mouseout', function(){
				clicked = false;
			});

			$('.bPaginatorSlider', pag).bind('mousemove', function(e){
				if ( clicked ) {
					var newLeft = nowLeft+(e.pageX-startX);

					if ( (newLeft >= 0) && (newLeft <= pagW - eSliderFillW) ) {
						$('.bPaginatorSlider_eFill', pag).css('left', nowLeft + (e.pageX - startX));
						scrollingByBar(newLeft);
					}
				}
			});
		};

		var init = function init() {
			pag.append('<div class="bPaginator_eWrap"></div>');
			pag.append('<div class="bPaginatorSlider"><div class="bPaginatorSlider_eWrap"><div class="bPaginatorSlider_eFill" style="width:'+eSliderFillW+'px"></div></div></div>');
			for ( var i = 0; i < self.inputVars.totalPages; i++ ) {
				$('.bPaginator_eWrap', pag).append('<a class="bPaginator_eLink" href="#' + i + '">' + (i + 1) + '</a>');

				if ( (i + 1) === self.inputVars.activePage ) {
					$('.bPaginator_eLink', pag).eq(i).addClass('active');
				}
			}
			var realLinkW = $('.bPaginator_eLink', pag).width(); // реальная ширина цифр

			$('.bPaginator_eLink', pag).css({'marginLeft':(onePage - realLinkW - 2)/2, 'marginRight':(onePage - realLinkW - 2)/2}); // размазываем цифры по ширине слайдера
			$('.bPaginator_eWrap', pag).addClass('clearfix').width(onePage * self.inputVars.totalPages); // устанавливаем ширину wrap'а, добавляем ему очистку
		};

		self.setActive = function ( page ) {
			var left = parseInt($('.bPaginator_eWrap', pag).css('left'), 10), // текущее положение пагинатора
				barLeft = parseInt($('.bPaginatorSlider_eFill', pag).css('left'), 10), // текущее положение бара
				nowLeftElH = Math.round(left/onePage) * (-1), // количество скрытых элементов
				diff = -(center - (page - nowLeftElH)); // на сколько элементов необходимо подвинуть пагинатор для центрирования
			// end of vars
			
			$('.bPaginator_eLink', pag).removeClass('active');
			$('.bPaginator_eLink', pag).eq(page).addClass('active');

			if ( left - (diff * onePage) > 0 ) {
				left = 0;
				barLeft = 0;
			}
			else if ( page > self.inputVars.totalPages - center ) {
				left = Math.round(self.inputVars.totalPages - self.inputVars.visPages) * onePage*(-1);
				barLeft = Math.round(self.inputVars.totalPages - self.inputVars.visPages) * onePageOnSlider;
			}
			else {
				left = left - (diff * onePage);
				barLeft = barLeft + (diff * onePageOnSlider);
			}

			$('.bPaginator_eWrap').animate({'left': left});
			$('.bPaginatorSlider_eFill', pag).animate({'left': barLeft});
		};

		init();
		enableHandlers();
	};

	/* promo catalog */
	if ( $('#promoCatalog').length ) {
		console.log('promoCatalog promoSlider');

		var
			body = $('body'),
			promoCatalog = $('#promoCatalog'),
			data = promoCatalog.data('slides'),

			//первоначальная настройка
			slider_SlideCount = data.length, //количество слайдов
			catalogPaginator = new EnterPaginator('promoCatalogPaginator',slider_SlideCount, 12, 1),

			activeInterval = promoCatalog.data('use-interval') !== undefined ? promoCatalog.data('use-interval') : false,
			interval = null,
			toSlide = 0,
			nowSlide = 0,//текущий слайд

			// Флаг под которым реализована дорисовка hash к url
			activeHash = promoCatalog.data('use-hash') !== undefined ? promoCatalog.data('use-hash') : true,
			hash,
			scrollingDuration = 500,

			/**
			 * Флаг включения карусели (бесконечная листалка влево/вправо).
			 * Если флаг отключен, то когда слайдер долистался до конца, он визуально перемещается в начало
			 * @type {Boolean}
			 */
			activeCarousel = promoCatalog.data('use-carousel') !== undefined ? promoCatalog.data('use-carousel') : false,
			slideId,// id слайда
			shift = 0,// сдвиг

			slider_SlideW,// ширина одного слайда
			slider_WrapW,// ширина обертки

			disabledBtns = false,// Активность кнопок для пролистования и пагинатора.

			// Настройки для аналитики слайдера
			analyticsConfig = typeof promoCatalog.data('analytics-config') !== "undefined" ? promoCatalog.data('analytics-config') : false,
            // Буфер, для коллекций. Пока _gaq не подгрузился, делаем запись в буфер. Затем трекаем все скопом.
			tchiboAnalyticsBuffer = [],
			categoryToken = typeof promoCatalog.data('category-token') !== "undefined" ? promoCatalog.data('category-token') : '',
			documentHidden = false;
		// end of vars

		var
			initSlider = function initSlider() {
				var
					slide,
					slideTmpl;
				// end of vars

				if ( activeCarousel ) {
					$('.bPromoCatalogSlider_eArrow.mArLeft').show();
					$('.bPromoCatalogSlider_eArrow.mArRight').show();
				}

				for ( slide = 0; slide < data.length; slide++ ) {
					slideTmpl = tmpl('slide_tmpl', data[slide]);

					if ( $(slideTmpl).length ) {
						slideTmpl = $(slideTmpl).attr("id", 'slide_id_' + slide);
					}

					var $slide = $(slideTmpl).appendTo('.bPromoCatalogSliderWrap');
					ko.applyBindings(ENTER.UserModel, $slide[0]);

					if ( $('.bPromoCatalogSliderWrap_eSlideLink').eq(slide).attr('href') === '' ) {
						$('.bPromoCatalogSliderWrap_eSlideLink').eq(slide).removeAttr('href');
					}

					$('.bPromoCatalogNav').append('<a id="promoCatalogSlide' + slide + '" href="#' + slide + '" class="bPromoCatalogNav_eLink">' + ((slide * 1) + 1) + '</a>');
				}

				slider_SlideW = $('.bPromoCatalogSliderWrap_eSlide').width();
				slider_WrapW = $('.bPromoCatalogSliderWrap').width( slider_SlideW * slider_SlideCount + (940/2 - slider_SlideW/2));
			},

			/**
			 * Задаем интервал для пролистывания слайдов
			 */
			setScrollInterval = function setScrollInterval( slide ) {
				var
					time = 3000,
					additionalTime = 0;
				// end of vars

				if ( !activeInterval ) {
					return;
				}

				if ( slide == undefined ) {
					slide = 0;
				}
				else {
					additionalTime = scrollingDuration;
				}

				if ( data.hasOwnProperty(slide) && data[slide].hasOwnProperty('time') ) {
					time = data[slide]['time'];
				}

				time = time + additionalTime;

				interval = setTimeout(function(){
					slide++;

					if ( !activeCarousel ) {
						if ( slider_SlideCount <= slide ) {
							slide = 0;
						}
					}

					moveSlide(slide);
					setScrollInterval(slide);

					if ('tchibo' == categoryToken && typeof _gaq != 'undefined') {
						_gaq.push(['_trackEvent', 'slider view', 'tchibo', getSlideIndex(slide) + 1 + '']);
					}
				}, time);
			},

			/**
			 * Убираем интервал для пролистывания слайдов
			 */
			removeScrollInterval = function removeScrollInterval() {
				if ( !interval ) {
					return;
				}

				clearTimeout(interval);
			},

			/**
			 * Click кнопки для листания
			 *
			 * @param e
			 */
			btnsClick = function( e ) {
				var
					pos = ( $(this).hasClass('mArLeft') ) ? '-1' : '1',
					slide = nowSlide + pos * 1;
				// end of vars

				e.preventDefault();

				if ( disabledBtns ) {
					return false;
				}

				removeScrollInterval();
				moveSlide(slide);
				setScrollInterval(slide);
				
				if ('tchibo' == categoryToken && typeof _gaq != 'undefined') {
					_gaq.push(['_trackEvent', 'slider view', 'tchibo', getSlideIndex(slide) + 1 + '']);
				}
			},

			/**
			 * Click пагинатора
			 *
			 * @param e
			 */
			paginatorClick = function( e ) {
				var
					link;
				// end of vars

				e.preventDefault();

				if ( $(this).hasClass('active') ) {
					return false;
				}

				if ( disabledBtns ) {
					return false;
				}

				link = $(this).attr('href').slice(1) * 1;
				removeScrollInterval();

				if ( activeCarousel ) {
					moveToSlideId(link);
				}
				else {
					moveSlide(link);
				}

				setScrollInterval(link);
				
				if ('tchibo' == categoryToken && typeof _gaq != 'undefined') {
					_gaq.push(['_trackEvent', 'slider view', 'tchibo', link + 1 + '']);
				}
			},

			/**
			 * Перемещение слайдов на указанный slideId.
			 * Данная функция должна использоваться только при включенном activeCarousel
			 *
			 * @param id Id слайда
			 */
			moveToSlideId = function( id ){
				var
					slidesWrap = $(".jsPromoCatalogSliderWrap"),
					slides = $(".bPromoCatalogSliderWrap_eSlide", slidesWrap),
					slide;
				// end of vars

				if ( id === undefined ) {
					id = 0;
				}

				slide = slides.index($('#slide_id_' + id, slidesWrap));
				moveSlide(slide);
			},

			/**
			 * Перемещение слайдов на указанный слайд
			 *
			 * @param slide Позиция слайда
			 */
			moveSlide = function moveSlide( slide ) {
				var
					leftBtn = $('.bPromoCatalogSlider_eArrow.mArLeft'),
					rightBtn = $('.bPromoCatalogSlider_eArrow.mArRight'),
					slidesWrap = $(".jsPromoCatalogSliderWrap"),
					buff,
					slideData;
				// end of vars

				var
					/**
					 * Перемещение последнего слайда в начало wrapper элемента
					 */
					moveLastSlideToStart = function() {
						var
							slides = $(".bPromoCatalogSliderWrap_eSlide", slidesWrap);
						// end of vars

						buff = slides.last();
						slides.last().remove();
						slidesWrap.prepend(buff);
						slidesWrap.css({left: slidesWrap.position().left - slider_SlideW});
					},

					/**
					 * Перемещение первого слайда в конец wrapper элемента
					 */
					moveFirstSlideToEnd = function() {
						var
							slides = $(".bPromoCatalogSliderWrap_eSlide", slidesWrap);
						// end of vars

						buff = slides.first();
						slides.first().remove();
						slidesWrap.append(buff);
						slidesWrap.css({left: slidesWrap.position().left + slider_SlideW});
					};
				// end of functions

				slideId = slide;
				nowSlide = slide;

				if ( !activeCarousel) {
					if ( slide === 0 ) leftBtn.hide();
					else leftBtn.show();

					if ( slide === slider_SlideCount - 1 ) rightBtn.hide();
					else rightBtn.show();
				}
				else {
					if ( slide > slider_SlideCount - 1 ) {
						moveFirstSlideToEnd();
						shift++;
						slide = 0;
						nowSlide = slider_SlideCount - 1;
					}
					else if ( slide < 0 ) {
						moveLastSlideToStart();
						shift--;
						slide = slider_SlideCount - 1;
						nowSlide = 0;
					}

					slideId = $(".jsPromoCatalogSliderWrap .bPromoCatalogSliderWrap_eSlide").eq(nowSlide).attr("id").replace('slide_id_', '');
				}

				// деактивируем кнопочки для пролистывания
				disabledBtns = true;

				$('.bPromoCatalogSliderWrap').animate({'left': -(slider_SlideW * nowSlide)}, scrollingDuration, function() {
					// активируем кнопочки для пролистывания
					disabledBtns = false;
				});

				catalogPaginator.setActive(slideId);

				if ( activeHash ) {
					window.location.hash = 'slide' + ((slideId * 1) + 1);
				}

				slideData = data[slideId];
				if ( (slideData.hasOwnProperty('title') && slideData.hasOwnProperty('time')) && tchiboAnalytics.checkRule('collection_view') ) {
					tchiboAnalytics.collectionShow(slideData.title, (slideId*1)+1, slideData.time);
				}
			},

			getSlideIndex = function(slide) {
				var slideId = parseInt(slide);

				if (activeCarousel) {
					if ( slide > slider_SlideCount - 1 ) {
						slide = slider_SlideCount - 1;
					} else if ( slide < 0 ) {
						slide = 0;
					}

					slideId = parseInt($(".jsPromoCatalogSliderWrap .bPromoCatalogSliderWrap_eSlide").eq(slide).attr("id").replace('slide_id_', ''));
				}

				return slideId;
			},

			tchiboAnalytics = {
				init: function() {
					if ( !tchiboAnalytics.isAnalyticsEnabled ){
						return;
					}

					var
						collectionClickHandler = function() {
							var
								self = $(this),
								slide = self.parent('.bPromoCatalogSliderWrap_eSlide'),
								slideId,
								slideData;
							// end of vars

							if ( !slide.length || !slide.attr('id') ) {
								return;
							}

							slideId = slide.attr('id').replace('slide_id_', '');
							slideData = data[slideId];

							if ( slideData.hasOwnProperty('title') ) {
								tchiboAnalytics.collectionClick(slideData.title, (slideId*1)+1);
							}
						},

						productClickHandler = function() {
							var
								self = $(this),
								slide = self.parents('.bPromoCatalogSliderWrap_eSlide'),
								slideElementId,
								slideId,
								slideData,
								productIndex;
							// end of vars

							if ( !slide.length || !slide.attr('id') ) {
								return;
							}

							slideElementId = slide.attr('id');
							slideId = slideElementId.replace('slide_id_', '');
							slideData = data[slideId];

							productIndex = $('.mTchiboSlider #'+slideElementId+' .prodItem > a').index(this);
							if ( -1 == productIndex ) {
								return;
							}

							if ( slideData.hasOwnProperty('title') && undefined != typeof(slideData.products[productIndex].name) ) {
								tchiboAnalytics.productClick(slideData.title, slideData.products[productIndex].name, (productIndex*1)+1);
							}
						};
					// end of functions

					if ( tchiboAnalytics.checkRule('collection_click') ) {
						body.on('click', '.mTchiboSlider .bPromoCatalogSliderWrap_eSlideLink', collectionClickHandler);
					}

					if ( tchiboAnalytics.checkRule('product_click') ) {
						body.on('click', '.mTchiboSlider .prodItem > a', productClickHandler);
					}

					tchiboAnalytics.pageVisibility();
				},

				/**
				 * Управление аналитикой в зависимости от присутствия пользователя на вкладке текущей страницы (Page Visibility API)
				 */
				pageVisibility: function() {
					var
						hidden, visibilityChange;
					// end of vars

					var
						handleVisibilityChange = function() {
							documentHidden = document[hidden] ? true : false;
						};
					// end of functions

					if (
						!tchiboAnalytics.isAnalyticsEnabled ||
						!analyticsConfig.hasOwnProperty('use_page_visibility') ||
						true != analyticsConfig.use_page_visibility
					) {
						return;
					}

					if ( typeof document.hidden !== "undefined" ) { // Opera 12.10 and Firefox 18 and later support
						hidden = "hidden";
						visibilityChange = "visibilitychange";
					} else if ( typeof document.mozHidden !== "undefined" ) {
						hidden = "mozHidden";
						visibilityChange = "mozvisibilitychange";
					} else if ( typeof document.msHidden !== "undefined" ) {
						hidden = "msHidden";
						visibilityChange = "msvisibilitychange";
					} else if ( typeof document.webkitHidden !== "undefined" ) {
						hidden = "webkitHidden";
						visibilityChange = "webkitvisibilitychange";
					}

					handleVisibilityChange();

					if ( typeof document.addEventListener === "undefined" || typeof hidden === "undefined" ) {
						// requires a browser, such as Google Chrome or Firefox, that supports the Page Visibility API.
					} else {
						// Handle page visibility change
						document.addEventListener(visibilityChange, handleVisibilityChange, false);
					}
				},

				/**
				 * @param collection_name		название коллекции
				 * @param collection_position	позиция в слайдере
				 * @param delay					текущая задержка на данном слайдере
				 */
				collectionShow: function(collection_name, collection_position, delay) {	},

				/**
				 * @param collection_name		название коллекции
				 * @param collection_position	позиция в слайдере
				 */
				collectionClick: function(collection_name, collection_position) {
					var item;

					if (
						!tchiboAnalytics.isAnalyticsEnabled ||
						'undefined' == typeof(_gaq) ||
						'undefined' == typeof(collection_name) ||
						'undefined' == typeof(collection_position)
						) {
						return;
					}

					item = ['_trackEvent', 'collection_click', collection_name+'_'+collection_position]

					console.info('TchiboSliderAnalytics collection_click');
					console.log(item);
					_gaq.push(item);
				},

				/**
				 * @param collection_name	название коллекции
				 * @param item_name			название товара
				 * @param position			позиция товара на слайдере (1, 2, 3 слева направо)
				 */
				productClick: function(collection_name, item_name, position) {
					var item;

					if (
						!tchiboAnalytics.isAnalyticsEnabled ||
						'undefined' == typeof(_gaq) ||
						'undefined' == typeof(collection_name) ||
						'undefined' == typeof(item_name) ||
						'undefined' == typeof(position)
						) {
						return;
					}

					item = ['_trackEvent', 'item_click', collection_name+'_'+item_name, position.toString()];

					console.info('TchiboSliderAnalytics item_click');
					console.log(item);
					_gaq.push(item);
				},

				isAnalyticsEnabled: function() {
					return analyticsConfig && analyticsConfig.hasOwnProperty('enabled') && true == analyticsConfig.enabled;
				},

				checkRule: function(rule) {
					if (
						tchiboAnalytics.isAnalyticsEnabled &&
						typeof rule !== "undefined" &&
						analyticsConfig.hasOwnProperty(rule) && true == analyticsConfig[rule].enabled &&
						((true == analyticsConfig[rule].tchiboOnly && 'tchibo' === categoryToken) || (true != analyticsConfig[rule].tchiboOnly))
					) {
						return true;
					}

					return false;
				}
			};
		// end of functions

		$(function(){
			initSlider(); //запуск слайдера

			tchiboAnalytics.init();

			body.on('click', '.bPromoCatalogSlider_eArrow', btnsClick);
			body.on('click', '.bPaginator_eLink', paginatorClick);

			if ( activeHash ) {
				hash = window.location.hash;
				if ( hash.indexOf('slide') + 1 ) {
					toSlide = parseInt(hash.slice(6), 10) - 1;
					moveSlide(toSlide);
				}
			}

			setScrollInterval(toSlide);

			// аналитика показа первого слайда
			if ( data.hasOwnProperty(toSlide) && data[toSlide].hasOwnProperty('title') && data[toSlide].hasOwnProperty('time') && tchiboAnalytics.checkRule('collection_view') ) {
				tchiboAnalytics.collectionShow(data[toSlide].title, ((toSlide*1)+1), data[toSlide].time);
			}
		});
	}
})(jQuery);
/* Обработчик смены региона */
;(function($){

    var $body = $('body'),
        $popup = $('.jsRegionPopup'),
        openRegionPopup, showPopup, initAutocomplete, changeRegionAction;

    // Wrapper показа окна
    openRegionPopup = function openRegionPopupF(){
        if ($popup.length == 0) {
            $.get('/region/init')
                .done(function (res) {
                    if (res.result) {
                        $popup = $(res.result);
                        $body.append($popup);
                        initAutocomplete($popup.find('#jscity'));
                        showPopup()
                    }
                });
        } else {
            showPopup();
        }

        // analytics only for main page
        if ( document.location.pathname === '/' ) {
            $body.trigger('trackGoogleEvent', [{category: 'citySelector', action: 'viewed', nonInteraction: true}]);
        }

    };

    // Основная функция, которая сначала отправляет аналитику, а потом меняет регион
    changeRegionAction = function changeRegionActionF(regionName, url) {
        $body.trigger('trackGoogleEvent',{
            category: 'citySelector',
            action: 'selected',
            label: regionName,
            hitCallback: url
        });
    };

	function isGeoshopCookieSet() {
		return Boolean(parseInt(docCookies.getItem('geoshop')));
	}

	function queryAutocompleteVariants(term, onSuccess) {
		$.ajax({
			url: $popup.data('autocomplete-url'),
			dataType: 'json',
			data: {
				q: term
			},
			success: function( data ) {
				if (onSuccess) {
					onSuccess(data.data.slice(0, 15));
				}
			}
		});
	}

    // Lightbox
    showPopup = function showPopupF() {
		var
			autoResolveUrl = $popup.data('autoresolve-url'),
			$autoresolve = $('.jsAutoresolve', $popup);

		if (autoResolveUrl != null && !$autoresolve.length) {
			$.ajax({
				type: 'GET',
				url: autoResolveUrl,
				success: function( res ) {
					if (!res.data.length) {
						$autoresolve.html('');
						return false;
					}

					var url = res.data[0].url,
						name = res.data[0].name,
						id = res.data[0].id;

					if (id === 14974 || id === 108136) {
						return false;
					}

					if ($autoresolve.length) {
						$autoresolve.html('<a href="' + url + '">' + name + '</a>');
					}  else {
						$('.jsCityInline', $popup).prepend('<div class="cityItem mAutoresolve jsAutoresolve"><a href="'+url+'">'+name+'</a></div>');
					}

				}
			});
		}

		$popup.lightbox_me({
            autofocus: true,
            onLoad: function(){
                $popup.find('#jscity').putCursorAtEnd();
            },
            onClose: function() {
				if (!isGeoshopCookieSet()) {
					docCookies.setItem('geoshop', $popup.data('current-region-id'), 31536e3, '/');
				}
            }
        })
    };

    // Init-функция, вызывается один раз, навешивает автокомплит
    initAutocomplete = function initAutoCompleteF($elem) {

        var submitBtn = $popup.find('#jschangecity');

        /**
         * Настройка автодополнения поля для ввода региона
         */
        $elem.autocomplete( {
            autoFocus: true,
            appendTo: '#jscities',
            source: function( request, response ) {
                queryAutocompleteVariants(request.term, function(res) {
                    response( $.map( res, function( item ) {
                        return {
                            label: item.name,
                            value: item.name,
                            url: item.url
                        };
                    }));
                });
            },
            minLength: 2,
            select: function( event, ui ) {
                submitBtn.data('url', ui.item.url ).removeClass('mDisabled').removeAttr('disabled');
            },
            open: function() {
                $(this).removeClass('ui-corner-all').addClass('ui-corner-top');
            },
            close: function() {
                $(this).removeClass('ui-corner-top').addClass('ui-corner-all');
            }
        });
    };

    // клик по названию региона в юзербаре
    $body.on('click', '.jsChangeRegion', function(e) {
		e.preventDefault();
		openRegionPopup();
	});

    // полный список городов
    $body.on('click', '.jsRegionListMoreCity', function(e){
        e.preventDefault();
        $(this).toggleClass('mExpand');
        $popup.find('.jsRegionSlidesWrap').slideToggle(300);
    });

    // очистка поля ввода
    $body.on('click', '.jsRegionInputClear', function(e){
        e.preventDefault();
        $popup.find('#jscity').val('');
        $popup.find('#jschangecity').addClass('mDisabled').attr('disabled','disabled');
    });

    // Пролистывание списка городов
    $body.on('click', '.jsRegionArrow', function(){
        var direction = $(this).data('dir'),
            $holder = $popup.find('.jsRegionSlidesHolder'),
            $leftArrow = $popup.find('.jsRegionArrowLeft'),
            $rightArrow = $popup.find('.jsRegionArrowRight'),
            holderWidth = $holder.width(),
            width = $popup.find('.jsRegionOneSlide').width(),
            leftAfterComplete;

        $holder.animate({
            'left' : direction + '=' + width
        }, function(){
            leftAfterComplete = parseInt($holder.css('left'), 10);
            if (leftAfterComplete < 0) $leftArrow.show();
            if (leftAfterComplete == 0) $leftArrow.hide();
            if (width - leftAfterComplete == holderWidth) $rightArrow.hide();
            if (width - leftAfterComplete < holderWidth) $rightArrow.show()
        })

    });

    // Клик по кнопке "Сохранить"
    $body.on('click', '#jschangecity', function submitCityHandler(e) {
		e.preventDefault();

        var url = $(this).data('url'),
            inputRegion = $popup.find('#jscity'),
            regionName = inputRegion.val();

		if (url) {
			changeRegionAction(regionName, url);
		} else {
			// SITE-5123
			if (ENTER.utils.trim(inputRegion[0].defaultValue) != ENTER.utils.trim(regionName)) {
				queryAutocompleteVariants(regionName, function(res) {
					if (res[0] && res[0].url) {
						location = res[0].url;
					}
				});
			}

			$popup.trigger('close');
		}
    });

    $body.on('click', '.jsChangeRegionLink', function(e){
        changeRegionAction($(this).text(), $(this).attr('href'));
        e.preventDefault();
    });

	if (!isGeoshopCookieSet()) {
		openRegionPopup();
	}
}(jQuery));
/**
 * SITE-2693
 * Показывать окно авторизации, если по аяксу был получен ответ с 403-м статусом
 *
 * @author		Shaposhnik Vitaly
 */
;(function() {
	var authBlock;// блок авторизации

	$.ajaxSetup({
		error : function(jqXHR, textStatus, errorThrown) {
			if ( 403 == jqXHR.status ) {
				authBlock = $('#auth-block');

				if ( !authBlock.length ) {
					return;
				}

				authBlock.lightbox_me({
					centered: true,
					autofocus: true,
					onLoad: function() {
						authBlock.find('input:first').focus();
					}
				});
			}
		}
	});
}());
/**
 * Всплывающая синяя плашка с предложением о подписке
 * Срабатывает при возникновении события showsubscribe.
 *
 * @author		Zaytsev Alexandr
 * @requires	jQuery, FormValidator, docCookies
 *
 * @param		{event}		event
 * @param		{Object}	subscribe			Информация о подписке
 * @param		{Boolean}	subscribe.agreed	Было ли дано согласие на подписку в прошлый раз
 * @param		{Boolean}	subscribe.show		Показывали ли пользователю плашку с предложением о подписке
 */
;(function() {
	var
		body = $('body'),
		subscribeCookieName = 'subscribed';
	// end of vars

	var
		lboxCheckSubscribe = function lboxCheckSubscribe( event ) {
			var
				subPopup = $('.bSubscribeLightboxPopup'),
				input = $('.bSubscribeLightboxPopup__eInput'),
				submitBtn = $('.bSubscribeLightboxPopup__eBtn'),
				subscribe = {
					'show': !window.docCookies.hasItem(subscribeCookieName),
					'agreed': 1 === window.docCookies.getItem(subscribeCookieName)
				},
				inputValidator = new FormValidator({
					fields: [
						{
							fieldNode: input,
							customErr: 'Неправильный емейл',
							require: true,
							validBy: 'isEmail'
						}
					]
				});
			// end of vars

			var
				subscribing = function subscribing() {
					var
						email = input.val(),
						url = $(this).data('url');
					//end of vars

					var
						/**
						 * Обработчик ответа пришедшего с сервера
						 * @param res Ответ с сервера
						 */
							serverResponseHandler = function serverResponseHandler( res ) {
							if( !res.success ) {
								return false;
							}

							subPopup.html('<span class="bSubscribeLightboxPopup__eTitle mType">Спасибо! подтверждение подписки отправлено на указанный e-mail</span>');
							window.docCookies.setItem('subscribed', 1, 157680000, '/');

							setTimeout(function() {
								subPopup.slideUp(300);
							}, 3000);

							// analytics
							if ( typeof _gaq !== 'undefined' ) {
								_gaq.push(['_trackEvent', 'Account', 'Emailing sign up', 'Page top']);
							}

							// subPopup.append('<iframe src="https://track.cpaex.ru/affiliate/pixel/173/'+email+'/" height="1" width="1" frameborder="0" scrolling="no" ></iframe>');
						};
					// end of functions

					if ( submitBtn.hasClass('mDisabled') ) {
						return false;
					}

					inputValidator.validate({
						onInvalid: function( err ) {
							console.log('Email is invalid');
							console.log(err);
						},
						onValid: function() {
							console.log('Email is valid');
							$.post(url, {email: email}, serverResponseHandler);
						}
					});

					return false;
				},

				subscribeNow = function subscribeNow() {
					var
						notNow = $('.bSubscribeLightboxPopup__eNotNow');
					// end of vars

					var
						/**
						 * Обработчик клика на ссылку "Спасибо, не сейчас"
						 * @param e
						 */
							notNowClickHandler = function( e ) {
							e.preventDefault();

							subPopup.slideUp(300);
							window.docCookies.setItem('subscribed', 0, 157680000, '/');
						};
					// end of functions

					subPopup.slideDown(300);

					submitBtn.bind('click', subscribing);

					notNow.off('click');
					notNow.bind('click', notNowClickHandler);
				};
			//end of functions

			input.placeholder();

			if ( !subscribe.show ) {
				return false;
			}
			else {
				subscribeNow();
			}
		};
	// end of functions

	body.bind('showsubscribe', lboxCheckSubscribe);
	body.trigger('showsubscribe');
}());
;(function() {
	$('.js-siteVersionSwitcher').click(function(e){
		e.preventDefault();
		var domain = window.location.host;
		var domainParts = domain.split(".");
        if (domainParts.length > 2) {
            domain = domainParts[domainParts.length - 2] + "." + domainParts[domainParts.length - 1];
        }

		var config = $(e.currentTarget).data('config');
		document.cookie = config.cookieName + "=1; expires=" + (new Date(Date.now() + config.cookieLifetime * 1000)).toUTCString() + "; domain=" + domain + "; path=/";
		location = e.currentTarget.href;
	});
}());

;$(document).ready(function() {
    var $body = $('body');

    /** Событие клика на товар в слайдере */
    $body.on('click', '.jsRecommendedItem', function(event) {

        try {
            var $el = $(this),
                $target = $(event.target),
                link = $el.attr('href'),
                aTarget = $el.attr('target'),
                $slider = $el.parents('.js-slider'),
                sender = $slider.length ? $slider.data('slider').sender : null;

            $body.trigger('TLT_processDOMEvent', [event]);

            if (!$target.hasClass('js-orderButton')) {
				var rrEventLabel = '';
				if (ENTER.config.pageConfig.product) {
					if (ENTER.config.pageConfig.product.isSlot) {
						rrEventLabel = '_marketplace-slot';
					} else if (ENTER.config.pageConfig.product.isOnlyFromPartner) {
						rrEventLabel = '_marketplace';
					}
				}

                $body.trigger('trackGoogleEvent', {
                    category: 'RR_взаимодействие' + rrEventLabel,
                    action: 'Перешел на карточку товара',
                    label: sender ? sender.position : null,
                    hitCallback: aTarget == '_blank' ? null : link
                });
            }

            event.stopPropagation();

        } catch (e) { console.error(e); }
    });

    /** Событие пролистывание в слайдере */
    $body.on('click', '.jsRecommendedSliderNav', function(event) {
        console.log('jsRecommendedSliderNav');

        try {
            var
                $el = $(this),
                $slider = $el.parents('.js-slider'),
                sender = $slider.length ? $slider.data('slider').sender : null
                ;

			var rrEventLabel = '';
			if (ENTER.config.pageConfig.product) {
				if (ENTER.config.pageConfig.product.isSlot) {
					rrEventLabel = '_marketplace-slot';
				} else if (ENTER.config.pageConfig.product.isOnlyFromPartner) {
					rrEventLabel = '_marketplace';
				}
			}

            $body.trigger('trackGoogleEvent',['RR_Взаимодействие' + rrEventLabel, 'Пролистывание', sender.position]);
        } catch (e) { console.error(e); }
    });

    // Запоминает просмотренные товары
    try {
        $('.js-slider').each(function(i, el) {
            var
                data = $(el).data('slider'),
                //rrviewed = docCookies.getItem('rrviewed')
                rrviewed = docCookies.getItem('product_viewed')
            ;

            if (('viewed' == data.type) && typeof rrviewed === 'string') {
                data['rrviewed'] = ENTER.utils.arrayUnique(rrviewed.split(','));

                $(el).data('slider', data);
            }
        });
    } catch (e) {
        console.error(e);
    }

    // попачик для слайдера
    $body.on('mouseenter', '.slideItem_i', function(e) {
        var
            $el = $(this),
            $bubble = $el.parents('.js-slider').find('.slideItem_flt')
        ;

        if ($bubble.length) {
            $bubble.find('.slideItem_flt_i').text($el.data('product').name);
            $bubble.addClass('slideItem_flt-show');
            $bubble.offset({left: $el.offset().left});
        }
    });

    $body.on('mouseleave', '.slideItem_i', function(e) {
        var
            $el = $(this),
            $bubble = $el.parents('.js-slider').find('.slideItem_flt')
        ;

        if ($bubble.length) {
            $bubble.find('.slideItem_flt_i').text('');
            $bubble.removeClass('slideItem_flt-show');
        }
    });
});
;$(function() {
	var
		$body = $('body'),
		errorCssClass = 'lbl-error',
		region = ENTER.config.pageConfig.user.region.name,
		catalogPath = ENTER.utils.getCategoryPath(),

		showError = function($input) {
			var $element = $input.closest('.js-slotButton-popup-element');
			$element.find('.js-slotButton-popup-element-field').addClass(errorCssClass);
			$element.find('.js-slotButton-popup-element-error').show();
		},

		hideError = function($input) {
			var $element = $input.closest('.js-slotButton-popup-element');
			$element.find('.js-slotButton-popup-element-field').removeClass(errorCssClass);
			$element.find('.js-slotButton-popup-element-error').hide();
		},

		validatePhone = function($form, disableFail) {
			var $phoneInput = $('.js-slotButton-popup-phone', $form);

			if (!/\+7\(\d{3}\)\d{3}-\d{2}-\d{2}/.test($phoneInput.val().replace(/\s+/g, ''))) {
				if (!disableFail) {
					showError($phoneInput);
				}

				return false;
			} else {
				hideError($phoneInput);
				return true;
			}
		},

		validateEmail = function($form, disableFail) {
			var $emailInput = $('.js-slotButton-popup-email', $form);

			if ($emailInput.val().length != 0 && !ENTER.utils.validateEmail($emailInput.val())) {
				if (!disableFail) {
					showError($emailInput);
				}

				return false;
			} else {
				hideError($emailInput);
				return true;
			}
		},

		validateConfirm = function($form, disableFail) {
			var $confirmInput = $('.js-slotButton-popup-confirm', $form);

			if (!$confirmInput[0].checked) {
				if (!disableFail) {
					showError($confirmInput);
				}

				return false;
			} else {
				hideError($confirmInput);
				return true;
			}
		},

		validate = function($form) {
			var isValid = true;

			if (!validatePhone($form)) {
				isValid = false;
			}

			if (!validateEmail($form)) {
				isValid = false;
			}

			if (!validateConfirm($form)) {
				isValid = false;
			}

			return isValid;
		};

	$body.on('click', '.js-slotButton', function(e) {
		e.preventDefault();

		var
			$button = $(this),
			sender = $button.data('sender') || {},
			productArticle = $button.data('product-article'),
			productPrice = $button.data('product-price'),
			$popup = $(Mustache.render($('#tpl-cart-slot-form').html(), {
				orderCreateUrl: ENTER.utils.generateUrl('order.slot.create'),
				full: $button.data('full'),
				partnerName: $button.data('partner-name'),
				partnerOfferUrl: $button.data('partner-offer-url'),
				productUrl: $button.data('product-url'),
				productId: $button.data('product-id'),
				sender: $button.attr('data-sender'),
				sender2: $button.data('sender2') || '',
				userPhone: String(ENTER.utils.Base64.decode(ENTER.config.userInfo.user.mobile || '')).replace(/^8/, '+7'),
				userEmail: ENTER.config.userInfo.user.email || '',
				userName: ENTER.config.userInfo.user.firstName || ''
			})),
			$form = $('form', $popup),
			$errors = $('.js-slotButton-popup-errors', $form),
			$phone = $('.js-slotButton-popup-phone', $form),
			$email = $('.js-slotButton-popup-email', $form),
			$name = $('.js-slotButton-popup-name', $form),
			$confirm = $('.js-slotButton-popup-confirm', $form),
			$goToProduct = $('.js-slotButton-popup-goToProduct', $form);

		$popup.lightbox_me({
			centered: true,
			sticky: true,
			closeClick: false,
			closeEsc: false,
			closeSelector: '.js-slotButton-popup-close',
			destroyOnClose: true
		});

		$.mask.definitions['x'] = '[0-9]';
		$.mask.placeholder = "_";
		$.mask.autoclear = false;
		$.map($('input', $popup), function(elem, i) {
			var $elem = $(elem);
			if (typeof $elem.data('mask') !== 'undefined') {
				$elem.mask($elem.data('mask'));
			}
		});

		$phone.blur(function() {
			validatePhone($form);
		});

		$phone.keyup(function() {
			validatePhone($form, true);
		});

		$email.blur(function() {
			validateEmail($form);
		});

		$email.keyup(function() {
			validateEmail($form, true);
		});

		$confirm.click(function() {
			validateConfirm($form, true);
		});

		$form.submit(function(e) {
			e.preventDefault();

			$errors.empty().hide();

			if (!validate($form)) {
				$body.trigger('trackGoogleEvent', ['Воронка_marketplace-slot_' + region, '7_1 Оформить ошибка', catalogPath]);
				return;
			}

			var $submitButton = $('.js-slotButton-popup-submitButton', $form);

			$submitButton.attr('disabled', 'disabled');
			$.ajax({
				type: 'POST',
				url: $form.attr('action'),
				data: $form.serializeArray(),
				success: function(result){
					if (result.error) {
						$errors.text(result.error).show();
						$body.trigger('trackGoogleEvent', ['Воронка_marketplace-slot_' + region, '7_1 Оформить ошибка', catalogPath]);
						return;
					}

					$form.after($(Mustache.render($('#tpl-cart-slot-form-result').html(), {
						orderNumber: result.orderNumber
					})));

					$form.remove();

					$('.js-slotButton-popup-okButton', $popup).click(function() {
						$popup.trigger('close');
					});

					$body.trigger('trackGoogleEvent', ['Воронка_marketplace-slot_' + region, '7 Оформить успешно', catalogPath]);

					if (typeof ENTER.utils.sendOrderToGA == 'function' && result.orderAnalytics) {
						ENTER.utils.sendOrderToGA(result.orderAnalytics);
					}
				},
				error: function(){
					$errors.text('Ошибка при создании заявки').show();
					$body.trigger('trackGoogleEvent', ['Воронка_marketplace-slot_' + region, '7_1 Оформить ошибка', catalogPath]);
				},
				complete: function(){
					$submitButton.removeAttr('disabled');
				}
			})
		});

		ENTER.utils.sendAdd2BasketGaEvent(productArticle, productPrice, true, true, sender.name);

		$body.trigger('trackGoogleEvent', ['Воронка_marketplace-slot_' + region, '1 Вход', catalogPath]);

		$phone.focus(function() {
			$body.trigger('trackGoogleEvent', ['Воронка_marketplace-slot_' + region, '2 Телефон', catalogPath]);
		});

		$email.focus(function() {
			$body.trigger('trackGoogleEvent', ['Воронка_marketplace-slot_' + region, '3 Email', catalogPath]);
		});

		$name.focus(function() {
			$body.trigger('trackGoogleEvent', ['Воронка_marketplace-slot_' + region, '4 Имя', catalogPath]);
		});

		$confirm.click(function(e) {
			if (e.currentTarget.checked) {
				$body.trigger('trackGoogleEvent', ['Воронка_marketplace-slot_' + region, '5 Оферта', catalogPath]);
			}
		});

		$goToProduct.click(function(e) {
			$body.trigger('trackGoogleEvent', ['Воронка_marketplace-slot_' + region, '6 Перейти в карточку', catalogPath]);
		});

		$phone.focus();
	});
});

;(function(){

    // https://jira.enter.ru/browse/SITE-3508
    // SITE-3508 Закрепить товары в листинге чибы

    if (/catalog\/tchibo/.test(document.location.href) && window.history && window.history.replaceState) {

        var history = window.history;

        $(window).on('beforeunload', function () {
            history.replaceState({pageYOffset: pageYOffset}, '');
        });

        if (history && history.state && history.state.pageYOffset) {
            window.scrollTo(0, history.state.pageYOffset);
        }

    }

}());
;(function($) {

    var
        $body = $('body'),

        TLT = (typeof this.TLT === 'object') ? this.TLT : null,

        TLT_logCustomEvent = function(event, TLT_eventName, TLT_eventData) {
            try {
                console.info('TLT_logCustomEvent', TLT_eventName, TLT_eventData);

                TLT.logCustomEvent(TLT_eventName, TLT_eventData);
            } catch (e) {
                console.error(e);
            }
        },

        TLT_processDOMEvent = function(event, originalEvent) {
            try {
                console.info('TLT_processDOMEvent', originalEvent);

                TLT.processDOMEvent(originalEvent);
            } catch (e) {
                console.error(e);
            }
        }
    ;

    //$body.on('TLT_logCustomEvent', TLT_logCustomEvent);
    $body.on('TLT_processDOMEvent', TLT_processDOMEvent);

})(jQuery);
/**
 * Кнопка наверх
 *
 * @requires	jQuery
 * @author		Zaytsev Alexandr
 */
;(function(){
	var
		$body = $('body'),
		$window = $(window),
		$upper = $('.js-upper'),
		visible = false,
		offset = $upper.data('offset'),
		showWhenFullCartOnly = $upper.data('showWhenFullCartOnly');

	if (typeof offset == 'string') {
		var $offset = $(offset);
		if ($offset.length) {
			offset = $offset.offset().top;
		}
	}

	function checkScroll() {
		var cartLength = ENTER.UserModel.cart().length;
		if (!visible && $window.scrollTop() > offset && (!showWhenFullCartOnly || cartLength)) {
			//появление
			visible = true;
			$upper.fadeIn(400);
		} else if (visible && ($window.scrollTop() < offset || showWhenFullCartOnly && !cartLength)) {
			//исчезновение
			visible = false;
			$upper.fadeOut(400);
		}
	}

	$upper.bind('click', function() {
		$window.scrollTo('0px',400);
		return false;
	});

	$window.scroll(checkScroll);

	// Если showWhenFullCartOnly = true, то проверку надо выполнять лишь после того, как станут доступны данные корзины (которые становятся доступны после userLogged)
	$body.on('userLogged closeBuyInfo showBuyInfo', function(){
		checkScroll();
	});
}());
/**
 * White floating user bar
 *
 *
 * @requires jQuery, ENTER.utils, ENTER.config
 * @author	Zaytsev Alexandr
 *
 * @param	{Object}	ENTER	Enter namespace
 */
;(function( ENTER ) {
	var
		utils = ENTER.utils,

		userBar = utils.extendApp('ENTER.userBar'),

		userBarFixed = userBar.userBarFixed = $('.js-topbar-fixed'),
		userbarStatic = userBar.userBarStatic = $('.js-topbar-static'),

		emptyCompareNoticeElements = {},
		emptyCompareNoticeShowClass = 'topbarfix_cmpr_popup-show',

		topBtn = userBarFixed.find('.js-userbar-upLink'),
		userbarConfig = userBarFixed.data('value'),
		$body = $('body'),
		w = $(window),
		buyInfoShowing = false,
		overlay = $('<div>').css({ position: 'fixed', display: 'none', width: '100%', height:'100%', top: 0, left: 0, zIndex: 900, background: 'black', opacity: 0.4 }),

		scrollTarget,
		filterTarget,
		showWhenFullCartOnly = userbarConfig && userbarConfig.showWhenFullCartOnly;
	// end of vars

	userBar.showOverlay = false;

	/**
	 * Показ юзербара
	 */
	function showUserbar(disableAnimation, onOpen) {
		$.each(emptyCompareNoticeElements, function(){
			this.removeClass(emptyCompareNoticeShowClass);
		});

		if (disableAnimation) {
			userBarFixed.show(0, onOpen || function(){});
		} else {
			userBarFixed.slideDown();
		}

		if (userBarFixed.length) {
			userbarStatic.css('visibility','hidden');
		}
	}

	/**
	 * Скрытие юзербара
	 */
	function hideUserbar() {
		userBarFixed.slideUp();
		userbarStatic.css('visibility','visible');
	}

	/**
	 * Проверка текущего скролла
	 */
	function checkScroll(hideOnly) {
		if ( buyInfoShowing ) {
			return;
		}

		if (scrollTarget && scrollTarget.length && w.scrollTop() >= scrollTarget.offset().top && !hideOnly && (!showWhenFullCartOnly || ENTER.UserModel.cart().length)) {
			showUserbar();
		}
		else {
			hideUserbar();
		}
	}

	/**
	 * Прокрутка до фильтра и раскрытие фильтров
	 */
	function upToFilter() {
		$.scrollTo(filterTarget, 500);
		ENTER.catalog.filter.openFilter();

		return false;
	}

	/**
	 * Закрытие окна о совершенной покупке
	 */
	function closeBuyInfo() {
		var
			wrap = userBarFixed.find('.topbarfix_cart'),
			wrapLogIn = userBarFixed.find('.topbarfix_log'),
			openClass = 'mOpenedPopup',
			upsaleWrap = wrap.find('.hintDd');
		// end of vars

		$body.trigger('closeBuyInfo');

		/**
		 * Удаление выпадающей плашки для корзины
		 */
		function removeBuyInfoBlock() {
			var
				buyInfo = $('.topbarfix_cartOn');
			// end of vars

			if ( !buyInfo.length ) {
				return;
			}

			buyInfo.slideUp(300, function() {
				buyInfo.removeAttr('style');
			});
		}

		/**
		 * Удаление Overlay блока
		 */
		function removeOverlay() {
			if (!overlay || !userBar.showOverlay) {
				checkScroll();
				return;
			}

			overlay.fadeOut(100, function() {
				overlay.off('click');
				overlay.remove();
				userBar.showOverlay = false;
				buyInfoShowing = false;
				checkScroll();
			});
		}
		// end of function

		setTimeout(function() {
			userBarFixed.removeClass('userbar--show');
		}, 100);

		// только BuyInfoBlock
		if ( !upsaleWrap.hasClass('mhintDdOn') ) {
			removeBuyInfoBlock();
			removeOverlay();
			return;
		}

		upsaleWrap.removeClass('mhintDdOn');
		wrapLogIn.removeClass(openClass);
		wrap.removeClass(openClass);

		removeBuyInfoBlock();
		removeOverlay();
		return false;
	}

	/**
	 * Показ окна о совершенной покупке
	 */
	function showBuyInfo( e, data, upsale ) {
		console.info('userbar::showBuyInfo');

		$body.trigger('showBuyInfo');

		$.each(emptyCompareNoticeElements, function(){
			this.removeClass(emptyCompareNoticeShowClass);
		});

		userBarFixed.addClass('userbar--show');

		var	buyInfo = $('.topbarfix_cartOn');

		if ( !userBar.showOverlay && overlay ) {
			$body.append(overlay);
			overlay.fadeIn(300);
			userBar.showOverlay = true;
			overlay.on('click', closeBuyInfo);
		}

		if ( e ) {
			buyInfo.slideDown(300);
		}
		else {
			buyInfo.show();
		}

		showUserbar(true);
		if (upsale) {
			showUpsell(data, upsale);
		}

		buyInfoShowing = true;
		$(document.body).trigger('showUserCart');
	}

	/**
	 * Удаление товара из корзины
	 */
	function deleteProductHandler() {
		console.log('deleteProductHandler click!');

		var btn = $(this);
		// end of vars

		$.ajax({
			type: 'GET',
			url: btn.attr('href'),
			success: function( res, data ) {
				console.warn( res );
				if ( !res.success ) {
					console.warn('удаление не получилось :(');

					return;
				}

				ENTER.UserModel.removeProductByID(res.product.id);

				// Удаляем товар на странице корзины
				$('.js-basketLineDeleteLink-' + res.product.id).click();

				if (ENTER.UserModel.cart().length == 0) {
					closeBuyInfo();
				} else {
					showBuyInfo();
				}

				$body.trigger('removeFromCart', [res.product]);
			}
		});

		return false;
	}

	/**
	 * Обновление блока с рекомендациями "С этим товаром покупают"
	 *
	 * @param	{Object}	data	Данные о покупке
	 * @param	{Object}	upsale
	 */
	function showUpsell( data, upsale ) {
		console.info('userbar::showUpsell');

		var
			cartWrap = userBarFixed.find('.topbarfix_cart'),
			upsaleWrap = cartWrap.find('.hintDd'),
			slider;
		// end of vars

		function responseFromServer( response ) {
			console.log(response);

			if ( !response.success || !userBar.showOverlay ) {
				return;
			}

			console.info('Получены рекомендации "С этим товаром покупают" от RetailRocket');

			upsaleWrap.find('.js-slider').remove();

			slider = $(response.content)[0];
			upsaleWrap.append(slider);
			upsaleWrap.addClass('mhintDdOn');
			$(slider).goodsSlider();

			ko.applyBindings(ENTER.UserModel, slider);

			if ( !data.product ) return;

			if ( !data.product.article ) {
				console.warn('Не получен article продукта');

				return;
			}

			console.log('Трекинг товара при показе блока рекомендаций');

			// Retailrocket. Показ товарных рекомендаций
			if ( response.data ) {
				try {
					rrApi.recomTrack(response.data.method, response.data.id, response.data.recommendations);
				} catch( e ) {
					console.warn('showUpsell() Retailrocket error');
					console.log(e);
				}
			}

			// google analytics
			typeof _gaq == 'function' && _gaq.push(['_trackEvent', 'cart_recommendation', 'cart_rec_shown', data.product.article]);
		}

		console.log(upsale);

		if ( !upsale.url ) {
			console.log('if upsale.url');
			return;
		}

		var
			url = upsale.url,
			sender2 = '';

		if (ENTER.config.pageConfig.product) {
			if (ENTER.config.pageConfig.product.isSlot) {
				sender2 = 'slot';
			} else if (ENTER.config.pageConfig.product.isOnlyFromPartner) {
				sender2 = 'marketplace';
			}
		}

		if (sender2) {
			url = ENTER.utils.setURLParam('sender2', sender2, url);
		}

		$.ajax({
			type: 'GET',
			url: url,
			success: responseFromServer
		});
	}

	/**
	 * Обработчик клика по товару из списка рекомендаций
	 */
	function upsaleProductClick() {
		var
			product = $(this).parents('.jsSliderItem').data('product');
		//end of vars

		if ( !product.article ) {
			console.warn('Не получен article продукта');

			return;
		}

		console.log('Трекинг при клике по товару из списка рекомендаций');
		// google analytics
		_gaq && _gaq.push(['_trackEvent', 'cart_recommendation', 'cart_rec_clicked', product.article]);

		//window.docCookies.setItem('used_cart_rec', 1, 1, 4*7*24*60*60, '/');
	}

	function showEmptyCompareNotice(e, emptyCompareNoticeName, $userbar) {
		e.stopPropagation();
		if (!emptyCompareNoticeElements[emptyCompareNoticeName]) {
			var element = $('.js-compare-popup', $userbar);

			$('.js-compare-popup-closer', element).click(function() {
				element.removeClass(emptyCompareNoticeShowClass);
			});

			$('.js-topbarfixLogin, .js-topbarfixNotEmptyCart', $userbar).mouseover(function() {
				element.removeClass(emptyCompareNoticeShowClass);
			});

			$('html').click(function() {
				element.removeClass(emptyCompareNoticeShowClass);
			});

			$(element).click(function(e) {
				e.stopPropagation();
			});

			$(document).keyup(function(e) {
				if (e.keyCode == 27) {
					element.removeClass(emptyCompareNoticeShowClass);
				}
			});

			emptyCompareNoticeElements[emptyCompareNoticeName] = element;
		}

		if (!$('.mhintDdOn').length){
			emptyCompareNoticeElements[emptyCompareNoticeName].addClass(emptyCompareNoticeShowClass);
		}

	}

	console.info('Init userbar module');
	console.log(userbarConfig);

	userBar.show = showUserbar;

	$body.on('click', '.jsUpsaleProduct', upsaleProductClick);
	$body.on('click', '.jsCartDelete', deleteProductHandler);

	$('.js-noProductsForCompareLink', userBarFixed).click(function(e) { showEmptyCompareNotice(e, 'fixed', userBarFixed); });
	$('.js-noProductsForCompareLink', userbarStatic).click(function(e) { showEmptyCompareNotice(e, 'static', userbarStatic); });

	if ( userBarFixed.length ) {
		if (window.location.pathname !== '/cart') $body.on('addtocart', showBuyInfo);
		scrollTarget = $(userbarConfig.target);

		if (userbarConfig.filterTarget) {
			filterTarget = $(userbarConfig.filterTarget);
		} else {
			filterTarget = scrollTarget;
		}

		if ( topBtn.length ) {
			topBtn.on('click', upToFilter);
		}

		if ( scrollTarget.length ) {
			w.on('scroll', function(){ checkScroll(); });
		} else {
			w.on('scroll', function(){ checkScroll(true); });
		}

		// Если showWhenFullCartOnly = true, то проверку надо выполнять лишь после того, как станут доступны данные корзины (которые становятся доступны после userLogged)
		$body.on('userLogged', function(){
			checkScroll();
		});
	}
	else {
		overlay.remove();
		overlay = false;
	}

}(window.ENTER));
