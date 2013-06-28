/* MAP Object */

function MapGoogleWithShops( center, templateIWnode, DOMid, updateIWT ) {
/* Arguments:
	center is a center of a map
	templateIWnode is node(jQ) which include template for InfoWindow popup
	DOMid is selector (id) for google.maps.Map initialization
	updateIWT is a procedure calling each time after marker is clicked
*/
	var self         = this,
		mapWS        = null,
		infoWindow   = null,
		positionC    = null,
		markers      = [],
		currMarker   = null,
		mapContainer = $('#'+DOMid),
		infoWindowTemplate = templateIWnode.prop('innerHTML');

	self.updateInfoWindowTemplate = function( marker ) {
		if( typeof(updateIWT) !== 'undefined' ) {
			updateIWT( marker );
		}
		infoWindowTemplate = templateIWnode.prop('innerHTML');
	};
	
	function create() {
		positionC = new google.maps.LatLng(center.latitude, center.longitude);
		var options = {
			zoom: 11,
			center: positionC,
			scrollwheel: false,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			mapTypeControlOptions: {
				style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
			}
		};
		mapWS      = new google.maps.Map( document.getElementById( DOMid ), options );
		infoWindow = new google.maps.InfoWindow({
			maxWidth: 400,
			disableAutoPan: false
		});
	}

	this.showInfobox = function( markerId ) {
		if( currMarker ){
			currMarker.setVisible(true); // show preceding marker
		}
		var marker = markers[markerId].ref;
		currMarker = marker;
		var item = markers[marker.id];
		marker.setVisible(false); // hides marker
		self.updateInfoWindowTemplate( item );
		infoWindow.setContent( infoWindowTemplate );
		infoWindow.setPosition( marker.position );
		infoWindow.open( mapWS );
		google.maps.event.addListener( infoWindow, 'closeclick', function() { 
			marker.setVisible(true);
		});
	};
	
	this.hideInfobox = function() {
		infoWindow.close();
	};

	var handlers = [];

	this.addHandlerMarker = function( e, callback ) {
		handlers.push( { 'event': e, 'callback': callback } );
	};
	
	this.showMarkers = function( argmarkers ) {
		mapContainer.show();
		$.each( markers, function(i, item) {
			if( typeof( item.ref ) !== 'undefined' ){
				item.ref.setMap(null);
			}
		});
		markers = argmarkers;
		google.maps.event.trigger( mapWS, 'resize' );
		mapWS.setCenter( positionC );
		var latMax = 0, longMax = 0, latMin = 90, longMin = 90;
		var len = 0;
		$.each( markers, function(i, item) {
			len ++;
			if( item.latitude > latMax ){
				latMax = item.latitude;
			}
			if( item.longitude > longMax ){
				longMax = item.longitude;
			}
			if( item.latitude < latMin ){
				latMin = item.latitude;
			}
			if( item.longitude < longMin ){
				longMin = item.longitude;
			}

			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(item.latitude, item.longitude),
				map: mapWS,
				title: item.name,
				icon: '/images/marker.png',
				id: item.id
			});
			google.maps.event.addListener(marker, 'click', function() {
				self.showInfobox(this.id);
			});
			$.each( handlers, function( h, handler ) {
				google.maps.event.addListener( marker, handler.event, function() {
					handler.callback(item);
				});
			});
			
			markers[marker.id].ref = marker;
		});
		if( len === 1 ) {
			latMin -= 0.001;
			latMin -= 0.001;
			latMax = latMax*1 +  0.001;
			longMax = longMax*1 + 0.001;
		}
		var sw = new google.maps.LatLng( latMin , longMin );
		var ne = new google.maps.LatLng( latMax , longMax );
		var bounds = new google.maps.LatLngBounds(sw, ne);
		if( len ){
			mapWS.fitBounds(bounds);
		}
	};

	this.closeMap = function( callback ) {
		infoWindow.close();
		mapContainer.hide('blind', null, 800, function() {
			if( callback ){
				callback();
			}
		});
	};

	this.closePopupMap = function( callback ) {
		infoWindow.close();
		if( callback ){
			callback();
		}
	};
			
	this.addHandler = function( selector, callback ) {
		mapContainer.delegate( selector, 'click', function(e) { //desktops			
			e.preventDefault();
			callback( e.target );
		});
		var bw = new brwsr();
		if( bw.isTouch ){
			mapContainer[0].addEventListener("touchstart",  //touch devices
			function(e) {
				e.preventDefault();
				if( e.target.is( selector ) ){
					callback( e.target );
				}
			} , false);
		}							
	};

	/* main() */
	create();

} // object MapGoogleWithShops

