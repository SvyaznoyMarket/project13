;(function($) {

    var
        $form = $('.jsOrderV3OneClickForm')
    ;


    // отслеживаем смену региона
    $form.on('submit', function(e){
        var
            $el = $(this),
            data = $el.serializeArray()
        ;

        $.post($el.attr('action'), data)
            .done(function(response) {
                if (typeof response.result !== 'undefined') {
                    $('#jsOneClickContentPage').hide();
                    $('#jsOneClickContent').append(response.result.page);

                    $('body').trigger('trackUserAction', ['3_1 Оформить_успешно']);
                }

                var $orderContainer = $('#jsOrderV3OneClickOrder');
                if ($orderContainer.length) {
                    $.get($orderContainer.data('url')).done(function(response) {
                        $orderContainer.html(response.result.page);
                    });
                }
            })
            .fail(function(jqXHR){
                var response = $.parseJSON(jqXHR.responseText);

                if (response.result && response.result.errorContent) {
                    $('#OrderV3ErrorBlock').html($(response.result.errorContent).html()).show();
                }

                var error = (response.result && response.result.error) ? response.result.error : {};

                $('body').trigger('trackUserAction', ['3_2 Оформить_ошибка', 'Поле ошибки: '+ ((typeof error !== 'undefined') ? error.join(', ') : '')]);
            })
            .complete(function() {
                $('.shopsPopup').find('.close').trigger('click');
            })
        ;

        e.preventDefault();
    })

})(jQuery);