/**
 * Карты лояльности
 *
 * @author    Shaposhnik Vitaly
 * @requires  jQuery
 */
(function($) {
	var
		body = $('body'),
		bonusCard = $('.jsBonusCard'),
		sclubId,
		sclubEditUrl,
		data,
		sclubCookieName = 'scid',
		userNumber; // номер с пользовательских данных
	// end of vars

	var
		cardChangeHandler = function cardChangeHandlerF() {
			var
				newCardData,
				cardIndex,
				activeCard = $('.jsActiveCard'),
				activeCardNumber = $('.jsActiveCard .jsCardNumber'),
				activeCardDescription = $('.jsActiveCard .jsDescription');
			// end of vars

			var
				changeCardImage = function changeCardImageF() {
					if ( !activeCard.length || !newCardData.hasOwnProperty('image') ) {
						return;
					}

					activeCard.css('background', 'url(' + newCardData.image + ') 260px -3px no-repeat');
				},

				changeCardMask = function changeCardMaskF() {
					if ( !activeCardNumber.length || !newCardData.hasOwnProperty('mask') ) {
						return;
					}

					activeCardNumber.attr('placeholder', newCardData.mask);
					activeCardNumber.mask(newCardData.mask, {placeholder: '*'});
				},

				changeCardDescription = function changeCardDescriptionF() {
					if ( !activeCardDescription.length || !newCardData.hasOwnProperty('description') ) {
						return;
					}

					activeCardDescription.text(newCardData.description);
				},

				changeCardValue = function changeCardValueF() {
					if ( !activeCardNumber.length || !newCardData.hasOwnProperty('value') ) {
						return;
					}

					activeCardNumber.val(newCardData.value);
				};
			// end of function

			if ( !activeCard.length ) {
				return;
			}

			cardIndex = $('.jsBonusCard .jsCard').index(this);
			if ( -1 == cardIndex ) {
				return;
			}

			if ( !data.hasOwnProperty(cardIndex) ) {
				return;
			}

			newCardData = data[cardIndex];

			console.info('cardChange');
			console.log(newCardData);

			changeCardValue();
			changeCardImage();
			changeCardMask();
			changeCardDescription();
		},

		setDefaults = function setDefaultsF() {
			var
				activeCardNumber = $('.jsActiveCard .jsCardNumber');
			// end of vars

			var
				setMask = function setMaskF() {
					if ( !data[0].hasOwnProperty('mask') ) {
						return;
					}

					activeCardNumber.mask(data[0].mask, {placeholder: '*'});
				},

				setValue = function setValueF() {
					if ( !data[0].hasOwnProperty('value') ) {
						return;
					}

					activeCardNumber.val(data[0].value);
				};
			// end of functions

			if ( !activeCardNumber.length || !data || !data.hasOwnProperty(0) ) {
				return;
			}

			console.info('setDefaults');
			console.log(data[0]);

			setValue();
			setMask();
		},

		sclub = {
			init: function () {
				sclubId = bonusCard.data('sclub-id');
				sclubEditUrl = bonusCard.data('sclub-edit-url');

				// если пользователь не авторизован ничего не делаем
				if ( ENTER.config.userInfo === false ) return;

				if ( !ENTER.config.userInfo ) {
					$("body").on("userLogged", function() {sclub.action(ENTER.config.userInfo)} );
				}
				else {
					// событие "userLogged" уже произошло
					console.log(ENTER.config.userInfo);
					sclub.action(ENTER.config.userInfo);
				}

				body.on('change', '.jsBonusCard .jsCard', sclub.message);
			},

			action: function (userInfo) {
				if (
					typeof userInfo.id === "undefined" || // не пришли пользовательские данные
					!userInfo.hasOwnProperty('sclubNumber') || // не передан номер
					!window.docCookies.getItem(sclubCookieName)
				) {
					return;
				}

				userNumber = userInfo.sclubNumber;

				// выводим сообщение
				sclub.message();
			},

			/**
			 * Номер Связного в личном кабинете равен номеру переданом от sclub (через get-параметр scid)
			 */
			isNumbersEqual: function (userNumber, cookieNumber) {
				return userNumber == cookieNumber;
			},

			/**
			 * Показать сообщение для карты Связного-клуба
			 */
			message: function () {
				var message, linkYes, linkNo;

				var
					showMessage = function () {
						var
							sclubMsgBlock = $('.jsBonusCard .jsCardMessage .sclub-message'),
							cardNumberField = $('.jsActiveCard .jsCardNumber'),
							msgText;

						if ( sclubMsgBlock.length ) {
							hideMessage();
						}

						msgText = userNumber ? 'Номер карты в ЛК и пришедшим от sclub не совпадают. Заменить номер в ЛК?' : 'Сохранить номер в ЛК?';

						message = $('<div/>', {class: 'sclub-message', text: msgText});

						// linkYes
						message.append('&nbsp;');
						linkYes = $('<a/>', {href: sclubEditUrl, text: 'Да'});
						linkYes.click(function ( e ) {
							e.preventDefault();

							$.post(this.href, {number: window.docCookies.getItem(sclubCookieName)}, function ( res ) {
								if ( !res.success ) {
									if ( res.error ) {
										$('.jsBonusCard .jsCardMessage .sclub-message').html(res.error).addClass('error');
									}

									if ( res.hasOwnProperty('code') && 735 == res.code ) {// 735 - Невалидный номер карты
										window.docCookies.removeItem(sclubCookieName, '/');

										if ( cardNumberField.length ) {
											cardNumberField.val(userNumber).mask(cardNumberField.attr('placeholder'), {placeholder: '*'});
										}

										// очищаем значение с данных по умолчанию
										if ( sclubId ) {
											for ( var i = 0; i < data.length; i++ ) {
												if ( sclubId != data[i].id ) continue;
												data[i].value = userNumber;
											}
										}
									}

									return;
								}

								window.docCookies.removeItem(sclubCookieName, '/');

								hideMessage();
							});
						});
						linkYes.appendTo(message);

						// linkNo
						if ( userNumber ) {
							message.append('&nbsp;');
							linkNo = $('<a/>', {href: sclubEditUrl, text: 'Нет'});
							linkNo.click(function ( e ) {
								e.preventDefault();

								window.docCookies.removeItem(sclubCookieName, '/');

								if ( cardNumberField.length && userNumber ) {
									cardNumberField.val(userNumber).mask(cardNumberField.attr('placeholder'), {placeholder: '*'});
								}

								if ( sclubId ) {
									for ( var i = 0; i < data.length; i++ ) {
										if ( sclubId != data[i].id ) continue;
										data[i].value = userNumber;
									}
								}

								hideMessage();
							});
							linkNo.appendTo(message);
						}

						message.appendTo('.jsBonusCard .jsCardMessage');
					},

					hideMessage = function () {
						var sclubMsgBlock = $('.jsBonusCard .jsCardMessage .sclub-message');

						if ( !sclubMsgBlock.length ) return;
						sclubMsgBlock.remove();
					};
				// end of functions

				// если не выбрана карта Связного, то скрывем сообщение
				if ( sclubId != $('input[name="order[bonus_card_id]"]:checked', '.jsBonusCard').val() ) {
					hideMessage();
					return;
				}

				if ( !window.docCookies.getItem(sclubCookieName) ) {
					return;
				}

				// номера идентичны, ничего не делаем
				if (true === sclub.isNumbersEqual(userNumber, window.docCookies.getItem(sclubCookieName))) {
					return;
				}

				showMessage();
			}
		};
	// end of functions

	if ( !bonusCard.length ) {
		return;
	}

	data = bonusCard.data('value');
	if ( !data.length ) {
		return;
	}

	console.groupCollapsed('BonusCard');

	$.mask.definitions['x'] = '[0-9]';
	setDefaults();

	// sclub
	sclub.init();

	body.on('change', '.jsBonusCard .jsCard', cardChangeHandler);
	console.groupEnd();

})(jQuery);
/**
 * Order delivery address
 *
 * @author  Shaposhnik Vitaly
 */

