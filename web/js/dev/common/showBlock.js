;(function($){

    $('body').on('click', '.js-showBlock', function() {
        var
            $el = $(this),
            data = $el.data('showBlock'),
            $target = data && data['target'] && $(data['target']),
            $hideTarget = data && data['hideTarget'] && $(data['hideTarget'])
        ;

        try {
            if (!$target.length) {
                throw {message: 'Не найден целевой блок'}
            }

            $target.toggle();
            if ($hideTarget.length) {
                $hideTarget.hide();
            }
        } catch (error) { console.error(erro); }
    });

}(jQuery));