<?php

use \Model\PaymentMethod\PaymentGroup\PaymentGroupEntity;

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Order\Entity[] $orders
 * @param \Model\PaymentMethod\PaymentEntity[] $ordersPayment
 * @param \Model\Product\Entity[] $productsById
 * @return string
 */
$f = function(
    \Helper\TemplateHelper $helper,
    $orders,
    $ordersPayment,
    $productsById
) {

    $order = reset($orders) ?: null;
    if (!$order) return '';
?>

<? foreach ($orders as $order): ?>
    <div class="orderOneClick jsOneClickCompletePage" data-order-id="<?= $order->getId() ?>" data-order-number="<?= $order->getNumber() ?>">
        <div class="orderU_fldsbottom ta-c orderOneClick_cmpl">
            <? if (\App::user()->getEntity()) : ?>
                <p class="orderOneClick_cmpl_t">Оформлен заказ № <a class="orderOneClick__order-link" href="<?= \App::router()->generate('user.order', ['orderId' =>$order->getId()]) ?>"><?= $order->getNumberErp() ?></a></p>
            <? else : ?>
                <p class="orderOneClick_cmpl_t">Оформлен заказ № <?= $order->getNumberErp() ?></p>
            <? endif ?>
            <p class="orderOneClick_recall" style="margin-bottom: 20px;">Наш сотрудник позвонит Вам для уточнения деталей заказа.</p>
        </div>

        <? if ($ordersPayment[$order->getNumber()] && array_key_exists(PaymentGroupEntity::PAYMENT_NOW, $ordersPayment[$order->getNumber()]->groups)) : ?>

            <?= $helper->render('order-v3-new/complete-blocks/_online-payments', [
                'order' => $order,
                'orderPayment' => $ordersPayment[$order->getNumber()],
                'blockVisible' => false,
                'bottomMessage' => 'Вы будете перемещены на сайт платежной системы'
            ]) ?>

            <!-- Блок оплата в два клика-->
            <div class="orderPayment orderPaymentWeb jsOnlinePaymentPossible">
                <div class="orderPayment_block orderPayment_noOnline">

                    <div class="orderPayment_msg orderPayment_noOnline_msg">
                        <div class="orderPayment_msg_head">
                            Онлайн-оплата в два клика
                        </div>
                        <div class="orderPayment_msg_shop orderPayment_pay">
                            <button class="orderPayment_btn btn3">Оплатить</button>
                            <ul class="orderPaymentWeb_lst-sm">
                                <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src="/styles/order/img/visa-logo-sm.jpg"></a></li>
                                <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src="/styles/order/img/psb.png" /></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <? endif ?>
    </div>
<? endforeach ?>

<? if (\App::partner()->getName() === \Partner\Counter\Actionpay::NAME): ?>
    <?= (new \Templating\HtmlLayout())->tryRender('order/partner-counter/_actionpay-complete', ['orders' => $orders, 'productsById' => $productsById]) ?>
<? endif ?>

<? }; return $f;