function MapYandexWithShops( center, templateIWnode, DOMid ) {
/* Arguments:
	center is a center of a map
	templateIWnode is node(jQ) which include template for InfoWindow popup
	DOMid is selector (id) for google.maps.Map initialization
*/
	var self         = this,
		mapWS        = null,
		infoWindow   = null,
		positionC    = null,
		markers      = [],
		currMarker   = null,
		mapContainer = $('#'+DOMid),
		infoWindowTemplate = templateIWnode.prop('innerHTML');
	
	this.updateInfoWindowTemplate = function( marker ) {
		// if( updateInfoWindowTemplate )
		//     updateInfoWindowTemplate( marker )
		// infoWindowTemplate = templateIWnode.prop('innerHTML')   
	};
	
	function create() {
		mapWS = new ymaps.Map(DOMid, {
			center: [center.latitude, center.longitude],
			zoom: 10
		});
		
		mapWS.controls
		.add('zoomControl');
		// setTimeout( function() {
		//     mapWS = new ymaps.Map(DOMid, {
		//         center: [center.latitude, center.longitude],
		//         zoom: 10
		//     })
			
		//     mapWS.controls
		//     .add('zoomControl')
		//     //.add('typeSelector', { left: 5, top: 15 })// Список типов карты
		// }, 1200)        
	}

	this.showInfobox = function( markerId ) {
		markers[markerId].ref.balloon.open();
	};
	
	this.hideInfobox = function() {
		// infoWindow.close()
	};

	var handlers = [];

	this.addHandlerMarker = function( e, callback ) {
		// handlers.push( { 'event': e, 'callback': callback } )
	};
	
	this.clear = function() {
		mapWS.geoObjects.each( function( mapObj ) {
			mapWS.geoObjects.remove(mapObj);
		});
	};

	this.showMarkers = function( argmarkers ) {   
		// console.info(argmarkers)
		mapContainer.show();
		mapWS.container.fitToViewport();
		mapWS.setCenter([center.latitude, center.longitude]);
		self.clear();
		markers = argmarkers;
		var myCollection = new ymaps.GeoObjectCollection();
		$.each( markers, function(i, item) {           
			// Создаем метку и задаем изображение для ее иконки
			var tmpitem = {
				id: item.id,
				name: item.name,
				address: item.address,
				link: item.link,
				regtime: (item.regtime) ? item.regtime : item.regime,
				regime: (item.regtime) ? item.regtime : item.regime
			};
			var marker = new ymaps.Placemark( [item.latitude, item.longitude], tmpitem, {
					iconImageHref: '/images/marker.png', // картинка иконки
					iconImageSize: [39, 59], 
					iconImageOffset: [-19, -57] 
				}
			);
			myCollection.add(marker);
			markers[item.id].ref = marker;
		});
// console.info(markers)        
		// Создаем шаблон для отображения контента балуна         
		var myBalloonLayout = ymaps.templateLayoutFactory.createClass(templateIWnode.prop('innerHTML').replace(/<%=([a-z]+)%>/g, '$[properties.$1]'));
		
		// Помещаем созданный шаблон в хранилище шаблонов. Теперь наш шаблон доступен по ключу 'my#superlayout'.
		ymaps.layout.storage.add('my#superlayout', myBalloonLayout);
		// Задаем наш шаблон для балунов геобъектов коллекции.
		myCollection.options.set({
			balloonContentBodyLayout:'my#superlayout',
			// Максимальная ширина балуна в пикселах
			balloonMaxWidth: 350
		});
		mapWS.geoObjects.add( myCollection );
		var bounds = myCollection.getBounds(); 
		if( bounds[0][0] !== bounds[1][0] ){ // cause setBounds() hit a bug if only one point  
			mapWS.setBounds( bounds );
		}
		else{
			$.each( markers, function(i, item) {
				mapWS.setCenter([markers[i].latitude, markers[i].longitude], 14);
			});
		}
	};

	this.showCluster = function( argmarkers ){
		// console.log('cluster!')
		// mapContainer.show()
		// mapWS.container.fitToViewport()
		mapWS.setCenter([center.latitude, center.longitude]);
		self.clear();
		var dots = argmarkers;
		var clusterer = new ymaps.Clusterer({clusterDisableClickZoom: false, maxZoom:8, synchAdd:true, minClusterSize:1});
		$.each( dots, function(i, item) {           
			// Создаем метку и задаем изображение для ее иконки
			var tmpitem = {
				id: item.id,
				name: item.name,
				address: item.address,
				link: item.link,
				regtime: (item.regtime) ? item.regtime : item.regime,
				regime: (item.regtime) ? item.regtime : item.regime
			};
			var marker = new ymaps.Placemark( [item.latitude, item.longitude], tmpitem, {
					iconImageHref: '/images/marker.png', // картинка иконки
					iconImageSize: [39, 59], 
					iconImageOffset: [-19, -57] 
				}
			);
			clusterer.add(marker);
			dots[i].ref = marker;
			// console.log(dots)
		});
		var myBalloonLayout = ymaps.templateLayoutFactory.createClass(
			templateIWnode.prop('innerHTML').replace(/<%=([a-z]+)%>/g, '$[properties.$1]')
		);
		
		// Помещаем созданный шаблон в хранилище шаблонов. Теперь наш шаблон доступен по ключу 'my#superlayout'.
		ymaps.layout.storage.add('my#superlayout', myBalloonLayout);
		// Задаем наш шаблон для балунов геобъектов коллекции.
		clusterer.options.set({
			balloonContentBodyLayout:'my#superlayout',
			// Максимальная ширина балуна в пикселах
			balloonMaxWidth: 350
		});
		mapWS.geoObjects.add(clusterer);
		mapWS.setZoom(4);
	};

	this.chZoomCenter = function( center, zoom ) {
		mapWS.setCenter([center.latitude, center.longitude], zoom, { checkZoomRange: true, duration:800 } );
	};

	this.closeMap = function( callback ) {
		// infoWindow.close()
		mapContainer.hide('blind', null, 800, function() {
			if( callback ){
				callback();
			}
		});
	};

	this.closePopupMap = function( callback ) {
		// infoWindow.close()
		if( callback ){
			callback();
		}
	};
			
	this.addHandler = function( selector, callback ) {
		mapContainer.delegate( selector, 'click', function(e) { //desktops          
			e.preventDefault();
			callback( e.target );
		});
		var bw = new brwsr();
		if( bw.isTouch ){
			mapContainer[0].addEventListener("touchstart",  //touch devices
			function(e) {
				e.preventDefault();
				if( e.target.is( selector ) ){
					callback( e.target );
				}
			} , false);
		}                   
	};

	/* main() */
	create();

} // object MapYandexWithShops

