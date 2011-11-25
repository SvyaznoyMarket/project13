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
    create: function(e, center, markers, infoWindowTemplate) {
			var el = $(this)

			var position = new google.maps.LatLng(center.latitude, center.longitude);
			var options = {
			  zoom: 11,
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
      //var infoWindow = new google.maps.InfoWindow()
      var infoWindow = new InfoBox({
        disableAutoPan: false,
        maxWidth: 0,
        pixelOffset: new google.maps.Size(-11, -108),
        zIndex: null,
        boxStyle: {
          opacity: 0.85,
          width: '280px'
        },
        //closeBoxMargin: "10px 2px 2px 2px",
        closeBoxURL: 'http://www.google.com/intl/en_us/mapfiles/close.gif',
        //closeBoxURL: '',
        infoBoxClearance: new google.maps.Size(1, 1),
        isHidden: false,
        pane: 'floatShadow',
        enableEventPropagation: false
      })
      //console.info(infoWindow);

      var showWindow = function() {
        var item = markers[this.id]

        el.trigger('infoWindow', [ this, item ])
      }

      // set markers
      el.data('markers', [])
      $.each(markers, function(i, item) {
        var marker = new google.maps.Marker({
          position: new google.maps.LatLng(item.latitude, item.longitude),
          map: map,
          title: item.name,
          icon: '/images/marker.png',
          id: item.id
        })
        google.maps.event.addListener(marker, 'click', showWindow);
        el.data('markers').push(marker)
      })

      google.maps.event.addListener(map, 'bounds_changed', function () {
        el.data('infoWindow').close()
      })
      google.maps.event.addListener(map, 'click', function () {
        el.data('infoWindow').close()
      })
      google.maps.event.addListener(infoWindow, 'closeclick', function () {
        $.each(el.data('markers'), function(i, marker) {
          if (null == marker.map) {
            marker.setMap(el.data('map'))
          }
        })
      })

      el.data('map', map)
      el.data('infoWindow', infoWindow)
      el.data('infoWindowTemplate', infoWindowTemplate)
    },
    move: function(e, center) {
      var el = $(this)
      var map = el.data('map')
    },
    infoWindow: function(e, marker, item) {
      var el = $(this)
      var map = el.data('map')
      var infoWindow = el.data('infoWindow')
      var infoWindowTemplate = el.data('infoWindowTemplate')
      marker.setMap(null)

      $.each(infoWindowTemplate.find('[data-name]'), function(i, el) {
        el.innerHTML = item[$(el).data('name')]
      })

      infoWindow.setContent(infoWindowTemplate.prop('innerHTML'));
      infoWindow.open(map, marker);
    }
  })

  if ($('#region_map-container').length) {
    $('#region_map-container').trigger('create', [
      $('#map-centers').data('content')[0],
      $('#map-markers').data('content'),
      $('#map-info_window-container')
    ])
  }

})
