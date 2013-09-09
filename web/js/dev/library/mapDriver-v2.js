;(function( ENTER ) {
	var userUrl = ENTER.config.pageConfig.userUrl,
		constructors = ENTER.constructors;
	// end of vars

	
	/**
	 * Новый класс по работе с картой
	 *
	 * @author	Zaytsev Alexandr
	 * 
	 * @this	{CreateMap}
	 *
	 * @param	{Object}	args			DOM объект в который необходимо вывести карту
	 * @param	{Array}		points			Массив точек, которые необходимо вывести на карту
	 * @param	{Object}	baloonTemplate	Шаблон для балунов на карте
	 *
	 * @constructor
	 */
	constructors.CreateMap = (function() {
		'use strict';
	
		function CreateMap( nodeId, points, baloonTemplate ) {
			// enforces new
			if ( !(this instanceof CreateMap) ) {
				return new CreateMap(nodeId, points, baloonTemplate);
			}
			// constructor body
			
			console.info('CreateMap');
			console.log(points);

			this.points = points;
			this.template = baloonTemplate.html();
			this.center = this._calcCenter();

			console.log(this.center);

			this.mapWS = new ymaps.Map(nodeId, {
				center: [this.center.latitude, this.center.longitude],
				zoom: 10
			});

			this.mapWS.controls.add('zoomControl');

			this._showMarkers();
		}

		/**
		 * Расчет центра карты для исходного массива точек
		 */
		CreateMap.prototype._calcCenter = function() {
			console.info('calcCenter');

			var latitude = 0,
				longitude = 0,
				l = 0,
				i = 0,

				mapCenter = {};
			// end of vars

			for ( i = this.points.length - 1; i >= 0; i-- ) {
				latitude  += this.points[i].latitude * 1;
				longitude += this.points[i].longitude * 1;

				l++;
			}

			mapCenter = {
				latitude  : latitude / l,
				longitude : longitude / l
			};

			return mapCenter;
		};

		CreateMap.prototype._showMarkers = function() {
			var tmpPointInfo = null,
				tmpPlacemark = null,
				pointsCollection = new ymaps.GeoObjectArray();
			// end of vars

			// layout for baloon
			var pointContentLayout = ymaps.templateLayoutFactory.createClass(this.template);

			for ( var i = this.points.length - 1; i >= 0; i--) {
				tmpPointInfo = {
					id: this.points[i].id,
					name: this.points[i].name,
					address: this.points[i].address,
					link: this.points[i].link,
					regtime: this.points[i].regtime,
					parentBoxToken: this.points[i].parentBoxToken
				};

				tmpPlacemark = new ymaps.Placemark(
					// координаты точки
					[
						this.points[i].latitude,
						this.points[i].longitude
					],

					// данные для шаблона
					tmpPointInfo,

					// оформление метки на карте
					{
						iconImageHref: '/images/marker.png', // картинка иконки
						iconImageSize: [39, 59],
						iconImageOffset: [-19, -57]
					}
				);

				pointsCollection.add(tmpPlacemark);
			}

			ymaps.layout.storage.add('my#superlayout', pointContentLayout);
			pointsCollection.options.set({
				balloonContentBodyLayout:'my#superlayout',
				balloonMaxWidth: 350
			});

			this.mapWS.geoObjects.add(pointsCollection);
		};
	
	
		return CreateMap;
	
	}());
}(window.ENTER));