function MapOnePoint( position, nodeId ) {
	if( !position ){
		return false;
	}
	if( !position.longitude || !position.latitude ){
		return false;
	}
	var self = this;

	var markerPreset = {
		iconImageHref: '/images/marker.png',
		iconImageSize: [39, 59], 
		iconImageOffset: [-19, -57] 
	};

	if ($('#staticYMap').length){ //static map for printPage
		var url = "http://static-maps.yandex.ru/1.x/?";
		var statType = 'l=map';
		var statCord = 'll='+position.longitude+','+position.latitude;
		var statZoom = 'spn=0.004,0.004';
		var statSize = 'size=650,450'; // it's max value :`(
		var statPlacemark = 'pt='+position.longitude+','+position.latitude+',pm2dol';
		var src = url+statCord+'&'+statZoom+'&'+statType+'&'+statSize+'&'+statPlacemark;
		$('#staticYMap img').attr('src',src);
	}

	self.yandex = function() {
		var point = [ position.latitude*1 , position.longitude*1 ];
		var myMap = new ymaps.Map ( nodeId, {
			center: point,
			zoom: 16
		});
		myMap.controls.add('zoomControl');

		var myPlacemark = new ymaps.Placemark( point, {}, markerPreset);
		myMap.geoObjects.add(myPlacemark);
		
		myMap.zoomRange.get( point ).then( function (range) {
			myMap.setZoom( range[1] );
		});
	};

	self.google = function() {
		var options = {
			zoom: 16,
			// center: position,
			scrollwheel: false,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			mapTypeControlOptions: {
				style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
			}
		};
		
		var point = new google.maps.LatLng( position.latitude , position.longitude );
		options.center = point;
		var map = new google.maps.Map(document.getElementById( nodeId ), options);

		var marker = new google.maps.Marker({
			position: point,
			map: map,
			icon: markerPreset.iconImageHref
		});
	};

} // object MapOnePoint

