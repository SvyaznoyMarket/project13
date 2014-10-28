;(function($) {

    var
        $form = $('#jsOrderV3OneClickForm')
    ;


    // отслеживаем смену региона
    $form.on('submit', function(e){
        var
            $el = $(this),
            data = $el.serializeArray()
        ;

        $.post($el.attr('action'), data).done(function(response) {

        });

        e.preventDefault();
    })

})(jQuery);