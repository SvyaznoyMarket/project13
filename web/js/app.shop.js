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

	MapInterface.ready( 'yandex', {
		yandex: $('#infowindowtmpl'), 
		google: $('#map-info_window-container')
	} )	

	marketfolio.delegate('div.map-google-link', 'click',function(e) {
		$( 'div.mChecked' , marketfolio ).removeClass('mChecked')
		if( ! $('#map-container div').length ) { // only once, on first click !
			var el = $(this)

			var position = {
				latitude: $('[name="shop[latitude]"]').val(),
				longitude: $('[name="shop[longitude]"]').val()
			}
			MapInterface.onePoint( position, 'map-container' )
		}
	})

	$('div.map-google-link:first', marketfolio ).trigger('click')

	if( $('#map-markers').length ) { // allshops page
		// $('#region-select').bind('change', function() {
		// 	window.location = $(this).find('option:selected').data('url')
		// })
		var allshops = $('#map-markers').data('content')

		var cityHandler = function(){
			// show all city
			$('.bMapShops__eTitle').click(function(){
				$('.bMapShops__eMapCityList ul').empty()
				window.regionMap.showCluster(  allshops )
				return false
			})
			// show current city
			$('.bMapShops__eMapCityList li').click( function(){
				var curCity = []
				$('.bMapShops__eMapCityList ul').empty()
				for (var i in allshops){
					if (allshops[i].region_id == $(this).attr('ref')){
						var shopTpl = tmpl('shopInCity', allshops[i])
						$(this).find('ul').append(shopTpl)
						curCity.push(allshops[i])
					}
				}
				if (curCity.length) {
					window.regionMap.showMarkers(  curCity )
				}
				else{
					alert('В этом городе пока еще нет наших магазинов')
				}
			})
		}

		function updateTmlt( marker ) {
			$('#map-info_window-container').html(
				tmpl('infowindowtmpl', marker )
			)
		}
		var mapCenter =  calcMCenter( allshops )
		var mapCallback = function() {
			window.regionMap.showCluster(  allshops )
			cityHandler()
		}
		MapInterface.init( mapCenter, 'region_map-container', mapCallback, updateTmlt )
	}

});
