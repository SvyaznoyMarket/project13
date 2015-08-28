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
    $motivationAction,
    $errors
) {
    /** @var $products \Model\Product\Entity[] */
    $page = new \View\OrderV3\CompletePage();
    /** @var $order \Model\Order\Entity */
    $order = reset($orders);
    /* @var $orderPayment \Model\PaymentMethod\PaymentEntity|null */
    $orderPayment = @$ordersPayment[$order->getNumber()];
    // Онлайн оплата возможна при существовании такой группы
    $isOnlinePaymentPossible = (bool)$orderPayment ? array_key_exists(PaymentGroupEntity::PAYMENT_NOW, $orderPayment->groups) : false;
    // При создании заказа выбрана онлайн-оплата
    $isOnlinePaymentChecked = in_array($order->getPaymentId(), [PaymentMethodEntity::PAYMENT_CARD_ONLINE, PaymentMethodEntity::PAYMENT_PAYPAL, PaymentMethodEntity::PAYMENT_PSB]);
    ?>

    <section class="orderCnt jsNewOnlineCompletePage"
             data-order-id="<?= $order->getId() ?>"
             data-order-number="<?= $order->getNumber() ?>"
             data-order-number-erp="<?= $order->getNumberErp() ?>"
             data-order-action="<?= $motivationAction ?>"
        >

        <!-- Блок оплата -->
        <div class="orderPayment_wrap">
            <!-- Заголовок-->
            <div class="orderPayment_head">
                <? if ($userEntity) : ?>
                    Оформлен заказ №<a href="<?= \App::router()->generate('user.order', ['orderId' =>$order->getId()]) ?>" class="orderPayment_num"><?= $order->getNumberErp() ?></a>
                <? else : ?>
                    Оформлен заказ №<?= $order->getNumberErp() ?>
                <? endif ?>
            </div>

            <?= $helper->render('order/complete-blocks/_errors', ['errors' => $errors]) ?>

            <? if ($isOnlinePaymentChecked && !$order->isPaid()) : ?>
                <?= $helper->render('order/complete-blocks/_online-payments', ['order' => $order, 'orderPayment' => $orderPayment, 'blockVisible' => true]) ?>
            <? endif ?>

            <? if (!$order->isCredit()) : ?>

                <? if ($order->getDeliveryTypeId() == 3 || $order->getDeliveryTypeId() == 4 || $order->point) : ?>
                    <?= $helper->render('order/complete-blocks/_point', ['order' => $order]) ?>
                <? endif ?>

                <? if ($order->getDeliveryTypeId() == 1) : ?>
                    <?= $helper->render('order/complete-blocks/_delivery', ['order' => $order]) ?>
                <? endif ?>

            <? endif ?>

        </div>

        <? if ($order->isCredit()) : ?>
            <?= $helper->render('order/complete-blocks/_credit', ['order' => $order, 'creditData' => $creditData, 'banks' => $banks]) ?>
        <? endif ?>


        <?= $helper->render('order/complete-blocks/_online-payments', ['order' => $order, 'orderPayment' => $orderPayment, 'topMessage' => 'Онлайн-оплата в два клика']) ?>

        <?= $motivationAction && !$order->isPaidBySvyaznoy() ? $helper->render('order/complete-blocks/_online_motivation_action', ['order' => $order, 'orderPayment' => $orderPayment, 'action' => $motivationAction]) : '' ?>

        <?= $orderPayment && $orderPayment->hasSvyaznoyClub() && !$order->isPaidBySvyaznoy() ? $helper->render('order/complete-blocks/_svyaznoy-club') : '' ?>

        <? /* if (\App::config()->flocktoryExchange['enabled'] && !$order->isCredit()) : ?>
<!--            <div>-->
                <div class="i-flocktory orderPayment" data-fl-action="exchange" data-fl-spot="thankyou2" data-fl-username="<?= $order->getFirstName() ?>" data-fl-user-email="<?= $order->email ?>"></div>
<!--            </div>-->
        <? endif */ ?>

        <div class="orderCompl orderCompl_final clearfix">
            <a class="orderCompl_continue_link" href="<?= $helper->url('homepage') ?>">Вернуться на главную</a>
        </div>
    </section>

    <? if (!$isOnlinePaymentPossible) : ?>
        <!--Аналитика-->
        <div class="jsGAOnlinePaymentNotPossible"></div>
    <? endif ?>

    <? // Показываем флоктори, если покупатель вернулся после оплаты заказа ?>
    <? /* if ($order->isPaid()) : ?>
        <?= $helper->render('order/partner-counter/_flocktory-complete',[
            'orders'    => $orders,
            'products'  => $products,
        ]); ?>
    <? endif; */ ?>

    <? if (!$sessionIsReaded): ?>
        <span class="js-orderV3New-complete-subscribe" data-value="<?=$helper->json(['subscribe' => $subscribe, 'email' => isset($orders[0]->email) ? $orders[0]->email : null])?>"></span>

        <?
        // Если сесиия уже была прочитана, значит юзер обновляет страницу, не трекаем партнёров вторично
        echo $page->render('order/_analytics', array(
            'orders'       => $orders,
            'productsById' => $products,
        ));

        /*
        echo $page->render('order/partner-counter/_complete', [
            'orders'       => $orders,
            'productsById' => $products,
        ]);
        */

        echo $helper->render('order/__analyticsData', ['orders' => $orders, 'productsById' => $products]);

        echo $helper->render('order/__saleAnalytics', ['orders' => $orders]);

        /* Показываем флоктори без нарушения конверсии онлайн-оплаты (т.е. не выбран онлайновый метод оплаты) */
        /*
        if (!$isOnlinePaymentChecked) {
            echo $helper->render('order/partner-counter/_flocktory-complete',[
                'orders'    => $orders,
                'products'  => $products,
            ]);
        }
        */

        ?>
    <? endif ?>

<? };



