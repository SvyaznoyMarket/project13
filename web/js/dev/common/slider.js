;(function() {

    $(document).ready(function() {
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

    });

}());