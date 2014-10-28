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

            })
            .fail(function(jqXHR){
                var response = $.parseJSON(jqXHR.responseText);

                if (response.result && response.result.errorContent) {
                    $('#OrderV3ErrorBlock').html($(response.result.errorContent).html()).show();
                }
            })
        ;

        e.preventDefault();
    })

})(jQuery);