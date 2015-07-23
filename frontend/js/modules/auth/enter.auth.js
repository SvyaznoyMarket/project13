/**
 * @module      enter.auth
 * @version     0.1
 *
 * [About YM Modules]{@link https://github.com/ymaps/modules}
 */
!function( modules, module ) {
    modules.define(
        'enter.auth',
        [],
        module
    );
}(
    this.modules,
    function( provide ) {
        'use strict';

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
            });
        });

        provide({});
    }
);
