+function($){

    var $supplierLoginButton = $('.jsSupplierLoginButton'),
        $authPopup = $('#auth-block'),
        $title = $authPopup.find('.jsAuthFormLoginTitle'),
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
    $phone.mask('8 (999) 999 99 99');


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

    // Показ модифицированного окна логина
    $supplierLoginButton.on('click', function(){
        $authPopup.addClass(authClass);
        $title.text('Вход в Enter B2B');
        $authPopup.lightbox_me({
            centered: true,
            onClose: function() {
                $authPopup.removeClass(authClass);
                $title.text('Вход в Enter')
            }
        })
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
                    $supplierLoginButton.click();
                    // Подставим email в попап логина
                    $authPopup.find('[name=signin\\[username\\]]').val($registerForm.find('[name=email]').val());
                    $('<div style="font-weight: bold; margin: 10px 0; color: gray" />').text('Пароль выслан на телефон и email').insertAfter($title)
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