<?

use \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity;

$f = function(
    \Model\PaymentMethod\PaymentEntity $orderPayment
) { ?>

    <? if (array_key_exists(PaymentMethodEntity::PAYMENT_CARD_ONLINE ,$orderPayment->methods)) : ?>
        <li class="orderPaymentWeb_lst-i jsPaymentMethod" data-value="<?= PaymentMethodEntity::PAYMENT_CARD_ONLINE ?>">
            <div class="orderPaymentWeb_lst_sys-logo"><img class="orderPaymentWeb_lst_sys-logo-img" src="/styles/order/img/logo-visa.jpg"></div>
            <button class="orderPayment_btn orderPayment_btn-toggle btn3">Оплатить</button>
            <a class="orderPaymentWeb_lst_sys" href="#"><span class="undrl">Банковская карта</span></a>
        </li>
    <? endif ?>
    <? if (array_key_exists(PaymentMethodEntity::PAYMENT_PAYPAL ,$orderPayment->methods)) : ?>
        <li class="orderPaymentWeb_lst-i jsPaymentMethod" data-value="<?= PaymentMethodEntity::PAYMENT_PAYPAL ?>">
            <div class="orderPaymentWeb_lst_sys-logo"><img class="orderPaymentWeb_lst_sys-logo-img" src="/styles/order/img/logo-paypal.jpg"></div>
            <button class="orderPayment_btn orderPayment_btn-toggle btn3">Оплатить</button>
            <a class="orderPaymentWeb_lst_sys" href="#"><span class="undrl">PayPal</span></a>
        </li>
    <? endif ?>
    <? if (false) : ?>
        <li class="orderPaymentWeb_lst-i">
            <div class="orderPaymentWeb_lst_sys-logo"><img class="orderPaymentWeb_lst_sys-logo-img" src="/styles/order/img/logo-yamoney.jpg"></div>
            <button class="orderPayment_btn orderPayment_btn-toggle btn3">Оплатить</button>
            <a class="orderPaymentWeb_lst_sys" href="#"><span class="undrl">Яндекс.Деньги</span></a>
        </li>
    <? endif ?>
    <? if (array_key_exists(PaymentMethodEntity::PAYMENT_PSB ,$orderPayment->methods)) : ?>
        <li class="orderPaymentWeb_lst-i jsPaymentMethod" data-value="<?= PaymentMethodEntity::PAYMENT_PSB ?>">
            <div class="orderPaymentWeb_lst_sys-logo"> <img src="/styles/order/img/logo-psb.jpg"></div>
            <button class="orderPayment_btn orderPayment_btn-toggle btn3">Оплатить</button>
            <a class="orderPaymentWeb_lst_sys" href="#"><span class="undrl">Интернет-банк Промсвязьбанка</span></a>
        </li>
    <? endif ?>

<? }; return $f;