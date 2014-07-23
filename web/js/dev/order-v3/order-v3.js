(function($) {

    var
        $body = $('body'),
        $orderContent = $('#js-order-content')
    ;

    $orderContent.on('click', '.js-order-changePlace-link', function(e) {
        e.stopPropagation();

        var
            $el = $(e.target),
            $content = $($el.data('content'))
        ;

        if (!$content.length) {
            console.error('js-order-changePlace-link', 'Не найден content');
            return false;
        }

        $content.show();

        e.preventDefault();
    });

    $orderContent.on('click', '.js-order-changePlace-close', function(e) {
        e.stopPropagation();

        var
            $el = $(e.target),
            $content = $($el.data('content'))
        ;

        if (!$content.length) {
            console.error('js-order-changePlace-link', 'Не найден content');
            return false;
        }

        $content.hide();

        e.preventDefault();
    });

})(jQuery);