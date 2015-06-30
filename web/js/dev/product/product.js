$(document).ready(function() {

    var $productDescriptionToggle = $('#productDescriptionToggle');


	/**
	 * Подключение нового зумера
	 *
	 * @requires jQuery, jQuery.elevateZoom
	 */
	(function () {
		var
			thumbImageActiveClass = 'prod-photoslider__gal__link--active',
			$image = $('.js-photo-zoomedImg');

		if (!$image.length) {
			console.warn('Нет изображения для elevateZoom');
			return;
		}

		var zoomConfig = {
			$imageContainer: $('.js-product-bigImg'),
			zoomWindowOffety: 0,
			zoomWindowOffetx: 19,
			zoomWindowWidth: $image.data('is-slot') ? 344 : 519,
			borderSize: 1,
			borderColour: '#C7C7C7'
		};

		if ($image.data('zoom-image')) {
			$image.elevateZoom(zoomConfig);
		}

		$('.jsPhotoGalleryLink').on('click', function(e) {
			e.preventDefault();

			var $link = $(e.currentTarget);
			if ($link.hasClass(thumbImageActiveClass)) {
				return;
			}

			$('.jsPhotoGalleryLink').removeClass(thumbImageActiveClass);
			$link.addClass(thumbImageActiveClass);

			if ($image.data('elevateZoom')) {
				$image.data('elevateZoom').destroy();
			}

			if ($link.data('zoom-image')) {
				$image.data('zoom-image', $link.data('zoom-image'));
				$image.one('load', function() {
					$image.elevateZoom(zoomConfig);
				});
			}

			$image.attr('src', $link.data('image'));
		});
	})();


	/**
	 * Каутер товара
	 *
	 * @requires	jQuery, jQuery.goodsCounter
	 * @param		{Number} count Возвращает текущее значение каунтера
	 */
	$('.bCountSection').goodsCounter({
		onChange:function( count ){
			var spinnerFor = this.attr('data-spinner-for'),
				bindButton = $('.'+spinnerFor),
                bindOneClickButton = $('.' + spinnerFor + '-oneClick'),
				newHref = bindButton.attr('href') || '';
			// end of vars

			bindButton.attr('href',newHref.addParameterToUrl('quantity',count));
            bindOneClickButton.data('quantity', count);

			// добавление в корзину после обновления спиннера
			// if (bindButton.hasClass('mBought')){
			// 	bindButton.eq('0').trigger('buy');
			// }
		}
	});


	/**
	 * Подключение слайдера товаров
	 */
	$('.js-slider').goodsSlider({
		onLoad: function(goodsSlider) {
			ko.applyBindings(ENTER.UserModel, goodsSlider);
		}
	});

	/**
	 * Подключение кастомных дропдаунов
	 */
	$('.bDescSelectItem').customDropDown({
		changeHandler: function( option ) {
			document.location.href = option.data('url');
		}
	});

	// карточка товара - характеристики товара краткие/полные
	if ( $productDescriptionToggle.length ) {
        $productDescriptionToggle.toggle(
			function( e ) {
				e.preventDefault();
				$(this).parent().parent().find('.descriptionlist:not(.short)').show();
				$(this).html('Скрыть все характеристики');
			},
			function( e ) {
				e.preventDefault();
				$(this).parent().parent().find('.descriptionlist:not(.short)').hide();
				$(this).html('Показать все характеристики');
			}
		);
	}

    try {
        var
            productId =ENTER.config.pageConfig.product ? ENTER.config.pageConfig.product.id : null,
            cookieValue = docCookies.getItem('product_viewed') || '',
            viewed = []
        ;

        if (productId) {
            viewed = cookieValue ? ENTER.utils.arrayUnique(cookieValue.split(',')) : [];
            viewed.push(productId);
            docCookies.setItem('product_viewed', viewed.slice(-20).join(','), 7 * 24 * 60 * 60, '/');
        }
    } catch (e) {
        console.error(e);
    }

});