;(function ( window, document, $, ENTER ) {
	var
		utils = ENTER.utils,
		config = $('#page-config').data('value'),
		orderData = $('#jsOrderForm').data('value'),
		subwayArray = $('#metrostations').data('name'),

		data = $('#jsDeliveryAddress').data('value'),

		active = config ? config.addressAutocomplete : false,

		token = null,
		key = null,
		limit = 6,

		street = $('#order_address_street'),
		building = $('#order_address_building'),
		buildingAdd = $('#order_address_number'),
		metro = $('#order_address_metro'),
		metroIdFiled = $('#order_subway_id'),

		error,

		map = null,
		map_created = false,

		cityName = data ? data.regionName : '',
		cityId,

		mapObj = $('#map'),
		currentAddr = null;
	// end of vars

	var
		/**
		 * Получение адреса
		 */
		getAddress = function() {
			var
				zoom = 12,
				address = '',
				name,
				type,
				obj,
				value;
			// end of vars

			// Город
			name = $.trim(cityName);
			type = 'город';

			if ( name ) {
				if ( address ) {
					address += ', ';
				}

				address += type + ' ' + name;
				zoom = 12;
			}

			// Улица
			name = null;
			type = null;
			obj = street.kladr('current');
			value = $.trim(street.val());

			if ( obj ) {
				name = obj.name;
				type = obj.type;
			}
			else if ( value ) {
				name = value;
				type = '';
			}

			if ( name ) {
				if ( address ) {
					address += ', ';
				}

				address += type + ' ' + name;
				zoom = 14;
			}

			// Дом
			name = null;
			type = null;
			obj = building.kladr('current');
			value = $.trim(building.val());

			if ( obj ) {
				name = obj.name;
				type = 'дом';
			}
			else if ( value ) {
				name = value;
				type = 'дом';
			}

			if ( name ) {
				if ( address ) {
					address += ', ';
				}

				address += type + ' ' + name;
				zoom = 16;
			}

			// Корпус
			name = null;
			type = null;
			value = $.trim(buildingAdd.val());

			if ( value ) {
				name = value;
				type = 'корпус';
			}

			if ( name ) {
				if ( address ) {
					address += ', ';
				}

				address += type + ' ' + name;
				zoom = 16;
			}

			return {address: address, zoom: zoom};
		},


		/**
		 * Обновление карты
 		 */
		mapUpdate = function(){
			var
				geocode,
				position,
				latitude, longitude,
				addrData = getAddress();
			// end of vars

			if ( !addrData.address || !map_created ) {
				return;
			}

			if ( currentAddr === addrData.address ) {
				return;
			}

			console.log(addrData.address);
			currentAddr = addrData.address;

			geocode = ymaps.geocode(addrData.address);
			geocode.then(function( res ) {
				position = res.geoObjects.get(0).geometry.getCoordinates();

				if ( !position ) {
					return;
				}

				latitude = position[0];
				longitude = position[1];

				map.points = [];
				map.mapWS.geoObjects.each(function (geoObject) {
					map.mapWS.geoObjects.remove(geoObject);
				});

				map.points.push({latitude: latitude, longitude: longitude});
				map._showMarkers();
				map.mapWS.setCenter(position, addrData.zoom);

				if ( metro.length ) {
					metroClosest(latitude, longitude);
				}
			});
		},


		/**
		 * Поиск ближайших станций метро
		 */
		metroClosest = function( latitude, longitude ) {
			var
				myGeocoder,
				nearest,
				name;
			// end of vars

			if ( !metroIdFiled.length ) {
				return;
			}

			myGeocoder = ymaps.geocode([latitude, longitude], {kind: 'metro'});
			myGeocoder.then(
				function ( res ) {
					nearest = res.geoObjects.get(0);
					name = nearest.properties.get('name');
					name = name.replace('метро ', '');

					metro.val(name);
					metroIdFiled.val('');

					// Снимаем маркировку с поля "Метро"
					utils.orderValidator && utils.orderValidator._unmarkFieldError(metro);

					if ( subwayArray !== undefined ) {
						for ( var i = subwayArray.length - 1; i >= 0; i-- ) {
							if ( name === subwayArray[i].label ) {
								metroIdFiled.val(subwayArray[i].val);

								break;
							}
						}
					}

				},
				function ( err ) {
					console.warn('При выполнении запроса произошла ошибка: ' + err);

					metro.val('');
					metroIdFiled.val('');

					// маркируем поле "Метро"
					utils.orderValidator && utils.orderValidator._markFieldError(metro, "Не выбрана станция метро");
				}
			);
		},


		/**
		 * Показ сообщений об ошибках
		 *
		 * @param   {String}    msg     Сообщение которое необходимо показать пользователю
		 */
		showError = function( msg ) {
			error = $('ul.error_list');

			if ( error.length ) {
				error.html('<li>' + msg + '</li>');
			}
			else {
				$('#map').before($('<ul class="error_list" />').append('<li>' + msg + '</li>'));
			}

			return false;
		},


		/**
		 * Убрать сообщения об ошибках
		 */
		removeErrors = function() {
			error = $('ul.error_list');

			if ( error.length ) {
				error.html('');
			}

			return false;
		},


		/**
		 * Задаем обект для поля дом
		 *
		 * @param	{String}	value	Значение поля которое будет запрашиваться у kladr-а
		 */
		setBuilding = function(value) {
			if ( '' !== $.trim(value) ) {
				return;
			}

			$.kladr.api(
				{
					token: building.kladr('token'),
					key: building.kladr('key'),
					type: building.kladr('type'),
					name: value,
					parentType: building.kladr('parentType'),
					parentId: building.kladr('parentId'),
					limit: 1
				},
				function( objs ){
					if ( !objs.length ) {
//						showError('Не нашли ваш адрес на карте. Уточните');

						return;
					}

					removeErrors();

					// задаем обект для дома
					building.kladr('current', objs[0]);
				}
			);
		}

		/**
		 * Задаем обекты для значений по умолчанию
		 */
		defaultValues = function() {
			if ( '' == $.trim(orderData['order[address_street]']) ) {
				return;
			}

			$.kladr.api(
				{
					token: token,
					key: key,
					type: $.kladr.type.street,
					name: orderData['order[address_street]'],
					parentType: $.kladr.type.city,
					parentId: cityId,
					limit: 1
				},
				function( objs ){
					if ( !objs.length ) {
//						showError('Не нашли ваш адрес на карте. Уточните');

						return;
					}

					removeErrors();

					// задаем обект для города
					street.kladr('current', objs[0]);
					building.kladr('parentType', $.kladr.type.street);
					building.kladr('parentId', objs[0].id);

					// задаем обект для дома
					setBuilding(orderData['order[address_building]']);
				}
			);
		},


		/**
		 * Обработчики полей
		 *
		 * @type {{city: Function, street: Function, building: Function, buildingAdd: Function}}
		 */
		fieldsHandler = {
			/**
			 * Получаем ID города в kladr
			 */
			city: function() {
				if ( !cityName ) {
					return null;
				}

				$.kladr.api(
					{
						token: token,
						key: key,
						type: $.kladr.type.city,
						name: cityName,
						limit: 1
					},
					function( objs ) {
						if ( !objs.length ) {
							console.log('КЛАДР не нашел город ' + cityName);
//							showError('Не нашли ваш адрес на карте. Уточните');

							return;
						}

						cityId = objs[0].id;

						fieldsHandler.street();
						fieldsHandler.building();
						fieldsHandler.buildingAdd();

						street.kladr('parentType', $.kladr.type.city);
						street.kladr('parentId', cityId);

						defaultValues();
					}
				);
			},

			/**
			 * Подключение плагина для поля ввода улицы
			 */
			street: function() {
				street.kladr({
					token: token,
					key: key,
					type: $.kladr.type.street,
					verify: true,
					limit: limit,
					source: function( query ) {
						$.kladr.api(
							{
								token: street.kladr('token'),
								key: street.kladr('key'),
								type: street.kladr('type'),
								name: query,
								parentType: street.kladr('parentType'),
								parentId: street.kladr('parentId'),
								limit: street.kladr('limit')
							},
							function( objs ){
								var
									items = [],
									i,
									obj;
								// end of vars

								if ( !objs.length ) {
//									showError('Не нашли ваш адрес на карте. Уточните');
									return;
								}

								removeErrors();

//								if ( street.val() !== objs[0].name ) {
//									showError('Не нашли ваш адрес на карте. Уточните');
//								}

								street.kladr('current', objs[0]);
								building.kladr('parentType', $.kladr.type.street);

								if ( objs[0].id !== building.kladr('parentId') ) {
									building.kladr('parentId', objs[0].id);

									// пытаемся задать обект для дома
									setBuilding(building.val());
								}
								mapUpdate();

								if ( 1 === objs.length ) {
									return;
								}

								// подготовка данных для autocomplete
								for ( i in objs ) {
									obj = objs[i];
									obj.label = obj.type + ' ' + obj.name;
									obj.value = obj.name;
									items.push(obj);
								}

								street.autocomplete({
									source: items,
									appendTo: '.jsInputStreet',
									minLength: 2,
									select : function( event, ui ) {
										removeErrors();

										street.kladr('current', ui.item);
										building.kladr('parentType', $.kladr.type.street);

										if ( ui.item.id !== building.kladr('parentId') ) {
											building.kladr('parentId', ui.item.id);

											// пытаемся задать обект для дома
											setBuilding(building.val());
										}

										mapUpdate();
									}
								});
							}
						);
					}
				});
			},

			/**
			 * Подключение плагина для поля ввода номера дома
			 */
			building: function() {
				building.kladr({
					token: token,
					key: key,
					type: $.kladr.type.building,
					verify: true,
					limit: limit,
					source: function( query ) {
						$.kladr.api(
							{
								token: building.kladr('token'),
								key: building.kladr('key'),
								type: building.kladr('type'),
								name: query,
								parentType: building.kladr('parentType'),
								parentId: building.kladr('parentId'),
								limit: building.kladr('limit')
							},
							function( objs ){
								var
									items = [],
									i,
									obj;
								// end of vars

								if ( !objs.length ) {
//									showError('Не нашли ваш адрес на карте. Уточните');
									return;
								}

								removeErrors();

//								if ( building.val() !== objs[0].name ) {
//									showError('Не нашли ваш адрес на карте. Уточните');
//								}

								// задаем обект для дома
								building.kladr('current', objs[0]);
								mapUpdate();

								if ( 1 === objs.length ) {
									return;
								}

								// подготовка данных для autocomplete
								for ( i in objs ) {
									obj = objs[i];
									obj.label = obj.name;
									items.push(obj);
								}

								building.autocomplete({
									source: items,
									appendTo: '.jsInputBuilding',
									minLength: 0,
									select : function( event, ui ) {
										removeErrors();

										building.kladr('current', ui.item);
										mapUpdate();
									}
								});
							});
					}
				});
			},

			/**
			 * Проверка названия корпуса
			 */
			buildingAdd: function() {
				buildingAdd.change(function(){
					mapUpdate();
				});
			}
		},


		/**
		 * Инициализация полей
		 */
		fieldsInit = function() {
			fieldsHandler.city();
		},


		/**
		 * Создание карты
		 */
		mapCreate = function() {
			var
				cityGeocoder,
				position,
				addrData = getAddress();
			// end of vars

			if ( map_created || !mapObj.length ) {
				return;
			}

			currentAddr = addrData.address;

			mapObj.show().width(477).height(350);
			map_created = true;

			cityGeocoder = ymaps.geocode(addrData.address);
			cityGeocoder.then(
				function ( res ) {
					position = res.geoObjects.get(0).geometry.getCoordinates();
					map = new ENTER.constructors.CreateMap('map', [{latitude: position[0], longitude: position[1]}]);

					map.points = [];
					map.mapWS.geoObjects.each(function (geoObject) {
						map.mapWS.geoObjects.remove(geoObject);
					});

					if ( '' !== $.trim(orderData['order[address_street]']) ) {
						map.points.push({latitude: position[0], longitude: position[1]});
					}

					map._showMarkers();
					map.mapWS.setCenter(position, addrData.zoom);
				},
				function ( err ) {
					// обработка ошибки
					console.log(err);
					showError('Не нашли ваш город на карте.');
					map = new ENTER.constructors.CreateMap('map', [{latitude: 55.76, longitude: 37.64}]);
				}
			);
		};
	// end of functions


	if ( !active ) {
		return;
	}

	if (data && data.kladr) {
		token = data.kladr.token ? data.kladr.token : null;
		key = data.kladr.key ? data.kladr.key : null;
		limit = data.kladr.itemLimit ? data.kladr.itemLimit : 6;
	}

	fieldsInit();
	$('body').bind('orderdeliverychange', function() {ymaps.ready(mapCreate)});

}(this, this.document, this.jQuery, this.ENTER));
;(function ( window, document, $, ENTER, ko ) {
	var
		constructors = ENTER.constructors,
		utils = ENTER.utils,
		OrderModel,
		pageConfig = ENTER.config.pageConfig,
		prepayment = pageConfig.prepayment,
        $body = $(document.body);
	// end of vars

	console.info('deliveryBox.js init');
	console.log(ENTER.OrderModel);

	constructors.DeliveryBox = (function() {
		'use strict';
	
		/**
		 * Создает блок доставки.
		 * Если для товара недоступна выбранная точка доставки, создается новый блок
		 * Стоимость блока расчитывается из суммы всех товаров.
		 * Стоимость доставки считается минимальная из стоимостей доставок всех товаров в блоке
		 *
		 * @author	Zaytsev Alexandr
		 * 
		 * @this	{DeliveryBox}
		 * 
		 * @param	{Array}			products			Массив продуктов которые необходимо добавить в блок
		 * @param	{String}		state				Текущий метод доставки для блока
		 * @param	{Number}		choosenPointForBox	Выбранная точка доставки
		 * 
		 * @constructor
		 */
		function DeliveryBox( products, state, choosenPointForBox ) {
			
			// enforces new
			if ( !(this instanceof DeliveryBox) ) {
				return new DeliveryBox(products, state, choosenPointForBox);
			}
			// constructor body
			
			console.info('Cоздание блока доставки %s (state) для %s (choosenPointForBox)', state, choosenPointForBox, this);


			OrderModel = ENTER.OrderModel;

			var 
				self = this;
			// end of vars

			// Уникальность продуктов в этом типе доставки
			//self.isUnique = isUnique || false;
			self.isUnique = OrderModel.orderDictionary.isUniqueDeliveryState(state);
			// Токен блока
			self.token = state+'_'+choosenPointForBox;
			/*if (self.isUnique) {
				self.token += self.addUniqueSuffix();
			}*/

			// Продукты в блоке
			self.products = [];
			// Общая стоимость товаров в блоке
			self.fullPrice = ko.observable(0);
			// Полная стоимость блока с учетом доставки
			self.totalBlockSum = 0;
			// Метод доставки
			self.state = state;
			// Название метода доставки
			self.deliveryName = OrderModel.orderDictionary.getNameOfState(state);
			// Стоимость доставки. Берем минимально возможное значение, чтобы сравнивая с ним находить максимальное
			self.deliveryPrice = Number.NEGATIVE_INFINITY;

			// Выбранная дата доставки
			self.choosenDate = ko.observable();

			self.choosenNameOfWeek = ko.observable();
			// Выбранная точка доставки
			self.choosenPoint = ko.observable({id:choosenPointForBox});
			// Выбранный интервал доставки
			self.choosenInterval = ko.observable();

			self.showPopupWithPoints = ko.observable(false);

			// Есть ли доступные точки доставки
			self.hasPointDelivery = OrderModel.orderDictionary.hasPointDelivery(state);

			// Стоимость заказа равна или больше напр. 100 тыс. руб.
			self.isExpensiveOrder = ko.computed(function(){
                if ( prepayment.enabled ) {
                    // отображение/скрытие блока предоплаты
                    return prepayment.priceLimit <= (parseInt(self.fullPrice(), 10) + parseInt(self.deliveryPrice, 10)) ? true : false;
                } else return false;
            });

			// Есть ли в заказе товар, требующий предоплату (шильдик предоплата)
			self.hasProductWithPrepayment = false;

			// Массив всех доступных дат для блока
			self.allDatesForBlock = ko.observableArray([]);
			// Массив всех точек доставок
			self.pointList = [];

			// Название пункта — магазина, постамата или тп
			//self.point_name = ''; // здесь не нужно это поле здесь (но в ядро передавать нужно)


			// Текст на кнопки смены точки доставки
			self.changePointButtonText = OrderModel.orderDictionary.getChangeButtonText(state);


			if ( self.hasPointDelivery && !OrderModel.orderDictionary.getPointByStateAndId(self.state, choosenPointForBox) ) {
				// Доставка в выбранный пункт
				console.info('есть точки доставки для выбранного метода доставки, но выбранная точка не доступна для этого метода доставки. Берем первую точку для выбранного метода доставки');

				self.choosenPoint( OrderModel.orderDictionary.getFirstPointByState(self.state) );
			}
			else if ( self.hasPointDelivery ) {
				// Доставка в первый пункт для данного метода доставки
				console.info('есть точки доставки для выбранного метода доставки, и выбранная точка доступна для этого метода доставки');

				self.choosenPoint( OrderModel.orderDictionary.getPointByStateAndId(self.state, choosenPointForBox) );
			}
			else {
				console.info('для выбранного метода доставки не нужна точка доставки');

				// Передаем в модель, что есть блок с доставкой домой и генерируем событие об этом
				OrderModel.hasHomeDelivery(true);
				$('body').trigger('orderdeliverychange',[true]);
			}

			// Отступ слайдера дат
			self.calendarSliderLeft = ko.observable(0);

            try {
                console.groupCollapsed('Таблица продуктов для блока %s', self.token);
                var consoleProducts = [];
                for (var a in products) {
                    var temp = products[a],
                        chPoint = Object.keys(products[a].deliveries[self.state])[0];
                    temp.choosenPoint = chPoint;
                    temp.deliveries_types = JSON.stringify(Object.keys(products[a].deliveries));
                    temp.firstDate = products[a].deliveries[self.state][chPoint].dates[0].name;
                    temp.lastDate = products[a].deliveries[self.state][chPoint].dates[products[a].deliveries[self.state][chPoint].dates.length - 1].name;
                    consoleProducts.push(temp);
                }
                console.table(consoleProducts, ['id', 'name', 'price', 'sum', 'quantity', 'stock', 'isPrepayment', 'choosenPoint', 'deliveries_types', 'firstDate', 'lastDate']);
            } catch (e) {
                console.debug('Delivery\'s box self.state: %s, self.choosenPoint.id: %s', self.state, self.choosenPoint().id);
                console.debug('Products', products);
                console.error(e);
            } finally {
                console.groupEnd();
            }

			self.addProductGroup(products);

			if ( self.products.length === 0 ) {
				// если после распределения в блоке не осталось товаров
				console.warn('в блоке '+self.token+' не осталось товаров');

				return;
			}

			/*if ( 'pickpoint' === state ) {
				// Получим и сохраним в названии пункта название выбранного пикпойнта:
				/*for ( i = self.pointList.length - 1; i >= 0; i-- ) {
					if ( choosenPointForBox == self.pointList[i].id ) {
						self.point_name = self.pointList[i].point_name;
					}
				}* ///old
				// название и так храниться в choosPoint
			}*/

			OrderModel.deliveryBoxes.push(self);
		}


		/**
		 * Делаем список общих для всех товаров в блоке точек доставок для данного метода доставки
		 *
		 * @this	{DeliveryBox}
		 */
		DeliveryBox.prototype._makePointList = function() {
			console.info('Создание списка точек доставки');

			var
				self = this,
				res = true,
				tmpPoint = null,
				point,
				i, j;
			// end of vars

			/**
			 * Перебираем точки доставки для первого товара
			 */
			for ( point in self.products[0].deliveries[self.state] ) {

				/**
				 * Перебираем все товары в блоке, проверяя доступна ли данная точка доставки для них
				 */
				for ( i = self.products.length - 1; i >= 0; i-- ) {
					res = self.products[i].deliveries[self.state].hasOwnProperty(point);

					if ( !res ) {
						break;
					}
				}

				if ( res ) {
					// Точка достаки доступна для всех товаров в блоке
					tmpPoint = OrderModel.orderDictionary.getPointByStateAndId(self.state, point);

					if ( self.isUniquePointIdInPointList(point, self.pointList) ) {
						console.warn('Add point ' + point + ' to pointList');
						self.pointList.push( tmpPoint );
					}
				}
				else {
					for ( j in self.pointList ) {
						if (undefined === self.pointList[j]['id']) continue;

						if ( point === self.pointList[j]['id'] ) {
							console.warn('Delete point ' + point + ' from pointList');
							self.pointList.splice(j);
						}
					}
				}
			}

			console.log('Точки доставки созданы');
			console.log(self.pointList);
		};

		/**
		 * Проверяем наличие точки доставки (pointId) в массиве pointList
		 *
		 * @param	{String}	pointId		Идентификатор точки доставки
		 * @param 	{Array}		pointList	Массив точек доставок
		 * @returns {boolean}
		 */
		DeliveryBox.prototype.isUniquePointIdInPointList = function ( pointId, pointList ) {
			var
				defaultValue = true,
				point;
			//end of vars

			if ( undefined === pointId || undefined === pointList ) {
				return defaultValue;
			}

			for ( point in pointList ) {
				if ( pointList[point]['id'] == pointId ) {
					return false;
				}
			}

			return true;
		}

		/**
		 * Генерирует случайное окончание (суффикс) для строки
		 *
		 * @param       {string}      str
		 * @returns     {string}      str
		 */
		DeliveryBox.prototype.addUniqueSuffix = function ( str ) {
			var
				randSuff;
			// end of vars

			str = str || '';

			//randSuff = new Date().getTime();
			randSuff = Math.floor((Math.random() * 10000) + 1);
			str += '_' + randSuff;

			return str;
		};


		/**
		 * Смена пункта доставки. Переименовываем token блока
		 * Удаляем старый блок из массива блоков и добавяем туда новый с новым токеном
		 * Если уже есть блок с таким токеном, необходиом добавить товары из текущего блока в него
		 *
		 * @this	{DeliveryBox}
		 * 
		 * @param	{Object}	data	Данные о пункте доставки
		 */
		DeliveryBox.prototype.selectPoint = function( data ) {
			var self = this,
				newToken = self.state+'_'+data.id,
				choosenBlock = null,
				productIds = [],
				i;
			// end of vars

			if ( OrderModel.hasDeliveryBox(newToken) ) {
				// запоминаем массив ids продуктов
				for ( i = self.products.length - 1; i >= 0; i-- ) {
					self.products[i].id && productIds.push(self.products[i].id);
				}

				if ( !self._hasProductsAlreadyAdded(productIds) ) {
					choosenBlock = OrderModel.getDeliveryBoxByToken(newToken);
					choosenBlock.addProductGroup( self.products );
					OrderModel.removeDeliveryBox(self.token);
				}
			}
			else {

				if (self.isUnique) {
					newToken += self.addUniqueSuffix();
				}
				console.info('удаляем старый блок');
				console.log('старый токен '+self.token);
				console.log('новый токен '+newToken);

				self.token = newToken;
				self.choosenPoint(OrderModel.orderDictionary.getPointByStateAndId(self.state, data.id));
				ENTER.OrderModel.choosenPoint(data.id);

				choosenBlock = OrderModel.getDeliveryBoxByToken(newToken);
				choosenBlock.allDatesForBlock([]);
				choosenBlock.calculateDate();

				console.log(OrderModel.deliveryBoxes());

				if ( OrderModel.paypalECS() ) {
					console.info('PayPal ECS включен. Необходимо сохранить выбранную точку доставки в cookie');

					window.docCookies.setItem('chPoint_paypalECS', data.id, 10 * 60);
				}
			}

			OrderModel.showPopupWithPoints(false);

			return false;
		};
		
		/**
		 * Показ окна с пунктами доставки
		 *
		 * @this	{DeliveryBox}
		 */
		DeliveryBox.prototype.changePoint = function( ) {
			var
				self = this,
				i;
			// end of vars

			// запонимаем токен бокса которому она принадлежит
			for ( i = self.pointList.length - 1; i >= 0; i-- ) {
				self.pointList[i].parentBoxToken = self.token;
			}

			OrderModel.popupWithPoints({
				header: 'Выберите точку доставки',
				points: self.pointList
			});

			OrderModel.showPopupWithPoints(true);

			return false;
		};


		/**
		 * Получить имя первого свойства объекта
		 *
		 * @this	{DeliveryBox}
		 * 
		 * @param	{Object}	obj		Объект у которого необходимо получить первое свойство
		 * @return	{Object}			Возвращает свойство объекта
		 */
		DeliveryBox.prototype._getFirstPropertyName = function( obj ) {
			for ( var i in obj ) {
				return i;
			}
		};

		/**
		 * Добавление продукта в блок доставки
		 *
		 * @this	{DeliveryBox}
		 * 
		 * @param	{Object}		product		Продукт который нужно добавить
		 */
		DeliveryBox.prototype._addProduct = function( product ) {
			var self = this,
				productDeliveryPrice = null,
				token = null,
				firstAvaliblePoint = null,
				tempProductArray = [],
				nowTotalSum,
				deletedBlock,
				newState,

				choosenBlock = null,

				tmpProduct = {};
			// end of vars

			if ( self._hasProductsAlreadyAdded([product.id]) ) {
				return;
			}

			/**
			 * Если для продукта нет доставки в выбранный пункт доставки, то нужно создать новый блок доставки
			 */
			if ( !product.deliveries[self.state].hasOwnProperty(self.choosenPoint().id) && !/_shipped$/.test(self.token) ) {
				console.warn('Для товара '+product.id+' нет пункта доставки '+self.choosenPoint().id+' Необходимо создать новый блок');

				firstAvaliblePoint = self._getFirstPropertyName(product.deliveries[self.state]);
				token = self.state+'_'+firstAvaliblePoint;

				tempProductArray.push(product);

				if ( OrderModel.hasDeliveryBox(token) ) {
					console.log('Блок для этого типа доставки в этот пункт уже существует. Добавляем продукт в блок' , token);

					choosenBlock = OrderModel.getDeliveryBoxByToken(token);
					//choosenBlock.addProductGroup( tempProductArray ); //массив на вход нужен // добавим ниже
					//OrderModel.removeDeliveryBox(self.token); // не находит и не удаляет никогда

					// Удаляем неполный блок и добавляем (push) полный
					//deletedBlock = OrderModel.deliveryBoxes.pop(); // удалит последний (обычно он и есть неполный)
					deletedBlock = OrderModel.removeDeliveryBox(token); // удалит по токену нужный

					// пересчитываем и обновляем общую сумму всех блоков
					nowTotalSum = OrderModel.totalSum() - deletedBlock.fullPrice() - choosenBlock.deliveryPrice;
					OrderModel.totalSum(nowTotalSum);

					choosenBlock.addProductGroup( tempProductArray ); //массив на вход нужен
					OrderModel.deliveryBoxes.push( choosenBlock );
				}
				else {
                    /* приоритет разбивки по типу доставки */
                    new DeliveryBox( tempProductArray, self.state, firstAvaliblePoint );

                    /* приоритет разбивки по магазину
					console.info('Блока для этого типа доставки в этот пункт еще не существует');
					console.warn('Необходимо попробовать найти другую доставку в тот же магазин');
					newState = OrderModel.orderDictionary.getStateToProductByDeliveryID(product.id, self.choosenPoint().id);
					console.info('newState complete');
					console.log(newState);

					if ( newState ) {
						console.log('Найден вариант доставки в тот же магазин но способом ' + newState);
						new DeliveryBox( tempProductArray, newState, self.choosenPoint().id );
					} else {
						console.warn('Не найден вариант доставки в тот же магазин. Будет выбран тот же способ доставки, но первый доступный магазин');
						new DeliveryBox( tempProductArray, self.state, firstAvaliblePoint );
					}
					*/
				}

				return;
			}

/*            if (product.stock == 9223372036854776000 && self.token != 'standart_furniture_1') {
                console.log('Есть продукт от поставщика, необходимо добавить в другой блок доставки: ', product);
                token = self.state+'_'+'1';
                tempProductArray.push(product);
                if (!OrderModel.hasDeliveryBox(token)) new DeliveryBox(tempProductArray, self.state, '1');
                return;
            }*/

			// Определение стоимости доставки. Если стоимость доставки данного товара выше стоимости доставки блока, то стоимость доставки блока становится равной стоимости доставки данного товара
            if (/_shipped$/.test(self.token)) self.choosenPoint({id: 0});

            productDeliveryPrice = parseInt(product.deliveries[self.state][self.choosenPoint().id].price, 10);
            self.deliveryPrice = ( self.deliveryPrice < productDeliveryPrice ) ? productDeliveryPrice : self.deliveryPrice;

			tmpProduct = {
				id: product.id,
				name: product.name,
				price: (product.sum) ? product.sum : product.price,
				quantity: product.quantity,
                stock: product.stock,
				deleteUrl: product.deleteUrl,
				setUrl: product.setUrl,
				productUrl: product.url,
				productImg: (product.image) ? product.image : product.productImg,
				deliveries: {},
				isPrepayment: product.isPrepayment
			};

			if ( self.isUnique && (product.oldQuantity - 1) > 0 ) {
				console.log('Переделываем deleteUrl:');
				console.log(tmpProduct.deleteUrl);
				tmpProduct.deleteUrl = tmpProduct.deleteUrl.replace('delete-', 'add-'); // TODO cart.product.set изменмить Url
				tmpProduct.deleteUrl += '?quantity=' + ( product.oldQuantity - 1 );
				console.log(tmpProduct.deleteUrl);
			}

			if ( tmpProduct.isPrepayment ) {
				self.hasProductWithPrepayment = true;
			}

			tmpProduct.deliveries[self.state] = product.deliveries[self.state];

			// Добавляем стоимость продукта к общей стоимости блока доставки
			self.fullPrice(ENTER.utils.numMethods.sumDecimal(tmpProduct.price, self.fullPrice()));

			self.products.push(tmpProduct);

		};

		/**
		 * Добавлены ли продукти в блок доставки
		 *
		 * @this	{DeliveryBox}
		 *
		 * @param	{Array}		ids		Ids продуктов
		 */
		DeliveryBox.prototype._hasProductsAlreadyAdded = function( ids ) {
			var
				self = this,
				exist = false,
				i;
			// end of vars

			if ( ids === undefined || !ids.length ) {
				return exist;
			}

			for ( i = self.products.length - 1; i >= 0; i-- ) {
				if ( -1 !== $.inArray( self.products[i].id, ids ) ) {
					exist = true;
				}
			}

			return exist;
		};

		/**
		 * Перерасчет общей стоимости заказа
		 */
		DeliveryBox.prototype.updateTotalPrice = function() {
			console.info('Перерасчет общей стоимости заказа');

			var self = this,
				nowTotalSum = OrderModel.totalSum();
			// end of vars

			self.totalBlockSum = ENTER.utils.numMethods.sumDecimal(self.fullPrice(), self.deliveryPrice);
			nowTotalSum = ENTER.utils.numMethods.sumDecimal(self.totalBlockSum, nowTotalSum);
			OrderModel.totalSum(nowTotalSum);

			console.log(OrderModel.totalSum());
		};

		/**
		 * Добавление нескольких товаров в блок доставки
		 * После добавления продуктов запускает получение общей даты доставки и наполнение списка точек доставок, если они доступны
		 * 
		 * @this	{DeliveryBox}
		 * 
		 * @param	{Array}			products	Продукты которые нужно добавить
		 */
		DeliveryBox.prototype.addProductGroup = function( products ) {
			var
				self = this,
                shipped = [],
				i;
			// end of vars
			
			console.groupCollapsed('Добавление товаров в блок, количество товаров: %s', products.length);
			// добавляем товары в блок
            // первая итерация
            if ( !/_shipped$/.test(self.token) ) {
                for (i = products.length - 1; i >= 0; i--) {
                    console.log(i + '-ый товар: ', products[i]);
                    if (products[i].stock != 9223372036854776000) self._addProduct(products[i]);
                    else shipped.push(products[i]);
                }
            }
            // вторая итерация, если есть товары от поставщика
            if ( /_shipped$/.test(self.token) ) {
                for ( i = products.length - 1; i >= 0; i-- ) {
                    console.log(i+'-ый товар: ', products[i]);
                    self._addProduct(products[i]);
                }
            }

            console.groupEnd();

            if (shipped.length && !/_shipped$/.test(self.token) ) new DeliveryBox(shipped, self.state, 'shipped');

			if ( !self.products.length ) {
				console.warn('в блоке '+self.token+' нет товаров');

				return;
			}


			self.calculateDate();
			self.updateTotalPrice();

			if ( self.hasPointDelivery ) {
				console.info('У товара есть точки доставки. Создаем список точек доставки');

				self._makePointList();
			}
		};

		/**
		 * Получение сокращенного человекочитаемого названия дня недели
		 * 
		 * @param	{Number}	dateFromModel	Номер дня недели
		 * @return	{String}					Человекочитаемый день недели
		 */
		DeliveryBox.prototype._getNameDayOfWeek = function( dayOfWeek ) {
			var
				days = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
			// end of vars

			return days[dayOfWeek];
		};

		/**
		 * Получение полного человекочитаемого названия дня недели
		 * 
		 * @param	{Number}	dateFromModel	Номер дня недели
		 * @return	{String}					Человекочитаемый день недели
		 */
		DeliveryBox.prototype._getFullNameDayOfWeek = function( dayOfWeek ) {
			var
				days = ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'];
			// end of vars

			return days[dayOfWeek];
		};

		/**
		 * Проверка даты, доступна ли дата доставки для всех товаров в боксе
		 *
		 * @this	{DeliveryBox}
		 *
		 * @param	{Number}	checkTS		Таймштамп даты, которую необходимо проверить
		 * 
		 * @return	{Boolean}
		 */
		DeliveryBox.prototype._hasDateInAllProducts = function( checkTS ) {
			var
				self = this,
				nowProductDates = null,
				nowTS = null,

				res = true,

				len,
				i,
				j;
			// end of vars

			/**
			 * Перебор всех продуктов в блоке
			 */
			for ( i = 0; i < self.products.length ; i++ ) {
                //console.groupCollapsed('Проверка существования даты доставки продукта %s в других продуктах', self.products[i].name);

				nowProductDates = self.products[i].deliveries[self.state][self.choosenPoint().id].dates;

				/**
				 * Перебор всех дат доставок в блоке
				 */
				for ( j = 0, len = nowProductDates.length; j < len; j++ ) {
					nowTS = nowProductDates[j].value;
                    //console.log('Diff dates: %s', nowTS - checkTS)

					if ( nowTS === checkTS ) {
						res = true;

						break;
					}
					else {
						res = false;
					}
				}

                //console.groupEnd();

				if ( !res ) {
					break;
				}
			}

			return res;
		};


		/**
		 * Выбор новой даты в календаре
		 *
		 * @this	{DeliveryBox}
		 *
		 * @param	{Object}	data		Данные о новой дате
		 */
		DeliveryBox.prototype.clickCalendarDay = function( data ) {
			var
				self = this,
                oldDate = self.choosenDate(),
                daysDiff;
			// end of vars
			
			if ( !data.avalible ) {
				return false;
			}

            try {
                daysDiff = (data.value - oldDate.value) / (24*60*60*1000);
                $body.trigger('trackUserAction', ['1_4_1 Смена даты', daysDiff]);
            } catch (e) {
                console.error(e);
            }

			// Если включен PayPal ECS необходимо сохранить выбранную дату в cookie
			if ( OrderModel.paypalECS() ) {
				console.info('PayPal ECS включен. Необходимо сохранить выбранную дату в cookie');

				window.docCookies.setItem('chDate_paypalECS', JSON.stringify(data), 10 * 60);
			}

			self.choosenNameOfWeek(self._getFullNameDayOfWeek(data.dayOfWeek));
			self.choosenDate(data);
		};

		DeliveryBox.prototype._hasDateInAllDatesForBlock = function( date ) {
			for (var i = 0; i < this.allDatesForBlock().length; i++) {
				if (this.allDatesForBlock()[i].value === date.value) {
					return true
				}
			}

			return false;
		};

		/**
		 * Получение общей ближайшей даты доставки
		 * Заполнение массива общих дат
		 *
		 * @this	{DeliveryBox}
		 */
		DeliveryBox.prototype.calculateDate = function() {
			console.info('Вычисление общей даты для продуктов в блоке', this);

			var
				self = this,
				todayTS = OrderModel.orderDictionary.getToday(),
				nowProductDates = null,
				nowTS = null,

				newToken = '',
				tempProduct = null,
                tempDate = null,
				tempProductArray = [],
				dateFromCookie = null,
				chooseDate = null,

				len,
				j,
				i;
			// end of vars
            if (!self.products.length) {
                console.warn('Нет продуктов для этого блока, выходим из calculateDate()');
                return;
            }
			console.log('Сегодняшняя дата с сервера '+todayTS);

			/**
			 * Перебираем даты в первом товаре
			 */
            if ( /_shipped$/.test(self.token) ) self.choosenPoint({id: 0});
			nowProductDates = self.products[0].deliveries[self.state][self.choosenPoint().id].dates;

			for ( i = 0, len = nowProductDates.length; i < len; i++ ) {
				nowTS = nowProductDates[i].value;

				if ( self._hasDateInAllProducts(nowTS) && nowTS >= todayTS && !self._hasDateInAllDatesForBlock(nowProductDates[i]) ) {
					nowProductDates[i].avalible = true;
					nowProductDates[i].humanDayOfWeek = self._getNameDayOfWeek(nowProductDates[i].dayOfWeek);

					self.allDatesForBlock().push(nowProductDates[i]);
				}
			}

			if ( !self.allDatesForBlock().length ) {
				console.warn('Нет общих дат для блока. Необходимо разделить продукты в блоке.');


                /* [start] Новый метод разделения */
                console.info('Выделяем в отдельный блок товары от поставщика');
                var shipperProductArray = [];
                shipperProductArray = self.products.reduceRight(function(previousValue, currentValue, index, arr) {
                    if (9223372036854776000 == currentValue.stock) {
                        arr.splice(index, 1);
                        previousValue.push(currentValue);
                        self.fullPrice(ENTER.utils.numMethods.sumDecimal(self.fullPrice(), -currentValue.price));
                    }
                    return previousValue;
                },[]);
                console.log('Количество товаров от поставщика = %s', shipperProductArray.length);
                if (shipperProductArray.length) {
                    new DeliveryBox( shipperProductArray, self.state, self.choosenPoint().id );
                }
                /* [end] Новый метод разделения */

                tempProductArray = self.products.reduceRight(function(previousValue, currentValue, index, arr) {
                    var currFirstDate = currentValue.deliveries[self.state][self.choosenPoint().id].dates[0].value;
                    if (tempDate === null) tempDate = currFirstDate;
                    if (tempDate == currFirstDate) {
                        arr.splice(index, 1);
                        previousValue.push(currentValue);
                        self.fullPrice(ENTER.utils.numMethods.sumDecimal(self.fullPrice(), -currentValue.price));
                    }
                    return previousValue;
                },[]);

                console.log('Продукты в новом блоке:', tempProductArray);

				newToken = self.state + '_' + self.choosenPoint().id + '_' + self.addUniqueSuffix();
				console.log('новый токен '+newToken);
				console.log(self);

				new DeliveryBox( tempProductArray, self.state, self.choosenPoint().id );

				self.calculateDate();
			}

			/**
			 * Выбираем ближайшую доступную дату
			 * Если включен PayPal ECS и уже есть сохраненная дата в куки - берем ее из куки
			 */
			if ( OrderModel.paypalECS() && window.docCookies.hasItem('chDate_paypalECS') ) {
				console.info('PayPal ECS включен. Необходимо взять выбранную дату из cookie');

				dateFromCookie = window.docCookies.getItem('chDate_paypalECS');
				chooseDate = JSON.parse(dateFromCookie);
			}
			// else if ( self.choosenDate() && self.choosenDate().avalible ) {
			// 	console.warn('======= self.choosenDate() уже была =========');
			// 	chooseDate = self.choosenDate();
			// }
			else {
				chooseDate = self.getFirstAvalibleDate();
			}

			console.log('Выбранная дата (chooseDate) ', chooseDate);
			console.log('Все даты для блока ', self.allDatesForBlock());
			if ( chooseDate && true === chooseDate.avalible ) {
				self.choosenDate( chooseDate );
			}
			else {
				console.warn('Блок недоступен. Вычисление общей даты для продуктов в блоке невозможно. Выходим.');
				return false;
			}

			if ( undefined === typeof(self.choosenDate().intervals) ) {
				console.warn('В блоке нет интервалов. Вычисление даты невозможно. Выходим.');
				return false;
			}

			/**
			 * Человекочитаемый день недели
			 */
			self.choosenNameOfWeek( self._getFullNameDayOfWeek(self.choosenDate().dayOfWeek) );
			/**
			 * Выбираем первый интервал
			 */
			if ( self.choosenDate().intervals.length !== 0 ) {
				self.choosenInterval( self.choosenDate().intervals[0] );
			}

			self.makeCalendar();
		};

		DeliveryBox.prototype.getFirstAvalibleDate = function() {
			var
				self = this,
				i;
			// end of vars

			for ( i = 0; i < self.allDatesForBlock().length; i++ ) {
				if ( self.allDatesForBlock()[i].avalible ) {
					return self.allDatesForBlock()[i];
				}
			}

			return false;
		};

		/**
		 * Создание календаря, округление до целых недель
		 *
		 * @this	{DeliveryBox}
		 */
		DeliveryBox.prototype.makeCalendar = function() {
			console.groupCollapsed('Создание календаря, округление до целых недель');
			console.log(this);
			var
				self = this,
				addCountDays = 0,
				tmpDay = {},
				tmpVal = null,
				tmpDate = null,

				ONE_DAY = 24*60*60*1000,

				i, j, k;
			// end of vars

			console.info(self.allDatesForBlock());

			/**
			 * Проверка дат на разрывы  в числах
			 * Если меются разрывы в числах - заполнить пробелы датами
			 */
			for ( k = 0; k < self.allDatesForBlock().length; k++ ) {
				console.log('k',k);
				if ( self.allDatesForBlock()[k + 1] === undefined ) {
					console.info('Следущая дата последняя. заканчиваем цикл');
					
					break;
				}
				if ( k > 99 ) {
					// Ограничение, дабы 100% не нарваться на вечный цикл
					break;
				}

				tmpDay = {};
				tmpVal = self.allDatesForBlock()[k].value + ONE_DAY;
				tmpDate = new Date(tmpVal);

				if (tmpVal > self.allDatesForBlock()[k + 1].value) {
					console.warn('однозначная ошибка, следующая дата меньше текущей');
					break;
				}

				if ( tmpVal !== self.allDatesForBlock()[k + 1].value ) {
					console.info('следующая дата', self.allDatesForBlock()[k + 1].value);
					console.info('текущая дата', tmpVal, tmpDate);
					tmpDay = {
						value: tmpVal,
						avalible: false,
						humanDayOfWeek: self._getNameDayOfWeek(tmpDate.getDay()),
						dayOfWeek: tmpDate.getDay(),
						day: tmpDate.getDate()
					};

					console.log(
						'предыдущая дата была ' + new Date(self.allDatesForBlock()[k].value).getDate() +
						' новая дата вклинилась ' + tmpDate.getDate() +
						' следущая дата ' + new Date(self.allDatesForBlock()[k + 1].value).getDate()
					);
					self.allDatesForBlock.splice(k + 1, 0, tmpDay);
				}
			}
			

			/**
			 * Проверка первой даты
			 * Если она не понедельник - достроить календарь в начале до понедельника
			 */
			if ( self.allDatesForBlock()[0].dayOfWeek !== 1 ) {
				addCountDays = ( self.allDatesForBlock()[0].dayOfWeek === 0 ) ? 6 : self.allDatesForBlock()[0].dayOfWeek - 1;
				tmpVal = self.allDatesForBlock()[0].value;

				console.info('добавляем в начало', addCountDays);

				for ( i = addCountDays; i > 0; i-- ) {
					tmpVal -= ONE_DAY;
					tmpDate = new Date(tmpVal);

					tmpDay = {
                        value: tmpVal,
						avalible: false,
						humanDayOfWeek: self._getNameDayOfWeek(tmpDate.getDay()),
						dayOfWeek: tmpDate.getDay(),
						day: tmpDate.getDate()
					};

					self.allDatesForBlock.unshift(tmpDay);
				}
			}

			/**
			 * Проверка последней даты
			 * Если она не воскресенье - достроить календарь в конце до воскресенья
			 */
			if ( self.allDatesForBlock()[self.allDatesForBlock().length - 1].dayOfWeek !== 0 ) {
				addCountDays = 7 - self.allDatesForBlock()[self.allDatesForBlock().length - 1].dayOfWeek;
				tmpVal = self.allDatesForBlock()[self.allDatesForBlock().length - 1].value;

				console.info('добавляем в конец', addCountDays);

				for ( j = addCountDays; j > 0; j-- ) {
					// dayOfWeek = ( self.allDatesForBlock()[self.allDatesForBlock().length - 1].dayOfWeek + 1 === 7 ) ? 0 : self.allDatesForBlock()[self.allDatesForBlock().length - 1].dayOfWeek + 1;
					tmpVal += ONE_DAY;
					tmpDate = new Date(tmpVal);

					tmpDay = {
                        value: tmpVal,
						avalible: false,
						humanDayOfWeek: self._getNameDayOfWeek(tmpDate.getDay()),
						dayOfWeek: tmpDate.getDay(),
						day: tmpDate.getDate()
					};

					self.allDatesForBlock.push(tmpDay);
				}
			}

            console.groupEnd();
		};


		/**
		 * =========== CALENDAR SLIDER ===================
		 */
		DeliveryBox.prototype.calendarLeftBtn = function() {
			var
				self = this,
				nowLeft = parseInt(self.calendarSliderLeft(), 10);
			// end of vars
			
			nowLeft += 380;
			self.calendarSliderLeft(nowLeft);
		};

		DeliveryBox.prototype.calendarRightBtn = function() {
			var
				self = this,
				nowLeft = parseInt(self.calendarSliderLeft(), 10);
			// end of vars
			
			nowLeft -= 380;
			self.calendarSliderLeft(nowLeft);
		};
		/**
		 * =========== END CALENDAR SLIDER ================
		 */

	
		return DeliveryBox;
	
	}());

}(this, this.document, this.jQuery, this.ENTER, this.ko));
/**
 * Работа с кредитными брокерами
 */
