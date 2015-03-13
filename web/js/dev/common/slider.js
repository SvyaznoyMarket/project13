;$(document).ready(function() {
    var $body = $('body');

    /** Событие клика на товар в слайдере */
    $body.on('click', '.jsRecommendedItem', function(event) {

        try {
            var $el = $(this),
                $target = $(event.target),
                link = $el.attr('href'),
                aTarget = $el.attr('target'),
                $slider = $el.parents('.js-slider'),
                sender = $slider.length ? $slider.data('slider').sender : null;

            $body.trigger('TLT_processDOMEvent', [event]);

            if (!$target.hasClass('js-orderButton')) {
				var rrEventLabel = '';
				if (ENTER.config.pageConfig.product) {
					if (ENTER.config.pageConfig.product.isSlot) {
						rrEventLabel = '_marketplace-slot';
					} else if (ENTER.config.pageConfig.product.isOnlyFromPartner) {
						rrEventLabel = '_marketplace';
					}
				}

                $body.trigger('trackGoogleEvent', {
                    category: 'RR_взаимодействие' + rrEventLabel,
                    action: 'Перешел на карточку товара',
                    label: sender ? sender.position : null,
                    hitCallback: aTarget == '_blank' ? null : link
                });
            }

            event.stopPropagation();

        } catch (e) { console.error(e); }
    });

    /** Событие пролистывание в слайдере */
    $body.on('click', '.jsRecommendedSliderNav', function(event) {
        console.log('jsRecommendedSliderNav');

        try {
            var
                $el = $(this),
                $slider = $el.parents('.js-slider'),
                sender = $slider.length ? $slider.data('slider').sender : null
                ;

			var rrEventLabel = '';
			if (ENTER.config.pageConfig.product) {
				if (ENTER.config.pageConfig.product.isSlot) {
					rrEventLabel = '_marketplace-slot';
				} else if (ENTER.config.pageConfig.product.isOnlyFromPartner) {
					rrEventLabel = '_marketplace';
				}
			}

            $body.trigger('trackGoogleEvent',['RR_Взаимодействие' + rrEventLabel, 'Пролистывание', sender.position]);
        } catch (e) { console.error(e); }
    });

    // Запоминает просмотренные товары
    try {
        $('.js-slider').each(function(i, el) {
            var
                data = $(el).data('slider'),
                //rrviewed = docCookies.getItem('rrviewed')
                rrviewed = docCookies.getItem('product_viewed')
            ;

            if (('viewed' == data.type) && typeof rrviewed === 'string') {
                data['rrviewed'] = ENTER.utils.arrayUnique(rrviewed.split(','));

                $(el).data('slider', data);
            }
        });
    } catch (e) {
        console.error(e);
    }

    // попачик для слайдера
    $body.on('mouseenter', '.slideItem_i', function(e) {
        var
            $el = $(this),
            $bubble = $el.parents('.js-slider').find('.slideItem_flt')
        ;

        if ($bubble.length) {
            $bubble.find('.slideItem_flt_i').text($el.data('product').name);
            $bubble.addClass('slideItem_flt-show');
            $bubble.offset({left: $el.offset().left});
        }
    });

    $body.on('mouseleave', '.slideItem_i', function(e) {
        var
            $el = $(this),
            $bubble = $el.parents('.js-slider').find('.slideItem_flt')
        ;

        if ($bubble.length) {
            $bubble.find('.slideItem_flt_i').text('');
            $bubble.removeClass('slideItem_flt-show');
        }
    });
});