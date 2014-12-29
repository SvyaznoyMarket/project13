<?php

use \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity;

$f = function(
    \Helper\TemplateHelper $helper,
    $topMessage = '',
    $bottomMessage = 'Вы будете перенаправлены на сайт платежной системы',
    \Model\Order\Entity $order,
    $orderPayment,
    $blockVisible = false
) { ?>

    <!-- Блок оплата платежные системы -->
    <div class="orderPayment orderPaymentWeb jsOnlinePaymentBlock <?= $blockVisible ? 'jsOnlinePaymentBlockVisible' : '' ?>" style="display: <?= $blockVisible ? 'block' : 'none' ?>">
        <!-- Заголовок-->
        <!-- Блок в обводке -->
        <div class="orderPayment_block orderPayment_noOnline">

            <div class="orderPayment_msg orderPayment_noOnline_msg">
                <div class="orderPayment_msg_head">
                    <? if ($topMessage) : ?>
                        <?= $topMessage ?>
                    <? else : ?>
                        К оплате: <?= $helper->formatPrice($order->getSum()) ?> <span class="rubl">p</span>
                    <? endif ?>
                </div>
                <ul class="orderPaymentWeb_lst clearfix">
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
                </ul>
                <div class="orderPayment_msg_info">
                    <?= $bottomMessage ?>
                </div>
            </div>
        </div>
    </div>

<? }; return $f;