;(function( window ){
	console.info('orderCredit.js init...');
	
	var
		bankWrap = $('div.bBankWrap'),
		//bankWrapInput = bankWrap.find('.bSelectInput'), // уже не используется
		//bankWrapLabel = bankWrapInput.find('.bCustomLabel'), // уже не используется
	// end of vars

		creditInit = function creditInit() {
			var
				bankField = $('#selectedBank'),
				//bankFieldInput = bankWrap.find('.bCustomInput'), // уже не используется
			// end of vars

				selectBank = function selectBank() {
					var
						checked = $('input:checked', bankWrap),
						chosenBankId = checked.val();
					// end of vars

					console.log('selectBank with ID', chosenBankId);

					if ( 'undefined' !== chosenBankId ) {
						// Навесим классы на неотмеченные блоки и уберём с отмеченного
						$('.bSelectInput', bankWrap).addClass('bUnchecked');
						checked.closest('.bSelectInput', bankWrap ).removeClass('bUnchecked');

						bankField.val(chosenBankId);
					}
				};
			// end of vars and functions

			// Если понадобится отмечать чекбокс по умолчанию, раскомментируем эти строки и связанные с ними элементы
			//$(bankFieldInput, bankWrap).eq(0).attr('checked','checked');
			//$(bankWrapLabel, bankWrap).eq(0).addClass('mChecked');
			//selectBank(); // уже не используется

			bankWrap.change(selectBank);

			if ( typeof(window.DirectCredit) && 'function' === typeof(window.DirectCredit.init) ) {
				window.DirectCredit.init( $('#jsCreditBank').data('value'), $('.credit_pay') );
			}
		}; // end of creditInit()
	// end of vars and functions
	
	if ( bankWrap.length ) {
		creditInit();
	}
}(this));
;(function (window, document, $, ENTER) {
	console.info('orderDictionary.js init...');

	var
		constructors = ENTER.constructors;
	// end of vars


	constructors.OrderDictionary = (function() {
		'use strict';

		/**
		 * Вспомагательная обертка вокруг данных приходящих с сервера
		 * Явлется неким драйвером для доступа к данным
		 *
		 * @author	Zaytsev Alexandr
		 * @this	{OrderDictionary}
		 *
		 * @param	{Object}	orderData	Данные о доставке
		 *
		 * @constructor
		 */
		function OrderDictionary( orderData ) {
			// enforces new
			if ( !(this instanceof OrderDictionary) ) {
				return new OrderDictionary(orderData);
			}
			// constructor body
			
			this.orderData = orderData;

			// alias
			this.serverTime = this.orderData.time;
			this.deliveryTypes = this.orderData.deliveryTypes;
			this.deliveryStates = this.orderData.deliveryStates;
			this.pointsByDelivery = this.orderData.pointsByDelivery;
			this.products = this.orderData.products;
			this.defPoints = this.orderData.defPoints || {};

            console.debug('OrderDictionary', this);
		}

		/**
		 * Получить имя метода доставки
		 *
		 * @this	{OrderDictionary}
		 * 
		 * @param	{String}	state	Метод доставки
		 * @return	{String}			Имя метода доставки
		 */
		OrderDictionary.prototype.getNameOfState = function( state ) {
			if ( !this.hasDeliveryState(state) ) {
				console.warn('Не найден метод доставки '+state);
				
				return false;
			}

			return this.deliveryStates[state].name;
		};

		/**
		 * Получить таймштамп от которого нужно вести расчет
		 *
		 * @this	{OrderDictionary}
		 * 
		 * @return	{Number}	Таймштамп
		 */
		OrderDictionary.prototype.getToday = function() {
			return this.serverTime;
		};

		/**
		 * Стандартная точка для метода доставки
		 * 
		 * @param token
		 * @returns {*}
		 */
		OrderDictionary.prototype.getDefaultPointId = function( token ) {
			return this.defPoints.hasOwnProperty(token) ? this.defPoints[token] : 0;
		};

		/**
		 * Есть ли метод доставки
		 *
		 * @this	{OrderDictionary}
		 * 
		 * @param	{String}	state	Метод доставки
		 * @return	{Boolean}
		 */
		OrderDictionary.prototype.hasDeliveryState = function( state ) {
			return this.deliveryStates.hasOwnProperty(state);
		};


		/**
		 * Флаг уникальности для типа доставки state.
		 * Например, для типа доставки pickpoint должен быть false (задаётся в РНР-коде на сервере)
		 *
		 * @this        {OrderDictionary}
		 *
		 * @param       {String}    state    Метод доставки
		 * @returns     {Boolean}
		 */
		OrderDictionary.prototype.isUniqueDeliveryState = function ( state ) {
			var
				st;
			// end of vars
			
			if ( this.hasDeliveryState(state) ) {
				st = this.deliveryStates[state];

				return st['unique'];
			}

			return false;
		};

		/**
		 * Есть ли для метода доставки пункты доставки
		 *
		 * @this	{OrderDictionary}
		 * 
		 * @param	{String}	state	Метод доставки
		 * @return	{Boolean}
		 */
		OrderDictionary.prototype.hasPointDelivery = function( state ) {
			if ( !this.hasDeliveryState(state) ) {
				return false;
			}

            return this.pointsByDelivery.hasOwnProperty(state) && this.pointsByDelivery[state].token;
		};

		/**
		 * Получить данные о точке доставки по методу доставки и идетификатору точки доставки
		 *
		 * @this	{OrderDictionary}
		 * 
		 * @param	{String}	state		Метод доставки
		 * @param	{String}	pointId		Идентификатор точки доставки
		 * @return	{Object}				Данные о точке доставки
		 */
		OrderDictionary.prototype.getPointByStateAndId = function( state, pointId ) {
			var
				points = this.getAllPointsByState(state),
				i;
			// end of vars
			
			pointId = pointId+'';
			
			for ( i = points.length - 1; i >= 0; i-- ) {
				if ( points[i].id === pointId ) {
					return window.ENTER.utils.cloneObject(points[i]);
				}
			}

			return false;
		};

		/**
		 * Получение первой точки доставки для метода доставки
		 *
		 * @this	{OrderDictionary}
		 * 
		 * @param	{String}	state		Метод доставки
		 * @return	{Object}				Данные о точке доставки
		 */
		OrderDictionary.prototype.getFirstPointByState = function( state ) {
			var
				points = this.getAllPointsByState(state);
			// end of vars
			return ( points[0] ) ? ENTER.utils.cloneObject(points[0]) : false;
		};

		/**
		 * @this	{OrderDictionary}
		 *
		 * @param	{String}	state	Метод доставки
		 */
		OrderDictionary.prototype.getAllPointsByState = function( state ) {
			if ( !this.hasDeliveryState(state) ) {
				return false;
			}

			var
				point = this.pointsByDelivery[state],
				pointName = point ? point.token : false,
				ret = pointName ? this.orderData[pointName] : false,
				retNew = [], i, type;
			// end of vars

			/*
			 SITE-2499 Некорректный первоначальный список магазинов при оформлении заказа
			 Фильтруем точки для типов доставки "now" и "self"
			 */
			if ( state == "now" || state == "self" || state == 'self_svyaznoy') {
				for ( i in ret ) {
					for ( type in ret[i].products ) {
						type == state && retNew.push(ret[i]);
					}
				}

				ret = retNew;
			}

			return ret || false;
		};


		OrderDictionary.prototype.getChangeButtonText = function( state ) {
			var
				text = ( this.pointsByDelivery[state] ) ? this.pointsByDelivery[state].changeName : 'Сменить';
			// end of vars
			
			return text;
		};


		/**
		 * Получить спискок продуктов для которых доступен данный метод доставки
		 * 
		 * @this	{OrderDictionary}
		 * 
		 * @param	{String}	state	Метод доставки
		 * @return	{Array}				Массив идентификаторов продуктов
		 */
		OrderDictionary.prototype.getProductFromState = function( state ) {
			if ( !this.hasDeliveryState(state) ) {
				console.warn('Не найден метод доставки '+state);

				return false;
			}

			return this.deliveryStates[state].products;
		};

		/**
		 * Получить данные о продукте по идентификатору продукта
		 *
		 * @this	{OrderDictionary}
		 * 
		 * @param	{Number}	productId	Идентификатор продукта
		 * @return	{Object}				Данные о продукте
		 */
		OrderDictionary.prototype.getProductById = function( productId ) {
			if ( !this.products.hasOwnProperty(productId) ) {
				console.warn('Такого продукта не найдено');

				return false;
			}

			return this.products[productId];
		};

		/**
		 * Метод ищет у продукта любую возможность забрать из конкретной точки доставки
		 * Если метод доставки находится - возвращается метод доставки,
		 * Если нет - возвращатеся false
		 *
		 * @param	{Number}	productId		Идентификатор продукта
		 * @param	{Number}	pointId			Идентификатор точки доставки
		 */
		OrderDictionary.prototype.getStateToProductByDeliveryID = function( productId, pointId ) {
			console.info('Перебираем все методы доставок и ищем среди них со схожим типом точек доставок');
			var
				productDeliveries = this.products[productId].deliveries,
				deliveriesType;
			// end of vars

			console.log('productId ' + productId);
			console.log('pointId ' + pointId);
			console.log(productDeliveries);

			for ( deliveriesType in productDeliveries ) {
				console.log('ищем в ' + deliveriesType);
				if ( productDeliveries.hasOwnProperty(deliveriesType) && productDeliveries[deliveriesType].hasOwnProperty(pointId) ) {
					console.log('возвращаем ' + deliveriesType);
					return deliveriesType;
				}
			}

			console.warn('не нашли...((');
			return false;
		};


		return OrderDictionary;
	
	}());
	
}(this, this.document, this.jQuery, this.ENTER));
/**
 * Sertificate
 *
 * 
 * @requires	jQuery
 */
