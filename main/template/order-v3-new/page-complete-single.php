<?php

use \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity, \Model\PaymentMethod\PaymentGroup\PaymentGroupEntity;

return function(
    \Helper\TemplateHelper $helper,
    $orders,
    $ordersPayment,
    $products,
    $userEntity,
    $sessionIsReaded,
    $banks,
    $creditData,
    $subscribe,
    $errors
) {
    /** @var \Model\Product\Entity[] $products */
    $page = new \View\OrderV3\CompletePage();
    /** @var \Model\Order\Entity $order */
    $order = reset($orders);
    /* @var \Model\PaymentMethod\PaymentEntity|null $orderPayment */
    $orderPayment = @$ordersPayment[$order->getNumber()];
    // Онлайн оплата возможна при существовании такой группы
    $isOnlinePaymentPossible =
        (bool)$orderPayment
        ? (
            array_key_exists(PaymentGroupEntity::PAYMENT_NOW, $orderPayment->groups)
            && !$order->isPaid()
            && !$order->isCredit()
            && !$order->isPaidBySvyaznoy()
        )
        : false;
    // При создании заказа выбрана онлайн-оплата
    $isOnlinePaymentChecked =
        ($orderPayment && $order->getPaymentId() && isset($orderPayment->methods[$order->getPaymentId()]))
        ? $orderPayment->methods[$order->getPaymentId()]->isOnline
        : false
    ;
?>
    <div class="order__wrap">
    <section class="orderCnt jsNewOnlineCompletePage"
         data-order-id="<?= $order->getId() ?>"
         data-order-number="<?= $order->getNumber() ?>"
         data-order-number-erp="<?= $order->getNumberErp() ?>"
    >

        <!-- Блок оплата -->
        <div class="orderPayment_wrap">
            <!-- Заголовок-->
            <div class="orderPayment_head">
                <? if ($userEntity) : ?>
                    Оформлен заказ № <a href="<?= \App::router()->generate('user.order', ['orderId' =>$order->getId()]) ?>" class="orderPayment_num"><?= $order->getNumberErp() ?></a>
                <? else: ?>
                    Оформлен заказ № <?= $order->getNumberErp() ?>
                <? endif ?>
            </div>

            <?= $helper->render('order-v3-new/complete-blocks/_errors', ['errors' => $errors]) ?>

            <? if ($isOnlinePaymentPossible && $isOnlinePaymentChecked): ?>
                <?= $helper->render('order-v3-new/complete-blocks/_online-payment-single', ['order' => $order, 'orderPayment' => $orderPayment, 'blockVisible' => true]) ?>
            <? endif ?>

            <? if (!$order->isCredit()): ?>
                <? if ($order->getDeliveryTypeId() == 3 || $order->getDeliveryTypeId() == 4 || $order->point) : ?>
                    <?= $helper->render('order-v3-new/complete-blocks/_point', ['order' => $order]) ?>
                <? endif ?>

                <? if ($order->getDeliveryTypeId() == 1): ?>
                    <?= $helper->render('order-v3-new/complete-blocks/_delivery', ['order' => $order]) ?>
                <? endif ?>
            <? endif ?>

            <? if ($isOnlinePaymentPossible && !$isOnlinePaymentChecked): ?>
                    <?= $helper->render('order-v3-new/complete-blocks/_online-payments', ['order' => $order, 'orderPayment' => $orderPayment, 'blockVisible' => true, 'title' => 'Оплатить онлайн со скидкой']) ?>
            <? endif ?>

        </div>

        <? if ($order->isCredit()): ?>
            <?= $helper->render('order-v3-new/complete-blocks/_credit', ['order' => $order, 'creditData' => $creditData, 'banks' => $banks]) ?>
        <? endif ?>

        <?= $orderPayment && $orderPayment->hasSvyaznoyClub() && !$order->isPaidBySvyaznoy() ? $helper->render('order-v3-new/complete-blocks/_svyaznoy-club') : '' ?>

        <? if (\App::config()->flocktory['exchange'] && !$order->isCredit()): ?>
            <div class="i-flocktory orderPayment orderPayment--static" data-fl-action="exchange" data-fl-spot="thankyou2" data-fl-username="<?= $order->getFirstName() ?>" data-fl-user-email="<?= $order->email ?>"></div>
        <? endif ?>

        <div class="orderCompl orderCompl_final clearfix">
            <a class="orderCompl_continue_link" href="<?= $helper->url('homepage') ?>">Вернуться на главную</a>
        </div>
    </section>
    </div>
    <? if (!$isOnlinePaymentPossible) : ?>
        <!--Аналитика-->
        <div class="jsGAOnlinePaymentNotPossible"></div>
    <? endif ?>

    <? // Показываем флоктори, если покупатель вернулся после оплаты заказа ?>
    <? if ($order->isPaid()): ?>
        <?= $helper->render('order-v3/partner-counter/_flocktory-complete',[
            'orders'    => $orders,
            'products'  => $products,
        ]); ?>
    <? endif; ?>

    <? if (!$sessionIsReaded): ?>
        <span class="js-orderV3New-complete-subscribe" data-value="<?=$helper->json(['subscribe' => $subscribe, 'email' => isset($orders[0]->email) ? $orders[0]->email : null])?>"></span>

        <?
        // Если сесиия уже была прочитана, значит юзер обновляет страницу, не трекаем партнёров вторично
        echo $page->render('order/_analytics', array(
            'orders'       => $orders,
            'productsById' => $products,
        ));

        echo $page->render('order/partner-counter/_complete', [
            'orders'       => $orders,
            'productsById' => $products,
        ]);

        echo $helper->render('order/__analyticsData', ['orders' => $orders, 'productsById' => $products]);

        echo $helper->render('order/__saleAnalytics', ['orders' => $orders]);

        /* Показываем флоктори без нарушения конверсии онлайн-оплаты (т.е. не выбран онлайновый метод оплаты) */
        if (!$isOnlinePaymentChecked) {
            echo $helper->render('order-v3/partner-counter/_flocktory-complete',[
                'orders'    => $orders,
                'products'  => $products,
            ]);
        }

        ?>
    <? endif ?>

<? };



