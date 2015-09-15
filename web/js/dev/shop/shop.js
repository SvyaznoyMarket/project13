$(function($){
    var $viewport = $('.js-shop-viewport');

    if ($viewport.length) {
        ymaps.ready(function () {
			var coords = [$viewport.data('map-latitude'), $viewport.data('map-longitude')];
            var map = new ymaps.Map($viewport[0], {
                center: coords,
                zoom: 16
            });

            map.geoObjects.add(new ymaps.Placemark(coords, {}, {
				iconLayout: 'default#image',
				iconImageHref: '/images/map/marker-shop.png',
				iconImageSize: [28, 39],
				iconImageOffset: [-14, -39]
			}));
        });

		$('.js-shop-image-opener').click(function(e){
			e.preventDefault();
			var $self = $(e.currentTarget);

			if ($self.data('type') == 'image') {
				var
					bigUrl = $self.data('big-url'),
					$img = $viewport.find('img')
				;

				$viewport.find('ymaps:first').hide();

				if ($img.length) {
					$img.attr('src', bigUrl).show();
				} else {
					$viewport.append($('<img />', {
						src: bigUrl,
						width: $viewport.width()
					}));
				}
			} else if ($self.data('type') == 'map') {
				$viewport.find('ymaps:first').show();
				$viewport.find('img:first').hide();
			}
		});
    }

     $('.js-tab-head').on('click',function(){
         var $this = $(this),
             $tab = $('#'+$this.data('target'));
         $('.js-tab-head').removeClass('active');
         $this.addClass('active');
         $('.js-tab').removeClass('active');
         $tab.addClass('active');
     })
});
