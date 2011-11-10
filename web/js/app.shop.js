$(document).ready(function() {
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
  });

})
