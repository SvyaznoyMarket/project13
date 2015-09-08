<?php

use \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity as PaymentMethod;

$f = function (
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order
) { ?>

    <div class="paymentMethods">
        <strong>Способы оплаты</strong>

        <!-- Новые способы оплаты - статика -->
        <div class="payment-methods__discount discount">
            <span class="discount__pay-type">Онлайн-оплата</span>
            <span class="discount__val">Скидка 15%</span>
        </div>
        <ul class="payment-methods__lst">
            <li class="payment-methods__i">
                    <input id="payment-card" type="radio" name="payment-type[]" value="by_card" class="customInput customInput-defradio2 jsPaymentMethodRadio js-customInput" checked="">
                    <label for="payment-card" class="customLabel customLabel-defradio2 mChecked">
                        Банковская карта
                        <img class="payment-methods__img" src="/styles/order-new/img/payment/pay-card.png">
                    </label>
            </li>
            <li class="payment-methods__i">
                <input id="payment-yandex" type="radio" name="payment-type[]" value="by_yandex" class="customInput customInput-defradio2 jsPaymentMethodRadio js-customInput" >
                <label for="payment-yandex" class="customLabel customLabel-defradio2">
                    Яндекс.Деньги
                    <img class="payment-methods__img" src="/styles/order-new/img/payment/pay-yandex.png">
                </label>
            </li>
            <li class="payment-methods__i">
                <input id="payment-webmoney" type="radio" name="payment-type[]" value="by_webmoney" class="customInput customInput-defradio2 jsPaymentMethodRadio js-customInput" >
                <label for="payment-webmoney" class="customLabel customLabel-defradio2">
                    WebMoney
                    <img class="payment-methods__img" src="/styles/order-new/img/payment/pay-webmoney.png">
                </label>
            </li>
            <li class="payment-methods__i">
                <input id="payment-qiwi" type="radio" name="payment-type[]" value="by_qiwi" class="customInput customInput-defradio2 jsPaymentMethodRadio js-customInput" >
                <label for="payment-qiwi" class="customLabel customLabel-defradio2">
                    Qiwi
                    <img class="payment-methods__img" src="/styles/order-new/img/payment/pay-qiwi.png">
                </label>
            </li>

            <li class="payment-methods__i top-space"><!-- ставим класс top-space на элемент, который имеет сверху бОльший оступ-->
                <input id="payment-psb" type="radio" name="payment-type[]" value="by_psb" class="customInput customInput-defradio2 jsPaymentMethodRadio js-customInput" >
                <label for="payment-psb" class="customLabel customLabel-defradio2">
                    Выставить счет в PSB
                    <img class="payment-methods__img" src="/styles/order-new/img/payment/pay-psb.png">
                </label>
            </li>
            <li class="payment-methods__i">
                <input id="payment-cash" type="radio" name="payment-type[]" value="by_cash" class="customInput customInput-defradio2 jsPaymentMethodRadio js-customInput" >
                <label for="payment-cash" class="customLabel customLabel-defradio2">
                    При получении
                </label>
            </li>

        </ul>
        <!-- END Новые способы оплаты - статика -->


    </div>

<? };

return $f;