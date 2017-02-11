$(document).ready(function() {

	if (!ENTER.product) {
		ENTER.product = {
			previousViewedProductIdsCookieValue: ''
		};
	}


	(function() {
		if (location.hash.indexOf('#sender') == 0) {
			var senders = $.deparam(location.hash.slice(1).trim());

			if (!senders.sender || typeof(senders.sender) != 'object') {
				senders.sender = null;
			}

			if (!senders.sender2 || typeof(senders.sender2) != 'string') {
				senders.sender2 = '';
			}

			if (senders.sender2) {
				$('.js-slider-2').each(function() {
					var
						$button = $(this),
						newData = $(this).data('slider');

					newData.sender2 = senders.sender2;
					$button.attr('data-slider', JSON.stringify(newData)).data('slider', newData);
				});

				$('.js-slider-2 .js-orderButton').each(function() {
					var $button = $(this);
					$button.attr('data-sender2', senders.sender2).data('sender2', senders.sender2);

					var newSearch = $.deparam(this.search.slice(1));
					newSearch.sender2 = $button.data('sender2');
					this.search = '?' + $.param(newSearch);
				});
			}

			if (senders.sender || senders.sender2) {
				$('.js-orderButton-product, .js-oneClickButton-main, .js-kitButton').each(function() {
					var $button = $(this);

					if (senders.sender) {
						var newSender = $.extend({}, $button.data('sender'), senders.sender);
						$button.attr('data-sender', JSON.stringify(newSender)).data('sender', newSender);
					}

					if (senders.sender2) {
						$button.attr('data-sender2', senders.sender2).data('sender2', senders.sender2);
					}

					if ($button.is('.js-orderButton-product')) {
						var newSearch = $.deparam(this.search.slice(1));
						if (senders.sender) {
							newSearch.sender = $button.data('sender');
						}

						if (senders.sender2) {
							newSearch.sender2 = $button.data('sender2');
						}

						this.search = '?' + $.param(newSearch);
					}
				});
			}

			$('.js-product-variations-dropbox-item-link').each(function() {
				var $link = $(this);
				$link.attr('href', $link.attr('href') + location.hash);
			});

			history.replaceState({}, '', location.pathname + (location.search ? '?' + location.search : ''));
		}
	})();

	$('.js-slider-2').goodsSlider({
		leftArrowSelector: '.goods-slider__btn--prev',
		rightArrowSelector: '.goods-slider__btn--next',
		sliderWrapperSelector: '.goods-slider__inn',
		sliderSelector: '.goods-slider-list',
		itemSelector: '.goods-slider-list__i',
		categoryItemSelector: '.js-product-accessoires-category',
		//pageTitleSelector: '.slideItem_cntr',
		onLoad: function(goodsSlider) {
			ko.applyBindings(ENTER.UserModel, goodsSlider);

			// Для табов в новой карточке товара
			if ($(goodsSlider).data('position') == 'ProductSimilar') $('.jsSimilarTab').show();
		}
	});

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
	 * Подключение слайдера товаров
	 */
	$('.js-slider').goodsSlider({
		onLoad: function(goodsSlider) {
			ko.applyBindings(ENTER.UserModel, goodsSlider);
		}
	});

    try {
        var
            productId = ENTER.config.pageConfig.product ? ENTER.config.pageConfig.product.id : null,
            cookieValue = docCookies.getItem('product_viewed') || '',
			i, viewed = []
        ;

		ENTER.product.previousViewedProductIdsCookieValue = cookieValue;

        if (productId) {
            viewed = cookieValue ? ENTER.utils.arrayUnique(cookieValue.split(',')) : [];
            if ((i = viewed.indexOf(productId.toString())) !== -1) {
				viewed.splice(i, 1);
			}
			viewed.push(productId);
            docCookies.setItem('product_viewed', viewed.slice(-20).join(','), 7 * 24 * 60 * 60, '/');
        }
    } catch (e) {
        console.error(e);
    }

});