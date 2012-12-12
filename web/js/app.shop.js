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
		var cityHoverHandler = function(){
			$('.bMapShops__eMapCityList').delegate('li','click', function(){
				var curCity = []
				for (var i in allshops){
					if (allshops[i].region_id == $(this).attr('ref')){
						curCity.push(allshops[i])
					}
				}
				if (curCity.length) {
					var mapCenter = calcMCenter( curCity )
					window.regionMap.chZoomCenter( mapCenter, '10')
				}
				else{
					alert('здесь магазнов нет!')
				}
			})
		}

		// console.info($('#map-markers').data('content'))
		function updateTmlt( marker ) {
			$('#map-info_window-container').html(
				tmpl('infowindowtmpl', marker )
			)
		}
		var mapCenter =  calcMCenter( allshops )
		var mapCallback = function() {
			window.regionMap.showCluster(  allshops )
			cityHoverHandler()
		}
		MapInterface.init( mapCenter, 'region_map-container', mapCallback, updateTmlt )
	}

});
