;(function() {

    $(document).ready(function() {
        $('.js-slider').each(function(i, el) {
            var data = $(el).data('slider');

            data['rrviewed'] = docCookies.getItem('rrviewed').split(',').unique();

            $(el).data('slider', data);
        });
    });

}());