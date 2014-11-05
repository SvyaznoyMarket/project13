;(function($) {

    var $body = $(document.body),
        $orderContent = $('.orderCnt'),
        $pageNew = $('#jsOneClickContentPage'),
        $validationErrors = $('.jsOrderValidationErrors'),
        errorClass = 'textfield-err',
        validateEmail = function validateEmailF(email) {
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        },
        validate = function validateF(){
            var error = [],
                $phoneInput = $('[name=user_info\\[mobile\\]]'),
                $emailInput = $('[name=user_info\\[email\\]]'),
                phone = $phoneInput.val().replace(/\s+/g, '');

            if (!/8\(\d{3}\)\d{3}-\d{2}-\d{2}/.test(phone)) {
                error.push('Неверный формат телефона');
                $phoneInput.addClass(errorClass).siblings('.errTx').show();
            } else {
                $phoneInput.removeClass(errorClass).siblings('.errTx').hide();
            }

            if ($emailInput.val().length != 0 && !validateEmail($emailInput.val())) {
                error.push('Неверный формат E-mail');
                $emailInput.addClass(errorClass).siblings('.errTx').show();
            } else {
                $emailInput.removeClass(errorClass).siblings('.errTx').hide();
            }

            return error;
        };

    if ($validationErrors.length) {
        console.warn('Validation errors', $validationErrors);
    }

    $pageNew.on('blur', 'input',function(){
        validate()
    });

    // проверка телефона и email
    $pageNew.find('form').on('submit', function (e) {
        var error = validate();
        if (error.length != 0) {
            e.preventDefault();
            e.stopPropagation();
            $body.trigger('trackUserAction', ['6_2 Далее_ошибка_Получатель', 'Поле ошибки: '+error.join(', ')])
        }
    });


}(jQuery));