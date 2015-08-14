<?php

use \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity as PaymentMethod;

$f = function (
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order
) { ?>

    <div class="order-payments">
        <div class="order-payments__title">Способы оплаты</div>
        <? if (isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CASH]) || isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CARD_ON_DELIVERY])) : ?>
            <div class="paymentRow">
                <? $checked = $order->payment_method_id == PaymentMethod::PAYMENT_CASH || $order->payment_method_id == PaymentMethod::PAYMENT_CARD_ON_DELIVERY; ?>
                <input id="payment-cash" type="radio" name="payment-type[]" value="by_cash" class="paymentRow__it custom-input custom-input_radio js-payment-method-radio" checked />
                <label for="payment-cash" class="paymentRow__label custom-label">При получении</label>

                <? if ($order->delivery_group_id != 1) : /* Скрываем выбор наличные/банковская карта при самовывозе */?>
                    <div class="customSel">
                        <select class="customSel-inner js-payment-method-select">
                            <? if (array_key_exists(PaymentMethod::PAYMENT_CASH, $order->possible_payment_methods)) : ?>
                                <option value="by_cash" <?= $order->payment_method_id == PaymentMethod::PAYMENT_CASH ? 'selected' : '' ?>>наличными</option>
                            <? endif ?>
                            <? if (array_key_exists(PaymentMethod::PAYMENT_CARD_ON_DELIVERY, $order->possible_payment_methods)) : ?>
                                <option value="by_credit_card" <?= $order->payment_method_id == PaymentMethod::PAYMENT_CARD_ON_DELIVERY ? 'selected' : '' ?>>банковской картой</option>
                            <? endif ?>
                        </select>
                    </div>
                <? endif ?>

            </div>
        <? endif ?>

        <? if (isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CARD_ONLINE])) : ?>
            <div class="paymentRow jsDeliveryChooseOnline">
                <? $checked = $order->payment_method_id == PaymentMethod::PAYMENT_CARD_ONLINE; ?>
                <input id="payment-online" type="radio" name="payment-type[]" value="by_online" class="custom-input custom-input_radio js-payment-method-radio js-customInput" <?= $checked ? 'checked' : '' ?>>
                <label for="payment-online" class="custom-label customLabel-defradio2 <?= $checked ? 'mChecked' : '' ?>">Онлайн-оплата:
                    <ul class="orderPaymentWeb_lst-sm">
                        <? if (array_key_exists(PaymentMethod::PAYMENT_CARD_ONLINE, $order->possible_payment_methods)) : ?>
                            <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/visa-logo-sm.jpg"></a></li>
                        <? endif ?>
                        <? if (false) : /* Яндекс-денег пока вообще нет */ ?>
                            <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/yamoney-sm.jpg"></a></li>
                        <? endif ?>
                        <? if (array_key_exists(PaymentMethod::PAYMENT_PAYPAL, $order->possible_payment_methods)) : ?>
                            <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/paypal.png"></a></li>
                        <? endif ?>
                        <? if (array_key_exists(PaymentMethod::PAYMENT_PSB, $order->possible_payment_methods)) : ?>
                            <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/psb.png"></a></li>
                        <? endif ?>
                    </ul>
                </label>
            </div>
        <? endif ?>

        <? if (isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CREDIT])) : ?>
            <div class="paymentRow jsDeliveryChooseCredit">
                <? $checked = $order->payment_method_id == PaymentMethod::PAYMENT_CREDIT; ?>
                <input id="payment-credit" type="radio" name="payment-type[]" value="by_online_credit" class="customInput customInput-defradio2 js-payment-method-radio js-customInput" <?= $checked ? 'checked' : '' ?>>
                <label for="payment-credit" class="customLabel customLabel-defradio2 <?= $checked ? 'mChecked' : '' ?>">Купить в кредит</label><br>
            </div>
        <? endif ?>

    </div>

<? };

return $f;