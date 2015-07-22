+function($){

    var $form = $('.js-auth-form');

    $('.js-auth-switch-state').on('click', function(e){
        e.preventDefault();

        $('.js-auth-state')
            .removeClass('login_auth login_reg login_hint login_success')
            .addClass($(this).data('state'))
    });

    // Отправка формы
    $form.on('submit', function(e){
        e.preventDefault();
    })

}(jQuery);