;(function( window ) {
	console.info('orderSertificate init...');


	if ( !$('#paymentMethod-10').length ) {
		console.warn('нет метода оплаты сертификатом');

		return false;
	}

	var
		sertificateWrap = $('#paymentMethod-10').parent(),
		code = sertificateWrap.find('.mCardNumber'),
		pin = sertificateWrap.find('.mCardPin'),
		fieldsWrap = sertificateWrap.find('.bPayMethodAction'),
		urlCheck = fieldsWrap.attr('data-url');
	// end of vars


	var SertificateCard = (function() {

		var
			checked = false,
			processTmpl = 'processBlock';
		// end of vars

		var
			getCode = function getCode() {
				return code.val().replace(/[^0-9]/g,'');
			},

			getPIN = function getPIN() {
				return pin.val().replace(/[^0-9]/g,'');
			},

			getParams = function getParams() {
				return { code: getCode() , pin: getPIN() };
			},

			isActive = function isActive() {
				if ( checked &&
					getCode() !== '' &&
					getCode().length === 14 &&
					getPIN() !== '' &&
					getPIN().length === 4 ) {
					return true;
				}

				return false;
			},

			checkCard = function checkCard() {
				if ( pin.val().length !== 4) {
					console.warn('пин код мал');
					return false;
				}
				
				if ( !code.val().length ) {
					console.warn('номер карты не введен');
					return false;
				}

				setProcessingStatus( 'mOrange', 'Проверка по номеру карты' );
				$.post( urlCheck, getParams(), function( data ) {
					if ( !('success' in data) ) {
						return false;
					}

					if ( !data.success ) {
						var err = ( typeof(data.error) !== 'undefined' ) ? data.error : 'ERROR';

						setProcessingStatus( 'mRed', err );

						return false;
					}

					setProcessingStatus( 'mGreen', data.data );
				});
				// pin.focus()
			},

			setProcessingStatus = function setProcessingStatus( status, data ) {
				console.info('setProcessingStatus');

				var
					blockProcess = $('.process').first(),
					options = { typeNum: status };
				// end of vars

				if( !blockProcess.hasClass('picked') ) {
					blockProcess.remove();
				}

				switch ( status ) {
					case 'mOrange':
						options.text = data;
						checked = false;

						break;
					case 'mRed':
						options.text = 'Произошла ошибка: ' + data;
						checked = false;

						break;
					case 'mGreen':
						if ( 'activated' in data ) {
							options.text = 'Карта '+ data.code + ' на сумму ' + data.sum + ' активирована!';
						}
						else {
							options.text = 'Карта '+ data.code + ' имеет номинал ' + data.sum;
						}

						checked = true;

						break;
				}

				fieldsWrap.after( tmpl( processTmpl, options) );

				if ( typeof data['activated']  !== 'undefined' ) {
					$('.process').first().addClass('picked');
				}
			};
		// end of function

		return {
			checkCard: checkCard,
			setProcessingStatus: setProcessingStatus,
			isActive: isActive,
			getCode: getCode,
			getPIN: getPIN
		};
	})();

	code.bind('change',function() {
		SertificateCard.checkCard();
	});

	pin.bind('change',function() {
		SertificateCard.checkCard();
	});

	$.mask.definitions['n'] = '[0-9]';
	pin.mask('nnnn', { completed: SertificateCard.checkCard, placeholder: '*' } );

}(this));
/**
 * Google Analytics steps tracking
 *
 * @author Zhukov Roman
 * @requires jQuery
 */

