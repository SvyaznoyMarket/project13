// карточка магазина мобильной версии сайта
$(document).ready(function() {
	// yandex map
	var initMap = function() {
		var latitude = $('#map').data('latitude')
		var longitude = $('#map').data('longitude')
        var marker = new ymaps.Placemark( [latitude, longitude], {
        		name:'enter'
        	},
			{
				iconImageHref: '/images/marker.png', // картинка иконки
				iconImageSize: [39, 59], 
				iconImageOffset: [-19, -57] 
			}
		)
		myMap = new ymaps.Map ("map", {
            center: [latitude, longitude],
            zoom: 14,
        })
        myMap.setCenter([latitude, longitude])
        myMap.geoObjects.add(marker)
	}
	ymaps.ready(initMap);
})