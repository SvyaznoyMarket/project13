/**
 * Order delivery address
 *
 * @author  Shaposhnik Vitaly
 */

;(function( ENTER ){
	var
		config = $('#page-config').data('value'),

		container = $('.jsDeliveryAddress'),
		data = container.data('value'),

		active = config.addressAutocomplete,

		token = data.kladr ? data.kladr.token : null,
		key = data.kladr ? data.kladr.key : null,
		limit = data.kladr ? data.kladr.itemLimit : 6,

		street = container.find('#order_address_street'),
		building = container.find('#order_address_building'),
		buildingAdd = container.find('#order_address_number'),
		metro = container.find('#order_address_metro'),

		error,

		map = null,
		map_created = false,

		cityName = data.regionName;
	// end of vars

	var
		/**
		 * Обновляем карту
 		 */
		mapUpdate = function(){
			var
				zoom = 12,
				address = '',

				name,
				type,
				obj,
				value,

				geocode,
				position,

				latitude, longitude;
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
				type = obj.type + '.';
			}
			else if ( value ) {
				name = value;
				type = 'улица';
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

			if ( address && map_created ) {
				console.log(address);

				geocode = ymaps.geocode(address);
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
					map.mapWS.setCenter(position, zoom);

					if ( metro.length ) {
						metroClosest(latitude, longitude);
					}
				});
			}
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

			myGeocoder = ymaps.geocode([latitude, longitude], {kind: 'metro'});
			myGeocoder.then(
				function ( res ) {
					nearest = res.geoObjects.get(0);
					name = nearest.properties.get('name');
					name = name.replace('метро ', '');

//					metro.parents('.jsInputMetro').hide();
					metro.val(name);
				},
				function ( err ) {
					console.warn('При выполнении запроса произошла ошибка: ' + err);

					metro.val('');
					metro.parents('.jsInputMetro').show();
				}
			);
		},


		/**
		 * Показ сообщений об ошибках
		 *
		 * @param   {String}    msg     Сообщение которое необходимо показать пользователю
		 */
		showError = function( msg ) {
			error = container.find('ul.error_list');

			if ( error.length ) {
				error.html('<li>' + msg + '</li>');
			}
			else {
				$('#map', container).before($('<ul class="error_list" />').append('<li>' + msg + '</li>'));
			}

			return false;
		},


		/**
		 * Убрать сообщения об ошибках
		 */
		removeErrors = function() {
			error = container.find('ul.error_list');

			if ( error.length ) {
				error.html('');
			}

			return false;
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
//							showError('КЛАДР не нашел указаный город');
							console.log('КЛАДР не нашел город ' + cityName);

							return;
						}

						street.kladr( 'parentType', $.kladr.type.city );
						street.kladr( 'parentId', objs[0].id );
						building.kladr( 'parentType', $.kladr.type.city );
						building.kladr( 'parentId', objs[0].id );
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
									showError('Не нашли ваш адрес на карте.<br />Уточните');

									return;
								}

								for ( i in objs ) {
									obj = objs[i];
									obj.label = obj.typeShort + '. ' + obj.name;
									items.push(obj);
								}

								street.autocomplete({
									source: items,
									appendTo: '.jsInputStreet',
									minLength: 2,
									select : function( event, ui ) {
										removeErrors();
										street.val(ui.item.name);
										building.kladr( 'parentType', $.kladr.type.street );
										building.kladr( 'parentId', ui.item.id );
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
									showError('Не нашли ваш адрес на карте.<br />Уточните');

									return;
								}

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
										building.val(ui.item.name);
										mapUpdate();
									}
								});
							}
						);
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
			fieldsHandler.street();
			fieldsHandler.building();
			fieldsHandler.buildingAdd();
		},


		/**
		 * Создание карты
		 */
		mapCreate = function() {
			var
				cityGeocoder,
                position;
			// end of vars

			if ( map_created ) {
				return;
			}

			$('#map').show();
			map_created = true;

			cityGeocoder = ymaps.geocode(cityName);
			cityGeocoder.then(
				function ( res ) {
					position = res.geoObjects.get(0).geometry.getCoordinates();
					map = new ENTER.constructors.CreateMap('map', [{latitude: position[0], longitude: position[1]}]);
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

	//	metro.parents('.jsInputMetro').hide();
	fieldsInit();
	$('body').bind('orderdeliverychange', mapCreate);

}(window.ENTER));