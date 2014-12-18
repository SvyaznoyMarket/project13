<?php

use \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity as PaymentMethod;

$f = function (
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order
) { ?>

    <div class="paymentMethods" style="background-color: white; margin: 0 -10px; padding: 10px;">

        <? if (isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CASH]) || isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CARD_ON_DELIVERY])) : ?>
            <input type="radio" name="" value="">При получении<br>
        <? endif ?>

        <? if (isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CARD_ONLINE])) : ?>
            <input type="radio" name="" value="">Онлайн-оплата<br>
        <? endif ?>

        <? if (isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CREDIT])) : ?>
            <input type="radio" name="" value="">Купить в кредит<br>
        <? endif ?>

    </div>

<? };

return $f;