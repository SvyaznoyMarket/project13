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

    $body.on('click', '.js-favorite-showMovePopup', function() {
        var
            $el = $(this),
            data = $el.data('action'),
            $container = data.container && $(data.container),
            $target = data.target && $(data.target),
            $productInputs,
            productUis = []
        ;

        try {
            if (!$container.length) {
                throw {name: 'Контейнер не найден'};
            }
            $productInputs = $container.find('input[data-type="product"]:checked');
            if (!$productInputs.length) {
                throw {name: 'Товары не выбраны'};
            }
            $productInputs.each(function(i, el) {
                productUis.push($(el).val());
            });

            if (!$target.length) {
                throw {name: 'Целевой элемент не найден'};
            }
            $formInput = $target.find('input[data-field=productUis]');
            if (!$formInput.length) {
                throw {name: 'Инпут формы не найден'};
            }
            $formInput.val(productUis.join(','));
        } catch (error) { console.error(error); }
    });

}(jQuery));