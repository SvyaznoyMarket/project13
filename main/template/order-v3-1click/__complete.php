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
    /** @var \Model\Order\Entity|null $order */
    $order = reset($orders) ?: null;
    if (!$order) return '';

    /** @var \Model\PaymentMethod\PaymentEntity|null $orderPayment */
    $orderPayment = isset($ordersPayment[$order->getNumber()]) ? $ordersPayment[$order->getNumber()] : null;

    $isOnlinePaymentPossible =
        (bool)$orderPayment
        && $orderPayment->getOnlineMethods()
        && !$order->isPaid()
        && !$order->isCredit()
        && !$order->isPaidBySvyaznoy()
    ;
?>

<? foreach ($orders as $order): ?>
    <div class="orderOneClick jsOneClickCompletePage" data-order-id="<?= $order->getId() ?>" data-order-number="<?= $order->getNumber() ?>">
        <div class="orderU_fldsbottom ta-c orderOneClick_cmpl">
            <? if (\App::user()->getEntity()) : ?>
                <p class="orderOneClick_cmpl_t">Оформлен заказ № <a class="orderOneClick__order-link" href="<?= \App::router()->generateUrl('user.order', ['orderId' =>$order->getId()]) ?>"><?= $order->getNumberErp() ?></a></p>
            <? else : ?>
                <p class="orderOneClick_cmpl_t">Оформлен заказ № <?= $order->getNumberErp() ?></p>
            <? endif ?>
            <p class="orderOneClick_recall" style="margin-bottom: 20px;">Наш сотрудник позвонит Вам для уточнения деталей заказа.</p>
        </div>

        <? if ($isOnlinePaymentPossible): ?>
            <?= $helper->render('order-v3-new/complete-blocks/_online-payments', ['order' => $order, 'orderPayment' => $orderPayment]) ?>
        <? endif ?>
    </div>
<? endforeach ?>

<?
    switch (\App::partner()->getName()) {
        case \Partner\Counter\Actionpay::NAME:
            echo (new \Templating\HtmlLayout())->tryRender('order/partner-counter/_actionpay-complete', ['orders' => $orders, 'productsById' => $productsById]);
            break;
        case 'admitad':
            echo (new \Templating\HtmlLayout())->tryRender('order/partner-counter/_admitad-complete-pixel', ['orders' => $orders, 'productsById' => $productsById]);
            break;
    }

    echo (new \Templating\HtmlLayout())->tryRender('order/partner-counter/_admitad-complete-retag', ['orders' => $orders]);
?>

<? }; return $f;
