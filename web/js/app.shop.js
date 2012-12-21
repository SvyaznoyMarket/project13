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
			$.when(MapInterface.ready( 'yandex', {
				yandex: $('#infowindowtmpl'), 
				google: $('#map-info_window-container')
			}))
			.done(function(){
				MapInterface.onePoint( position, 'map-container' )
			})
		}
	})

	$('div.map-google-link:first', marketfolio ).trigger('click')

	if( $('#map-markers').length ) {
		var allshops = $('#map-markers').data('content')
		var showShopTrigger = false // показан ли конкретный город

		//показываем бабл магазина при наведении на него в списке
		var hoverTimer = { 'timer': null, 'id': 0 }
	    $('.bMapShops__eMapCityList ul').delegate('li', 'hover', function() {
	        var id = $(this).attr('ref')//$(this).data('id')
	        if( hoverTimer.timer ) {
	            clearTimeout( hoverTimer.timer )
	        }
	        if( id && id != hoverTimer.id) {
	            hoverTimer.id = id
	            hoverTimer.timer = setTimeout( function() {   
	                window.regionMap.showInfobox( id )
	            }, 350)
	        }
	    })

	    // превращаем список магазинов в удобоваримый стек для яндекс карт
	    function getShopsStack(shops) {
			var shopsStack = {}
			for( var sh in shops ){
				shopsStack[ shops[sh].id ] = shops[sh]
			}
			return shopsStack
	    }
	    // выбранный город всегда остается наверху
	    function shopScroll(){
	    	
	    }

	    //показываем карту города, при клике на название города
		var cityHandler = function(){
			// show all city
			$('.bMapShops__eTitle').click(function(){
				$('.bMapShops__eMapCityList_city ul').fadeOut(500, function(){
					$('.bMapShops__eMapCityList_city ul').empty()
					window.regionMap.showCluster(  allshops )
				})
				return false
			})
			// show current city
			$('.bMapShops__eMapCityList_city').click( function(){
				$('.bMapShops__eMapCityList_city ul').fadeOut(500, function(){
					$('.bMapShops__eMapCityList_city ul').empty()
				})
				if (showShopTrigger){
					// город открыт, схлопываем список магазинов
					$(this).removeClass('chosedCity')
					$('.bMapShops__eMapCityList_city').show()
					showShopTrigger = false
				}
				else{
					// город не открыт открываем спискок магазинов
					var curCity = []
					for (var i in allshops){ //получаем список магазинов в этом городе
						if (allshops[i].region_id == $(this).attr('ref')){
							var shopTpl = tmpl('shopInCity', allshops[i])
							$(this).find('ul').append(shopTpl)
							curCity.push(allshops[i])
						}
					}
					if (curCity.length) { // если магазины есть
						$('.bMapShops__eMapCityList_city').hide()
						$(this).addClass('chosedCity').show()
						var startOffsetTop = $('.chosedCity').offset().top
						$('.bMapShops__eMapCityList').scroll(function(){
							var nowOffestTop = $('.chosedCity').offset().top
							$('.chosedCity .cityName').css('top', startOffsetTop-nowOffestTop)
						})
						$(this).find('ul').fadeIn(500, function(){
							window.regionMap.showMarkers(  getShopsStack(curCity) )
						})
					}
					else{
						alert('В этом городе пока еще нет наших магазинов')
					}
					showShopTrigger = true
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
		$.when(MapInterface.ready( 'yandex', {
			yandex: $('#infowindowtmpl'), 
			google: $('#map-info_window-container')
		}))
		.done(function(){
			MapInterface.init( mapCenter, 'region_map-container', mapCallback, updateTmlt )
		})
	}

});
