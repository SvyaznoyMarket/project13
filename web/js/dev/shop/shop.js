$(function($){
    var
		$viewport = $('.js-shop-viewport'),
		$mapImageOpener = $('.js-shop-image-opener-map'),
		loadingClass = 'loading',
		shadowClass = 'shadow';

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
				var $img = $viewport.find('img');

				$viewport.find('ymaps:first').hide();

				if (!$img.length) {
					$img = $('<img />').appendTo($viewport);
					$img.load(function() {
						// Центрируем изображение, выходящее за пределы viewport
						var left = parseInt(($img.width() - $viewport.width()) / 2);
						$img.css('left', left > 0 ? '-' + left + 'px' : 0);
					});
				}

				$img.attr('src', $self.data('big-url')).show();
			} else if ($self.data('type') == 'map') {
				$viewport.find('ymaps:first').show();
				$viewport.find('img:first').hide();
			}
		});

		$('.js-shop-gallery').scroll(function(){
			if ($(this).scrollTop()) {
				$mapImageOpener.addClass(shadowClass);
			} else {
				$mapImageOpener.removeClass(shadowClass);
			}
		});
    }

	$('.js-shop-tab-head').on('click',function(){
		var $this = $(this),
			$tab = $('#' + $this.data('target'));

		$('.js-shop-tab-head').removeClass('active');
		$this.addClass('active');
		$('.js-shop-tab').removeClass('active');
		$tab.addClass('active');
	});

	$('.js-shop-print-opener').click(function(e) {
		e.preventDefault();
		print();
	});

	$('.js-shop-email-opener').click(function(e) {
		e.preventDefault();
		var
			$popup = $('.js-shop-email-popup'),
			$form = $('.js-shop-email-popup-form', $popup),
			$error = $('.js-shop-email-popup-error', $form),
			isPopupCreated = false;

		$popup.enterLightboxMe({
			centered: true,
			closeSelector: '.js-shop-email-popup-closer',
			onLoad: function() {
				if (isPopupCreated) {
					return;
				}

				isPopupCreated = true;

				var gaClientId = '';
				ENTER.utils.analytics.ga.getClientId(function(gaClientIdArg) {
					gaClientId = gaClientIdArg;
				});

				$('.js-shop-email-popup-send', $popup).click(function(e) {
					e.preventDefault();
					$form.submit();
				});

				$form.submit(function(e) {
					e.preventDefault();

					if ($form.hasClass(loadingClass)) {
						return;
					}

					$form.addClass(loadingClass);

					$.ajax({
						type: $form.attr('method'),
						url: $form.attr('action'),
						data: {
							email: $('.js-shop-email-popup-input', $form).val(),
							gaClientId: gaClientId
						},
						complete: function() {
							$form.removeClass(loadingClass);
						},
						success: function(data) {
							if (data.error) {
								$error.text(data.error).show();
							} else {
								$popup.trigger('close');
							}
						},
						error: function() {
							$error.text('Не удалось отправить письмо').show();
						}
					});
				});
			},
			onClose: function() {
				$error.text('').hide();
			}
		});
	});
});