;(function(){
    var // variables
        w = window,
        _gaq = w._gaq || [],
        ga = w[w['GoogleAnalyticsObject']],
        $ = w.jQuery,
        body = $(document.body),
        region = $('#jsDeliveryAddress').data('value')['regionName'],

        // functions
        sendAnalytic = function sendAnalyticF (event, step, action) {
            var act = action || '',
                st = step || '',
				oneClickOrder = ENTER.config.pageConfig.currentRoute == 'order.oneClick.new',
				categoryPrefix = 'воронка_';

			if (oneClickOrder) categoryPrefix += 'старый_1_клик_';

            if (event && event.data) {
                if (event.data.step) st = event.data.step;
                if (event.data.action) act = event.data.action;
            }

            if (typeof ga === 'undefined') ga = window[window['GoogleAnalyticsObject']]; // try to assign ga

            // sending
            if (typeof _gaq === 'object') _gaq.push(['_trackEvent', categoryPrefix + region, st, act]);
            if (typeof ga === 'function') ga('send', 'event', categoryPrefix + region, st, act);

            // log to console
            console.log('[Google Analytics] Step "%s" sended with action "%s" for %s', st, act, categoryPrefix + region);
        };

    console.log('[Init] Google Analytics Tracking');

    // common listener for triggering from another files or functions
    body.on('trackUserAction.orderTracking', sendAnalytic);

    body.on('click.orderTracking', 'a.bBackCart', function(e) {
        if ( $(e.target).hasClass('mOrdeRead') ) body.trigger('trackUserAction', ['3 Редактировать товары']);
        else body.trigger('trackUserAction', ['1_3 Доставка, ушел в корзину']);
    });

    body.on('click.orderTracking', 'a#auth-link', {step: '4_0 Авторизация'}, sendAnalytic);

    body.on('focusin.orderTracking', function(e) {
        var $target = $(e.target);
        switch ( $target.attr('id') ) {
            case 'order_recipient_first_name':
                body.trigger('trackUserAction', ['4_1 Имя']); break;
            case 'order_recipient_last_name':
                body.trigger('trackUserAction', ['4_2 Фамилия']); break;
            case 'order_recipient_email':
                body.trigger('trackUserAction', ['4_3 Email']); break;
            case 'order_recipient_phonenumbers':
                body.trigger('trackUserAction', ['4_4 телефон']);
                for (var i in ENTER.OrderModel.deliveryBoxes()) {
                    if (/standart/.test(ENTER.OrderModel.deliveryBoxes()[i].state)) {
                        body.trigger('trackUserAction', ['4_5_1 ЛД Доставка - Адрес']);
                        break;
                    }
                }
                break;
            case 'order_address_street':
                body.trigger('trackUserAction', ['4_5_2 ЛД Доставка - Улица']); break;
            case 'order_address_building':
                body.trigger('trackUserAction', ['4_5_3 ЛД Доставка - Дом']); break;
            case 'order_address_metro':
                body.trigger('trackUserAction', ['4_5_4 ЛД Доставка - Метро']); break;
            case 'bonus-card-number':
                body.trigger('trackUserAction', ['5 Связной-клуб']); break;
        }
    });

    body.on('click.orderTracking', '.mPayMethods .bCustomLabel', function(e){
        body.trigger('trackUserAction', ['6 Тип оплаты', $(e.target).text()]);
    });

    body.on('click.orderTracking', '.mRules .bCustomLabel', function(e) {
        var $target = $(e.target);
        if ($target.hasClass('bCustomLabel')) body.trigger('trackUserAction', ['7 Согласие']);
        if ($target.attr('href') == '/terms') body.trigger('trackUserAction', ['7_1 Условия']);
        if ($target.attr('href') == '/legal') body.trigger('trackUserAction', ['7_2 Право']);
    });

    // Time interval change
    body.on('focus', 'select.bSelect', function() {
        var oldIndex = $(this).prop('selectedIndex');
        $(this).off('blur').on('blur', function(){
            var diff = oldIndex - $(this).prop('selectedIndex');
            if (diff == 0) body.trigger('trackUserAction', ['1_4_2 Смена времени', 'оставил']);
            else body.trigger('trackUserAction', ['1_4_2 Смена времени', 'сменил']);
        })
    });

    // initial trigger
    body.trigger('trackUserAction', ['0 Вход'])

})();
/**
 * Валидация формы. Отправка на сервер. Аналитика
 */
;(function ( window, document, $, ENTER ) {
	console.info('orderValidation.js init');

	var
		utils = ENTER.utils,
		
		orderValidator = {},
		subwayArray = $('#metrostations').data('name'),

		// form fields
		firstNameField = $('#order_recipient_first_name'),
		lastNameField = $('#order_recipient_last_name'),
		emailField = $('#order_recipient_email'),
		phoneField = $('#order_recipient_phonenumbers'),
		subwayField = $('#order_address_metro'),
		metroIdFiled = $('#order_subway_id'),
		streetField = $('#order_address_street'),
		buildingField = $('#order_address_building'),
		bonusCardNumber = $('#bonus-card-number'),
		paymentRadio = $('.jsCustomRadio[name="order[payment_method_id]"]'),
		qiwiPhone = $('#qiwi-phone'),
		orderAgreed = $('#order_agreed'),

		// complete button
		orderCompleteBtn = $('#completeOrder'),


		// analytics data
		ajaxStart = null,
		ajaxStop = null,
		ajaxDelta = null,

		/**
		 * Конфигурация валидатора
		 * @type {Object}
		 */
		validationConfig = {
			fields: [
				{
					fieldNode: firstNameField,
					require: true,
					customErr: 'Введите имя получателя',
					validateOnChange: true
				},
				{
					fieldNode: emailField,
					validBy: 'isEmail',
					customErr: 'Некорректно введен e-mail',
					validateOnChange: true
				},
				{
					fieldNode: phoneField,
					require: true,
					customErr: 'Некорректно введен телефон',
					validateOnChange: true
				},
				{
					fieldNode: orderAgreed,
					require: true,
					customErr: 'Необходимо согласие'
				},
				{
					fieldNode: bonusCardNumber,
					customErr: 'Некорректно введен номер карты лояльности'
				}
			]
		},

		subwayAutocompleteConfig = {
			source: subwayArray,
			appendTo: '#metrostations',
			minLength: 2,
			select : function( event, ui ) {
				metroIdFiled.val(ui.item.val);
			}
		};
	// end of vars
	
	console.log(ENTER.OrderModel);
	console.log('orderValidation:: vars initd');

	orderValidator = new FormValidator(validationConfig);
	utils.orderValidator = orderValidator;

	var
		/**
		 * Показ сообщений об ошибках при оформлении заказа
		 * 
		 * @param	{String}	msg		Сообщение которое необходимо показать пользователю
		 */
		showError = function showError( msg, callback ) {
			var
				content = '<div class="popupbox width290">' +
					'<div class="font18 pb18"> '+msg+'</div>'+
					'</div>' +
					'<p style="text-align:center"><a href="#" class="closePopup bBigOrangeButton">OK</a></p>',
				block = $('<div>').addClass('popup').html(content);
			// end of vars
			
			block.appendTo('body');

			var
				errorPopupCloser = function() {
					block.trigger('close');
					block.remove();

					if ( callback !== undefined ) {
						callback();
					}

					return false;
				};
			// end of functions

			block.lightbox_me({
				centered:true,
				onClose: errorPopupCloser
			});

			block.find('.closePopup').bind('click', errorPopupCloser);
		},

		/**
		 * Обработка ошибок формы
		 * 
		 * @param	{Object}	formError	Объект с полем содержащим ошибки
		 */
		formErrorHandler = function formErrorHandler( formError ) {
			console.warn('Ошибка в поле');
			
			var field = $('[name="order['+formError.field+']"]');

			var clearError = function clearError() {
				orderValidator._unmarkFieldError($(this));
			};

			orderValidator._markFieldError(field, formError.message);
			field.bind('focus', clearError);
		},
	
		/**
		 * Обработка ошибок из ответа сервера
		 */
		serverErrorHandler = {
			'default': function( res ) {
				console.log('Обработчик ошибки');

				if ( 'undefined' === typeof(res.redirect) ) {
					res.redirect = '/cart';
				}

				if ( res.error && res.error.message ) {
					showError(res.error.message, function() {
						if ( 0 !== res.redirect ) {
							// Если в ответе точно 0, значит ошибка валидации — не редиректим,
							// предоставляем возможность изменить выбор и жизнь
							document.location.href = res.redirect;
						}
					});

					return;
				}

				document.location.href = res.redirect;
			},

			0: function( res ) {
				console.warn('Обработка ошибок формы');

				var formError = null;

				if ( res.redirect ) {
					showError(res.error.message, function(){
						document.location.href = res.redirect;
					});

					return;
				}

				showError(res.error.message);

				for ( var i = res.form.error.length - 1; i >= 0; i-- ) {
					formError = res.form.error[i];
					console.warn(formError);
					formErrorHandler(formError);
				}

				$.scrollTo($('.mError').eq(0), 500, {offset:-15});
			},

			743: function( res ) {
				showError(res.error.message);
			}
		},

		/**
		 * Аналитика завершения заказа
		 */
		completeAnalytics = function completeAnalytics() {
			if ( typeof _gaq !== 'undefined') {
				for ( var i = ENTER.OrderModel.deliveryBoxes().length - 1; i >= 0; i-- ) {
					_gaq.push(['_trackEvent', 'Order card', 'Completed', 'выбрана '+ENTER.OrderModel.choosenDeliveryTypeId+' доставят '+ENTER.OrderModel.deliveryBoxes()[i].state]);
				}

				_gaq.push(['_trackEvent', 'Order complete', ENTER.OrderModel.deliveryBoxes().length, ENTER.OrderModel.orderDictionary.products.length]);
				_gaq.push(['_trackTiming', 'Order complete', 'DB response', ajaxDelta]);
			}

            $(document.body).trigger('trackUserAction', ['9 Завершение - успех']);
		},

		/**
		 * Обработка ответа от сервера
		 *
		 * @param	{Object}	res		Ответ сервера
		 */
		processingResponse = function processingResponse( res ) {
			console.info('данные отправлены. получен ответ от сервера');
			
			ajaxStop = new Date().getTime();
			ajaxDelta = ajaxStop - ajaxStart;

			console.log(res);

			if ( !res.success ) {
				console.log('ошибка оформления заказа');
                $(document.body).trigger('trackUserAction', ['8 Завершение - ошибка', res.error.code]);

				utils.blockScreen.unblock();

				if ( serverErrorHandler.hasOwnProperty(res.error.code) ) {
					console.log('Есть обработчик');

					serverErrorHandler[res.error.code](res);
				}
				else {
					console.log('Стандартный обработчик');

					serverErrorHandler['default'](res);
				}

				return false;
			}

			completeAnalytics();

			if ( ENTER.OrderModel.paypalECS() && !orderCompleteBtn.hasClass('mConfirm') ) {
				console.info('PayPal ECS включен. Заказ оформлен. Необходимо удалить выбранные параметры из cookie');

				window.docCookies.removeItem('chDate_paypalECS', '/');
				window.docCookies.removeItem('chTypeBtn_paypalECS', '/');
				window.docCookies.removeItem('chPoint_paypalECS', '/');
				window.docCookies.removeItem('chTypeId_paypalECS', '/');
				window.docCookies.removeItem('chStetesPriority_paypalECS', '/');
			}

			document.location.href = res.redirect;
		},

		/**
		 * Подготовка данных для отправки на сервер
		 * Отправка данных
		 */
		preparationData = function preparationData() {
			var
				currentDeliveryBox = null,
				choosePoint,
				parts = [],
				dataToSend = [],
				tmpPart = {},
				i, j,
				orderForm = $('#order-form');
			// end of vars
			
			utils.blockScreen.block('Ваш заказ оформляется');
			dataToSend = orderForm.serializeArray();

			/**
			 * Перебираем блоки доставки
			 */
			console.info('Перебираем блоки доставки');
			for ( i = ENTER.OrderModel.deliveryBoxes().length - 1; i >= 0; i-- ) {
				tmpPart = {};
				currentDeliveryBox = ENTER.OrderModel.deliveryBoxes()[i];
				choosePoint = currentDeliveryBox.choosenPoint();
				console.log('currentDeliveryBox:');
				console.log(currentDeliveryBox);

				tmpPart = {
					deliveryMethod_token: currentDeliveryBox.state,
					date: currentDeliveryBox.choosenDate().value,
					interval: [
						( currentDeliveryBox.choosenInterval() ) ? currentDeliveryBox.choosenInterval().start : '',
						( currentDeliveryBox.choosenInterval() ) ? currentDeliveryBox.choosenInterval().end : ''
					],
					point_id: choosePoint.id,
					products : [],
                    deliveryPrice : currentDeliveryBox.deliveryPrice
				};

				console.log('choosePoint:');
				console.log(choosePoint);

				if ( 'self_partner_pickpoint' === currentDeliveryBox.state ) {
					console.log('Is PickPoint!');

					// Передаём на сервер корректный id постамата, не id точки, а номер постамата
					tmpPart.point_id = choosePoint['number'];

					// В качестве адреса доставки необходимо передавать адрес постамата,
					// так как поля адреса при заказе через pickpoint скрыты
					/*orderForm.find('#order_address_street').val( choosePoint['street'] );
					orderForm.find('#order_address_building').val( choosePoint['house'] );
					orderForm.find('#order_address_number').val('');
					orderForm.find('#order_address_apartment').val('');
					orderForm.find('#order_address_floor').val('');*/ // old

					/* Передаём сразу без лишней сериализации и действий с формами
					 * и не в dataToSend, а в массив parts, отдельным полем,
					 * т.к. может быть разный адрес у разных пикпойнтов
					 * */
					// parts.push( {pointAddress: choosePoint['street'] + ' ' + choosePoint['house']} );
					tmpPart.point_address = {
						street:	choosePoint['street'],
						house:	choosePoint['house']
					};
					tmpPart.point_name = choosePoint.point_name; // нужно передавать в ядро
				}

				for ( j = currentDeliveryBox.products.length - 1; j >= 0; j-- ) {
					tmpPart.products.push(currentDeliveryBox.products[j].id);
				}

				console.log('tmpPart:');
				console.log(tmpPart);

				parts.push(tmpPart);
			}

			dataToSend.push({ name: 'order[delivery_type_id]', value: ENTER.OrderModel.choosenDeliveryTypeId });
			dataToSend.push({ name: 'order[part]', value: JSON.stringify(parts) });

			if ( typeof(window.KM) !== 'undefined' ) {
				dataToSend.push({ name: 'kiss_session', value: window.KM.i });
			}

			console.log('dataToSend:');
			console.log(dataToSend);

			ajaxStart = new Date().getTime();

			$.ajax({
				url: orderForm.attr('action'),
				timeout: 120000,
				type: 'POST',
				data: dataToSend,
				success: processingResponse,
				statusCode: {
					500: function() {
						showError('Не удалось создать заказ. Попробуйте позднее. 500');
					},
					504: function() {
						showError('Не удалось создать заказ. Попробуйте позднее. 504');
					}
				}
			});
		},

		/**
		 * Обработчик нажатия на кнопку завершения заказа
		 */
		orderCompleteBtnHandler = function orderCompleteBtnHandler() {
			console.info('Завершить оформление заказа');

			/**
			 * Для акции «подари жизнь» валидация полей на клиенте не требуется
			 */
			if ( ENTER.OrderModel.lifeGift() ) {
				preparationData();

				return false;
			}

			orderValidator.validate({
				onInvalid: function( err ) {
					console.warn('invalid');
					console.log(err);

					$.scrollTo(err[err.length - 1].fieldNode, 500, {offset:-15});
				},
				onValid: preparationData
			});

			return false;
		},

		/**
		 * Обработчик изменения в поле выбора станции метро
		 * Проверка корректности заполнения поля
		 */
		subwayChange = function subwayChange() {
			for ( var i = subwayArray.length - 1; i >= 0; i-- ) {
				if ( subwayField.val() === subwayArray[i].label ) {
					return;
				}
			}

			subwayField.val('');
		},

		/**
		 * Изменение типа доставки в одом из блоков
		 * 
		 * @param	{Event}		event				Данные о событии
		 * @param	{Boolean}	hasHomeDelivery		Есть ли блок с доставкой домой
		 */
		orderDeliveryChangeHandler = function orderDeliveryChangeHandler( event, hasHomeDelivery ) {
			if ( hasHomeDelivery ) {
				// Добавялем поле ввода улицы в список валидируемых полей
				orderValidator.setValidate( streetField, {
					require: true,
					customErr: 'Не введено название улицы',
					validateOnChange: true
				});

				// Добавялем поле ввода номера дома в список валидируемых полей
				orderValidator.setValidate( buildingField, {
					require: true,
					customErr: 'Не введен номер дома',
					validateOnChange: true
				});

				if ( subwayArray !== undefined ) {
					// Добавлем валидацию поля метро
					orderValidator.setValidate( subwayField , {
						fieldNode: subwayField,
						customErr: 'Не выбрана станция метро',
						require: true,
						validateOnChange: true
					});
				}
			}
			else {
				// Удаляем поле ввода улицы из списка валидируемых полей
				orderValidator.setValidate( streetField, {
					require: false
				});

				// Удаляем поле ввода номера дома из списка валидируемых полей
				orderValidator.setValidate( buildingField, {
					require: false
				});

				if ( subwayArray !== undefined ) {
					// Удаляем поле метро из списка валидируемых полей
					orderValidator.setValidate( subwayField , {
						require: false
					});
				}
			}

			console.info('Изменен тип доставки');
			console.log(orderValidator);
		};
	// end of functions
	
	$.mask.definitions['n'] = '[0-9]';
	bonusCardNumber.mask('2 98nnnn nnnnnn', {
		placeholder: '*'
	});
	qiwiPhone.mask('(nnn) nnn-nn-nn');
	phoneField.mask('(nnn) nnn-nn-nn');

	/**
	 * AB-test
	 * Обязательное поле e-mail
	 */
	if ( window.docCookies.getItem('emails') ) {
		console.log('AB TEST: e-mail require');

		orderValidator.setValidate( emailField , {
			require: true
		});
	}


	/**
	 * Подстановка значений в поля
	 */
	var defaultValueToField = function defaultValueToField( fields ) {
		var
			fieldNode = null,
			field;
		// end of vars

		console.groupCollapsed('Подстановка значений в поля defaultValueToField()');
		for ( field in fields ) {
			console.log('поле '+field);
			
			if ( fields[field] ) {
				console.log('для поля есть значение '+fields[field]);
				fieldNode = $('input[name="'+field+'"]');
				var fieldType = fieldNode.attr('type');

				// радио кнопка
				if ( fieldType === 'radio' ) {
					fieldNode.filter('[value="'+fields[field]+'"]').attr('checked', 'checked').trigger('change');
					
					continue;
				}


				// поле текстовое
				if ( $.inArray(fieldType, ['text', 'password', 'color', 'date', 'datetime', 'datetime-local', 'email', 'number', 'range', 'search', 'tel', 'time', 'url', 'month', 'week']) != -1 ) {
					fieldNode.val( fields[field] );
				}
			}
		}
        console.groupEnd();
	};
	defaultValueToField($('#jsOrderForm').data('value'));


	/**
	 * Включение автокомплита метро
	 * Подстановка станции метро по id
	 */
	if ( subwayArray !== undefined ) {
		subwayField.autocomplete(subwayAutocompleteConfig);
		subwayField.bind('change', subwayChange);

		for ( var i = subwayArray.length - 1; i >= 0; i-- ) {
			if ( parseInt(metroIdFiled.val(), 10) === subwayArray[i].val ) {
				subwayField.val(subwayArray[i].label);

				break;
			}
		}
	}

	$('body').bind('orderdeliverychange', orderDeliveryChangeHandler);
	orderCompleteBtn.bind('click', orderCompleteBtnHandler);

	console.log('orderValidation.js inited');

}(this, this.document, this.jQuery, this.ENTER));
/**
/**
 * Получение данных с сервера
 * Разбиение заказа
 * Модель knockout
 * Аналитика
 *
 * @author	Zaytsev Alexandr
 */
