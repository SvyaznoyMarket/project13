/**
 * Order delivery address
 *
 * @author  Shaposhnik Vitaly
 */

;(function( ENTER ){
	var
		container = $('.jsDeliveryAddress'),
		data = container.data('value'),

		token = '52b04de731608f2773000000',
		key = 'c20b52a7dc6f6b28023e3d8ef81b9dbdb51ff74b',

		street = container.find('#order_address_street'),
		building = container.find('#order_address_building'),
		buildingAdd = container.find('#order_address_number'),
		metro = container.find('#order_address_metro'),

		error,

		limit = 6,

		map = null,
		placemark = null,
		map_created = false,

		cityId,
		cityName = data.regionName;
	// end of vars

	var
		/**
		 * Формируем подписи в autocomplete
		 */
		labelFormat = function( obj, query ) {
			var
				label = '',
				name = obj.name.toLowerCase(),
				start,

				k,
				parent;
			// end of vars

			query = query.toLowerCase();

			start = name.indexOf(query);
			start = start > 0 ? start : 0;

			if ( obj.typeShort ) {
				label += '<span class="ac-s2">' + obj.typeShort + '. ' + '</span>';
			}

			if ( query.length < obj.name.length ) {
				label += '<span class="ac-s2">' + obj.name.substr(0, start) + '</span>';
				label += '<span class="ac-s">' + obj.name.substr(start, query.length) + '</span>';
				label += '<span class="ac-s2">' + obj.name.substr(start+query.length, obj.name.length-query.length-start) + '</span>';
			} else {
				label += '<span class="ac-s">' + obj.name + '</span>';
			}

			if ( obj.parents ) {
				for( k = obj.parents.length-1; k>-1; k-- ) {
					parent = obj.parents[k];
					if ( parent.name ) {
						if (label) {
							label += '<span class="ac-st">, </span>';
						}
						label += '<span class="ac-st">' + parent.name + ' ' + parent.typeShort + '.</span>';
					}
				}
			}

			return label;
		},


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

					metroClosest(latitude, longitude);
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

						cityId = objs[0].id;

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
					labelFormat: labelFormat,
					verify: true,
					limit: limit,
					select: function( obj ) {
						removeErrors();
						building.kladr( 'parentType', $.kladr.type.street );
						building.kladr( 'parentId', obj.id );
						mapUpdate();
					},
					check: function( obj ) {
						if ( !obj ) {
							showError('Не нашли ваш адрес на карте.<br />Уточните');

							return;
						}

						building.kladr( 'parentType', $.kladr.type.street );
						building.kladr( 'parentId', obj.id );

						mapUpdate();
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
					labelFormat: labelFormat,
					verify: true,
					limit: limit,
					select: function( obj ) {
						removeErrors();
						mapUpdate();
					},
					check: function( obj ) {
						if ( !obj ) {
							showError('Не нашли ваш адрес на карте.<br />Уточните');

							return;
						}

						mapUpdate();
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


//	metro.parents('.jsInputMetro').hide();
	fieldsInit();

    $('body').bind('orderdeliverychange', mapCreate);

}(window.ENTER));