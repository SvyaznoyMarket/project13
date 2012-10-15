$(document).ready(function() {
	/* bMap */
	var marketfolio = $('.bMap__eScrollWrap')

	marketfolio.delegate('div.map-image-link', 'click',function(e) {
		if( $(this).hasClass('first') && $('div.map-360-link', marketfolio ).length ) {
			$('div.map-360-link', marketfolio ).trigger('click')
			return
		}
		if( $(this).hasClass('mChecked') ){}
		else{
			$( 'div.mChecked' ,$(this).parent() ).removeClass('mChecked')
			$(this).addClass('mChecked')
		}
	})

	if( $('div.map-360-link', marketfolio ).length ) {
		marketfolio.delegate('div.map-360-link', 'click',function(e) {
			$( 'div.mChecked' , $('.bMap__eScrollWrap') ).removeClass('mChecked')
			if( ! $('#map-container embed').length ) {
				var el = $(this)
				var data = $('#map-panorama').data()
				embedpano({swf: data.swf, xml: data.xml, target: 'map-container', wmode: 'transparent'})
			}
			e.stopPropagation() // cause in .map-image-link
		})

		//$('div.map-360-link', marketfolio ).trigger('click')
	}

	marketfolio.delegate('div.map-google-link', 'click',function(e) {
		$( 'div.mChecked' , marketfolio ).removeClass('mChecked')
		if( ! $('#map-container div').length ) { // only once, on first click !
			var el = $(this)

			var position = {
				latitude: $('[name="shop[latitude]"]').val(),
				longitude: $('[name="shop[longitude]"]').val()
			}
			var shopOnMap = new MapOnePoint( position, 'map-container' ) 
		}
	})

	var vendor = 'yandex' // yandex or google
	if( vendor==='yandex' )
		ymaps.ready( function() {
// console.info('yandexIsReady')			
	        PubSub.publish('yandexIsReady')
	        ymaps.isReady = true
	    })		

	function MapOnePoint( position, nodeId ) {
		if( !position )
			return
		if( !position.longitude || !position.latitude )
			return
		var self = this

		var markerPreset = {
			iconImageHref: '/images/marker.png',
			iconImageSize: [39, 59], 
	        iconImageOffset: [-19, -57] 
		}

		self.yandex = function() {		
			var point = [ position.latitude , position.longitude ]
			var myMap = new ymaps.Map ( nodeId, {
			    center: point,
			    zoom: 16
			})
			myMap.controls.add('zoomControl')
			
			var myPlacemark = new ymaps.Placemark( point, {}, markerPreset)
			
			myMap.geoObjects.add(myPlacemark)
		}

		self.google = function() {
			var options = {
				zoom: 16,
				// center: position,
				scrollwheel: false,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				mapTypeControlOptions: {
					style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
				}
			}
			
			var point = new google.maps.LatLng( position.latitude , position.longitude )
			options.center = point
			var map = new google.maps.Map(document.getElementById( nodeId ), options)

			var marker = new google.maps.Marker({
				position: point,
				map: map,
				icon: markerPreset.iconImageHref
			})
		}

		if( vendor === 'yandex' ) {
			PubSub.subscribe('yandexIsReady', function() {
				self[vendor]()
			})
		} else {
			self.google()
		}

	} // MapOnePoint obj

	$('div.map-google-link:first', marketfolio ).trigger('click')
	if( $('#map-markers').length ) { // allshops page
		function updateTmlt( marker ) {
			$('#map-info_window-container').html(
				tmpl('infowindowtmpl', marker )
			)
		}
		var mapCenter =  calcMCenter( $('#map-markers').data('content') )
		if( vendor === 'yandex' ) {
			PubSub.subscribe('yandexIsReady', function() {
				window.regionMap = new MapYandexWithShops( mapCenter, $('#infowindowtmpl'), 'region_map-container' )
				window.regionMap.showMarkers(  $('#map-markers').data('content') )
			})
		} else {
			window.regionMap = new MapGoogleWithShops( mapCenter, $('#map-info_window-container'), 'region_map-container', updateTmlt )
			window.regionMap.showMarkers(  $('#map-markers').data('content') )
		}
		
		//window.regionMap.addHandler( '.shopchoose', pickStoreMVM )
	}

	$('#region-select').bind('change', function() {
		window.location = $(this).find('option:selected').data('url')
	})

});