;(function ( window, document, $, ENTER, ko ) {
	console.info('separate-order.js init');
	
	var
		serverData = $('#jsOrderDelivery').data('value'),
		utils = ENTER.utils,
		body = $(document.body);
	// end of vars


	var
		/**
		 * Логика разбиения заказа на подзаказы
		 * Берутся states из выбранного способа доставки в порядке приоритета.
		 * Каждый новый states - новый подзаказ.
		 *
		 * @param	{Array}		statesPriority		Приоритет методов доставки
		 * 
		 * @param	{Object}	preparedProducts	Уже обработанные продукты, которые попали в какой-либо блок доставки
		 * @param	{Array}		productInState		Массив продуктов, которые есть в данном способе доставки
		 * @param	{Array}		productsToNewBox	Массив продуктов, которые должны попасть в новый блок доставки
		 * @param	{Number}	choosenPointForBox	Точка доставки для блока самовывоза
		 * @param	{String}	token				Временное имя для создаваемого блока
		 * @param	{String}	nowState			Текущий тип доставки который находится в обработке
		 * @param	{String}	nowProduct			Текущий id продукта который находится в обработке
		 */
		separateOrder = function separateOrder( statesPriority ) {

		var
			preparedProducts = {},
			productInState = [],
			productsToNewBox = [],
			choosenPointForBox = null,
			token = null,
			nowState = null,
			nowProduct = null,
			choosenBlock = null,
			isUnique = null,
			nowProductsToNewBox = [],
            oldDeliveryBoxes = [],
			discounts = ENTER.OrderModel.orderDictionary.orderData.discounts || [],
			deliveryBoxFound;
		// end of vars
		
		if ( ENTER.OrderModel.paypalECS() ) {
			console.info('PayPal ECS включен. Необходимо сохранить выбранные параметры в cookie');

			window.docCookies.setItem('chTypeBtn_paypalECS', ENTER.OrderModel.deliveryTypesButton, 10 * 60);
			window.docCookies.setItem('chPoint_paypalECS', ENTER.OrderModel.choosenPoint(), 10 * 60);
			window.docCookies.setItem('chTypeId_paypalECS', ENTER.OrderModel.choosenDeliveryTypeId, 10 * 60);
			window.docCookies.setItem('chStetesPriority_paypalECS', JSON.stringify(ENTER.OrderModel.statesPriority), 10 * 60);
		}

        if (ENTER.OrderModel.deliveryBoxes().length) {
            oldDeliveryBoxes = ko.toJS(ENTER.OrderModel.deliveryBoxes());
            console.info('[DeliveryBox] Существуют старые блоки доставки', oldDeliveryBoxes);
        }

		// очищаем объект созданых блоков, удаляем блоки из модели
		ENTER.OrderModel.deliveryBoxes.removeAll();

		// обнуляем примененный купон
		ENTER.OrderModel.hasCoupons(false);

		// Маркируем выбранный способ доставки
		console.log('Маркируем выбранный способ доставки');
		$('#'+ENTER.OrderModel.deliveryTypesButton).attr('checked','checked').trigger('change');
			
		// Обнуляем общую стоимость заказа
		ENTER.OrderModel.totalSum(0);

		// Обнуляем блоки с доставкой на дом и генерируем событие об этом
		ENTER.OrderModel.hasHomeDelivery(false);
		body.trigger('orderdeliverychange',[false]);


		/**
		 * Перебор states в выбранном способе доставки в порядке приоритета
		 */
		for ( var i = 0, len = statesPriority.length; i < len; i++ ) {
			nowState = statesPriority[i];
			isUnique = ENTER.OrderModel.orderDictionary.isUniqueDeliveryState(nowState);

			console.info('перебирем ' + (isUnique ? 'уникальный* ' : '') + 'метод ' + nowState);

			productsToNewBox = [];

			if ( !ENTER.OrderModel.orderDictionary.hasDeliveryState(nowState) ) {
				console.info('для метода '+nowState+' нет товаров');

				continue;
			}

			productInState = ENTER.OrderModel.orderDictionary.getProductFromState(nowState);

			/**
			 * Перебор продуктов в текущем deliveryStates
			 */
			for ( var j = productInState.length - 1; j >= 0; j-- ) {
				nowProduct = productInState[j];

				if ( preparedProducts[nowProduct] ) {
					// если этот товар уже находили
					console.log('товар '+nowProduct+' уже определялся к блоку');

					continue;
				}
				
				console.log('добавляем товар '+nowProduct+' в блок для метода '+nowState);

				preparedProducts[nowProduct] = true;
				productsToNewBox.push( ENTER.OrderModel.orderDictionary.getProductById(nowProduct) );
			}

			if ( productsToNewBox.length ) {
				choosenPointForBox = ( ENTER.OrderModel.orderDictionary.hasPointDelivery(nowState) ) ?
					ENTER.OrderModel.choosenPoint() :
					ENTER.OrderModel.orderDictionary.getDefaultPointId(nowState);

				token = nowState+'_'+choosenPointForBox;

				if ( ENTER.OrderModel.hasDeliveryBox(token) ) {
					// Блок для этого типа доставки в этот пункт уже существует
					choosenBlock = ENTER.OrderModel.getDeliveryBoxByToken(token);
					choosenBlock.addProductGroup( productsToNewBox );
				}
				else if ( isUnique ) {
					// Блока для этого типа доставки в этот пункт еще не существует, создадим его:
					// Если есть флаг уникальности, каждый товар в отдельном блоке будет

					// Разделим товары, продуктом считаем уникальную единицу товара:
					// Пример: 5 тетрадок ==> 5 товаров количеством 1 шт
					nowProductsToNewBox = ENTER.OrderModel.prepareProductsQuantityByUniq(productsToNewBox);
					for ( j = nowProductsToNewBox.length - 1; j >= 0; j-- ) {
						nowProduct = [ nowProductsToNewBox[j] ];
						ENTER.constructors.DeliveryBox(nowProduct, nowState, choosenPointForBox);
					}

				} else {
					// Блока для этого типа доставки в этот пункт еще не существует, создадим его:
					// Без флага уникальности, все товары скопом:
					// Пример: 5 тетрадок ==> 1 товар количеством 5 шт
					ENTER.constructors.DeliveryBox(productsToNewBox, nowState, choosenPointForBox);
				}
			}
		}

        for ( var a in oldDeliveryBoxes ) {
            if (ENTER.OrderModel.hasDeliveryBox(oldDeliveryBoxes[a].token)) {
            	deliveryBoxFound = ENTER.OrderModel.getDeliveryBoxByToken(oldDeliveryBoxes[a].token);

                console.log('[Deliverybox] Обнаружен старый блок доставки: ', oldDeliveryBoxes[a].token, ' c выбранной датой ', oldDeliveryBoxes[a].choosenDate);
                console.info('[Deliverybox] Применяю старую дату на блок ', oldDeliveryBoxes[a].token);

                console.warn(oldDeliveryBoxes[a].allDatesForBlock);
                console.warn(oldDeliveryBoxes[a].allDatesForBlock.length);

                deliveryBoxFound.choosenDate(oldDeliveryBoxes[a].choosenDate);
                deliveryBoxFound.allDatesForBlock.removeAll();
                deliveryBoxFound.allDatesForBlock(oldDeliveryBoxes[a].allDatesForBlock);

                console.log(deliveryBoxFound.allDatesForBlock());
                console.log(deliveryBoxFound.allDatesForBlock().length);
            }
        }

		console.info('Созданные блоки:', ENTER.OrderModel.deliveryBoxes());

        if (ENTER.OrderModel.deliveryBoxes().length > 1) body.trigger('trackUserAction', ['1_2 Доставка, заказ разбит', ENTER.OrderModel.deliveryBoxes().length]);

		// Добавляем купоны
		ENTER.OrderModel.couponsBox(discounts);

		// выбираем URL для проверки купонов - первый видимый купон
		ENTER.OrderModel.couponUrl( $('.bSaleList__eItem:visible .jsCustomRadio').eq(0).val() );
		$('.bSaleList__eItem:visible .jsCustomRadio').eq(0).trigger('change');


		// выбираем первый доступный метод оплаты
		if ( 0 === $('.bPayMethod:visible .jsCustomRadio:checked').length ) {
			$('.bPayMethod:visible .jsCustomRadio').eq(0).attr('checked', 'checked').trigger('change');
		}

		/**
		 * Проверка примененных купонов
		 *
		 * Если заказ разбился, то купон применять нельзя или
		 * Если сумма заказа меньше либо равана размеру скидки купона
		 */
		if ( ( ENTER.OrderModel.hasCoupons() && ENTER.OrderModel.deliveryBoxes().length > 1 ) || 
			( ENTER.OrderModel.appliedCoupon() && ENTER.OrderModel.appliedCoupon().sum && 
			( parseFloat(ENTER.OrderModel.totalSum())+parseFloat(ENTER.OrderModel.appliedCoupon().sum) <= parseFloat(ENTER.OrderModel.appliedCoupon().sum) ) ) ) {
			console.warn('Нужно удалить купон');

			var msg = 'Купон не может быть применен при текущем разбиении заказа и будет удален';

			var callback = function() {
				console.log('удаление');
				ENTER.OrderModel.deleteItem(ENTER.OrderModel.appliedCoupon());
			};

			$.when(showError(msg)).then(callback);

			return false;
		}

		if ( preparedProducts.length !== ENTER.OrderModel.orderDictionary.orderData.products.length ) {
			console.warn('не все товары были обработаны');
		}

		console.warn('end');


		$('.bCountSection').goodsCounter({
			onChange:function( count ) {
				console.info('counter change', count);

                body.trigger('trackUserAction', ['1_4_3 Число товаров']);

				var
					seturl = $(this).data('seturl') || '',
					newURl = seturl.addParameterToUrl('quantity', count),
					reqArray;
				// end of vars
				
				console.log(seturl);
				console.log(newURl);

				var
					/**
					 * Обработка ответа измеения количества товаров
					 * 
					 * @param	{Object}	res		Ответ от сервера
					 */
					spinnerResponceHandler = function spinnerResponceHandler( res ) {
						if ( !res.success ) {
							ENTER.OrderModel.couponError(res.error.message);
							utils.blockScreen.unblock();

							return;
						}

						ENTER.OrderModel.couponNumber('');
					};
				// end of functions

				utils.blockScreen.block('Обновляем');

				reqArray = [
					{
						type: 'GET',
						url: newURl,
						// data: dataToSend,
						callback: spinnerResponceHandler
					},
					{
						type: 'GET',
						url: ENTER.OrderModel.updateUrl,
						callback: ENTER.OrderModel.modelUpdate
					}
				];

				utils.packageReq(reqArray);
			}
		});
	};


	/**
	 * Кастомный бинд для открытия окна магазинов
	 */
	ko.bindingHandlers.popupShower = {
		update: function( element, valueAccessor ) {
			var val = valueAccessor(),
				unwrapVal = ko.utils.unwrapObservable(val),
				map = null;
			// end of vars

			if ( unwrapVal ) {
				// create map
				map = new ENTER.constructors.CreateMap('pointPopupMap', ENTER.OrderModel.popupWithPoints().points, $('#mapInfoBlock'));

				$(element).lightbox_me({
					centered: true,
					onClose: function() {
						console.info('закрываем');
						val(false);
					}
				});
				
				map._showMarkers();
			}
			else {
				$('#pointPopupMap').empty();
				$(element).trigger('close');
			}
		}
	};

	/**
	 * Кастомный бинд для отображения блоков с методами оплаты: "прямо сейчас", "при получении", в кредит..
	 */
	ko.bindingHandlers.payBlockVisible = {
		update: function( element ) {
			var
				node = $(element),
				vars = node.data('vars'),
				toHide = (vars && vars.toHide) ? vars.toHide : false,
				choosenDeliveryTypeId = ENTER.OrderModel.choosenDeliveryTypeId,
				deliveryBoxes = ENTER.OrderModel.deliveryBoxes(),
				dCount = deliveryBoxes.length,
				testDeliveryId,
				testPaymentId,
				nodeHidded = 1
				;

			if ( !dCount ) {
				return;
			}

			/*
			 * Cтарый механизм показа/сокрытия блоков
			 * показываем "кредиты" и "оплату сейчас", если кол-во блоков доставки == 1
			 */
			if ( 1 === dCount ) {
				nodeHidded = 0;
				console.log('Кол-во deliveryBoxes == 1: Показываем payBlock');
			}
			else {
				nodeHidded = 1;
				console.log('Кол-во deliveryBoxes > 1: Скрываем payBlock');
			}

			/**
			 * Если указано toHide в дата-аттрибуте, то скрываем блоки с недоступными методами
			 */
			if ( toHide ) {

				for ( testDeliveryId in toHide ) {
					if ( 'undefined' === typeof toHide[testDeliveryId].length ) {	// !не массив, скрываем для всех
						if ( $.inArray(choosenDeliveryTypeId, toHide) >= 0 ) {
							nodeHidded = 1;
							console.log('toHide NoArr: Скрываем payBlock');
						}
					}
					else if ( choosenDeliveryTypeId === testDeliveryId ) {			// !массив, обходим блоки оплаты
						for ( testPaymentId in toHide[testDeliveryId] ) {
							if ( testPaymentId === vars.typeId ) {
								nodeHidded = 1;
								console.log('toHide Arr: Скрываем payBlock');
							}
						}// end of second for
					}
				}// end of first for

			}

			nodeHidded ? node.hide() : node.show(); // показываем либо скрываем элемент
		}
	};


	/**
	 * Кастомный бинд отображения методов оплаты
	 */
	ko.bindingHandlers.paymentMethodVisible = {
		update: function( element, valueAccessor ) {
			var
				val = valueAccessor(),
				unwrapVal = ko.utils.unwrapObservable(val),
				node = $(element),
				nodeData = node.data('value'),
				maxSum = parseInt( nodeData['max-sum'], 10 ),
				minSum = parseInt( nodeData['min-sum'], 10 ),
				methodId = nodeData['method_id'],
				isAvailableToPickpoint = nodeData['isAvailableToPickpoint'];
			// end of vars


			if (
			/* 6 is DeliveryTypeId for PickPoint  */
			( 6 === ENTER.OrderModel.choosenDeliveryTypeId && false === isAvailableToPickpoint ) ||
			( 4 === ENTER.OrderModel.choosenDeliveryTypeId && 13 === methodId && ENTER.OrderModel.lifeGift() === false ) ||
			( !isNaN(maxSum) && maxSum < unwrapVal ) || /* Если существует максимальная сумма и текущая сумма больше максимальнодопустимой для этого варианта оплаты */
			( !isNaN(minSum) && minSum > unwrapVal ) /* Если существует минимальная сумма и текущая сумма больше минимальнодопустимой для этого варианта оплаты */ ) {
				node.hide();
                if (methodId == 13 && -1 == $.inArray(ENTER.OrderModel.choosenDeliveryTypeId, [3,4])) node.show(); // показываем Paypal для всех методов доставки, кроме "самовывоза" и "заберу сейчас"
				return;
			}

			node.show();
		}
	};

	/**
	 * Кастомный бинд для смены недель, анимирование слайдера
	 */
	ko.bindingHandlers.calendarSlider = {
		update: function( element, valueAccessor, allBindingsAccessor, viewModel, bindingContext ) {
			var
				slider = $(element),
				nowLeft = valueAccessor(),

				dateItem = slider.find('.bBuyingDatesItem'),
				dateItemW = dateItem.width() + parseInt(dateItem.css('marginRight'), 10) + parseInt(dateItem.css('marginLeft'), 10);
			// end of vars

			slider.width(dateItem.length * dateItemW);

			if ( nowLeft > 0 ) {
				nowLeft -= 380;
				bindingContext.box.calendarSliderLeft(nowLeft);

				return;
			}

			if ( nowLeft < -slider.width() ) {
				nowLeft += 380;
				bindingContext.box.calendarSliderLeft(nowLeft);

				return;
			}

			slider.animate({'left': nowLeft});
		}
	};

	/**
	 * Кастомынй бинд отображения и смены купонов
	 */
	ko.bindingHandlers.couponsVisible = {
		update: function( element, valueAccessor ) {
			var
				val = valueAccessor(),
				unwrapVal = ko.utils.unwrapObservable(val),

				node = $(element),
				fieldNode = node.find('.mSaleInput'),
				buttonNode = node.find('.mSaleBtn'),

				emptyBlock = node.find('.bSaleData__eEmptyBlock'),

				i;
			// end of vars

			$('.bSaleList__eItem').removeClass('hidden');

			for ( i = unwrapVal.length - 1; i >= 0; i-- ) {
				node.find('.bSaleList__eItem[data-type="'+unwrapVal[i].type+'"]').addClass('hidden');

				if ( unwrapVal[i].type === 'coupon' ) {
					console.log('Есть примененный купон');

					ENTER.OrderModel.hasCoupons(true);
					ENTER.OrderModel.appliedCoupon(unwrapVal[i]);
				}
			}

			if ( $('.bSaleList__eItem.hidden').length === $('.bSaleList__eItem').length ||
				$('.bSaleList__eItem:hidden').length === $('.bSaleList__eItem').length ) {
				// если все скидки применены

				fieldNode.attr('disabled', 'disabled');
				buttonNode.attr('disabled', 'disabled').addClass('mDisabled');
				emptyBlock.show();
			}
			else {
				// не все скидки применены

				fieldNode.removeAttr('disabled');
				buttonNode.removeAttr('disabled').removeClass('mDisabled');
				emptyBlock.hide();
			}
		}
	};


	/**
	 * === ORDER MODEL ===
	 */
	ENTER.OrderModel = {
		/**
		 * URL для обновления данных с сервера
		 */
		updateUrl: $('#jsOrderDelivery').data('url'),

		/**
		 * Флаг завершения обработки данных
		 *
		 * @type {Boolean}
		 */
		prepareData: ko.observable(false),

		/**
		 * Флаг открытия окна с выбором точек доставки
		 *
		 * @type {Boolean}
		 */
		showPopupWithPoints: ko.observable(false),

		/**
		 * Ссылка на элемент input который соответствует выбранному методу доставки
		 */
		deliveryTypesButton: null,

		/**
		 * Приоритет методов доставок на время выбора точек доставки
		 * Если пункт доставки не был выбран - не используется
		 */
		tmpStatesPriority: null,

		/**
		 * Реальный приоритет методов доставок
		 * Сохраняется при выборе пункта доставки или методе доставки не имеющем пунктов доставки
		 */
		statesPriority: null,

		/**
		 * Флаг того что это оформление заказа по акции «Подари жизнь»
		 * https://jira.enter.ru/browse/SITE-2383
		 * 
		 * @type {Boolean}
		 */
		lifeGift: ko.observable(false),

		/**
		 * Флаг того что это оформление заказа типа one-click
		 * https://jira.enter.ru/browse/SITE-2592
		 * 
		 * @type {Boolean}
		 */
		oneClick: ko.observable(false),

		/**
		 * Флаг того что это страница PayPal: схема ECS
		 * https://jira.enter.ru/browse/SITE-1795
		 *
		 * @type {Boolean}
		 */
		paypalECS: ko.observable(false),

		/**
		 * Первоначальная сумма корзины
		 */
		cartSum: null,

		/**
		 * Ссылка на словарь
		 */
		orderDictionary: null,

		/**
		 * Идетификатор приоритетного пункта доставки выбранного пользователем
		 */
		choosenPoint: ko.observable(),

		/**
		 * Есть ли хотя бы один блок доставки на дом
		 */
		hasHomeDelivery: ko.observable(false),

		/**
		 * Массив способов доставок доступных пользователю
		 */
		deliveryTypes: ko.observableArray([]),

		/**
		 * Массив блоков доставок
		 */
		deliveryBoxes: ko.observableArray([]),

		/**
		 * Объект данных для отображения окна с пунктами доставок
		 */
		popupWithPoints: ko.observable({}),

		/**
		 * Общая сумма заказа
		 */
		totalSum: ko.observable(0),

		/**
		 * Есть ли примененные купоны
		 *
		 * @type {Boolean}
		 */
		hasCoupons: ko.observable(false),

		/**
		 * Размер скидки примененного купона
		 */
		appliedCoupon: ko.observable(),

		/**
		 * Номер введенного сертификата
		 */
		couponNumber: ko.observable(),

		/**
		 * URL по которому нужно проверять карту
		 */
		couponUrl: ko.observable(),

		/**
		 * Ошибки сертификата
		 */
		couponError: ko.observable(),

		/**
		 * Массив примененных купонов
		 */
		couponsBox: ko.observableArray([]),


		/**
		 * Существует ли блок доставки
		 * 
		 * @param	String}		token	Токен блока доставки
		 * @return	{boolean}
		 */
		hasDeliveryBox: function( token ) {
			console.info('Существует ли блок доставки '+token);

			var i = null;

			for ( i = ENTER.OrderModel.deliveryBoxes().length - 1; i >= 0; i--) {
				if ( ENTER.OrderModel.deliveryBoxes()[i].token === token ) {
					return true;
				}
			}

			return false;
		},

		/**
		 * Получить ссылку на блок по токену
		 * 
		 * @param	String}		token	Токен блока доставки
		 * @return	{Object}			Объект блока
		 */
		getDeliveryBoxByToken: function( token ) {
			console.info('Получить ссылку на блок по токену '+token);

			var i = null;

			for ( i = ENTER.OrderModel.deliveryBoxes().length - 1; i >= 0; i--) {
				if ( ENTER.OrderModel.deliveryBoxes()[i].token === token ) {
					return ENTER.OrderModel.deliveryBoxes()[i];
				}
			}

			return false;
		},

		/**
		 * Удаление блока доставки по токену
		 * 
		 * @param	String}		token	Токен блока доставки
		 * @returns	{*}			DeliveryBox (удалённый блок доставки либо null)
		 */
		removeDeliveryBox: function( token ) {
			console.info('Поиск для удаления блока по токену ' + token);

			var
				i,
				ret = null,
				dBoxes = ENTER.OrderModel.deliveryBoxes(),
				dBCount = dBoxes.length;

			for ( i = dBCount - 1; i >= 0; i-- ) {
				if ( dBoxes[i].token === token ) {
					console.info('Удаление блока по токену ' + token);
					ret = ENTER.OrderModel.deliveryBoxes.splice(i, 1);
					if ( 'object' === typeof(ret[0]) ) {
						ret = ret[0];
					}
					break;
				}
			}

			return ret;
		},

		/**
		 * Проверка сертификата
		 */
		checkCoupon: function() {
			console.info('проверяем купон');

			var dataToSend = {
					number: ENTER.OrderModel.couponNumber()
				},

				url = ENTER.OrderModel.couponUrl(),

				reqArray;
			// end of vars

			var couponResponceHandler = function couponResponceHandler( res ) {
				if ( !res.success ) {
					ENTER.OrderModel.couponError(res.error.message);
					utils.blockScreen.unblock();
                    body.trigger('trackUserAction', ['2 Купон', 'Отказ']);
					return;
				}
                body.trigger('trackUserAction', ['2 Купон', 'Принят']);
				ENTER.OrderModel.couponNumber('');
			};

			ENTER.OrderModel.couponError('');

			if ( url === undefined ) {
				console.warn('Не выбран тип скидки');
				ENTER.OrderModel.couponError('Не выбран тип скидки');

				return;
			}

			if ( dataToSend.number === undefined || !dataToSend.number.length ) {
				console.warn('Не введен номер');
				ENTER.OrderModel.couponError('Не введен номер');

				return;
			}

			utils.blockScreen.block('Применяем купон');

			reqArray = [
				{
					type: 'POST',
					url: url,
					data: dataToSend,
					callback: couponResponceHandler
				},
				{
					type: 'GET',
					url: ENTER.OrderModel.updateUrl,
					callback: ENTER.OrderModel.modelUpdate
				}
			];

			utils.packageReq(reqArray);

			return false;
		},

		/**
		 * Обработка выбора пункта доставки
		 * 
		 * @param	{String}	id				Идентификатор
		 * @param	{String}	address			Адрес
		 * @param	{Number}	latitude		Широта
		 * @param	{Number}	longitude		Долгота
		 * @param	{String}	name			Полное имя
		 * @param	{String}	regime			Время работы
		 * @param	{Array}		products		Массив идентификаторов продуктов доступных в данном пункте
		 */
		selectPoint: function( data ) {
			console.info('point selected...');
			console.log(data.parentBoxToken);

			var choosenBlock = null;

			if ( data.parentBoxToken ) {
				choosenBlock = ENTER.OrderModel.getDeliveryBoxByToken(data.parentBoxToken);
				console.log(choosenBlock);
				choosenBlock.selectPoint.apply(choosenBlock,[data]);

				return false;
			}

			// Сохраняем приоритет методов доставок
			ENTER.OrderModel.statesPriority = ENTER.OrderModel.tmpStatesPriority;

			// Сохраняем выбранную приоритетную точку доставки
			ENTER.OrderModel.choosenPoint(data.id);

			// Скрываем окно с выбором точек доставок
			ENTER.OrderModel.showPopupWithPoints(false);

			// Разбиваем на подзаказы
			separateOrder( ENTER.OrderModel.statesPriority );

			return false;
		},

		/**
		 * Выбор метода доставки
		 * 
		 * @param	{Object}	data			Данные о типе доставки
		 * @param	{String}	data.token		Выбранный способ доставки
		 * @param	{String}	data.name		Имя выбранного способа доставки
		 * @param	{Array}		data.states		Варианты типов доставки подходящих к этому методу
		 *
		 * @param	{String}	priorityState	Приоритетный метод доставки из массива
		 * @param	{Object}	checkedInputId	Ссылка на элемент input по которому кликнули
		 */
		chooseDeliveryTypes: function( data, event ) {
			console.info('chooseDeliveryTypes');

			var
				priorityState = data.states[0],
				checkedInputId = event.target.htmlFor;
			// end of vars

			console.log(priorityState);
			console.log(checkedInputId);

            body.trigger('trackUserAction', ['1_1 Доставка', data.shortName]);

			if ( $('#'+checkedInputId).attr('checked') ) {
				console.warn('Этот пункт '+checkedInputId+' уже был выбран');

				return false;
			}

			ENTER.OrderModel.deliveryTypesButton = checkedInputId;
			console.log(ENTER.OrderModel.deliveryTypesButton);

			ENTER.OrderModel.tmpStatesPriority = data.states;
			console.log(ENTER.OrderModel.tmpStatesPriority);

			ENTER.OrderModel.choosenDeliveryTypeId = data.id;
			console.log(ENTER.OrderModel.choosenDeliveryTypeId);

			// если для приоритетного метода доставки существуют пункты доставки, то пользователю необходимо выбрать пункт доставки, если нет - то приравниваем идентификатор пункта доставки к 0
			if ( ENTER.OrderModel.orderDictionary.hasPointDelivery(priorityState) ) {
				console.log('Необходимо показать окно с выбором точки доставки');

				ENTER.OrderModel.popupWithPoints({
					header: data.name,
					points: ENTER.OrderModel.orderDictionary.getAllPointsByState(priorityState)
				});

				ENTER.OrderModel.showPopupWithPoints(true);

				return false;
			}

			console.log('Выбор точки доставки не требуется');

			// Сохраняем приоритет методов доставок
			ENTER.OrderModel.statesPriority = ENTER.OrderModel.tmpStatesPriority;

			// Сохраняем выбранную приоритетную точку доставки (для доставки домой = 0)
			ENTER.OrderModel.choosenPoint(0);

			// Разбиваем на подзаказы
			console.info('Отправляем данные на разбивку');
			separateOrder( ENTER.OrderModel.statesPriority );

			return false;
		},


		/**
		 * Обновление данных
		 */
		modelUpdate: function( res ) {
			console.info('обновление данных с сервера');

			renderOrderData(res);

			separateOrder( ENTER.OrderModel.statesPriority );
		},

		/**
		 * Удаление товара
		 * 
		 * @param	{Object}	data	Данные удалямого товара
		 */
		deleteItem: function( data ) {
			console.info('удаление товара');

			var reqArray = null;

			utils.blockScreen.block('Удаляем');

			var itemDeleteAnalytics = function itemDeleteAnalytics( data ) {
					var products = ENTER.OrderModel.orderDictionary.products,
						totalPrice = 0,
						totalQuan = 0,

						toKISS = {};
					// end of vars

					if ( !data.product ) {
						return false;
					}

					for ( var product in products ) {
						totalPrice += products[product].price;
						totalQuan += products[product].quantity;
					}

					toKISS = {
						'Checkout Step 1 SKU Quantity': totalQuan,
						'Checkout Step 1 SKU Total': totalPrice
					};

					if ( typeof _kmq !== 'undefined' ) {
						_kmq.push(['set', toKISS]);
					}

					if ( typeof _gaq !== 'undefined' ) {
						_gaq.push(['_trackEvent', 'Order card', 'Item deleted']);
					}

					if ( data.hasOwnProperty('product') && data.product.hasOwnProperty('id') ) {
						/* RetailRocket */
						console.info('RetailRocket removeFromCart');
						console.log('product_id=' + data.product.id);
						window.rrApiOnReady.push(function(){ window.rrApi.removeFromBasket(data.product.id) });
					}
				},

				deleteItemResponceHandler = function deleteItemResponceHandler( res ) {
					console.info('deleteItemResponceHandler');
					console.log( res );

					if ( !res.success ) {
						console.warn('не удалось удалить товар');
						utils.blockScreen.unblock();

						return false;
					}

					// запуск аналитики
					itemDeleteAnalytics(res);

					if ( res.product ) {
						var productId = res.product.id;
						var categoryId = res.category_id;

						// Soloway
						// Чтобы клиент не видел баннер с товаром которого нет на сайте и призывом купить
						(function(s){
							var d = document, i = d.createElement('IMG'), b = d.body;
							s = s.replace(/!\[rnd\]/, Math.round(Math.random()*9999999)) + '&tail256=' + escape(d.referrer || 'unknown');
							i.style.position = 'absolute'; i.style.width = i.style.height = '0px';
							i.onload = i.onerror = function(){b.removeChild(i); i = b = null;};
							i.src = s;
							b.insertBefore(i, b.firstChild);
						})('http://ad.adriver.ru/cgi-bin/rle.cgi?sid=182615&sz=del_basket&bt=55&pz=0&custom=10='+productId+';11='+categoryId+'&![rnd]');
					}
				};
			// end of functions

			console.log(data.deleteUrl);

			reqArray = [
				{
					type: 'GET',
					url: data.deleteUrl,
					callback: deleteItemResponceHandler
				},
				{
					type: 'GET',
					url: ENTER.OrderModel.updateUrl,
					callback: ENTER.OrderModel.modelUpdate
				}
			];

			utils.packageReq(reqArray);

			return false;
		},


		/**
		 *  Раразбивка массива товаров в массив по уникальным единицам (для PickPoint)
		 *  т.е. вместо продукта в количестве 2 шт, будут 2 проудкта по 1 шт.
		 *
		 * @param       {Array}   productsToNewBox
		 * @returns     {Array}   productsUniq
		 */
		prepareProductsQuantityByUniq: function prepareProductsQuantityByUniq( productsToNewBox ) {
			var productsUniq = [],
				nowProduct,
				j, k;

			for ( j = productsToNewBox.length - 1; j >= 0; j-- ) {
				//!!! важно клонировать объект, дабы не портить для др. типов доставки
				nowProduct = ENTER.utils.cloneObject(productsToNewBox[j]);
				nowProduct.sum = nowProduct.price;
				nowProduct.quantity = 1;
				nowProduct.oldQuantity = productsToNewBox[j].quantity; // сохраняем старое кол-во товаров в блоке
				for ( k = productsToNewBox[j].quantity - 1; k >= 0; k-- ) {
                    productsUniq.push(nowProduct);
				}
			}

			return productsUniq;
		}
	};

	ko.applyBindings(ENTER.OrderModel);
	/**
	 * ===  END ORDER MODEL ===
	 */
	


	var
		/**
		 * Показ сообщений об ошибках
		 * 
		 * @param	{String}	msg		Сообщение об ошибке
		 * @return	{Object}			Deferred объект
		 */
		showError = function showError( msg ) {
			var content = '<div class="popupbox width290">' +
					'<div class="font18 pb18"> '+msg+'</div>'+
					'</div>' +
					'<p style="text-align:center"><a href="#" class="closePopup bBigOrangeButton">OK</a></p>',
				block = $('<div>').addClass('popup').html(content),

				popupIsClose = $.Deferred();
			// end of vars
			
			block.appendTo('body');

			var errorPopupCloser = function() {
				block.trigger('close');
				block.remove();

				popupIsClose.resolve();
			};

			block.lightbox_me({
				centered:true,
				closeClick:false,
				closeEsc:false
			});

			block.find('.closePopup').bind('click', errorPopupCloser);

			return popupIsClose.promise();
		},

		/**
		 * Обработка ошибок в продуктах
		 */
		productError = {
			// Товар недоступен для продажи
			'default': function( product ) {
				var msg = 'Товар ' + (product.name ? product.name : '') + ' недоступен для продажи.',

					productErrorIsResolve = $.Deferred();
				// end of vars

				$.when(showError(msg)).then(function() {
					$.ajax({
						type:'GET',
						url: product.deleteUrl
					}).then(productErrorIsResolve.resolve);
				});

				return productErrorIsResolve.promise();

			},

			// Нет необходимого количества товара
			708: function( product ) {
				var msg = '',

					productErrorIsResolve = $.Deferred();
				// end of vars
				
				if ( product.name && product.error.message && product.quantity ) {
					msg = 'Вы заказали товар ' + product.name + ' в количестве ' + product.quantity + ' шт. <br/ >' + product.error.message;
				}
				else {
					msg = 'Товар недоступен для продажи';
				}

				$.when(showError(msg)).then(function() {
				    var reqArray = [
				        {
				            type: 'GET',
				            url: product.setUrl,
				            callback: productErrorIsResolve.resolve
				        },
				        {
				            type: 'GET',
				            url: ENTER.OrderModel.updateUrl,
				            callback: ENTER.OrderModel.modelUpdate
				        }
				    ];

				    utils.packageReq(reqArray);

				    return productErrorIsResolve.promise();
				});
			}
		},

		/**
		 * Обработка ошибок в данных
		 *
		 * @param	{Object}	res		Данные о заказе
		 * 
		 * @param	{Object}	product	Данные о продукте
		 * @param	{Number}	code	Код ошибки
		 */
		allErrorHandler = function allErrorHandler( res ) {
			var product = null,

				productsWithError = [];
			// end of vars

			// Cоздаем массив продуктов содержащих ошибки
			for ( product in res.products ) {
				if ( res.products[product].error && res.products[product].error.code ) {
					productsWithError.push(res.products[product]);
				}
			}

			// Обрабатываем ошибки продуктов по очереди
			var errorCatcher = function errorCatcher( i, callback ) {
				var code = null;

				if ( i < 0 ) {
					console.warn('return');

					callback();
					return;
				}

				code = productsWithError[i].error.code;

				code = ( productError.hasOwnProperty(code) ) ? code : 'default';

				$.when( productError[code](productsWithError[i]) ).then(function() {
					var newI = i - 1;

					errorCatcher( newI, callback );
				});
			};

			/**
			 * Если ошибок в продуктах нет, но есть сообщаение об ошибке, вывести сообщение
			 * Иначе начать обработку ошибок в продуктах
			 */
			if ( productsWithError.length === 0 && res.error.message ) {
				$.when(showError(res.error.message)).then(function() {
					if ( res.redirect ) {
						document.location.href = res.redirect;
					}
				});
			}
			else {
				errorCatcher(productsWithError.length - 1, function() {
					console.warn('1 этап закончен');
					if ( res.redirect ) {
						document.location.href = res.redirect;
					}
				});
			}
		},

		/**
		 * Обработка полученных данных
		 * Создание словаря
		 * 
		 * @param	{Object}	res		Данные о заказе
		 */
		renderOrderData = function renderOrderData( res ) {
			var data, firstPoint;
			utils.blockScreen.unblock();

			if ( !res.success ) {
				console.warn('Данные содержат ошибки');
				console.log(res.error);
				allErrorHandler(res);

				return false;
			}

            if (res.error && res.error.message) showError(res.error.message);

			console.info('Данные с сервера получены');

			ENTER.OrderModel.orderDictionary = new ENTER.constructors.OrderDictionary(res);

			if ( res.paypalECS ) {
				console.info('paypal true');
				ENTER.OrderModel.paypalECS(true);
			}

			if ( res.cart && res.cart.sum ) {
				console.info('Есть первоначальная сумма корзины : '+res.cart.sum);
				ENTER.OrderModel.cartSum = res.cart.sum;
			}

			ENTER.OrderModel.deliveryTypes(res.deliveryTypes);
			ENTER.OrderModel.lifeGift(res.lifeGift || false);
			ENTER.OrderModel.oneClick(res.oneClick || false);
			ENTER.OrderModel.prepareData(true);

			if ( ENTER.OrderModel.paypalECS() &&
				window.docCookies.hasItem('chTypeBtn_paypalECS') && 
				window.docCookies.hasItem('chPoint_paypalECS') &&
				window.docCookies.hasItem('chTypeId_paypalECS') && 
				window.docCookies.hasItem('chStetesPriority_paypalECS') ) {

				console.info('PayPal ECS включен. Необходимо применить параметры из cookie');

				ENTER.OrderModel.deliveryTypesButton = window.docCookies.getItem('chTypeBtn_paypalECS');
				ENTER.OrderModel.choosenPoint( window.docCookies.getItem('chPoint_paypalECS') );
				ENTER.OrderModel.choosenDeliveryTypeId = window.docCookies.getItem('chTypeId_paypalECS');
				ENTER.OrderModel.statesPriority = JSON.parse( window.docCookies.getItem('chStetesPriority_paypalECS') );

				separateOrder( ENTER.OrderModel.statesPriority );
			}


			if ( 1 === res.deliveryTypes.length ) {
				data = res.deliveryTypes[0];
				firstPoint =  ENTER.OrderModel.orderDictionary.getFirstPointByState( data.states[0] ) || data.id;

				console.log('Обнаружен только 1 способ доставки: ' + data.name +' — выбираем его.');
				console.log('Выбран первый пункт* доставки:');
				console.log( firstPoint );

				ENTER.OrderModel.statesPriority = data.states;
				ENTER.OrderModel.deliveryTypesButton = 'method_' + data.id;
				ENTER.OrderModel.choosenDeliveryTypeId = data.id;
				ENTER.OrderModel.choosenPoint( firstPoint );
				separateOrder( ENTER.OrderModel.statesPriority );
			}
		},

		selectPointOnBaloon = function selectPointOnBaloon( event ) {
			console.log('selectPointOnBaloon');
			console.log(event);

			console.log($(this).data('pointid'));
			console.log($(this).data('parentbox'));

			ENTER.OrderModel.selectPoint({
				id: $(this).data('pointid'),
				parentBoxToken: $(this).data('parentbox')				
			});

			return false;
		},

		/**
		 * Аналитика загрузки страницы orders/new
		 * 
		 * @param	{Object}	orderData		Данные о заказе
		 */
		analyticsStep_1 = function analyticsStep1( orderData ) {
			console.info('analyticsStep_1');

			var
				totalPrice = 0,
				totalQuan = 0,
                basketProd = [],

				toKISS = {},

				product;
			// end of vars

			for ( product in orderData.products ) {
				totalPrice += orderData.products[product].price;
				totalQuan += orderData.products[product].quantity;

                basketProd.push(
                    {
                    'id':       orderData.products[product].id,
                    'name':     orderData.products[product]['name'],
                    'price':    orderData.products[product].price,
                    'quantity': orderData.products[product].quantity
                    }
                );
			}

			toKISS = {
				'Checkout Step 1 SKU Quantity': totalQuan,
				'Checkout Step 1 SKU Total': totalPrice,
				'Checkout Step 1 Order Type': 'cart order'
			};

			if ( typeof _gaq !== 'undefined' ) {
				_gaq.push(['_trackEvent', 'New order', 'Items', totalQuan]);
			}

			if ( typeof _kmq !== 'undefined' ) {
				_kmq.push(['record', 'Checkout Step 1', toKISS]);
			}

            // ActionPay Analytics:
            window.APRT_DATA = window.APRT_DATA || {};
            window.APRT_DATA.pageType = 5; // оформление заказа (после корзины и до последней страницы заказа)
            window.APRT_DATA.orderInfo = window.APRT_DATA.orderInfo || {};
            window.APRT_DATA.orderInfo.totalPrice = totalPrice;
            window.APRT_DATA.basketProducts = basketProd;

        };
	// end of functions


	console.log(ENTER.OrderModel);

	renderOrderData( serverData );
	analyticsStep_1( serverData );

	body.on('click', '.shopchoose', selectPointOnBaloon);

}(this, this.document, this.jQuery, this.ENTER, this.ko));