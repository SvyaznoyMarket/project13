$(function() {
    $('body').on('click', '.jsFavoriteLink', function(e){
        var
            $el = $(e.currentTarget),
            xhr = $el.data('xhr')
        ;

        console.info({'.jsFavoriteLink click': $el});

        try {
            if (xhr)  xhr.abort();
        } catch (error) { console.error(error); }

        xhr = $.post($el.attr('href'))
            .done(function(response) {
                // TODO: добавить модификатор к кнопке
            })
            .always(function() {
                $el.data('xhr', null);
            })
        ;
        $el.data('xhr', xhr);

        e.preventDefault();
    });
});