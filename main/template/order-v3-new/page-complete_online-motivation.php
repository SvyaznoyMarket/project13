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
                    Оформлен заказ № <a href="<?= \App::router()->generate('user.order', ['orderId' =>$order->getId()]) ?>" class="orderPayment_num"><?= $order->getNumberErp() ?></a>
                <? else : ?>
                    Оформлен заказ № <?= $order->getNumberErp() ?>
                <? endif ?>
            </div>

            <?= $helper->render('order-v3-new/complete-blocks/_errors', ['errors' => $errors]) ?>

            <? if ($isOnlinePaymentChecked && !$order->isPaid()) : ?>
                <?= $helper->render('order-v3-new/complete-blocks/_online-payments', ['order' => $order, 'orderPayment' => $orderPayment, 'blockVisible' => true]) ?>
            <? endif ?>

            <? if (!$order->isCredit()) : ?>

                <? if ($order->getDeliveryTypeId() == 3 || $order->getDeliveryTypeId() == 4 || $order->point) : ?>
                    <?= $helper->render('order-v3-new/complete-blocks/_point', ['order' => $order]) ?>
                <? endif ?>

                <? if ($order->getDeliveryTypeId() == 1) : ?>
                    <?= $helper->render('order-v3-new/complete-blocks/_delivery', ['order' => $order]) ?>
                <? endif ?>

            <? endif ?>

        </div>

        <? if ($order->isCredit()) : ?>
            <?= $helper->render('order-v3-new/complete-blocks/_credit', ['order' => $order, 'creditData' => $creditData, 'banks' => $banks]) ?>
        <? endif ?>


        <?= $helper->render('order-v3-new/complete-blocks/_online-payments', ['order' => $order, 'orderPayment' => $orderPayment, 'topMessage' => 'Онлайн-оплата в два клика']) ?>

        <? if ($isOnlinePaymentPossible && !$isOnlinePaymentChecked && !$order->isCredit() && !$motivationAction && !$order->isPaidBySvyaznoy()) : ?>

            <!-- Блок оплата в два клика-->
            <div class="orderPayment orderPaymentWeb jsOnlinePaymentPossible jsOnlinePaymentPossibleNoMotiv">
                <!-- Заголовок-->
                <!-- Блок в обводке -->
                <div class="orderPayment_block orderPayment_noOnline">

                    <div class="orderPayment_msg orderPayment_noOnline_msg">
                        <div class="orderPayment_msg_head">
                            Онлайн-оплата
                        </div>
                        <div class="order-payment__sum-msg">
                            К оплате <span class="order-payment__sum">1500 <span class="rubl">p</span></span>
                        </div>
                        <div class="orderPayment_msg_shop orderPayment_pay">
                            <ul class="orderPaymentWeb_lst-sm">
                                <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src="/styles/order-new/img/payment/pay-card.png"></a></li>
                                <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src="/styles/order-new/img/payment/pay-yandex.png"></a></li>
                                <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src="/styles/order-new/img/payment/pay-qiwi.png"></a></li>
                                <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src="/styles/order-new/img/payment/pay-webmoney.png"></a></li>
                                <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src="/styles/order-new/img/payment/pay-psb.png"></a></li>
                            </ul>
                            <button class="orderPayment_btn btn3">Оплатить онлайн</button>
                        </div>
                        <p class="orderPayment_msg_hint">Вы будете перенаправлены на сайт платежной системы.</p>
                    </div>
                </div>
            </div>


            <!-- статика - блоки новых способов оплаты -->

            <!-- блок когда была выбран конкретный способ оплаты -->
            <div class="orderPayment orderPaymentWeb jsOnlinePaymentPossible jsOnlinePaymentPossibleNoMotiv">
                <!-- Заголовок-->
                <!-- Блок в обводке -->
                <div class="orderPayment_block orderPayment_noOnline">

                    <div class="orderPayment_msg orderPayment_noOnline_msg">
                        <div class="orderPayment_msg_head">
                            Онлайн-оплата
                        </div>

                        <div class="orderPayment_msg_shop orderPayment_pay">
                            <!-- Здесь выводим иконки побольше -->
                            <div class="order-payment__choosed">
                                Qiwi <img src="/styles/order-new/img/payment/pay-qiwi-big.png">
                            </div>

                            <button class="orderPayment_btn btn3">Оплатить онлайн</button>
                        </div>
                        <p class="orderPayment_msg_hint">Вы будете перенаправлены на сайт платежной системы.</p>
                    </div>
                </div>
            </div>
            <!-- END блок когда была выбран конкретный способ оплаты -->

            <!-- блок появляется, когда нажали оплатить сейчас -->
            <div class="orderPayment orderPaymentWeb">
                <!-- Заголовок-->
                <!-- Блок в обводке -->
                <div class="orderPayment_block orderPayment_noOnline">

                    <div class="orderPayment_msg orderPayment_noOnline_msg">
                        <div class="orderPayment_msg_head">
                            Онлайн-оплата
                        </div>
                        <div class="order-payment__sum-msg">
                            К оплате <span class="order-payment__sum">1500 <span class="rubl">p</span></span>
                        </div>

                        <!-- Этот блок идентичен блоку, который используется на 2м шаге, но у меня не получилось здесь отрендерить его-->
                        <!-- Новые способы оплаты - статика -->
                        <div class="payment-methods__discount discount">
                            <span class="discount__pay-type">Онлайн-оплата</span>
                            <span class="discount__val">Скидка 15%</span>
                        </div>
                        <ul class="payment-methods__lst">
                            <li class="payment-methods__i">
                                <input id="payment-card" type="radio" name="payment-type[]" value="by_card" class="customInput customInput-defradio2 jsPaymentMethodRadio js-customInput" checked="">
                                <label for="payment-card" class="customLabel customLabel-defradio2 mChecked">
                                    Банковская карта
                                    <img class="payment-methods__img" src="/styles/order-new/img/payment/pay-card.png">
                                </label>
                            </li>
                            <li class="payment-methods__i">
                                <input id="payment-yandex" type="radio" name="payment-type[]" value="by_yandex" class="customInput customInput-defradio2 jsPaymentMethodRadio js-customInput" >
                                <label for="payment-yandex" class="customLabel customLabel-defradio2">
                                    Яндекс.Деньги
                                    <img class="payment-methods__img" src="/styles/order-new/img/payment/pay-yandex.png">
                                </label>
                            </li>
                            <li class="payment-methods__i">
                                <input id="payment-webmoney" type="radio" name="payment-type[]" value="by_webmoney" class="customInput customInput-defradio2 jsPaymentMethodRadio js-customInput" >
                                <label for="payment-webmoney" class="customLabel customLabel-defradio2">
                                    WebMoney
                                    <img class="payment-methods__img" src="/styles/order-new/img/payment/pay-webmoney.png">
                                </label>
                            </li>
                            <li class="payment-methods__i">
                                <input id="payment-qiwi" type="radio" name="payment-type[]" value="by_qiwi" class="customInput customInput-defradio2 jsPaymentMethodRadio js-customInput" >
                                <label for="payment-qiwi" class="customLabel customLabel-defradio2">
                                    Qiwi
                                    <img class="payment-methods__img" src="/styles/order-new/img/payment/pay-qiwi.png">
                                </label>
                            </li>

                            <li class="payment-methods__i top-space"><!-- ставим класс top-space на элемент, который имеет сверху бОльший оступ-->
                                <input id="payment-psb" type="radio" name="payment-type[]" value="by_psb" class="customInput customInput-defradio2 jsPaymentMethodRadio js-customInput" >
                                <label for="payment-psb" class="customLabel customLabel-defradio2">
                                    Выставить счет в PSB
                                    <img class="payment-methods__img" src="/styles/order-new/img/payment/pay-psb.png">
                                </label>
                            </li>
                            <li class="payment-methods__i">
                                <input id="payment-cash" type="radio" name="payment-type[]" value="by_cash" class="customInput customInput-defradio2 jsPaymentMethodRadio js-customInput" >
                                <label for="payment-cash" class="customLabel customLabel-defradio2">
                                    При получении
                                </label>
                            </li>

                        </ul>
                        <!-- END Новые способы оплаты - статика -->

                        <div class="orderPayment_msg_shop orderPayment_pay">
                            <button class="orderPayment_btn btn3">Оплатить</button>
                        </div>
                        <p class="orderPayment_msg_hint">Вы будете перенаправлены на сайт платежной системы.</p>
                    </div>
                </div>
            </div>
            <!-- END блок появляется, когда нажали оплатить сейчас -->

            <!--END статика - блоки новых способов оплаты -->


        <? endif ?>

        <?= $motivationAction && !$order->isPaidBySvyaznoy() ? $helper->render('order-v3-new/complete-blocks/_online_motivation_action', ['order' => $order, 'orderPayment' => $orderPayment, 'action' => $motivationAction]) : '' ?>

        <?= $orderPayment && $orderPayment->hasSvyaznoyClub() && !$order->isPaidBySvyaznoy() ? $helper->render('order-v3-new/complete-blocks/_svyaznoy-club') : '' ?>

        <? if (\App::config()->flocktoryExchange['enabled'] && !$order->isCredit()) : ?>
<!--            <div>-->
                <div class="i-flocktory orderPayment" data-fl-action="exchange" data-fl-spot="thankyou2" data-fl-username="<?= $order->getFirstName() ?>" data-fl-user-email="<?= $order->email ?>"></div>
<!--            </div>-->
        <? endif ?>


        <div class="orderCompl orderCompl_final clearfix">
            <a class="orderCompl_continue_link" href="<?= $helper->url('homepage') ?>">Вернуться на главную</a>
        </div>
    </section>

    <? if (!$isOnlinePaymentPossible) : ?>
        <!--Аналитика-->
        <div class="jsGAOnlinePaymentNotPossible"></div>
    <? endif ?>

    <? // Показываем флоктори, если покупатель вернулся после оплаты заказа ?>
    <? if ($order->isPaid()) : ?>
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



