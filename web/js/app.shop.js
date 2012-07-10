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
		if( ! $('#map-container div').length ) {
			var el = $(this)

			var position = new google.maps.LatLng($('[name="shop[latitude]"]').val(), $('[name="shop[longitude]"]').val());
			var options = {
			  zoom: 16,
			  center: position,
			  scrollwheel: false,
			  mapTypeId: google.maps.MapTypeId.ROADMAP,
			  mapTypeControlOptions: {
				style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
			  }
			};

			var map = new google.maps.Map(document.getElementById('map-container'), options);

			var marker = new google.maps.Marker({
			  position: new google.maps.LatLng($('[name="shop[latitude]"]').val(), $('[name="shop[longitude]"]').val()),
			  map: map,
			  title: $('[name="shop[name]"]').val(),
			  icon: '/images/marker.png'
			})
		}
	})

	$('div.map-google-link:first', marketfolio ).trigger('click')
	if( $('#map-markers').length ) {
		function updateTmlt( marker ) {
			$('#map-info_window-container').html(
				tmpl('infowindowtmpl', marker )
			)
		}
		var mapCenter =  calcMCenter( $('#map-markers').data('content') )
		window.regionMap = new MapWithShops( mapCenter, $('#map-info_window-container'), 'region_map-container', updateTmlt )
		window.regionMap.showMarkers(  $('#map-markers').data('content') )
		//window.regionMap.addHandler( '.shopchoose', pickStoreMVM )
	}

	$('#region-select').bind('change', function() {
		window.location = $(this).find('option:selected').data('url')
	})

});
