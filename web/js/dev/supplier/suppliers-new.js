+function($){

    var $supplierLoginButton = $('.jsSupplierLoginButton'),
        authClass = 'supplier-login',
        inputErrorClass = 'error',
        $registerForm = $('#b2bRegisterForm'),
        validate,
        $inputs = $registerForm.find('input'),
        $detailName = $registerForm.find('[name=detail\\[name\\]]'),
        $userName = $registerForm.find('[name=first_name]'),
        $email = $registerForm.find('[name=email]'),
        $phone = $registerForm.find('[name=mobile]'),
        $agreed = $registerForm.find('[name=agree]');

    $.mask.placeholder= "_";
    $.mask.autoclear = false;
    $phone.mask('+7 (nnn) nnn-nn-nn');

    /* Функция валидации формы */
    validate = function(){

        // Очищаем классы ошибок
        $inputs.removeClass(inputErrorClass);
        $agreed.next().removeClass('red');

        if ($detailName.val() == '') $detailName.addClass(inputErrorClass);
        if ($userName.val() == '') $userName.addClass(inputErrorClass);
        if (!ENTER.utils.validateEmail($email.val())) $email.addClass(inputErrorClass);
        if ($phone.val() == '') $phone.addClass(inputErrorClass);
        if (!$agreed.is(':checked')) $agreed.next().addClass('red');

        return $registerForm.find('input.error').length == 0 && $agreed.is(':checked');
    };

    function openAuth(loginEmail, loginMessage) {
        ENTER.auth.open({
            loginTitle: 'Вход в Enter B2B',
            loginEmail: loginEmail,
            loginMessage: loginMessage,
            onBeforeLoad: function($authContent) {
                $authContent.addClass(authClass);
            },
            onClose: function($authContent) {
                $authContent.removeClass(authClass);
            }
        });
    }

    // Показ модифицированного окна логина
    $supplierLoginButton.on('click', function(e){
        e.preventDefault();
        openAuth();
    });

    $('.jsSupplierToHide').on('click', function(e){
        e.preventDefault();
        $('.supplierToHide').hide()
    });

    // Обработчик
    $registerForm.on('submit', function(e) {
        e.preventDefault();
        if (!validate()) return;
        $.ajax($registerForm.attr('action'), {
            type: 'POST',
            data: $registerForm.serialize(),
            success: function(data) {
                console.log('success function', data);
                if (data.success) {
                    openAuth($registerForm.find('[name=email]').val(), 'Пароль выслан на телефон и email');
                }

                if (data.error == 'Некорректный email' || data.error == 'Такой email уже занят') $registerForm.find('[name=email]').addClass(inputErrorClass);
                if (data.error == '"Должен быть мобильный номер телефона"' || data.error == 'Такой номер уже занят') $registerForm.find('[name=mobile]').addClass(inputErrorClass);
            },
            error: function(){
                console.error('User registration error');
            }
        })
    });

}(jQuery);