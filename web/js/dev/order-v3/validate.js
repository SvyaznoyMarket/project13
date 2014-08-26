;(function($) {

    var $orderContent = $('.orderCnt'),
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
            phone = $phoneInput.val().replace(/\s+/g, '');

        if (!/8\d{10}/.test(phone)) {
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

        if (error.length != 0) {
            showError(error);
            e.preventDefault();
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
            if (!ENTER.OrderV3.address || !ENTER.OrderV3.address.building.name) error.push('Укажите адрес доставки');
        }

        $('.orderCol_addrs_tx').each(function(i,val){
            if ($(val).text().replace(/\s+/, '').length == 0) error.push('Укажите адрес самовывоза');
        });

        if (error.length != 0) {
            $errorBlock = $orderContent.find('#OrderV3ErrorBlock'); // TODO не очень хорошее поведение
            showError(error);
            e.preventDefault()
        }

    });

}(jQuery));