+function($){

    var $form = $('.js-auth-form');

    $.mask.definitions['n'] = '[0-9]';
    $('.js-registerForm .js-phoneField').mask('+7 (nnn) nnn-nn-nn');

    $('.js-auth-switch-state').on('click', function(e){
        e.preventDefault();

        $('.js-auth-state')
            .removeClass('login_auth login_reg login_hint login_success')
            .addClass($(this).data('state'))
    });

    // Отправка формы
    $form.on('submit', function(e){
        e.preventDefault();

        $.post($form.attr('action'), $form.serialize()).done(function(data){
            if (data.form && data.form.error) {
                data.form.error.forEach(function(val){
                    if (val.message) {
                        switch (val.field) {
                            case 'username':
                                $('.js-auth-username-input').addClass('error');
                                $('.js-auth-username-label').text(val.message);
                                break;
                            case 'password':
                                $('.js-auth-password-input').addClass('error');
                                $('.js-auth-password-label').text(val.message);
                                break;
                        }
                    }
                })
            }
        })
    })

}(jQuery);
