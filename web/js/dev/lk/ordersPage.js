/**
 * Orders Page
 *
 * @author  Zhukov Roman
 */
;(function($){

    var $body = $(document.body);

    /* Скрытие/раскрытие истории заказов за год */
    $body.on('click', '.personalTable_cell_rowspan', function(){

        var $corner = $(this).find('.textCorner'),
            year = $(this).data('value'),
            $table = $('.personalTable_rowgroup_' + year);

        $table.toggle();

        if ($corner.hasClass('textCorner-open')) {
            $corner.removeClass('textCorner-open')
        } else {
            $corner.addClass('textCorner-open')
        }

    });

    $body.on('click', '.js-orderContainer-link', function() {
        var
            $el = $(this),
            relations = $el.data('relation'),
            $container = relations['container'] && $(relations['container'])
        ;

        try {
            if (!$container.length) {
                throw {message: 'Не найден контейнер'};
            }

            $container.toggleClass('expanded');
        } catch (error) { console.error(error); }
    });

    /* Init */
    $('.textCorner.mOldYear').removeClass('textCorner-open');
    $('.personalTable_rowgroup.mOldYear').hide();

})(jQuery);