function calcMCenter( shops ) {
	var latitude = 0, longitude = 0, l = 0;
	for(var i in shops ) {
		latitude  += shops[i].latitude*1;
		longitude += shops[i].longitude*1;
		l++;
	}
	var mapCenter = {
		latitude  : latitude / l,
		longitude : longitude / l
	};
	return mapCenter;
}

window.MapInterface = (function() {
	var vendor, tmplSource;

	return {
		ready: function( vendorName, tmpl) {
			var mapReady = $.Deferred();
			vendor     = vendorName;
			tmplSource = tmpl;
			if( vendor==='yandex' ) {
				ymaps.ready( function() {
					// console.info('yandexIsReady')            
					PubSub.publish('yandexIsReady');
					ymaps.isReady = true;
					mapReady.resolve();
				});
			}
			return mapReady.promise();
			// if( vendor==='google' ) {
			//      $LAB.sandbox().script( 'http://maps.google.com/maps/api/js?sensor=false' )
			// } else // $LAB.sandbox().script( 'http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU' ).wait( function() {
		},

		init: function( coordinates, mapContainerId, callback, updater ) {
			// console.log('инитимся..', coordinates, mapContainerId, callback, updater)
			if( vendor === 'yandex' ) {
				if( typeof(ymaps)!=='undefined') {
					// console.info('1')
					window.regionMap = new MapYandexWithShops(
						coordinates,
						tmplSource.yandex, 
						mapContainerId
					);
					if( typeof( callback ) !== 'undefined' ) {
						callback();
					}
				}
				else {
					// console.info('2')
					PubSub.subscribe( 'yandexIsReady', function() {
						window.regionMap = new MapYandexWithShops( 
							coordinates,
							tmplSource.yandex,
							mapContainerId
						);
						if( typeof( callback ) !== 'undefined' ) {
							callback();
						}
					});
				}
			}
			if( vendor === 'google' ) {        
				window.regionMap = new MapGoogleWithShops(
					coordinates,
					tmplSource.google,
					mapContainerId,
					updater
				);
				if( typeof( callback ) !== 'undefined' ) {
					callback();
				}
			}
		},

		onePoint: function( coordinates, mapContainerId ) {
			var mtmp = new MapOnePoint( coordinates, mapContainerId );
			
			if( vendor === 'yandex' ) {
				if( typeof(ymaps)!=='undefined' && ymaps.isReady ) {
					mtmp[vendor]();
				}
				else {
					PubSub.subscribe('yandexIsReady', function() {
						mtmp[vendor]();
					});
				}
			} 
			if( vendor === 'google' ) {
				mtmp[vendor]();
			}
		},

		getMapContainer: function() {
			// TODO
			// return window.regionMap
		}

		// TODO wrap fn calcMCenter as a method
	};
}() ); // singleton