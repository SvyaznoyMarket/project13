<?php

use \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity as PaymentMethod;

$f = function (
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order
) { ?>

    <div class="paymentMethods" style="background-color: white; margin: 0 -10px; padding: 10px;">
        <strong>Способы оплаты</strong>
        <? if (isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CASH]) || isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CARD_ON_DELIVERY])) : ?>
            <div class="paymentRow">
                <input id="payment-cash" type="radio" name="payment-type[]" value="" class="customInput customInput-radio customInput-defradio2">
                <label for="payment-cash" class="customLabel customLabel-defradio2">При получении
                    <div class="customSel">
                        <select class="customSel-inner">
                            <option>наличными</option>
                        </select>
                    </div>
                </label>
            </div>
        <? endif ?>

        <? if (isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CARD_ONLINE])) : ?>
            <div class="paymentRow">
                <input id="payment-online" type="radio" name="payment-type[]" value="" class="customInput customInput-radio customInput-defradio2">
                <label for="payment-online" class="customLabel customLabel-defradio2">Онлайн-оплата:
                <ul class="orderPaymentWeb_lst-sm">
                                <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/visa-logo-sm.jpg"></a></li>
                                <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/yamoney-sm.jpg"></a></li>
                                <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/paypal.png"></a></li>
                                <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/psb.png"></a></li>
                            </ul>
                </label>
            </div>
        <? endif ?>

        <? if (isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CREDIT])) : ?>
            <div class="paymentRow">
                <input id="payment-credit" type="radio" name="payment-type[]" value="" class="customInput customInput-radio customInput-defradio2">
                <label for="payment-credit" class="customLabel customLabel-defradio2">Купить в кредит</label><br>
            </div>
        <? endif ?>

    </div>

<? };

return $f;