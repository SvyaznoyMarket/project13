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
	 * @param	{Object}	nodeId			DOM объект в который необходимо вывести карту
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
			this.template = baloonTemplate ? baloonTemplate.html() : null;
			this.center = this._calcCenter();

            this.$nodeId = $('#'+nodeId);

			console.log(this.center);

//            var
//                init = function init() {
//
//                };
//            // end of functions
//
//
//            ymaps.ready(init);

            console.info('ymaps.ready. init map');

            if ( !this.$nodeId.length || this.$nodeId.width() === 0 || this.$nodeId.height() === 0 || this.$nodeId.is('visible') === false ) {
                console.warn('Do you have a problem with init map?');

                console.log(this.$nodeId.width());
                console.log(this.$nodeId.height());
                console.log(this.$nodeId.is('visible'));
            }


            this.mapWS = new ymaps.Map(nodeId, {
                center: [this.center.latitude, this.center.longitude],
                zoom: 10
            });

            this.mapWS.controls.add('zoomControl');

            //this._showMarkers();
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
                if (!this.points[i].latitude || !this.points[i].longitude) continue;
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
			var currPoint = null,
				tmpPlacemark = null,
				pointsCollection = new ymaps.GeoObjectArray(),
				pointContentLayout = ymaps.templateLayoutFactory.createClass(this.template), // layout for baloon
				i;
			// end of vars

			for ( i = this.points.length - 1; i >= 0; i--) {
				currPoint = this.points[i];
                if (!currPoint.latitude || !currPoint.longitude) continue;

				tmpPlacemark = new ymaps.Placemark(
					// координаты точки
					[
						currPoint.latitude,
						currPoint.longitude
					],

					// данные для шаблона
					{
						id: currPoint.id,
						name: currPoint.name,
						address: currPoint.address,
						link: currPoint.link,
						regtime: currPoint.regtime,
						parentBoxToken: currPoint.parentBoxToken,
						buttonName: currPoint.buttonName
					},

					// оформление метки на карте
					{
						iconImageHref: currPoint.pointImage, // картинка иконки
						//iconImageHref: '/images/marker.png', // картинка иконки
						//iconImageSize: [39, 59],
						//iconImageOffset: [-19, -57]
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