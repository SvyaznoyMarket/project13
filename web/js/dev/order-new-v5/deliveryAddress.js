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

		street = container.find( '#order_address_street'),
		building = container.find( '#order_address_building'),
		buildingAdd = container.find( '#order_address_number'),

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
				obj = null,
				value,

				geocode,
				position,

				latitude, longitude;
			// end of vars

			// Город
			name = $.trim(cityName);
			type = 'город';

			if ( name ) {
				if ( address ) address += ', ';
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
			} else if ( value ) {
				name = value;
				type = 'улица';
			}

			if ( name ) {
				if ( address ) address += ', ';
				address += type + ' ' + name;
				zoom = 14;
			}

			// Дом
			name = null;
			type = null;
			obj = building.kladr('current');
			value = $.trim(building.val());

			if(obj){
				name = obj.name;
				type = 'дом';
			} else if(value){
				name = value;
				type = 'дом';
			}

			if(name){
				if(address) address += ', ';
				address += type + ' ' + name;
				zoom = 16;
			}

			// Корпус
			name = null;
			type = null;
			value = $.trim(buildingAdd.val());

			if(value){
				name = value;
				type = 'корпус';
			}

			if(name){
				if(address) address += ', ';
				address += type + ' ' + name;
				zoom = 16;
			}

			console.warn(address);

			if( address && map_created ){
				geocode = ymaps.geocode(address);
				geocode.then(function(res){
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

					// рисуем точку на карте
//					nearest.properties.set('iconContent', name);
//					nearest.options.set('preset', 'twirl#redStretchyIcon');
//					map.mapWS.geoObjects.add(nearest);

					$('#order_address_metro').val(name);
					$('#order_address_metro').select();
				},
				function ( err ) {
					console.warn("При выполнении запроса произошла ошибка: " + err);
				}
			);
		},


		/**
		 * Обработчики полей
		 *
		 * @type {{city: Function, street: Function, building: Function, housing: Function}}
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
						console.warn(111);
						console.warn(obj);



						building.kladr( 'parentType', $.kladr.type.street );
						building.kladr( 'parentId', obj.id );
						mapUpdate();
					},
					check: function( obj ) {
						if ( !obj ) {
							street.val('');
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
						mapUpdate();
					},
					check: function( obj ) {
						mapUpdate();
					}
				});
			},

			/**
			 * Проверка названия корпуса
			 */
			housing: function() {
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
			fieldsHandler.housing();
		},

		mapCreate = function() {
			if(map_created) return;
			map_created = true;

			map = new ENTER.constructors.CreateMap('map', [{latitude: 55.76, longitude: 37.64}]);
		};
	// end of functions


	fieldsInit();
	setTimeout(mapCreate, 5000);

}(window.ENTER));