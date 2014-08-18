;(function($) {

    var $orderContent = $('.orderCnt'),
        $errorBlock = $orderContent.find('#OrderV3ErrorBlock'),
        $pageNew = $('.jsOrderV3PageNew'),
        $pageDelivery = $('.jsOrderV3PageDelivery'),
//        $pageComplete = $('.jsOrderV3PageComplete'),
        $validationErrors = $('.jsOrderValidationErrors'),
        validateEmail = function validateEmailF(email) {
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        },
        showError = function showErrorF(message) {
            if (!$errorBlock) $orderContent.prepend($('<div />',{id: 'OrderV3ErrorBlock'}));
            $errorBlock.text(message).show()
        };

    if ($validationErrors.length) {
        console.warn('Validation errors', $validationErrors);
    }

    // PAGE NEW

    // проверка телефона и email
    $pageNew.find('form').on('submit', function (e) {
        var error = false,
            $phoneInput = $('[name=user_info\\[phone\\]]'),
            $emailInput = $('[name=user_info\\[email\\]]'),
            phone = $phoneInput.val().replace(/\s+/g, '');

        if (!/8\d{10}/.test(phone)) error = 'Неверный формат телефона';
        if ($emailInput.val().length != 0 && !validateEmail($emailInput.val())) error = 'Неверный формат E-mail';

        if (error) {
            showError(error);
            e.preventDefault();
        }
    });

    // PAGE DELIVERY

    $pageDelivery.find('form').on('submit', function(e){
        var error = false;

        if (!$('.jsAcceptAgreement').is(':checked')) error = 'Необходимо согласие с информацией о продавце и его офертой';

        if (error) {
            showError(error);
            e.preventDefault()
        }

    });

}(jQuery));