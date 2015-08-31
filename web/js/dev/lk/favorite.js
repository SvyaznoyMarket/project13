;(function($) {
    var
        $body = $('body')
    ;

    $body.on('click', '.personal-favorit__price-change', function() {
        $(this).toggleClass('on');
    });
    $body.on('click', '.personal-favorit__stock', function() {
        $(this).toggleClass('on');
    });
    $body.on('click', '.js-fav-popup-show', 'click',function() {
        var popup = $(this).data('popup');

        $('body').append('<div class="overlay"></div>');
        $('.overlay').data('popup', popup).show();
        $('.'+popup).show();
    });
    $body.on('click', '.overlay',function() {
        var popup = $(this).data('popup');
        $('.' + popup).hide();
        $('.overlay').remove();
    });
    $body.on('change', '.js-fav-all', function() {

        var
            list = $(this).closest('.personal__favorits').find('.personal-favorit__checkbox'),
            val = !!$(this).attr('checked')
        ;

        $(list).each(function(){
            $(this).attr('checked', val);
        });
    });
    $body.on('click', '.popup-closer', function() {
        $(this).parent().hide();
        $('.overlay').remove();
    });

}(jQuery));