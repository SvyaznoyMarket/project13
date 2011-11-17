$(document).ready(function() {
	/* bMap */
	$('.bMap__eContainer').live('click', function(){
		if( $(this).hasClass('mChecked') ){}
		else{
			$( '.mChecked' ,$(this).parent() ).removeClass('mChecked');
			$(this).addClass('mChecked');
		}
	});
	/* /bMap */


  $('.map-360-link').bind('click', function() {
    var el = $(this)
    var data = $('#map-panorama').data()

    embedpano({swf: data.swf, xml: data.xml, target: 'map-container'});
  })


  $('.map-google-link').bind('click', function() {
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
  })

  //$('.map-360-link').trigger('click')

})
