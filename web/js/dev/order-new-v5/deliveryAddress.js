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
						showError('Не нашли ваш адрес на карте. Уточните');

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
						showError('Не нашли ваш адрес на карте. Уточните');

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
							showError('Не нашли ваш адрес на карте. Уточните');

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
									showError('Не нашли ваш адрес на карте. Уточните');

									return;
								}

								removeErrors();

								if ( street.val() !== objs[0].name ) {
									showError('Не нашли ваш адрес на карте. Уточните');
								}

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
									showError('Не нашли ваш адрес на карте. Уточните');

									return;
								}

								removeErrors();

								if ( building.val() !== objs[0].name ) {
									showError('Не нашли ваш адрес на карте. Уточните');
								}

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

			mapObj.show().width(460).height(350);
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