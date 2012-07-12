
  
function MapWithShopsOLD(center, infoWindowTemplate, DOMid, selectCallback) {
  var self = this
  self.mapWS = null
  self.infoWindow = null
  self.positionC = null
  self.markers = []
  self.inited = false

  function create() {
console.log( 'LatLng' in google.maps )   
alert('AAAA') 
    self.positionC = new google.maps.LatLng(center.latitude, center.longitude)
    var options = {
      zoom:11,
      center:self.positionC,
      scrollwheel:false,
      mapTypeId:google.maps.MapTypeId.ROADMAP,
      mapTypeControlOptions:{
        style:google.maps.MapTypeControlStyle.DROPDOWN_MENU
      }
    }
    self.mapWS = new google.maps.Map(document.getElementById(DOMid), options)
    self.infoWindow = new google.maps.InfoWindow({
      maxWidth:400,
      disableAutoPan:false
    })
  }

  function getMarkers() {
    var shops = $('#order-delivery_map-data').data().value.shops;
    var markers = {}, n = 1;
    for (var i in shops) {
      var tmp = shops[i];
      tmp.markerImg = '/images/marker_' + n + '.png';
      markers[tmp.id] = tmp;
      n++;
    }
    return markers;
  }

  this.showInfobox = function (marker) {
    var item = self.markers[marker.id]
    //marker.setVisible(false) // hides marker
    $.each(infoWindowTemplate.find('[data-name]'), function (i, el) {
      el.innerHTML = item[$(el).data('name')]
    })

    self.infoWindow.setContent(infoWindowTemplate.prop('innerHTML'))
    self.infoWindow.setPosition(marker.position)
    self.infoWindow.open(self.mapWS)
    google.maps.event.addListener(self.infoWindow, 'closeclick', function () {
      marker.setVisible(true)
    })
  }

  this.showInfoWindow = function(marker) {
    self.infoWindow.setContent('<div>'+marker.name+'<br />'+marker.regime+'</div>')
    self.infoWindow.setPosition(marker.ref.position)
    self.infoWindow.open(self.mapWS, marker.ref)
  }

  this.renderShopInfo = function(marker) {
    var tpl = '<li data-id="' + marker['id'] + '">';
    tpl += '<div class="bMapShops__eListNum"><img src="/images/shop.png" alt=""/></div>';
    tpl += '<div>' + marker['name'] + '</div>';
    tpl += '<span>Работаем</span> <span>' + marker['regime'] + '</span>';
    tpl += '</li>';
    $('#mapPopup_shopInfo').append(tpl);
  }

  this.showMarkers = function () {
    if (self.inited === true) {
      return;
    }
    var markers = getMarkers();
    $.each(self.markers, function (i, item) {
      if (typeof( item.ref ) !== 'undefined')
        item.ref.setMap(null)
    })
    self.markers = markers;
    google.maps.event.trigger(self.mapWS, 'resize')
    self.mapWS.setCenter(self.positionC)
    $.each(markers, function (i, item) {
      var marker = new google.maps.Marker({
        position:new google.maps.LatLng(item.latitude, item.longitude),
        map:self.mapWS,
        title:item.name,
        icon:item.markerImg,
        id:item.id
      })
      google.maps.event.addListener(marker, 'click', function () {
        //self.showInfobox(this)
        selectCallback(this.id);
      })

      google.maps.event.addListener(marker, 'mouseover', function () {
          self.showInfoWindow(self.markers[this.id]);
      })

      self.markers[marker.id].ref = marker;
      self.renderShopInfo(item);
    })
    $('#mapPopup_shopInfo li').click(function(){ selectCallback($(this).data('id')); });
    self.inited = true;
  }

  this.closeMap = function() {
    self.infoWindow.close()
    $('.mMapPopup').trigger('close') // close lightbox_me
  }

  this.openMap = function() {
    $('.mMapPopup').lightbox_me({
      centered:true,
      onLoad:function() {
        self.showMarkers()
      }
    })
  }

  $('#mapPopup_shopInfo').data('timer', null).delegate('li', 'hover', function(e) {
      var id = $(this).data('id');

      var timer = $('#mapPopup_shopInfo').data('timer');
      if (timer) {
          clearTimeout(timer);
      }

      if (id && self.markers[id]) {
          var timer = setTimeout(function() {
              self.showInfoWindow(self.markers[id]);
          }, 500);

          $('#mapPopup_shopInfo').data('timer', timer);
     }
  })

  /* main() */
  create()

} // object MapWithShops
