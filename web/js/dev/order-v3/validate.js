;(function($) {

    var $body = $(document.body),
        $orderContent = $('.orderCnt'),
        $errorBlock = $orderContent.find('#OrderV3ErrorBlock'),
        $pageNew = $('.jsOrderV3PageNew'),
        $pageDelivery = $('.jsOrderV3PageDelivery'),
//        $pageComplete = $('.jsOrderV3PageComplete'),
        $validationErrors = $('.jsOrderValidationErrors'),
        errorClass = 'textfield-err',
        validateEmail = function validateEmailF(email) {
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        },
        checkEan = function checkEanF(data) {
            // Check if only digits
            var ValidChars = "0123456789",
                i, digit, originalCheck, even, odd, total, checksum, eanCode;

            eanCode = data.toString().replace(/\s+/g, '');

            for (i = 0; i < eanCode.length; i++) {
                digit = eanCode.charAt(i);
                if (ValidChars.indexOf(digit) == -1) return false;
            }

            // Add five 0 if the code has only 8 digits
            if (eanCode.length == 8 ) eanCode = "00000" + eanCode;
            // Check for 13 digits otherwise
            else if (eanCode.length != 13) return false;

            // Get the check number
            originalCheck = eanCode.substring(eanCode.length - 1);
            eanCode = eanCode.substring(0, eanCode.length - 1);

            // Add even numbers together
            even = Number(eanCode.charAt(1)) +
                Number(eanCode.charAt(3)) +
                Number(eanCode.charAt(5)) +
                Number(eanCode.charAt(7)) +
                Number(eanCode.charAt(9)) +
                Number(eanCode.charAt(11));
            // Multiply this result by 3
            even *= 3;

            // Add odd numbers together
            odd = Number(eanCode.charAt(0)) +
                Number(eanCode.charAt(2)) +
                Number(eanCode.charAt(4)) +
                Number(eanCode.charAt(6)) +
                Number(eanCode.charAt(8)) +
                Number(eanCode.charAt(10));

            // Add two totals together
            total = even + odd;

            // Calculate the checksum
            // Divide total by 10 and store the remainder
            checksum = total % 10;
            // If result is not 0 then take away 10
            if (checksum != 0) {
                checksum = 10 - checksum;
            }

            // Return the result
            return checksum == originalCheck;
        },
        showError = function showErrorF(errArr) {
            var text = '';
            if (!$errorBlock) $orderContent.prepend($('<div />',{id: 'OrderV3ErrorBlock'}));
            $.each(errArr, function(i,val){
                text += val;
                if (i != errArr - 1) text += '<br/>';
            });
            $errorBlock.html(text).show()
        };

    if ($validationErrors.length) {
        console.warn('Validation errors', $validationErrors);
    }

    // PAGE NEW

    // проверка телефона и email
    $pageNew.find('form').on('submit', function (e) {
        var error = [],
            $phoneInput = $('[name=user_info\\[phone\\]]'),
            $emailInput = $('[name=user_info\\[email\\]]'),
            $nameInput =  $('[name=user_info\\[first_name\\]]'),
            $bonusCardInput =  $('[name=user_info\\[bonus_card_number\\]]'),
            phone = $phoneInput.val().replace(/\s+/g, '');

        if (!/\+7\(\d{3}\)\d{3}-\d{2}-\d{2}/.test(phone)) {
            error.push('Неверный формат телефона');
            $phoneInput.addClass(errorClass);
        }

        if ($emailInput.val().length != 0 && !validateEmail($emailInput.val())) {
            error.push('Неверный формат E-mail');
            $emailInput.addClass(errorClass);
        }

        if ($nameInput.val().length == 0) {
            error.push('Поле имени не может быть пустым');
            $nameInput.addClass(errorClass);
        }

        if ($bonusCardInput.val().length != 0 && !checkEan($bonusCardInput.val())) {
            error.push('Неверный код карты лояльности');
            $bonusCardInput.addClass(errorClass);
        }

        if (error.length != 0) {
            showError(error);
            e.preventDefault();
            $body.trigger('trackUserAction', ['6_2 Далее_ошибка_Получатель', 'Поле ошибки: '+error.join(', ')])
        }
    });

    // PAGE DELIVERY

    $pageDelivery.on('submit', 'form', function(e){
        var error = [];

        if (!$('.jsAcceptAgreement').is(':checked')) {
            error.push('Необходимо согласие с информацией о продавце и его офертой');
        }

        // Доставка
        if ($('.orderCol_delivrLst_i-act').text().indexOf('Доставка') != -1) {
            if (ENTER.OrderV3.address.buildingName() == '') error.push('Укажите адрес доставки');
        }

        $('.orderCol_addrs_tx').each(function(i,val){
            if ($(val).text().replace(/\s+/, '').length == 0) error.push('Укажите адрес самовывоза');
        });

        if (error.length != 0) {
            $errorBlock = $orderContent.find('#OrderV3ErrorBlock'); // TODO не очень хорошее поведение
            showError(error);
            e.preventDefault();
            $body.trigger('trackUserAction', ['15_2 Оформить_ошибка_Доставка', 'Поле ошибки: '+error.join(', ')]);
        } else {
            $body.trigger('trackUserAction', ['15_1 Оформить_успешно_Доставка_ОБЯЗАТЕЛЬНО']);
        }

    });

}(jQuery));