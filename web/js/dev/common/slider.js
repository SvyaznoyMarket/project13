;(function() {

    $(document).ready(function() {
        // Запоминает просмотренные товары
        try {
            $('.js-slider').each(function(i, el) {
                var
                    data = $(el).data('slider'),
                    //rrviewed = docCookies.getItem('rrviewed')
                    rrviewed = docCookies.getItem('product_viewed')
                ;

                if (typeof rrviewed === 'string') {
                    data['rrviewed'] = rrviewed.split(',').unique();
                }

                $(el).data('slider', data);
            });
        } catch (e) {
            console.error(e);
        }

        // попачик для слайдера

        $('body').on('mouseenter', '.slideItem_i', function(e) {
            var
                $el = $(this),
                $bubble = $el.parents('.js-slider').find('.slideItem_flt')
            ;

            if ($bubble.length) {
                $bubble.find('.slideItem_flt_i').text($el.data('product').name);
                $bubble.addClass('slideItem_flt-show');
                console.info($el.offset());
                $bubble.offset({left: $el.offset().left});
            }
        });

        $('body').on('mouseleave', '.slideItem_i', function(e) {
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

}());