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
    $shops,
    $creditData,
    $subscribe,
    $motivationAction
) {
    /** @var $products \Model\Product\Entity[] */
    $page = new \View\OrderV3\CompletePage();
    /** @var $order \Model\Order\Entity */
    $order = reset($orders);
    /* @var $shop \Model\Shop\Entity|null */
    $shop = @$shops[$order->getShopId()];
    /* @var $orderPayment \Model\PaymentMethod\PaymentEntity|null */
    $orderPayment = @$ordersPayment[$order->getNumber()];
    // Онлайн оплата возможна при существовании такой группы
    $isOnlinePaymentPossible = (bool)$orderPayment ? array_key_exists(PaymentGroupEntity::PAYMENT_NOW, $orderPayment->groups) : false;
    // При создании заказа выбрана онлайн-оплата
    $isOnlinePaymentChecked = in_array($order->getPaymentId(), [PaymentMethodEntity::PAYMENT_CARD_ONLINE, PaymentMethodEntity::PAYMENT_PAYPAL, PaymentMethodEntity::PAYMENT_PSB]);
    ?>

    <?= $helper->render('order-v3-new/__head', ['step' => 3]) ?>

    <section class="orderCnt jsNewOnlineCompletePage"
             data-order-id="<?= $order->getId() ?>"
             data-order-number="<?= $order->getNumber() ?>"
             data-order-number-erp="<?= $order->getNumberErp() ?>"
             data-order-action="<?= $motivationAction ?>"
        >

        <!-- Блок оплата -->
        <div class="orderPayment">
            <!-- Заголовок-->
            <div class="orderPayment_head">
                <? if ($userEntity) : ?>
                    Оформлен заказ № <a href="<?= \App::router()->generate('user.order', ['orderId' =>$order->getId()]) ?>" class="orderPayment_num"><?= $order->getNumberErp() ?></a>
                <? else : ?>
                    Оформлен заказ № <?= $order->getNumberErp() ?>
                <? endif ?>
            </div>

            <? if ($isOnlinePaymentChecked && !$order->isPaid()) : ?>
                <?= $helper->render('order-v3-new/complete-blocks/_online-payments', ['order' => $order, 'orderPayment' => $orderPayment, 'blockVisible' => true]) ?>
            <? endif ?>

            <? if ($order->getPaymentId() != PaymentMethodEntity::PAYMENT_CREDIT) : ?>

                <? if ($order->getDeliveryTypeId() == 3 || $order->getDeliveryTypeId() == 4) : ?>

                <div class="orderPayment <?= $order->isPaid() ? 'orderPaid jsOrderPaid': '' ?>">
                    <!-- Блок в обводке -->
                    <div class="orderPayment_block orderPayment_noOnline">

                        <? if ($shop) : ?>

                        <div class="orderPayment_msg orderPayment_noOnline_msg">
                            <div class="orderPayment_msg_head">
                                Ждем вас <?= $order->getDeliveredAt()->format('d.m.Y') ?> в магазине
                            </div>
                            <div class="orderPayment_msg_shop markerLst_row">
                                <? if ((bool)$shop->getSubway()) : ?>
                                <span class="markerList_col markerList_col-mark">
                                    <i class="markColor" style="background-color: <?= $shop->getSubway()[0]->getLine()->getColor() ?>"></i>
                                </span>
                                <span class="markerList_col">
                                <span class="orderPayment_msg_shop_metro"><?= $shop->getSubway()[0]->getName() ?></span>
                                <? endif ?>
                                <span class="orderPayment_msg_shop_addr"><?= $shop->getAddress() ?></span>
                                    <a href="<?= \App::router()->generate('shop.show', ['regionToken' => \App::user()->getRegion()->getToken(), 'shopToken' => $shop->getToken()])?>" class="orderPayment_msg_addr_link jsCompleteOrderShowShop" target="_blank">
                                        Как добраться
                                    </a>
                                </span>
                                <? if ($order->comment) : ?>
                                    <div class="orderPayment_msg_adding">Дополнительные пожелания:<br/> «<?= $order->comment ?>»</div>
                                <? endif ?>
                            </div>
                            <div class="orderPayment_msg_info">
                                <? if ($order->isPaid()) : ?>
                                Заказ оплачен
                                <!--<div class="orderPaymentWeb_lst_sys-logo noFlnoWdt"><img src="/styles/order/img/logo-yamoney.jpg"></div>-->
                                <? elseif (in_array($order->getPaymentId(), [PaymentMethodEntity::PAYMENT_PAYPAL, PaymentMethodEntity::PAYMENT_CARD_ONLINE, PaymentMethodEntity::PAYMENT_PSB])) : ?>
                                Вы можете оплатить заказ при получении.
                                <? else : ?>
                                Оплата при получении — наличными или картой.
                                <? endif ?>
                            </div>
                        </div>

                        <? else : ?>

                            <div class="orderPayment_msg orderPayment_noOnline_msg">
                                <div class="orderPayment_msg_head">
                                    Время и место
                                </div>
                                <span class="markerList_col">
                                    Адрес и дату доставки вашего заказа уточнит по&nbsp;телефону наш менеджер.
                                </span>
                                    <? if ($order->comment) : ?>
                                        <div class="orderPayment_msg_adding">Дополнительные пожелания:<br/> «<?= $order->comment ?>»</div>
                                    <? endif ?>
                                <div class="orderPayment_msg_info">
                                    <? if ($order->isPaid()) : ?>
                                        Заказ оплачен
                                    <? else : ?>
                                        Вы сможете оплатить заказ при получении.
                                    <? endif ?>
                                </div>
                            </div>

                        <? endif ?>

                    </div>

                </div>

                <? endif ?>

                <? if ($order->getDeliveryTypeId() == 1) : ?>

                    <!-- Блок доставка -->
                    <div class="orderPayment orderDelivery <?= $order->isPaid() ? 'orderPaid': '' ?>">
                        <!-- Заголовок-->
                        <!-- Блок в обводке -->
                        <div class="orderPayment_block orderPayment_noOnline">

                            <div class="orderPayment_msg orderPayment_noOnline_msg">
                                <div class="orderPayment_msg_head">
                                    <? if ($order->getAddress()) : ?>
                                        Доставка назначена на <?= $order->getDeliveredAt()->format('d.m.Y') ?>
                                    <? else : ?>
                                        Время и место
                                    <? endif ?>
                                </div>
                                <div class="orderPayment_msg_shop markerLst_row">
                            <span class="markerList_col">
                                <? if ($order->getAddress()) : ?>
                                <span class="orderPayment_msg_shop_addr"><?= $order->getAddress() ?></span>
                                <? else : ?>
                                    Адрес и дату доставки вашего заказа уточнит по&nbsp;телефону наш менеджер.
                                <? endif ?>
                                <? if ($order->comment) : ?>
                                <div class="orderPayment_msg_adding">Дополнительные пожелания:<br/> «<?= $order->comment ?>»</div>
                                <? endif ?>
                            </span>

                                </div>
                                <div class="orderPayment_msg_info">
                                    <? if ($order->isPaid()) : ?>
                                    Заказ оплачен
                                    <!--<div class="orderPaymentWeb_lst_sys-logo noFlnoWdt"><img src="/styles/order/img/logo-yamoney.jpg"></div>-->
                                    <? elseif ($order->getPaymentId() == PaymentMethodEntity::PAYMENT_CARD_ON_DELIVERY) : ?>
                                    Оплата заказа банковской картой при получении.
                                    <? else : ?>
                                    Вы можете оплатить заказ при получении.
                                    <? endif ?>
                                </div>
                            </div>
                        </div>
                    </div>

                <? endif ?>

            <? endif ?>

        </div>

        <? if ($order->getPaymentId() == PaymentMethodEntity::PAYMENT_CREDIT) : ?>
            <?= $helper->render('order-v3-new/complete-blocks/_credit', ['order' => $order, 'creditData' => $creditData, 'banks' => $banks]) ?>
        <? endif ?>


        <?= $helper->render('order-v3-new/complete-blocks/_online-payments', ['order' => $order, 'orderPayment' => $orderPayment, 'topMessage' => 'Онлайн-оплата в два клика']) ?>

        <? if ($isOnlinePaymentPossible && !$isOnlinePaymentChecked && $order->getPaymentId() != PaymentMethodEntity::PAYMENT_CREDIT && !$motivationAction) : ?>

            <!-- Блок оплата в два клика-->
            <div class="orderPayment orderPaymentWeb jsOnlinePaymentPossible jsOnlinePaymentPossibleNoMotiv">
                <!-- Заголовок-->
                <!-- Блок в обводке -->
                <div class="orderPayment_block orderPayment_noOnline">

                    <div class="orderPayment_msg orderPayment_noOnline_msg">
                        <div class="orderPayment_msg_head">
                            Онлайн-оплата в два клика
                        </div>
                        <div class="orderPayment_msg_shop orderPayment_pay">
                            <button class="orderPayment_btn btn3">Оплатить</button>
                            <ul class="orderPaymentWeb_lst-sm">
                                <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src="/styles/order/img/visa-logo-sm.jpg"></a></li>
<!--                                <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src ="/styles/order/img/yamoney-sm.jpg"></a></li>-->
<!--                                <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src="/styles/order/img/paypal.png"></a></li>-->
                                <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src="/styles/order/img/psb.png" /></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        <? endif ?>

        <?= $motivationAction ? $helper->render('order-v3-new/complete-blocks/_online_motivation_action', ['order' => $order, 'orderPayment' => $orderPayment, 'action' => $motivationAction]) : '' ?>

        <div class="orderCompl orderCompl_final clearfix">
            <a class="orderCompl_continue_link" href="<?= $helper->url('homepage') ?>">Вернуться на главную</a>
        </div>
    </section>

    <? if (!$isOnlinePaymentPossible) : ?>
        <!--Аналитика-->
        <div class="jsGAOnlinePaymentNotPossible"></div>
    <? endif ?>

    <? if ($order->isPaid()) : ?>
        <?= $helper->render('order-v3/partner-counter/_flocktory-complete',[
            'orders'    => $orders,
            'products'  => $products,
        ]); ?>
    <? endif ?>

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
        ?>
    <? endif ?>

<? };



