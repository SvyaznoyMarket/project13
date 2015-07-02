$(function() {
    $('body').on('click', '.jsFavoriteLink', function(e){
        var
            $el = $(e.currentTarget),
            xhr = $el.data('xhr')
        ;

        console.info({'.jsFavoriteLink click': $el});



        if ($el.data('ajax')) {
            e.stopPropagation();

            try {
                if (xhr)  xhr.abort();
            } catch (error) { console.error(error); }

            xhr = $.post($el.attr('href'))
                .done(function(response) {
                    $('body').trigger('updateWidgets', {
                        widgets: response.widgets,
                        callback: $el.attr('href').indexOf('delete-product') !== -1 ? null : function() {
                            var $widget = $("#favourite-userbar-popup-widget"),
                                showClass = 'topbarfix_cmpr_popup-show';

                            $widget.addClass(showClass);
                            setTimeout(function(){ $widget.removeClass(showClass) }, 2000)
                            }
                        });
                })
                .always(function() {
                    $el.data('xhr', null);
                })
            ;
            $el.data('xhr', xhr);

            e.preventDefault();
        }
    });
});