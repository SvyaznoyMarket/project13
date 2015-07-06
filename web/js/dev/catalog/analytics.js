$(function() {
    var
        $body = $('body'),

        sliderData = $('#jsSlice').data('value')
    ;

    if (sliderData && (true === sliderData.isSale)) {
        $body.on('click', '.js-productCategory-link', function() {
            var
                $el = $(this)
            ;

            $body.trigger('trackGoogleEvent', {
                category: 'slices_sale',
                action: 'category',
                label: $el.find('[data-type="name"]').text()
            });
        });
    }
});