(function($) {
    var $orderContent = $('.orderCnt'),
        $inputs = $orderContent.find('input');

    // jQuery masked input
    $.mask.definitions['x']='[0-9]';
    $.mask.placeholder= "_";
    $.map($inputs, function(elem, i) {
        if (typeof $(elem).data('mask') !== 'undefined') $(elem).mask($(elem).data('mask'));
    });

    // переключение бонусных карт
    $orderContent.on('click', '.bonusCnt_i', function(e) {
        e.stopPropagation();

        var $elem = $(this),
            eq = $elem.data('eq'),
            $cardsDescriptions = $('.bonusCnt_it');

        if ($elem.hasClass('bonusCnt_i-act')) return;

        $('.bonusCnt_i').removeClass('bonusCnt_i-act');
        $elem.addClass('bonusCnt_i-act');
        $cardsDescriptions.hide().eq(eq).show();
    });

})(jQuery);