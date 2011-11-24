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
			  /*
			  scaleControl: ,
			  navigationControlOptions: {
				style: google.maps.NavigationControlStyle.DEFAULT
			  },
			  */
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


  $('#region_map-container').bind({
    create: function(e, center, markers) {
			var el = $(this)

			var position = new google.maps.LatLng(center.latitude, center.longitude);
			var options = {
			  zoom: 10,
			  center: position,
			  scrollwheel: false,
			  mapTypeId: google.maps.MapTypeId.ROADMAP,
			  /*
			  scaleControl: ,
			  navigationControlOptions: {
				style: google.maps.NavigationControlStyle.DEFAULT
			  },
			  */
			  mapTypeControlOptions: {
				style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
			  }
			}

      var map = new google.maps.Map(document.getElementById(el.attr('id')), options)
      var infoWindow = new google.maps.InfoWindow()

      var showWindow = function() {
        var item = markers[this.id]

        el.trigger('infoWindow', [ this, item ])
      }

      // set markers
      $.each(markers, function(i, item) {
        var marker = new google.maps.Marker({
          position: new google.maps.LatLng(item.latitude, item.longitude),
          map: map,
          title: item.name,
          icon: '/images/marker.png',
          id: item.id
        })
        google.maps.event.addListener(marker, 'click', showWindow);
      })

      google.maps.event.addListener(map, 'bounds_changed', function () {})

      el.data('map', map)
      el.data('infoWindow', infoWindow)

    },
    move: function(e, center) {
      var el = $(this)
      var map = el.data('map')
    },
    infoWindow: function(e, marker, item) {
      var el = $(this)
      var map = el.data('map')
      var infoWindow = el.data('infoWindow')

      var content = ''
        + '<h2 class="title">' + item.name  + '</h2>'
        + item.link

      infoWindow.setContent(content);
      infoWindow.open(map, marker);
    }
  })

  $('#region_map-container').trigger('create', [ $('#map-centers').data('content')[0], $('#map-markers').data('content') ])

})
