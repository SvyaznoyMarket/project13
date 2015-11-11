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
            ? (
            array_key_exists(PaymentGroupEntity::PAYMENT_NOW, $orderPayment->groups)
            && !$order->isPaid()
            && !$order->isCredit()
            && !$order->isPaidBySvyaznoy()
        )
        : false
    ;
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

        <? if ($isOnlinePaymentPossible): ?>
            <?= $helper->render('order-v3-new/complete-blocks/_online-payments', ['order' => $order, 'orderPayment' => $orderPayment, 'title' => 'Оплатить онлайн' . (false ? 'со скидкой 15%' : '')]) ?>
        <? endif ?>
    </div>
<? endforeach ?>

<? if (\App::partner()->getName() === \Partner\Counter\Actionpay::NAME): ?>
    <?= (new \Templating\HtmlLayout())->tryRender('order/partner-counter/_actionpay-complete', ['orders' => $orders, 'productsById' => $productsById]) ?>
<? endif ?>

<? }; return $f;
