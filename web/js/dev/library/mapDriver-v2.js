/**
 * New map driver
 */
function CreateMap( nodeId, points, baloonTemplate ) {
	console.info('CreateMap');
	console.log(points);

	this.points = points;
	console.log(baloonTemplate)
	this.template = baloonTemplate.html();

	console.log(this.template);

	this.center = this._calcCenter();
	console.log(this.center);

	this.mapWS = new ymaps.Map(nodeId, {
		center: [this.center.latitude, this.center.longitude],
		zoom: 10
	});

	this.mapWS.controls.add('zoomControl');

	this._showMarkers();
}

CreateMap.prototype._calcCenter = function() {
	console.info('calcCenter');
	var latitude = 0,
		longitude = 0,
		l = 0,

		mapCenter = {};
	// end of vars

	for ( var i = this.points.length - 1; i >= 0; i--) {
		latitude  += this.points[i].latitude*1;
		longitude += this.points[i].longitude*1;

		l++;
	}

	mapCenter = {
		latitude  : latitude / l,
		longitude : longitude / l
	};

	return mapCenter;
}

CreateMap.prototype._showMarkers = function() {
	var tmpPointInfo = null,
		tmpPlacemark = null,
		pointsCollection = new ymaps.GeoObjectArray();

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
}